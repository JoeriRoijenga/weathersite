<?php

namespace Data;

class StationReader extends Reader
{

    public function __construct(){
        parent::__construct(__DIR__);
    }

    protected function getColumns(){
        $pos = 0;
        return [
            'id' => DataType::Integer($pos, 3),
            'latitude' => DataType::Double($pos, 3, 3, true),
            'longitude' => DataType::Double($pos, 3, 3, true),
            'name' => DataType::String($pos, 64)
        ];
    }


}