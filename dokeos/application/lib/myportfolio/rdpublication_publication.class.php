<?php
/**

 */
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_user_path(). 'lib/user_data_manager.class.php';
require_once dirname(__FILE__).'/data_manager/database.class.php';
/**

 *	@author Pieter Hens
 */
class RdpublicationPublication
{
	const PROPERTY_ID = 'id';
	const PROPERTY_RDPUBLICATION = 'rdpublication';
	const PROPERTY_PUBLISHER = 'publisher';
	const PROPERTY_PUBLISHED = 'published';

	private $id;
	private $defaultProperties;

	
	function RdpublicationPublication($id = 0, $defaultProperties = array ())
	{
		$this->id = $id;
		$this->defaultProperties = $defaultProperties;
	}

	
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	
	function get_default_properties()
	{
		return $this->defaultProperties;
	}


	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID,  self :: PROPERTY_RDPUBLICATION, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED);
	}

	
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}

	
	function get_id()
	{
		return $this->id;
	}

	
	function get_rdpublication()
	{
		return $this->get_default_property(self :: PROPERTY_RDPUBLICATION);
	}

	 
	function get_publisher()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHER);
	}

	
	function get_published()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHED);
	}

	function set_id($id)
	{
		$this->id = $id;
	}

	
	function set_rdpublication($id)
	{
		$this->set_default_property(self :: PROPERTY_RDPUBLICATION, $id);
	}

	function set_publisher($publisher)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
	}

	function set_published($published)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
	}


	function get_publication_object()
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_learning_object($this->get_rdpublication());
	}

	function get_publication_publisher()
	{
		$udm = UserDataManager :: get_instance();
		return $udm->retrieve_user($this->get_publisher());
	}

    function create()
	{
		$now = time();
		$this->set_published($now);
		$pmdm = DatabasePortfolioDataManager :: get_instance();
		$id = $pmdm->get_next_rdpublication_id();
		$this->set_id($id);
		return $pmdm->create_rdpublication($this);
	}

    function delete()
	{
		return DatabasePortfolioDataManager :: get_instance()->delete_portfolio_publication($this);
	}

	
}
?>
