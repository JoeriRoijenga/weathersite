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
        $endDate = $this->input('end_date', 'string');
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
        unset($results, $reader);

        $reader = new WeatherReader($aggregate, $type);
        $reader->setStations($station['id']);
        $reader->setStartDate($startDate);
        $reader->setEndDate($endDate);
        $station['weather'] = $reader->readData(['air_pressure_station', 'rainfall']);

        $this->json(['item' => $station, 'group_by' => $aggregate, 'group_type' => $type]);
    }

    public function weather()
    {

    }

}