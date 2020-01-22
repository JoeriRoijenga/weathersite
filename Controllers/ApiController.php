<?php

namespace Controllers;

use Data\StationReader;

class ApiController extends Controller
{
    public function index(){
        $reader = new StationReader();
        $reader->addFilter('latitude', '>', 50);
        $results = $reader->read("stations.dat");

        header('Content-Type: application/json');
        echo json_encode([
            'data' => $results,
            'amount' => count($results)
        ]);
    }

}