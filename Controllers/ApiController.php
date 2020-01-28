<?php

namespace Controllers;

use Data\StationReader;

class ApiController extends Controller
{
    private $stationFilters = [
        'stn' => ['integer', 'id', '='],
        'lat_start' => ['float', 'latitude', '>='],
        'lat_end' => ['float', 'latitude', '<='],
        'long_start' => ['float', 'longitude', '>='],
        'long_end' => ['float', 'latitude', '<='],
    ];

    public function stations(){
        $reader = new StationReader();

        foreach ($this->stationFilters as $key => $filter){
            $value = $this->input($key, $filter[0]);
            if ($value !== false){
                $reader->addFilter($filter[1], $filter[2], $value);
            }
        }

        $results = $reader->read("/stations.dat", [
            'id', 'name', 'latitude', 'longitude'
        ]);

        header('Content-Type: application/json');
        echo json_encode([
            'items' => $results,
            'amount' => count($results)
        ], JSON_UNESCAPED_SLASHES);
    }

}