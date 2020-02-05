<?php

use Data\StationReader;
use Data\WeatherReader;

set_time_limit(-1);

spl_autoload_register(function ($className) {
    if (file_exists(($file = str_replace("\\", "/", $className) . '.php'))) {
        include $file;
    }
});

$path = __DIR__ . '/weather_data/historical/';

$startDate = strtotime('yesterday - 1 month');
$yesterday = strtotime('yesterday');
echo "Generating data from " . date('Y-m-d', $startDate) . " to " . date('Y-m-d', $yesterday) . "\n";

// Initialize station reader
$stationReader = new StationReader();
$stationReader->addFilter('latitude', '>=', -5);
$stationReader->addFilter('latitude', '<=', 30);

$ids = array_keys($stationReader->readData([], 'id'));
sort($ids);
$total = count($ids);
echo "Limiting stations to: " . $total . "\n";

// Free up memory
unset($stationReader);

// Initialize Weather station
$weatherReader = new WeatherReader('second', 'avg');
$weatherReader->setStartDate(date("Y-m-d", $startDate));
$weatherReader->setEndDate(date("Y-m-d", $yesterday));

$resultList = [];
// Get results per each station
for ($i = 0; $i < $total; $i++){
    $stationId = $ids[$i];

    $weatherReader->setStations($stationId);

    $results = $weatherReader->readData(['id', 'time', 'rainfall', 'air_pressure_station']);
    foreach ($results as $date => $dailyResults){
        foreach ($dailyResults as $result){
            if (!isset($resultList[$date])){
                $resultList[$date] = [];
            }
            if (!isset($resultList[$date][$result['time']])){
                $resultList[$date][$result['time']] = [];
            }

            $resultList[$date][$result['time']][] = [
                'STN' => $result['id'],
                'TIME' => $result['time'],
                'STP' => $result['air_pressure_station'],
                'PRECIPITATION' => $result['rainfall']
            ];
        }
    }

    echo ($i + 1) . '/' . $total . "\n";
}

echo "Generating XML file\r\n";

// Initialize XML root
$doc  = new DomDocument();
$doc->formatOutput = true;
$root = $doc->createElement('WEATHER');
$root = $doc->appendChild($root);

// Append each result as element
foreach ($resultList as $dateValue => $dailyResults){
    $date = $doc->createElement("DATE");
    $date->setAttribute("DATE", $dateValue);

    foreach ($dailyResults as $timeResults) {

        ksort($timeResults);
        // Sort so XML is in order
        foreach ($timeResults as $result) {
            $measurement = $doc->createElement('MEASUREMENT');
            foreach ($result as $key => $value) {
                $child = $doc->createElement($key);
                $child = $measurement->appendChild($child);
                $value = $doc->createTextNode($value);
                $value = $child->appendChild($value);
            }
        }
        $date->appendChild($measurement);
    }

    $root->appendChild($date);
}

// Write output file
$xml = $doc->saveXML();
$handle = fopen(__DIR__ . '/assets/historical/' . date('Y-m-d') . '.xml', "w");
fwrite($handle, $xml);
fclose($handle);

