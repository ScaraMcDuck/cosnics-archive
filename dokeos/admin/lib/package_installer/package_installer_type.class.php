<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_dependency.class.php';

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

    function verify_dependencies()
    {
        $source = $this->get_source();
        $attributes = $source->get_attributes();
        $dependency = unserialize($attributes->get_dependencies());

        foreach($dependency as $type => $dependencies)
        {
            $verifier = PackageInstallerDependency :: factory($type, $dependencies['dependency']);
            if (!$verifier->verify())
            {
                return false;
            }
        }

        return true;
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