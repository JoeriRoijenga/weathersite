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
            if ($column != null) {
                $this->length += $column->getLength();
            }
        }
    }

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
                    if (is_array($filter[2])){
                        if (!in_array($this->getColumn($file, $base, $filter[0]), $filter[2]))
                            return false;
                    }else{
                        if ($this->getColumn($file, $base, $filter[0]) != $filter[2])
                            return false;
                    }
                    break;
                case 'time':
                    if (strtotime($this->getColumn($file, $base, $filter[0])) < $filter[2])
                        return false;
                    break;
            }


        }
        return true;
    }

    protected function getColumn($file, $base, $name){
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

    protected abstract function getColumns();

    protected abstract function getFiles();

    protected function getStart($fileName, $size){
        return 0;
    }

    protected function getFilters(){
        return $this->filters;
    }

    protected function getRoot(){
        return $this->root;
    }

    protected function getLength(){
        return $this->length;
    }

    protected final function read(&$results, $fileName, $columns, $key){
        $doFilter = count($this->filters) > 0;
        $file = fopen($this->root . $fileName, "r");
        $size = fstat($file)['size'];
        $base = $this->getStart($fileName, $size);

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

    protected final function list($path){
        return array_diff(scandir($path), ['.', '..']);
    }

    public final function addFilter($name, $condition, $value){
        $this->filters[] = [$name, $condition, $value];
    }

    public function readData($columns = false, $key = false){
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