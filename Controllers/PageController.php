<?php

namespace Controllers;

/**
 * Class PageController
 * @package Controllers
 */
class PageController extends Controller
{

    public function home()
    {
        $this->html("home",

            ["topTen" => $this->createArray()]);
    }

    private function createArray() {
        $ch = curl_init();
        $url = $_SERVER['HTTP_HOST'] . "/api/v1/weather/latest";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch); 
        curl_close($ch);

        $object = json_decode($data);

        return $object->items ?? [];
    }

    public function map()
    {
        $this->html("map");
    }
}