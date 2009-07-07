<?php

/**
 * Class that defines whirlpool hashing
 * @author Samumon
 *
 */
class WhirlpoolHashing extends Hashing
{

    function create_hash($value)
    {
        return hash('whirlpool', $value);
    }

    function create_file_hash($file)
    {
        return hash_file('whirlpool', $file);
    }

}
?>