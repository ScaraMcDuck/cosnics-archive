<?php
require_once Path :: get_library_path() . 'security/security.class.php';

class Request
{

    function get($variable)
    {
        if (isset($_GET[$variable]))
        {
            $value = $_GET[$variable];
            // TODO: Add the necessary security filters if and where necessary
            $value = Security :: remove_XSS($value);
            return $value;
        }
        else
        {
            return null;
        }
    }

    function set_get($variable, $value)
    {
        $_GET[$variable] = $value;
    }

    function post($variable)
    {
        $value = $_POST[$variable];
        // TODO: Add the necessary security filters if and where necessary
        return $value;
    }

    function server($variable)
    {
        $value = $_SERVER[$variable];
        // TODO: Add the necessary security filters if and where necessary
        return $value;
    }

    function file($variable)
    {
        $value = $_FILES[$variable];
        // TODO: Add the necessary security filters if and where necessary
        return $value;
    }

    function environment($variable)
    {
        $value = $_ENV[$variable];
        // TODO: Add the necessary security filters if and where necessary
        return $value;
    }
}
?>