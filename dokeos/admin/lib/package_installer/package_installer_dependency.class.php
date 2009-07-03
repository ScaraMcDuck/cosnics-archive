<?php
abstract class PackageInstallerDependency
{
    const COMPARE_EQUAL = 1;
    const COMPARE_NOT_EQUAL = 2;
    const COMPARE_GREATER_THEN = 3;
    const COMPARE_GREATER_THEN_OR_EQUAL = 4;
    const COMPARE_LESS_THEN = 5;
    const COMPARE_LESS_THEN_OR_EQUAL = 6;

    private $dependencies;

    function PackageInstallerDependency($dependencies)
    {
        $this->dependencies = $dependencies;
    }

    function get_dependencies()
    {
        return $this->dependencies;
    }

    function compare($type, $reference, $value)
    {
//        echo $reference . ' ' . $value . '<br />';
        switch($type)
        {
            case self :: COMPARE_EQUAL :
                return ($reference == $value);
                break;
            case self :: COMPARE_NOT_EQUAL :
                return ($reference != $value);
                break;
            case self :: COMPARE_GREATER_THEN :
                return ($value > $reference);
                break;
            case self :: COMPARE_GREATER_THEN_OR_EQUAL :
                return ($value >= $reference);
                break;
            case self :: COMPARE_LESS_THEN :
                return ($value < $reference);
                break;
            case self :: COMPARE_LESS_THEN_OR_EQUAL :
                return ($value <= $reference);
                break;
            default :
                return false;
                break;
        }
    }

    function version_compare($type, $reference, $value)
    {
//        echo $reference . ' ' . $value . '<br />';
        switch($type)
        {
            case self :: COMPARE_EQUAL :
                return version_compare($reference, $value, '==');
                break;
            case self :: COMPARE_NOT_EQUAL :
                return version_compare($reference, $value, '!=');
                break;
            case self :: COMPARE_GREATER_THEN :
                return version_compare($value, $reference, '>');
                break;
            case self :: COMPARE_GREATER_THEN_OR_EQUAL :
                return version_compare($value, $reference, '>=');
                break;
            case self :: COMPARE_LESS_THEN :
                return version_compare($value, $reference, '<');
                break;
            case self :: COMPARE_LESS_THEN_OR_EQUAL :
                return version_compare($value, $reference, '<=');
                break;
            default :
                return false;
                break;
        }
    }

    function verify()
    {
        $dependencies = $this->get_dependencies();
//        dump($dependencies);

        foreach($dependencies as $dependency)
        {
            if (!$this->check($dependency))
            {
                dump($dependencies);
                return false;
            }
        }

        return true;
    }

    abstract function check($dependency);

	/**
	 * Invokes the constructor of the class that corresponds to the specified
	 * type of package installer type.
	 */
	static function factory($type, $dependencies)
	{
		$class = 'PackageInstaller' . DokeosUtilities :: underscores_to_camelcase($type) . 'Dependency';
		require_once dirname(__FILE__) . '/dependency/' . $type . '.class.php';
		return new $class($dependencies);
	}
}
?>