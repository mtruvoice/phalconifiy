<?php

namespace Phalconify\Utils;

/**
 * Implements utilities related to arrays.
 */
trait Arrays
{
    function insertArrayToArray(&$array, $insert, $position)
    {
        settype($array, "array");
        settype($insert, "array");
        settype($position, "int");

        // If pos is start, just merge them
        if ($position == 0) {
            $array = array_merge($insert, $array);
        } else {
            // If pos is end just merge them
            if ($position >= (count($array) - 1)) {
                $array = array_merge($array, $insert);
            } else {
                // Split into head and tail, then merge head+inserted bit+tail
                $head = array_slice($array, 0, $position);
                $tail = array_slice($array, $position);
                $array = array_merge($head, $insert, $tail);
            }
        }

        return $array;
    }
}
