<?php
require_once Path :: get_common_path() . 'filecompression/filecompression.class.php';

abstract class PackageInstallerSource
{
    private $package_file;
    private $package_folder;
    private $attributes;

    function PackageInstallerSource()
    {
        $this->set_package_file(null);
        $this->set_package_folder(null);
        $this->set_attributes(null);
    }

	/**
	 * Invokes the constructor of the class that corresponds to the specified
	 * type of package installer source.
	 */
	static function factory($type)
	{
		$class = 'PackageInstaller' . DokeosUtilities :: underscores_to_camelcase($type) . 'Source';
		require_once dirname(__FILE__) . '/source/' . $type . '.class.php';
		return new $class();
	}

	abstract function get_archive();

    function process()
    {
        $this->set_package_file($this->get_archive());
        if (!$this->get_package_file())
        {
            return false;
        }
        else
        {
            $this->set_package_folder($this->extract_archive());
            return true;
        }
    }

    function extract_archive()
    {
        $file_path = $this->get_package_file();
        $compression = Filecompression :: factory();
        return $compression->extract_file($file_path);
    }

    function get_package_file()
    {
        return $this->package_file;
    }

    function set_package_file($package_file)
    {
        $this->package_file = $package_file;
    }

    function get_package_folder()
    {
        return $this->package_folder;
    }

    function set_package_folder($package_folder)
    {
        $this->package_folder = $package_folder;
    }

    function get_attributes()
    {
        return $this->attributes;
    }

    function set_attributes($attributes)
    {
        $this->attributes = $attributes;
    }
}
?>