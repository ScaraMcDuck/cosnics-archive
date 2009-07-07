<?php

/**
 * Class that defines sha512 hashing
 * @author vanpouckesven
 *
 */
class Sha512Hashing extends Hashing
{

    function create_hash($value)
    {
        return hash('sha512', $value);
    }

    function create_file_hash($file)
    {
        return hash_file('sha512', $file);
    }

}
?>