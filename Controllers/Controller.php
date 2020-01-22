<?php

namespace Controllers;

abstract class Controller
{

    protected function html($file){
        include __DIR__ . '\..\Views\\' . $file . '.php';
    }

}