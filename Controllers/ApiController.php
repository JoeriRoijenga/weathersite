<?php

namespace Controllers;

use Data\StationReader;
use Data\WeatherReader;

class ApiController extends Controller
{
    private $filters = [
        'stn' => ['integer', 'id', '='],
        'lat_start' => ['float', 'latitude', '>='],
        'lat_end' => ['float', 'latitude', '<='],
        'long_start' => ['float', 'longitude', '>='],
        'long_end' => ['float', 'latitude', '<='],
    ];

    private function addFilters(&$reader, $keys){
        foreach ($keys as $index => $key){
            if (!is_numeric($index)){
                $filter = $this->filters[$index];
                $value = $key;
            }else{
                $filter = $this->filters[$key];
                $value = $this->input($key, $filter[0]);
            }

            if ($value !== false){
                $reader->addFilter($filter[1], $filter[2], $value);
            }
        }
    }

    public function stations(){
        $reader = new StationReader();
        $this->addFilters($reader, [
            'stn', 'lat_start', 'lat_end', 'long_start', 'long_end'
        ]);

        $results = $reader->readData(['id', 'name', 'latitude', 'longitude']);

        $this->json([
            'items' => $results ?? [],
            'amount' => count($results ?? []),
        ]);
    }

    public function stationWeather($stationId){
        $stationId =  (int)filter_var($stationId, FILTER_SANITIZE_NUMBER_INT);

        $reader = new StationReader();
        $this->addFilters($reader, ['stn' => $stationId]);
        $results = $reader->readData(['id', 'name', 'latitude', 'longitude']);

        if (count($results) <= 0){
            $this->json(['message' => 'station not found'], 404);
            exit;
        }

        $station = $results[0];
        $station['measurements'] = [];
        $reader = new WeatherReader();
        $this->addFilters($reader, ['stn' => $stationId]);
        $results = $reader->readData();
        foreach ($results as $result){
            $date = $result['date'];
            if (!isset($station['measurements'][$date])){
                $station['measurements'][$date] = [];
            }
            unset($result['id'], $result['date']);
            $station['measurements'][$date][] = $result;
        }

        $this->json(['item' => $station]);
    }

}