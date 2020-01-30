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

    protected abstract function getColumns();

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
     * @param bool $columns
     * @param bool $key
     * @return array
     */
    public function readData($columns = false, $key = false)
    {
        if (!$columns) {
            $columns = array_keys($this->columns);
        }

        $results = [];
        foreach ($this->getFiles() as $file) {
            $this->read($results, $file, $columns, $key);
        }

        return $results;
    }

    protected abstract function getFiles();

    /**
     * @param $results
     * @param $fileName
     * @param $columns
     * @param $key
     * @param bool $noFilters
     */
    protected final function read(&$results, $fileName, $columns, $key, $noFilters = false)
    {
        $doFilter = count($this->filters) > 0;
        $file = fopen($this->root . $fileName, "r");
        $size = fstat($file)['size'];
        $base = $this->getStart($fileName, $size);

        while ($base < $size) {
            $this->temp_cache = [];
            if ($noFilters || !$doFilter || $this->checkFilters($file, $base)) {
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
     * @param $fileName
     * @param $size
     * @return int
     */
    protected function getStart($fileName, $size)
    {
        return 0;
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