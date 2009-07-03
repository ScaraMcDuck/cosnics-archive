<?php
require_once Path :: get_common_path() . 'filecompression/filecompression.class.php';

abstract class PackageInstallerSource
{
    private $parent;
    private $package_file;
    private $package_folder;
    private $attributes;

    function PackageInstallerSource($parent)
    {
        $this->set_parent($parent);
        $this->set_package_file(null);
        $this->set_package_folder(null);
        $this->set_attributes(null);
    }

    function get_parent()
    {
        return $this->parent;
    }

    function set_parent($parent)
    {
        $this->parent = $parent;
    }

    function add_message($message, $type = PackageInstaller :: TYPE_NORMAL)
    {
        $this->get_parent()->add_message($message, $type);
    }

    function installation_failed($error_message)
    {
        $this->get_parent()->installation_failed($error_message);
    }

    function installation_successful($type)
    {
        $this->get_parent()->installation_succesful($type);
    }

    function process_result($type)
    {
        $this->get_parent()->process_result($type);
    }

	/**
	 * Invokes the constructor of the class that corresponds to the specified
	 * type of package installer source.
	 */
	static function factory($parent, $type)
	{
		$class = 'PackageInstaller' . DokeosUtilities :: underscores_to_camelcase($type) . 'Source';
		require_once dirname(__FILE__) . '/source/' . $type . '.class.php';
		return new $class($parent);
	}

	abstract function get_archive();

    function process()
    {
        $this->set_package_file($this->get_archive());
        if (!$this->get_package_file())
        {
            return $this->get_parent()->installation_failed('source', Translation :: get('RemotePackageNotRetrieved'));
        }
        else
        {
            $extract_path = $this->extract_archive();
            if (!$extract_path)
            {
                return $this->get_parent()->installation_failed('source', Translation :: get('RemotePackageNotExtracted'));
            }
            else
            {
                $this->set_package_folder($extract_path);
                $this->get_parent()->add_message(Translation :: get('RemotePackageExtracted'));
                return true;
            }
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