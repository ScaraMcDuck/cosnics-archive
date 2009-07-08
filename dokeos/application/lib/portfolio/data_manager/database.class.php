<?php
/**
 * @package portfolio.datamanager
 */
require_once dirname(__FILE__).'/../portfolio_publication.class.php';
require_once dirname(__FILE__).'/../portfolio_publication_group.class.php';
require_once dirname(__FILE__).'/../portfolio_publication_user.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Sven Vanpoucke
 */

class DatabasePortfolioDataManager extends PortfolioDataManager
{
	private $database;

	function initialize()
	{
		$aliasses = array();
		$aliasses[PortfolioPublication :: get_table_name()] = 'poon';
		$aliasses[PortfolioPublicationGroup :: get_table_name()] = 'poup';
		$aliasses[PortfolioPublicationUser :: get_table_name()] = 'poer';

		$this->database = new Database($aliasses);
		$this->database->set_prefix('portfolio_');
	}

    function get_database()
    {
        return $this->database;
    }

	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}

	function get_next_portfolio_publication_id()
	{
		return $this->database->get_next_id(PortfolioPublication :: get_table_name());
	}

	function create_portfolio_publication($portfolio_publication)
	{
		$succes = $this->database->create($portfolio_publication);

		foreach($portfolio_publication->get_target_groups() as $group)
		{
			$pfpg = new PortfolioPublicationGroup();
			$pfpg->set_portfolio_publication($portfolio_publication->get_id());
			$pfpg->set_group_id($group);
			$succes &= $pfpg->create();
		}

		foreach($portfolio_publication->get_target_users() as $user)
		{
			$pfpg = new PortfolioPublicationUser();
			$pfpg->set_portfolio_publication($portfolio_publication->get_id());
			$pfpg->set_user($user);
			$succes &= $pfpg->create();
		}

		return $succes;
	}

	function update_portfolio_publication($portfolio_publication)
	{
		$condition = new EqualityCondition(PortfolioPublication :: PROPERTY_ID, $portfolio_publication->get_id());
		$succes = $this->database->update($portfolio_publication, $condition);

		$condition = new EqualityCondition(PortfolioPublicationGroup :: PROPERTY_PORTFOLIO_PUBLICATION, $portfolio_publication->get_id());
		$succes &= $this->database->delete(PortfolioPublicationGroup :: get_table_name(), $condition);

		$condition = new EqualityCondition(PortfolioPublicationUser :: PROPERTY_PORTFOLIO_PUBLICATION, $portfolio_publication->get_id());
		$succes &= $this->database->delete(PortfolioPublicationUser :: get_table_name(), $condition);

		foreach($portfolio_publication->get_target_groups() as $group)
		{
			$pfpg = new PortfolioPublicationGroup();
			$pfpg->set_portfolio_publication($portfolio_publication->get_id());
			$pfpg->set_group_id($group);
			$succes &= $pfpg->create();
		}

		foreach($portfolio_publication->get_target_users() as $user)
		{
			$pfpu = new PortfolioPublicationUser();
			$pfpu->set_portfolio_publication($portfolio_publication->get_id());
			$pfpu->set_user($user);
			$succes &= $pfpu->create();
		}

		return $succes;
	}

	function delete_portfolio_publication($portfolio_publication)
	{
		$condition = new EqualityCondition(PortfolioPublication :: PROPERTY_ID, $portfolio_publication->get_id());
		$succes = $this->database->delete($portfolio_publication->get_table_name(), $condition);

		$condition = new EqualityCondition(PortfolioPublicationGroup :: PROPERTY_PORTFOLIO_PUBLICATION, $portfolio_publication->get_id());
		$succes &= $this->database->delete(PortfolioPublicationGroup :: get_table_name(), $condition);

		$condition = new EqualityCondition(PortfolioPublicationUser :: PROPERTY_PORTFOLIO_PUBLICATION, $portfolio_publication->get_id());
		$succes &= $this->database->delete(PortfolioPublicationUser :: get_table_name(), $condition);

		return $succes;
	}

	function count_portfolio_publications($condition = null)
	{
		return $this->database->count_objects(PortfolioPublication :: get_table_name(), $condition);
	}

	function retrieve_portfolio_publication($id)
	{
		$condition = new EqualityCondition(PortfolioPublication :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(PortfolioPublication :: get_table_name(), $condition);
	}

	function retrieve_portfolio_publications($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->database->retrieve_objects(PortfolioPublication :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}

	function create_portfolio_publication_group($portfolio_publication_group)
	{
		return $this->database->create($portfolio_publication_group);
	}

	function delete_portfolio_publication_group($portfolio_publication_group)
	{
		$condition = new EqualityCondition(PortfolioPublicationGroup :: PROPERTY_ID, $portfolio_publication_group->get_id());
		return $this->database->delete($portfolio_publication_group->get_table_name(), $condition);
	}

	function count_portfolio_publication_groups($condition = null)
	{
		return $this->database->count_objects(PortfolioPublicationGroup :: get_table_name(), $condition);
	}

	function retrieve_portfolio_publication_groups($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->database->retrieve_objects(PortfolioPublicationGroup :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}

	function create_portfolio_publication_user($portfolio_publication_user)
	{
		return $this->database->create($portfolio_publication_user);
	}

	function delete_portfolio_publication_user($portfolio_publication_user)
	{
		$condition = new EqualityCondition(PortfolioPublicationUser :: PROPERTY_ID, $portfolio_publication_user->get_id());
		return $this->database->delete($portfolio_publication_user->get_table_name(), $condition);
	}

	function count_portfolio_publication_users($condition = null)
	{
		return $this->database->count_objects(PortfolioPublicationUser :: get_table_name(), $condition);
	}

	function retrieve_portfolio_publication_users($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->database->retrieve_objects(PortfolioPublicationUser :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}

}
?>