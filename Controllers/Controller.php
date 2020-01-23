<?php

namespace Controllers;

abstract class Controller
{
    protected function input($key, $filter = false){
        if (is_array($key)){
            $input = [];
            foreach ($key as $index => $value){
                if (is_numeric($index)){
                    $input[$value] = $this->input($value);
                }else{
                    $input[$index] = $this->input($index, $value);
                }
            }
            return $input;
        }else{
            $value = $_GET[$key] ?? $_POST[$key] ?? false;
            if($value !== false){
                switch ($filter){
                    case 'string':
                        $value = filter_var($value, FILTER_SANITIZE_STRING);
                        break;
                    case 'float':
                        $value = (double)filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
                        break;
                    case 'integer':
                        $value = (int)filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                        break;
                }
            }

            return $value;
        }
    }

    protected function html($file, $variables = []){

        foreach ($variables as $key => $value){
            $$key = $value;
        }

        include __DIR__ . '/../Views/' . $file . '.php';
    }

}