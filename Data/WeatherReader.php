<?php

namespace Data;

class WeatherReader extends Reader
{

    private $aggregate;
    private $type;
    private $categoryCount;
    private $date_start;

    public function __construct($aggregate, $type, $categoryCount = []){
//        parent::__construct("D:/unwdmishit");
        parent::__construct("C:/Users/MVISSER-ZEPHYRUS/Downloads/unwdmi/data");

        $this->aggregate = $aggregate;
        $this->type = $type;
        $this->categoryCount = $categoryCount;

        $date = strtotime('today');
        switch ($aggregate){
            case 'hour':
                $date = strtotime('today - 7');
                break;
        }

        $this->date_start = $date;
    }

    protected function getColumns(){
        $position = 0;
        return [
            'id' => DataType::Integer($position, 2),
            'date' => null,
            'time' => DataType::Integer($position, 2),
            'temperature' => DataType::Double($position, 3, 1, true),
            'dew_point' => DataType::Double($position, 3, 1, true),
            'air_pressure_sea' => DataType::Double($position, 3, 1),
            'air_pressure_station' => DataType::Double($position, 3, 1),
            'visibility' => DataType::Double($position, 2, 1),
            'wind_speed' => DataType::Double($position, 2, 1),
            'rainfall' => DataType::Double($position, 3, 2),
            'snowfall' => DataType::Double($position, 3, 1, true),
            'events' => DataType::Integer($position, 1),
            'cloudiness' => DataType::Double($position, 2, 1),
            'wind_direction' => DataType::Integer($position, 2)
        ];
    }

    protected function getFiles(){
        $files = [];
        $categories = [];
        foreach ($this->getFilters() as $filter){
            if ($filter[0] == 'id'){
                if (!is_array($filter[2])){
                    $filter[2] = [$filter[2]];
                }

                foreach ($filter[2] as $value) {
                    $category = substr($value, -6, -4);
                    if (empty($category)) {
                        $category = 0;
                    }

                    $categories[$category] = true;
                }
            }
        }

        $latestUpdate = 0;
        foreach ($this->list($this->getRoot()) as $dateDir){
            if (strtotime($dateDir) >= $this->date_start) {
                $datePath = $this->getRoot() . '/' . $dateDir;
                foreach ($this->list($datePath) as $categoryDir) {
                    if (isset($categories[$categoryDir])) {
                        $categoryPath = $datePath . '/' . $categoryDir;
                        $maxHour = 0;
                        foreach (array_reverse($this->list($categoryPath)) as $hourFile) {
                            if ($hourFile > $maxHour){
                                $maxHour = $hourFile;
                            }
                            $time = filectime($categoryPath . '/' . $hourFile);
                            if ($time > $latestUpdate){
                                $latestUpdate = $time;
                            }
                            if($this->aggregate != 'latest' || $maxHour == $hourFile) {
                                $files[] = '/' . $dateDir . '/' . $categoryDir . '/' . $hourFile;
                            }
                        }
                    }
                }
            }
        }
        $latestUpdate -= $latestUpdate % 60;
        if ($this->aggregate == 'latest') {
            // timezone difference
            $this->addFilter('time', 'time', $latestUpdate - 3600);
        }

        return $files;
    }

    protected function getStart($fileName, $size)
    {
        if($this->aggregate == 'latest'){
            $fileParts = explode('/', $fileName);
            end($fileParts);
            $cat = prev($fileParts);
            return $size - $this->categoryCount[$cat] * $this->getLength();
        }
        return 0;
    }

    protected function getColumn($file, $base, $name)
    {
        if ($name == 'id'){
            $value = parent::getColumn($file, $base, $name);
            $filePath = explode('/', stream_get_meta_data($file)['uri']);
            end($filePath);
            $category = prev($filePath);
            if ($category != 0){
                return (int)($category . $value);
            }
        }elseif ($name == 'date'){
            $filePath = explode('/', stream_get_meta_data($file)['uri']);
            end($filePath);
            prev($filePath);
            return prev($filePath);
        }elseif ($name == 'time'){
            $value = parent::getColumn($file, $base, $name);
            $filePath = explode('/', stream_get_meta_data($file)['uri']);
            $hour = str_pad(end($filePath), 2, '0',STR_PAD_LEFT);
            $minutes = str_pad(floor($value / 60), 2, '0',STR_PAD_LEFT);
            $seconds = str_pad($value % 60, 2, '0',STR_PAD_LEFT);
            return $hour . ':' . $minutes . ':' . $seconds;
        }elseif ($name == 'events'){
            $value = [];
            $event = parent::getColumn($file, $base, $name);

            foreach (['freezing', 'rain', 'snow', 'hail', 'thunder', 'tornado/whirlwind'] as $name){
                $value[$name] = $event & 0xE0 ? true : false;
                $event <<= 1;
            }

            return $value;
        }else{
            return parent::getColumn($file, $base, $name);
        }
    }

    public function readData($columns = false, $key = false)
    {
        $aggregates = [];
        $measurements = [];
        $results =  parent::readData($columns, $key);

        foreach ($results as $result){
            $time = '';
            switch ($this->aggregate){
                case 'minute':
                case 'latest':
                    $time = substr($result['time'], 0, 6) . '00';
                    break;
                case 'hour':
                    $time = substr($result['time'], 0, 3) . '00:00';
                    break;
            }

            $id = $result['id'];
            if (!isset($aggregates[$id])){
                $aggregates[$id] = [];
            }

            if (!isset($aggregates[$id][$time])){
                $aggregates[$id][$time] = [
                    'id' => $result['id'],
                    'date' => $result['date'],
                    'time' => $time,
                    'count' => 0,
                    'min' => [],
                    'max' => [],
                    'total' => [],
                    'avg' => [],
                    'events' => []
                ];
            }
            $aggregates[$id][$time]['events'] = $result['events'];
            $aggregates[$id][$time]['count']++;
            unset($result['id'], $result['date'], $result['time'], $result['events']);

            foreach ($result as $key => $value){
                if (!isset($aggregates[$id][$time]['total'][$key])){
                    $aggregates[$id][$time]['total'][$key] = $value;
                    $aggregates[$id][$time]['min'][$key] = $value;
                    $aggregates[$id][$time]['max'][$key] = $value;
                }else{
                    $aggregates[$id][$time]['total'][$key] += $value;
                    if ($aggregates[$id][$time]['min'][$key] > $value){
                        $aggregates[$id][$time]['min'][$key] = $value;
                    }
                    if ($aggregates[$id][$time]['max'][$key] > $value){
                        $aggregates[$id][$time]['max'][$key] = $value;
                    }
                }
            }

        }

        $columns = $this->getColumns();
        foreach ($aggregates as $result){
            foreach ($result as $aggregate){
                foreach ($aggregate['total'] as $key => $value){
                    $aggregate['avg'][$key] = round($value / $aggregate['count'], $columns[$key]->getDecimals());
                }

                $index = $aggregate['date'];
                if (!isset($measurements[$index])){
                    $measurements[$index] = [];
                }

                foreach ($aggregate[$this->type] as $key => $value){
                    $aggregate[$key] = $value;
                }

                if ($this->aggregate != 'latest'){
                    unset($aggregate['date'], $aggregate['id']);
                }
                unset($aggregate['count'], $aggregate['total'], $aggregate['min'], $aggregate['max'], $aggregate['avg']);

                $measurements[$index][] = $aggregate;
            }
        }

        return $measurements;
    }

}