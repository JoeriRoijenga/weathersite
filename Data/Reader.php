<?php

namespace Data;

abstract class Reader
{
    private $root;
    private $length = 0;
    private $columns = [];

    private $filters = [];
    private $temp_cache = false;

    protected abstract function getColumns();

    protected function __construct($root){
        $this->root = $root;
        $this->columns = $this->getColumns();
        foreach ($this->columns as $name => $column){
            $this->length += $column->getLength();
        }
    }


    private function checkFilters($file, $base){
        foreach ($this->filters as $filter){
            switch ($filter[1]){
                case '>=':
                    if ($this->getColumn($filter, $base, $filter[0]) < $filter[2])
                        return false;
                    break;
                case '<=':
                    if ($this->getColumn($filter, $base, $filter[0]) > $filter[2])
                        return false;
                    break;
                case '=':
                    if ($this->getColumn($filter, $base, $filter[0]) != $filter[2])
                        return false;
                    break;
            }


        }
        return true;
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

    public final function addFilter($name, $condition, $value){
        $this->filters[] = [$name, $condition, $value];
    }

    public final function read($file, $columns = false){
        $results = [];
        if (!$columns){
            $columns = array_keys($this->columns);
        }

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
                $results[] = $result;
            }
            $base += $this->length;
        }
        fclose($file);
        $this->temp_cache = false;
        return $results;
    }


}