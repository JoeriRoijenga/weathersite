<?php

namespace Data;

use http\Params;

class WeatherReader extends Reader
{

    public function __construct(){
//        parent::__construct("D:/unwdmishit");
        parent::__construct("C:/Users/MVISSER-ZEPHYRUS/Downloads/unwdmi/data");
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

        foreach ($this->list($this->getRoot()) as $dateDir){
            $datePath = $this->getRoot() . '/' . $dateDir;
            foreach ($this->list($datePath) as $categoryDir){
                if (isset($categories[$categoryDir])) {
                    $categoryPath = $datePath . '/' . $categoryDir;
                    foreach ($this->list($categoryPath) as $hourFile) {
                        $files[] = '/' . $dateDir . '/' . $categoryDir . '/' . $hourFile;
                    }
                }
            }
        }

        return $files;
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

}