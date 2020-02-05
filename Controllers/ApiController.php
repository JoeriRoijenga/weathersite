<?php

namespace Controllers;

use Data\StationReader;
use Data\WeatherReader;

/**
 * Class ApiController
 * @package Controllers
 */
class ApiController extends Controller
{
    private $filters = [
        'stn' => ['integer', 'id', '='],
        'lat_start' => ['float', 'latitude', '>='],
        'lat_end' => ['float', 'latitude', '<='],
        'long_start' => ['float', 'longitude', '>='],
        'long_end' => ['float', 'latitude', '<='],
    ];

    /**
     * @param $reader
     * @param $keys
     */
    private function addFilters(&$reader, $keys)
    {
        foreach ($keys as $index => $key) {
            $filter = $this->filters[!is_numeric($index) ? $index : $key];
            $value = !is_numeric($index) ? $key : $this->input($key, $filter[0]);

            if ($value !== false)
                $reader->addFilter($filter[1], $filter[2], $value);
        }
    }

    public function stations()
    {
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

    /**
     * @param $stationId
     */
    public function station($stationId)
    {
        $stationId = (int)filter_var($stationId, FILTER_SANITIZE_NUMBER_INT);
        $startDate = $this->input('start_date', 'string');
        if(!$startDate)
            $startDate = 'today';

        $endDate = $this->input('end_date', 'string');
        if(!$endDate)
            $endDate = 'today';

        $aggregate = $this->input('group_by', 'string');
        if (!$aggregate || !in_array($aggregate, ['second', 'minute', 'hour', '5_second', '10_second']))
            $aggregate = 'minute';
        $type = $this->input('group_type', 'string');
        if (!$type || !in_array($type, ['min', 'max', 'avg']))
            $type = 'avg';

        $reader = new StationReader();
        $this->addFilters($reader, ['stn' => $stationId]);
        $results = $reader->readData(['id', 'name', 'latitude', 'longitude']);

        if (count($results) <= 0) {
            $this->json(['message' => 'station not found'], 404);
            exit;
        }
        $station = $results[0];
        unset($results, $reader);

        $reader = new WeatherReader($aggregate, $type);
        $reader->setStations($station['id']);
        $reader->setStartDate($startDate);
        $reader->setEndDate($endDate);
        $station['weather'] = $reader->readData(['temperature', 'air_pressure_station', 'rainfall']);

        $limit = $this->input('limit', 'integer');
        if ($limit !== false){
            foreach ($station['weather'] as $date => $value){
                if (count($value) > $limit){
                    $station['weather'][$date] = array_slice($value, count($value) - $limit);
                }
            }
        }

        $reader = new WeatherReader('second', 'avg');
        $reader->setStations($station['id']);

        foreach ($reader->readData(['temperature', 'air_pressure_station', 'rainfall'], false, true) as $date => $result){
            $result = $result[0];
            unset($result['id']);
            $result['date'] = $date;
            $keys = ['id' => 0, 'date' => 1, 'time' => 2];
            uksort($result, function ($a, $b) use($keys){
                return ($keys[$a] ?? 3) > ($keys[$b] ?? 3);
            });
            $station['weather_latest'] = $result;
        }


        $this->json([
            'item' => $station,
            'group_by' => $aggregate,
            'group_type' => $type,
            'start_date' => date('Y-m-d', strtotime($startDate)),
            'end_date' => date('Y-m-d', strtotime($endDate))
        ]);
    }

    public function weather()
    {
        $reader = new StationReader();
        $this->addFilters($reader, [
            'stn', 'lat_start', 'lat_end', 'long_start', 'long_end'
        ]);

        $ids = false;
        if ($reader->hasFilters()){
            $stations = $reader->readData([], 'id');
            $ids = array_keys($stations);
            unset($stations, $results);

            if (count($ids) <= 0){
                $this->json(['message' => 'no stations not found'], 404);
                exit;
            }
        }else{
            $this->json(['message' => 'Apply a filter. Unable to request this call for all stations.'], 500.);
            exit;
        }

        $reader = new WeatherReader('second', 'avg');

        if ($ids)
            $reader->setStations($ids);

        $results = $reader->readData(['temperature', 'air_pressure_station', 'rainfall'], false, true);
        $orderBy = $this->input('order_by', 'string');
        if (!in_array($orderBy, array_keys($reader->getColumns())))
            $orderBy = 'rainfall';

        $limit = $this->input('limit', 'integer');
        if ($limit <= 0)
            $limit = 10;

        $weather = [];
        foreach ($results as $date => $resultsPerDate){
            foreach ($resultsPerDate as $result){
                $result['id'] = (int) $result['id'];
                $result['date'] = $date;
                $keys = ['id' => 0, 'date' => 1, 'time' => 2];
                uksort($result, function ($a, $b) use($keys){
                    return ($keys[$a] ?? 3) > ($keys[$b] ?? 3);
                });
                $weather[] = $result;
            }
        }

        usort($weather, function ($a, $b) use ($orderBy) {
            return $a[$orderBy] < $b[$orderBy];
        });

        if (count($weather) > $limit) {
            $weather = array_slice($weather, 0, $limit);
        }

        $this->json([
            'items' => $weather,
            'count' => count($weather),
            'order_by' => $orderBy
        ]);
    }

}