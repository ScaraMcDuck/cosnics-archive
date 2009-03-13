<?php
require_once Path :: get_user_path() . '/validator/user_validator.class.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of validatorclass
 *
 * @author Samumon
 */
abstract class Validator
{
    public static function get_validator($type)
    {
        switch($type)
        {
            case 'user':
                return new UserValidator();
        }
    }

    abstract function get_required_property_names();

    abstract function validate_create(&$object);

    abstract function validate_update(&$object);

    abstract function validate_delete(&$object);

    public function validate_properties($properties)
    {
        foreach($this->get_required_property_names() as $property)
        {
            if($properties[$property]==null)
            {
                return false;
            }
        }
        return true;
    }


}
?>
