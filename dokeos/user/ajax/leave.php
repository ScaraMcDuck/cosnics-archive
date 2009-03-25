<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/../../common/global.inc.php';
//require_once(Path :: get_tracking_path().'lib/events.class.php');
require_once dirname(__FILE__).'/../../tracking/lib/events.class.php';
$tracker = $_POST['tracker'];
$return = Events :: trigger_event('leave','user',array('tracker'=>$tracker,'location' => $_SERVER['REQUEST_URI'], 'user' => $user,'event'=>'leave'));
//echo $tracker;
?>
