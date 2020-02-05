<?php

namespace Data;

/**
 * Class Reader
 * @package Data
 */
abstract class Reader
{
    private $root;
    private $length = 0;
    private $columns = [];

    private $filters = [];
    private $temp_cache = false;

    /**
     * Reader constructor.
     * @param $root
     */
    protected function __construct($root)
    {
        $this->root = $root;
        $this->columns = $this->getColumns();
        foreach ($this->columns as $name => $column) {
            if ($column != null) {
                $this->length += $column->getLength();
            }
        }
    }

    public abstract function getColumns();

    /**
     * @param $name
     * @param $condition
     * @param $value
     */
    public final function addFilter($name, $condition, $value)
    {
        $this->filters[] = [$name, $condition, $value];
    }

    /**
     * Reader has filters.
     * @return bool
     */
    public final function hasFilters()
    {
        return count($this->filters) > 0;
    }

    /**
     * @param bool $columns
     * @param bool $key
     * @param bool $last
     * @return array
     */
    public function readData($columns = false, $key = false, $last = false)
    {
        if (!$columns) {
            $columns = array_keys($this->columns);
        }

        $results = [];
        $files = $this->getFiles($last);
        foreach ($files as $file) {
            $this->read($results, $file, $columns, $key, $last);
        }

        return $results;
    }

    protected abstract function getFiles($last = false);

    /**
     * @param $results
     * @param $fileName
     * @param $columns
     * @param $key
     * @param bool $last
     */
    protected final function read(&$results, $fileName, $columns, $key, $last = false)
    {
        $file = fopen($this->root . $fileName, "r");
        $size = fstat($file)['size'];
        $base = $last ? ($size - $this->getLength()) : 0;

        while ($base < $size) {
            $this->temp_cache = [];
            if (!$this->hasFilters() || $this->checkFilters($file, $base)) {
                $result = [];
                foreach ($columns as $column) {
                    $result[$column] = $this->getColumn($file, $base, $column);
                }
                if ($key == false || $this->getColumn($file, $base, $key) == false) {
                    $results[] = $result;
                } else {
                    $results[$this->getColumn($file, $base, $key)] = $result;
                }
            }
            $base += $this->length;
        }
        fclose($file);
        $this->temp_cache = false;
    }

    /**
     * @param $file
     * @param $base
     * @return bool
     */
    private function checkFilters($file, $base)
    {
        foreach ($this->filters as $filter) {
            switch ($filter[1]) {
                case '>=':
                    if ($this->getColumn($file, $base, $filter[0]) < $filter[2])
                        return false;
                    break;
                case '<=':
                    if ($this->getColumn($file, $base, $filter[0]) > $filter[2])
                        return false;
                    break;
                case '=':
                    if (is_array($filter[2])) {
                        if (!in_array($this->getColumn($file, $base, $filter[0]), $filter[2]))
                            return false;
                    } else {
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

    /**
     * @param $file
     * @param $base
     * @param $name
     * @return bool|mixed
     */
    protected function getColumn($file, $base, $name)
    {
        if (!array_key_exists($name, $this->columns)) {
            return false;
        }
        if (array_key_exists($name, $this->temp_cache)) {
            return $this->temp_cache[$name];
        }

        $type = $this->columns[$name];

        fseek($file, $base + $type->getPosition());
        $value = fread($file, $type->getLength());

        return $type->decodeValue($value);
    }

    /**
     * @return array
     */
    protected function getFilters()
    {
        return $this->filters;
    }

    protected function getRoot()
    {
        return $this->root;
    }

    /**
     * @return int
     */
    protected function getLength()
    {
        return $this->length;
    }

    /**
     * @param $path
     * @return array
     */
    protected final function list($path)
    {
        return array_diff(scandir($path), ['.', '..']);
    }

}