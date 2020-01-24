<?php

namespace Data;

abstract class Reader
{
    private $root;
    private $length = 0;
    private $columns = [];

    private $filters = [];
    private $temp_cache = false;

    protected function __construct($root){
        $this->root = $root;
        $this->columns = $this->getColumns();
        foreach ($this->columns as $name => $column){
            $this->length += $column->getLength();
        }
    }

    protected abstract function getColumns();

    protected abstract function getFiles();

    private function checkFilters($file, $base){
        foreach ($this->filters as $filter){
            switch ($filter[1]){
                case '>=':
                    if ($this->getColumn($file, $base, $filter[0]) < $filter[2])
                        return false;
                    break;
                case '<=':
                    if ($this->getColumn($file, $base, $filter[0]) > $filter[2])
                        return false;
                    break;
                case '=':
                    if ($this->getColumn($file, $base, $filter[0]) != $filter[2])
                        return false;
                    break;
            }


        }
        return true;
    }

    protected function getFilters(){
        return $this->filters;
    }

    private function getColumn($file, $base, $name){
        if (!array_key_exists($name, $this->columns)){
            return false;
        }
        if (array_key_exists($name, $this->temp_cache)){
            return $this->temp_cache[$name];
        }

        $type = $this->columns[$name];

        fseek($file, $base + $type->getPosition());
        $value = fread($file, $type->getLength());

        return $type->decodeValue($value);
    }

    protected final function read(&$results, $file, $columns, $key){
        $doFilter = count($this->filters) > 0;
        $file = fopen($this->root . $file, "r");
        $size = fstat($file)['size'];
        $base = 0;
        while ($base < $size){
            $this->temp_cache = [];
            if(!$doFilter || $this->checkFilters($file, $base)){
                $result = [];
                foreach ($columns as $column){
                    $result[$column] = $this->getColumn($file, $base, $column);
                }
                if ($key == false || $this->getColumn($file, $base, $key) == false){
                    $results[] = $result;
                }else{
                    $results[$this->getColumn($file, $base, $key)] = $result;
                }
            }
            $base += $this->length;
        }
        fclose($file);
        $this->temp_cache = false;
    }

    public final function addFilter($name, $condition, $value){
        $this->filters[] = [$name, $condition, $value];
    }

    public final function readData($columns = false, $key = false){
        if (!$columns){
            $columns = array_keys($this->columns);
        }

        $results = [];
        foreach ($this->getFiles() as $file){
            $this->read($results, $file, $columns, $key);
        }

        return $results;
    }

}