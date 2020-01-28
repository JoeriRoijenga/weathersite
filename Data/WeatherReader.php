<?php

namespace Data;

/**
 * Class WeatherReader
 * @package Data
 */
class WeatherReader extends Reader
{

    private $aggregate;
    private $type;
    private $categoryCount;
    private $date_start;
    private $timeCheck = false;

    /**
     * WeatherReader constructor.
     * @param $aggregate
     * @param $type
     * @param array $categoryCount
     */
    public function __construct($aggregate, $type, $categoryCount = [])
    {
        parent::__construct(__DIR__ . '/../weather_data/weather');

        $this->aggregate = $aggregate;
        $this->type = $type;
        $this->categoryCount = $categoryCount;

        $date = strtotime('today');
        switch ($aggregate) {
            case 'hour':
                $date = strtotime('today - 7');
                break;
        }

        $this->date_start = $date;
    }

    /**
     * @param bool $columns
     * @param bool $key
     * @return array
     */
    public function readData($columns = false, $key = false)
    {
        $aggregates = [];
        $measurements = [];
        $results = parent::readData($columns, $key);

        foreach ($results as $result) {
            $time = '';
            switch ($this->aggregate) {
                case 'minute':
                case 'latest':
                    $time = substr($result['time'], 0, 6) . '00';
                    break;
                case 'hour':
                    $time = substr($result['time'], 0, 3) . '00:00';
                    break;
            }

            $id = $result['id'];
            if (!isset($aggregates[$id]))
                $aggregates[$id] = [];

            if (!isset($aggregates[$id][$time])) {
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

            foreach ($result as $key => $value) {
                if (!isset($aggregates[$id][$time]['total'][$key])) {
                    $aggregates[$id][$time]['total'][$key] = $value;
                    $aggregates[$id][$time]['min'][$key] = $value;
                    $aggregates[$id][$time]['max'][$key] = $value;
                } else {
                    $aggregates[$id][$time]['total'][$key] += $value;
                    if ($aggregates[$id][$time]['min'][$key] > $value) {
                        $aggregates[$id][$time]['min'][$key] = $value;
                    }
                    if ($aggregates[$id][$time]['max'][$key] > $value) {
                        $aggregates[$id][$time]['max'][$key] = $value;
                    }
                }
            }

        }

        $columns = $this->getColumns();
        foreach ($aggregates as $result) {
            foreach ($result as $aggregate) {
                foreach ($aggregate['total'] as $key => $value) {
                    $aggregate['avg'][$key] = round($value / $aggregate['count'], $columns[$key]->getDecimals());
                }

                $index = $aggregate['date'];
                if (!isset($measurements[$index]))
                    $measurements[$index] = [];

                foreach ($aggregate[$this->type] as $key => $value) {
                    $aggregate[$key] = $value;
                }

                if ($this->aggregate != 'latest') {
                    unset($aggregate['date'], $aggregate['id']);
                }
                unset($aggregate['count'], $aggregate['total'], $aggregate['min'], $aggregate['max'], $aggregate['avg']);

                $measurements[$index][] = $aggregate;
            }
        }

        return $measurements;
    }

    /**
     * @return array
     */
    protected function getColumns()
    {
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

    /**
     * @return array
     */
    /**
     * @return array
     */
    protected function getFiles()
    {
        $files = [];
        $categories = [];
        foreach ($this->getFilters() as $filter) {
            if ($filter[0] == 'id') {
                if (!is_array($filter[2])) {
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

        $maxDate = 0;
        $latestFile = '';
        foreach (array_reverse($this->list($this->getRoot())) as $dateDir) {
            if (strtotime($dateDir) >= $this->date_start) {
                if (strtotime($dateDir) > $maxDate) {
                    $maxDate = strtotime($dateDir);
                }
                $datePath = $this->getRoot() . '/' . $dateDir;
                foreach ($this->list($datePath) as $categoryDir) {
                    if (isset($categories[$categoryDir])) {
                        $categoryPath = $datePath . '/' . $categoryDir;
                        $maxHour = 0;
                        foreach (array_reverse($this->list($categoryPath)) as $hourFile) {
                            if ($hourFile > $maxHour) {
                                $maxHour = $hourFile;
                            }
                            if ($this->aggregate != 'latest' || $maxDate == strtotime($dateDir) && $maxHour == $hourFile) {
                                $filePath = '/' . $dateDir . '/' . $categoryDir . '/' . $hourFile;
                                $files[] = $filePath;
                                if ($this->aggregate == 'latest'){
                                    $latestFile = $filePath;
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($this->aggregate == 'latest') {
            $data = [];
            $this->timeCheck = true;
            $this->read($data, $latestFile, ['time'], false, true);
            $this->timeCheck = false;
            $latestUpdate = strtotime($data[0]['time']);
            $latestUpdate -= $latestUpdate % 60;
            $this->addFilter('time', 'time', $latestUpdate);
        }

        return $files;
    }

    /**
     * @param $fileName
     * @param $size
     * @return float|int
     */
    /**
     * @param $fileName
     * @param $size
     * @return float|int
     */
    protected function getStart($fileName, $size)
    {
        if($this->timeCheck){
            return $size - $this->getLength();
        }

        if ($this->aggregate == 'latest') {
            $fileParts = explode('/', $fileName);
            end($fileParts);
            $cat = prev($fileParts);
            return $size - $this->categoryCount[$cat] * $this->getLength();
        }
        return 0;
    }

    /**
     * @param $file
     * @param $base
     * @param $name
     * @return array|bool|int|mixed|string
     */
    /**
     * @param $file
     * @param $base
     * @param $name
     * @return array|bool|int|mixed|string
     */
    protected function getColumn($file, $base, $name)
    {
        $filePath = explode('/', stream_get_meta_data($file)['uri']);
        $value = parent::getColumn($file, $base, $name);

        if ($name == 'date') {
            end($filePath);
            prev($filePath);
            return prev($filePath);
        }

        if ($name == 'id') {
            end($filePath);
            $category = prev($filePath);
            if ($category != 0) {
                return (int)($category . $value);
            } else {
                return (int)$value;
            }
        }elseif ($name == 'time') {
            $hour = str_pad(end($filePath), 2, '0', STR_PAD_LEFT);
            $minutes = str_pad(floor($value / 60), 2, '0', STR_PAD_LEFT);
            $seconds = str_pad($value % 60, 2, '0', STR_PAD_LEFT);
            return $hour . ':' . $minutes . ':' . $seconds;
        }elseif ($name == 'events') {
            $events = [];
            foreach (['freezing', 'rain', 'snow', 'hail', 'thunder', 'tornado/whirlwind'] as $name) {
                $events[$name] = $value & 0xE0 ? true : false;
                $value <<= 1;
            }
            return $events;
        } else {
            return parent::getColumn($file, $base, $name);
        }
    }

}