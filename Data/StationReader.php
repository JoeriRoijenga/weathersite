<?php

namespace Data;

/**
 * Class StationReader
 * @package Data
 */
class StationReader extends Reader
{

    public function __construct()
    {
        parent::__construct(__DIR__ . '/../weather_data');
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        $pos = 0;
        return [
            'id' => DataType::Integer($pos, 3),
            'latitude' => DataType::Double($pos, 3, 3, true),
            'longitude' => DataType::Double($pos, 3, 3, true),
            'name' => DataType::String($pos, 64),
            'country' => DataType::String($pos, 64)
        ];
    }

    /**
     * @param bool $last | Irrelevant for stations
     * @return array
     */
    protected function getFiles($last = false)
    {
        // Split up files for speed purposes.
        $files = [
            'ne' => '/stations-ne.dat',
            'nw' => '/stations-nw.dat',
            'se' => '/stations-se.dat',
            'sw' => '/stations-sw.dat'
        ];

        // Get file based on the latitude or longitude settings.
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

    /**
     * Transform value as necessary.
     * @param $file
     * @param $base
     * @param $name
     * @return bool|mixed
     */
    protected function getColumn($file, $base, $name)
    {
        $value = parent::getColumn($file, $base, $name);
        if ($name == 'name' || $name == "country"){
            $value = ucwords(strtolower($value));
        }
        return $value;
    }
}