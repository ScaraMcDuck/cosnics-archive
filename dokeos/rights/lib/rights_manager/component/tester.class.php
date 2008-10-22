<?php
/**
 * @package user.usermanager
 */
require_once dirname(__FILE__).'/../rights_manager.class.php';
require_once dirname(__FILE__).'/../rights_manager_component.class.php';
require_once dirname(__FILE__).'/../../rights_data_manager.class.php';
require_once 'Tree/Tree.php';

class RightsManagerTesterComponent extends RightsManagerComponent
{
	private $location_id;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		error_reporting(E_ALL);
		
		//		echo '<pre>';
		//		print_r($tree->add( array("name"=>"B41") , '20' ));
		//		echo '</pre>';
		
		
		$config = array(
		    'type' => 'Nested',
		    'storage' => array(
		        'name' => 'MDB2',
		        'dsn' => 'mysql://root:root@localhost/lcms',
		        // 'connection' =>
		    ),
		    'options' => array(
		        'table' => 'rights_location',
		        'order' =>  'id',
		        'fields' => array(),
		    ),
		);
		
		$tree =& Tree::factoryDynamic($config);
		
		$show[] = '$tree->getRoot()';
		$show[] = '$tree->getElement(1)';
		$show[] = '$tree->getChildren(1, true)';
		$show[] = '$tree->getPath(7)';
		$show[] = '$tree->getPath(2)';
		$show[] = '$tree->add(array("name"=>"c0") , 5 )';
		$show[] = '$tree->remove( $res )';  // remove the last element that was added in the line before :-)
		$show[] = '$tree->getRight( 5 )';
		$show[] = '$tree->getLeft( 5 )';
		$show[] = '$tree->getChildren( 1 )';
		$show[] = '$tree->getParent( 2 )';
		$show[] = '$tree->nextSibling( 2 )';
		$show[] = '$tree->nextSibling( 4 )';
		$show[] = '$tree->nextSibling( 8 )';
		$show[] = '$tree->previousSibling( 2 )';
		$show[] = '$tree->previousSibling( 4 )';
		$show[] = '$tree->previousSibling( 8 )';
		
		$show[] = '$tree->move( 4,3 )';
		
		
		foreach ($show as $aRes) {
		    echo "<strong>$aRes</strong><br />";
		    eval("\$res=".$aRes.';');
		    if ($res == false) {
		        print "false";
		    } else {
		        echo '<pre>';
		        print_r($res);
		        echo '</pre>';
		    }
		    echo '<br /><br />';
		}
	}
}

?>