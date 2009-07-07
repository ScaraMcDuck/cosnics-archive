<?php

/**
 * Class that defines sha1 hashing
 * @author vanpouckesven
 *
 */
class Sha1Hashing extends Hashing
{

    function create_hash($value)
    {
        return sha1($value);
    }

    function create_file_hash($file)
    {
        return sha1_file($file);
    }

}
?>