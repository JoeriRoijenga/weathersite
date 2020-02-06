<?php

namespace Data;

/**
 * Class DataType
 * @package Data
 */
class DataType
{

    // Available data types.
    private const INTEGER = 0;
    private const DOUBLE = 1;
    private const STRING = 2;

    private $type;
    private $position;
    private $length;

    private $signed;
    private $decimals;

    /**
     * DataType constructor.
     * @param $type int Integer which should correspond to a type in this class.
     * @param $position int Integer which represents the position in a byte row.
     * @param $length int Integer which represents the length of the data in bytes.
     */
    private function __construct($type, &$position, $length)
    {
        $this->type = $type;
        $this->position = $position;
        $this->length = $length;

        $position += $length;
    }

    /**
     * Create an Integer data type.
     *
     * @param $position int Integer which represents the position in a byte row.
     * @param $length int Integer which represents the length of the data in bytes.
     * @param bool $signed If the numeric value is signed.
     * @return DataType Created data type.
     */
    public static function Integer(&$position, $length, $signed = false)
    {
        $type = new DataType(self::INTEGER, $position, $length);
        $type->signed = $signed;
        $type->decimals = 0;
        return $type;
    }

    /**
     * Create an Double data type.
     *
     * @param $position int Integer which represents the position in a byte row.
     * @param $length int Integer which represents the length of the data in bytes.
     * @param $decimals int Amount of decimals in the value.
     * @param bool $signed If the numeric value is signed.
     * @return DataType Created data type.
     */
    public static function Double(&$position, $length, $decimals, $signed = false)
    {
        $type = new DataType(self::DOUBLE, $position, $length);
        $type->decimals = $decimals;
        $type->signed = $signed;
        return $type;
    }

    /**
     * Create an Integer data type.
     *
     * @param $position int Integer which represents the position in a byte row.
     * @param $length int Integer which represents the length of the data in bytes.
     * @return DataType Created data type.
     */
    public static function String(&$position, $length)
    {
        return new DataType(self::STRING, $position, $length);
    }

    public function getLength()
    {
        return $this->length;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getDecimals()
    {
        return $this->decimals;
    }

    /**
     * @param $value String byte value to be decoded.
     * @return mixed Decoded value.
     */
    public function decodeValue($value)
    {
        $decodedValue = false;
        $byteArray = unpack("C*", $value);
        switch ($this->type) {
            case self::INTEGER:
            case self::DOUBLE:
                $decodedValue = 0;
                $negative = false;
                if ($this->signed && $byteArray[1] & 0x80) {
                    $negative = true;
                    $byteArray[$this->length] -= 0b1;
                }

                $shift = 0;
                foreach (array_reverse($byteArray) as $byte) {
                    if ($negative) {
                        $byte = $byte ^ 0xFF;
                    }
                    $decodedValue += $byte << $shift;
                    $shift += 8;
                }

                if ($negative) {
                    $decodedValue = 0 - $decodedValue;
                }

                if ($this->decimals > 0) {
                    $decodedValue = $decodedValue / pow(10, $this->decimals);
                }

                break;
            case self::STRING:
                $decodedValue = '';
                foreach ($byteArray as $byte) {
                    $decodedValue .= chr($byte);
                }
                $decodedValue = trim($decodedValue);
                break;
        }

        return $decodedValue;
    }

}