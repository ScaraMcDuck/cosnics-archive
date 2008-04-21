<?php

/**
 * @package users.lib.trackers
 */
 
require_once dirname(__FILE__) . '/usertracker.class.php';
 
/**
 * This class tracks the os that a user uses
 */
class OSTracker extends UserTracker
{
	/**
	 * Constructor sets the default values
	 */
    function OSTracker() 
    {
    	parent :: UserTracker();
    	$this->set_property(self :: PROPERTY_TYPE, 'os');
    }
    
    function track($parameters = array())
    {
    	$server = $parameters['server'];
    	$user_agent = $server['HTTP_USER_AGENT'];
    	$os = $this->extract_os_from_useragent($user_agent);
    	
    	$conditions = array();
    	$conditions[] = new EqualityCondition('type', 'os');
    	$conditions[] = new EqualityCondition('name', $os);
    	$condtion = new AndCondition($conditions);
    	
    	$trackeritems = $this->retrieve_tracker_items($condtion);
    	if(count($trackeritems) != 0)
    	{
    		$ostracker = $trackeritems[0];
    		$ostracker->set_value($ostracker->get_value() + 1);
    		$ostracker->update();
    	}
    	else
    	{
    		$this->set_name($os);
    		$this->set_value(1);
    		$this->create();
    	}
    }
    
    /**
	 * Extracts a os from the useragent
	 * @param User Agent $user_agent
	 * @return string The Os
	 */
	function extract_os_from_useragent($user_agent)
	{
		// default values, if nothing corresponding found
		$viewable_os = "Unknown";
		$list_os = $this->load_os();
		
		// search for corresponding pattern in $_SERVER['HTTP_USER_AGENT']
		// for os
		for($i = 0; $i < count( $list_os ); $i++)
		{
			$pos = strpos( $user_agent, $list_os[$i][0] );
			if( $pos !== false )
			{	
				$viewable_os = $list_os[$i][1];
			}	
		}
		
		return $viewable_os;
	}
	
	/**
     * Function used to list all the available os with their names
     * @return array of os 
     */
    function load_os()
	{	
	    $buffer = split ("#","Windows 95|Win 95#Windows_95|Win 95#Windows 98|Win 98#Windows NT|Win NT#Windows NT 5.0|Win 2000#Windows NT 5.1|Win XP#Windows 2000|Win 2000#Windows XP|Win XP#Windows ME|Win Me#Win95|Win 95#Win98|Win 98#WinNT|Win NT#linux-2.2|Linux 2#Linux|Linux#Linux 2|Linux 2#Macintosh|Mac#Mac_PPC|Mac#Mac_PowerPC|Mac#SunOS 5|SunOS 5#SunOS 6|SunOS 6#FreeBSD|FreeBSD#beOS|beOS#InternetSeer|InternetSeer#Googlebot|Googlebot#Teleport Pro|Teleport Pro");
	    $i=0;
	    foreach( $buffer as $buffer1 ) {
	       list ( $list_os[$i][0], $list_os[$i][1]) = split ('[|]', $buffer1 );
	       $i+=1;
	    }
	    return $list_os;
	}
}
?>