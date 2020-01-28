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

    private function addFilters(&$reader, $keys)
    {
        foreach ($keys as $index => $key) {
            if (!is_numeric($index)) {
                $filter = $this->filters[$index];
                $value = $key;
            } else {
                $filter = $this->filters[$key];
                $value = $this->input($key, $filter[0]);
            }

            if ($value !== false) {
                $reader->addFilter($filter[1], $filter[2], $value);
            }
        }
    }

    public function station($stationId)
    {
        $stationId = (int)filter_var($stationId, FILTER_SANITIZE_NUMBER_INT);
        $aggregate = $this->input('group_by', 'string');
        $type = $this->input('group_type', 'string');
        if (!$aggregate || !in_array($aggregate, ['minute', 'hour'])) {
            $aggregate = 'minute';
        }

        if (!$type || !in_array($type, ['min', 'max', 'avg'])) {
            $type = 'avg';
        }

        $reader = new StationReader();
        $this->addFilters($reader, ['stn' => $stationId]);
        $results = $reader->readData(['id', 'name', 'latitude', 'longitude']);

        if (count($results) <= 0) {
            $this->json(['message' => 'station not found'], 404);
            exit;
        }

        $station = $results[0];
        $station['measurements'] = [];
        $reader = new WeatherReader($aggregate, $type);
        $this->addFilters($reader, ['stn' => $stationId]);
        $station['weather'] = $reader->readData();

        $this->json(['item' => $station, 'group_by' => $aggregate, 'group_type' => $type]);
    }

    public function weather()
    {
        $reader = new StationReader();
        $this->addFilters($reader, [
            'stn', 'lat_start', 'lat_end', 'long_start', 'long_end'
        ]);
        $results = $reader->readData(['id', 'name', 'latitude', 'longitude', 'category_count'], 'id');
        $ids = [];
        $categoryCount = [];
        foreach ($results as $result) {
            $ids[] = $result['id'];
            $categoryCount[floor($result['id'] / 10000)] = $result['category_count'];
        }

        $orderBy = $this->input('order_by', 'string');
        if (!in_array($orderBy, array_keys($reader->getColumns()))) {
            $orderBy = 'rainfall';
        }
        $limit = $this->input('limit', 'integer');
        if ($limit <= 0) {
            $limit = 10;
        }

        $reader = new WeatherReader('latest', 'avg', $categoryCount);
        $this->addFilters($reader, ['stn' => $ids]);

        $time = 0;
        $date = 0;
        $weather = [];
        foreach (array_values($reader->readData())[0] as $info) {
            $date = $info['date'];
            $time = $info['time'];
            $info['station'] = $results[$info['id']];
            unset($info['station']['category_count'], $info['id'], $info['date'], $info['time']);
            $weather[] = $info;
        }

        usort($weather, function ($a, $b) use ($orderBy) {
            return $a[$orderBy] < $b[$orderBy];
        });

        if (count($weather) > $limit) {
            $weather = array_slice($weather, 0, $limit);
        }

        $this->json([
            'date' => $date,
            'time' => $time,
            'count' => count($weather),
            'order_by' => $orderBy,
            'items' => $weather
        ]);
    }

}