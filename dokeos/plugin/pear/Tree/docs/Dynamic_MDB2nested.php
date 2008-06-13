<?php
    //
    //  $Id: Dynamic_MDBnested.php,v 1.1 2004/12/21 19:59:20 dufuz Exp $
    //

//ini_set('include_path',realpath(dirname(__FILE__).'/../../').':'.realpath(dirname(__FILE__).'/../../../includes').':'.ini_get('include_path'));
//ini_set('error_reporting',E_ALL);

	require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
    require_once 'Tree/Tree.php';

#    $tree = Tree::setupDynamic('MDBnested' , 'mysql://root@localhost/tree_test' , array('table' => 'nestedTree'));
#   OR
    $tree = Tree::setup('Dynamic_MDB2nested' , 'mysql://root:root@localhost/lcms' , array('table' => 'rights_location', 'columnNameMaps'=>array('left' => 'left_value', 'right' => 'right_value', 'parentId' => 'parent', 'name' => 'location')));
	
	echo '<pre>';
	
	print_r($tree->getRoot());
	
	echo '</pre>';
//    $show[] = '$tree->getRoot()';
//    $show[] = '$tree->getElement( 1 )';
//    $show[] = '$tree->getChild( 1 )';
//    $show[] = '$tree->getPath( 7 )';
//    $show[] = '$tree->getPath( 2 )';
//    $show[] = '$tree->add( array("name"=>"c0") , 5 )';
//    $show[] = '$tree->remove( $res )';  // remove the last element that was added in the line before :-)
//    $show[] = '$tree->getRight( 5 )';
//    $show[] = '$tree->getLeft( 5 )';
//    $show[] = '$tree->getChildren( 1 )';
//    $show[] = '$tree->getParent( 2 )';
//    $show[] = '$tree->getNext( 2 )';
//    $show[] = '$tree->getNext( 4 )';
//    $show[] = '$tree->getNext( 8 )';
//    $show[] = '$tree->getPrevious( 2 )';
//    $show[] = '$tree->getPrevious( 4 )';
//    $show[] = '$tree->getPrevious( 8 )';
//    $show[] = '$tree->getPreviousId( 8 )';
//
//    $show[] = '$tree->move( 4,3 )';
//
//
//    foreach($show as $aRes) {
//        echo "<b>$aRes</b><br>";
//        eval("\$res=".$aRes.';');
//        if ($res == false) {
//            print "false";
//        } else {
//            print_r($res);
//        }
//        echo '<br><br>';
//    }


?>

<a href="http://research.calacademy.org/taf/proceedings/ballew/sld029.htm">the tree structure visualisation</a>