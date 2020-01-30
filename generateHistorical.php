<?php

use Data\StationReader;
use Data\WeatherReader;

set_time_limit(-1);

spl_autoload_register(function ($className) {
    if (file_exists(($file = str_replace("\\", "/", $className) . '.php'))) {
        include $file;
    }
});

echo "Starting at: " . date("H:i:s") . "\r\n";
$path = __DIR__ . '/weather_data/historical/';

$today = strtotime('yesterday');
$time = strtotime('yesterday - 1 month - 1 day');
foreach (scandir($path) as $file){
    if (preg_match('/\.csv/', $file)){
        $date = pathinfo($path . $file)['filename'];

        if (strtotime($date) < strtotime('yesterday - 1 month - day')){
            unlink($path . $file);
        }elseif(strtotime($date) > $time){
            $time = strtotime($date);
        }
    }
}

$time += 24 * 60 * 60;
echo "Starting from: " . date('Y-m-d', $time) . "\n";

$stationReader = new StationReader();
$stationReader->addFilter('latitude', '>-', -5);
$stationReader->addFilter('latitude', '<-', 30);
$ids = array_keys($stationReader->readData([], 'id'));

echo "Limiting stations to: " . count($ids) . "\n";
unset($stationReader);

while ($time <= $today){
    $file = null;
    $date = date('Y-m-d', $time);
    echo $date . ":\n";

    $weatherReader = new WeatherReader('10_second', 'avg');
    $weatherReader->setStartDate($date);
    $weatherReader->setEndDate($date);
    $weatherReader->addFilter('id', '=', $ids);
    $weatherReader->outputTerminalProgress();
    while (count($results = $weatherReader->readData([])) > 0){
        if (is_null($file)) {
            if (file_exists($path . $date . '.csv')) {
                $file = fopen($path . $date . '.csv', 'a');
            } else {
                $file = fopen($path . $date . '.csv', 'w');
                fwrite($file, "STN,TIME,STP,PRECIPITATION\r\n");
            }
        }

        foreach ($results as $date => $items) {
            foreach ($items as $item) {
                $line = join(',', [$item['id'], $item['time'], $item['air_pressure_station'], $item['rainfall']]) . "\r\n";
                fwrite($file, $line);
            }
        }
        unset($results);
    }
    echo "\n";
    if(!is_null($file)){
        fclose($file);
        unset($file, $weatherReader);
    }
    $time += 24 * 60 * 60;
}

echo "Generating XML file\r\n";
$doc  = new DomDocument();
$doc->formatOutput = true;
$root = $doc->createElement('WEATHER');
$root = $doc->appendChild($root);


foreach (scandir($path) as $file){
    if ($file != '.' && $file != '..'){
        $inputFile  = fopen($path . $file, 'r');
        $headers = fgetcsv($inputFile);
        $date = $doc->createElement("DATE");
        $dateValue = $date->setAttribute("DATE", pathinfo($path . $file)['filename']);

        while (($row = fgetcsv($inputFile)) !== FALSE) {
            $measurement = $doc->createElement('MEASUREMENT');
            foreach($headers as $i => $header) {
                $child = $doc->createElement($header);
                $child = $measurement->appendChild($child);
                $value = $doc->createTextNode($row[$i]);
                $value = $child->appendChild($value);
            }

            $date->appendChild($measurement);
        }
        $root->appendChild($date);
    }
}

$xml = $doc->saveXML();
$handle = fopen(__DIR__ . '/weather_data/historical.xml', "w");
fwrite($handle, $xml);
fclose($handle);

echo "Done on: " . date("H:i:s") . "\r\n";