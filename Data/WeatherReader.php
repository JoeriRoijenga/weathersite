<?php

namespace Data;

/**
 * Class WeatherReader
 * @package Data
 */
class WeatherReader extends Reader
{
    private $stations = 'all';
    private $dateStart = false;
    private $dateEnd = false;
    private $aggregate;
    private $type;

    /**
     * WeatherReader constructor.
     */
    public function __construct($aggregate, $type)
    {
        if ($_SERVER['HTTP_HOST'] == 'abc.climate-express.xyz'){
            parent::__construct('/var/nfs/parsed_files');
        }else{
            parent::__construct(__DIR__ . '/../weather_data/weather');
        }

        $this->aggregate = $aggregate;
        $this->type = $type;
    }

    public function setStations($stations)
    {
        $this->stations = is_array($stations) ? array_flip($stations) : [$stations => true];
    }

    public function setStartDate($startDate)
    {
        $this->dateStart = strtotime($startDate);
    }

    public function setEndDate($endDate)
    {
        $this->dateEnd = strtotime($endDate);
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        $position = 0;
        return [
            'id' => null,
            'date' => null,
            'time' => DataType::Integer($position, 4),
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
     * @param bool $columns
     * @param bool $key
     * @param bool $last
     * @return array
     */
    public function readData($columns = false, $key = false, $last = false)
    {
        $aggregates = [];
        $measurements = [];
        $fixedColumns = array_merge($columns, ['id', 'time', 'date']);
        $results = parent::readData($fixedColumns, $key, $last);

        foreach ($results as $result) {
            $time = '';
            switch ($this->aggregate) {
                case 'second':
                    $time = $result['time'];
                    break;
                case '5_second':
                    $time = substr($result['time'], 0, 7) . (floor(substr($result['time'], -1) / 5) * 5);
                    break;
                case '10_second':
                    $time = substr($result['time'], 0, 7) . '0';
                    break;
                case 'minute':
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
                ];
            }
            $aggregates[$id][$time]['count']++;
            unset($result['id'], $result['date'], $result['time']);

            if (isset($result['events'])){
                $aggregates[$id][$time]['events'] = $result['events'];
                unset($result['events']);
            }

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
        foreach ($aggregates as $station => $result) {
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

                if (!$last)
                    unset($aggregate['id']);

                unset($aggregate['date'], $aggregate['count'], $aggregate['total'], $aggregate['min'], $aggregate['max'], $aggregate['avg']);

                $measurements[$index][] = $aggregate;
            }
        }

        return $measurements;
    }

    /**
     * @param bool $last
     * @return array
     */
    protected function getFiles($last = false)
    {

        $files = [];
        $iterator =  new \RecursiveDirectoryIterator($this->getRoot(), \FilesystemIterator::SKIP_DOTS|\FilesystemIterator::KEY_AS_FILENAME|\FilesystemIterator::UNIX_PATHS);
        $s = 0;
        while ($iterator->valid()){
            $station = $iterator->key();
            if ($this->stations == 'all' || isset($this->stations[$station])){

                $dateIterator = $iterator->getChildren();
                while ($dateIterator->valid()){
                    $date = strtotime($dateIterator->key());
                    if ($last || $date >= $this->dateStart && $date <= $this->dateEnd){

                        $hourIterator = $dateIterator->getChildren();
                        while ($hourIterator->valid()){
                            if ($last)
                                $files[$s] = '/' . $hourIterator->getSubPath() . '/' . $hourIterator->key();
                            else
                                $files[] = '/' . $hourIterator->getSubPath() . '/' . $hourIterator->key();
                            $hourIterator->next();
                        }

                    }
                    $dateIterator->next();
                }

                $s++;
            }
            $iterator->next();
        }

        return $files;
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
        list($station, $date, $hour) = array_slice(explode('/', stream_get_meta_data($file)['uri']), -3);

        if ($name == 'id'){
            return $station;
        }elseif ($name == 'date'){
            return $date;
        }

        $value = parent::getColumn($file, $base, $name);
        if ($name == 'rainfall'){
            return $value * 10; // Convert to mm
        }elseif ($name == 'time') {
           return date("H:i:s", $value);
        }elseif ($name == 'events') {
            $events = [];
            foreach (['freezing', 'rain', 'snow', 'hail', 'thunder', 'tornado/whirlwind'] as $name) {
                $events[$name] = $value & 0xE0 ? true : false;
                $value <<= 1;
            }
            return $events;
        }

        return $value;
    }

}