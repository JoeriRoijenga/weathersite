<?php

namespace Data;

class StationReader extends Reader
{

    public function __construct()
    {
        parent::__construct(__DIR__ . '/../weather_data');
    }

    public function getColumns()
    {
        $pos = 0;
        return [
            'id' => DataType::Integer($pos, 3),
            'latitude' => DataType::Double($pos, 3, 3, true),
            'longitude' => DataType::Double($pos, 3, 3, true),
            'name' => DataType::String($pos, 64),
            'category_count' => DataType::Integer($pos, 2)
        ];
    }

    protected function getFiles()
    {
        $files = [
            'ne' => '/stations-ne.dat',
            'nw' => '/stations-nw.dat',
            'se' => '/stations-se.dat',
            'sw' => '/stations-sw.dat'
        ];

        foreach ($this->getFilters() as $filter) {
            if ($filter[0] == 'latitude') {
                if ($filter[1] == '>=' && $filter[2] >= 0) {
                    unset($files['se'], $files['sw']);
                } elseif ($filter[1] == '<=' && $filter[2] <= 0) {
                    unset($files['ne'], $files['nw']);
                }
            } elseif ($filter[0] == 'longitude') {
                if ($filter[1] == '>=' && $filter[2] >= 0) {
                    unset($files['nw'], $files['sw']);
                } elseif ($filter[1] == '<=' && $filter[2] <= 0) {
                    unset($files['ne'], $files['se']);
                }
            }
        }

        return $files;
    }
}