<?php
abstract class PackageInstallerType
{
    private $source;

    function PackageInstallerType($source)
    {
        $this->source = $source;
    }

    function get_source()
    {
        return $this->source;
    }

    abstract function install();

    function cleanup()
    {
        $source = $this->get_source();
        Filesystem :: remove($source->get_package_file());
        Filesystem :: remove($source->get_package_folder());
    }

	/**
	 * Invokes the constructor of the class that corresponds to the specified
	 * type of package installer type.
	 */
	static function factory($type, $source)
	{
		$class = 'PackageInstaller' . DokeosUtilities :: underscores_to_camelcase($type) . 'Type';
		require_once dirname(__FILE__) . '/type/' . $type . '.class.php';
		return new $class($source);
	}
}
?>