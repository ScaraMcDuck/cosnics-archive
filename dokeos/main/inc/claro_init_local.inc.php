<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) 2003-2005 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Hugues Peeters
	Copyright (c) Roan Embrechts (Vrije Universiteit Brussel)
	Copyright (c) Patrick Cool

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/
/**
==============================================================================
 *
 *                             SCRIPT PURPOSE
 *
 * This script initializes and manages Dokeos session information. It
 * keeps available session information up to date.
 *
 * You can request a course id. It will check if the course Id requested is the
 * same as the current one. If it isn't it will update session information from
 * the database. You can also force the course reset if you want ($cidReset).
 *
 * All the course information is stored in the $_course array.
 *
 * You can request a group id. The script will check if the group id requested is the
 * same as the current one. If it isn't it will update session information from
 * the database. You can also force the course reset if you want ($gidReset).
 *
The course id is stored in $_cid session variable.
 * The group  id is stored in $_gid session variable.
 *
 *
 *                    VARIABLES AFFECTING THE SCRIPT BEHAVIOR
 *
 * string  $login
 * string  $password
 * boolean $logout
 *
 *
 *
 *                   VARIABLES SET AND RETURNED BY THE SCRIPT
 *
 * All the variables below are set and returned by this script.
 *
 * USER VARIABLES
 *
 * int $_uid (the user id)
 *
 *
 *
 *                       IMPORTANT ADVICE FOR DEVELOPERS
 *
 * We strongly encourage developers to use a connection layer at the top of
 * their scripts rather than use these variables, as they are, inside the core
 * of their scripts. It will make code maintenance much easier.
 *
 *	Many if the functions you need you can already find in the
 *	main_api.lib.php
 *
 * We encourage you to use functions to access these global "kernel" variables.
 * You can add them to e.g. the main API library.
 *
 *
 *                               SCRIPT STRUCTURE
 *
 * 1. The script determines if there is an authentication attempt. This part
 * only chek if the login name and password are valid. Afterwards, it set the
 * $_uid (user id) and the $uidReset flag. Other user informations are retrieved
 * later. It's also in this section that optional external authentication
 * devices step in.
 *
 * 2. The script determines what other session informations have to be set or
 * reset, setting correctly $cidReset (for course) and $gidReset (for group).
 *
 * 3. If needed, the script retrieves the other user informations (first name,
 * last name, ...) and stores them in session.
 *
 * 4. If needed, the script retrieves the course information and stores them
 * in session
 *
 * 5. The script initializes the user permission status and permission for the
 * course level
 *
 * 6. If needed, the script retrieves group informations an store them in
 * session.
 *
 * 7. The script initializes the user status and permission for the group level.
 *
 *	@package dokeos.include
==============================================================================
*/
/*
==============================================================================
		INIT SECTION
		variables should be initialised here
==============================================================================
*/

// parameters passed via GET
$logout = isset($_GET["logout"]) ? $_GET["logout"] : '';

// parameters passed via POST
$login = isset($_POST["login"]) ? $_POST["login"] : '';

/*
==============================================================================
		MAIN CODE
==============================================================================
*/
if (isset($_SESSION['_uid']) && $_SESSION['_uid'] && ! ($login || $logout))
{
    // uid is in session => login already done, continue with this value
    $_uid = $_SESSION['_uid'];
}
else
{
    unset($_uid); // uid not in session ? prevent any hacking

    if(isset($_POST['login']) && isset($_POST['password'])) // $login && $password are given to log in
    {
		$login = $_POST['login'];
		$password = $_POST['password'];
        $parsed_login = trim(addslashes($login));
		
		// TODO: Scara - Use UsersManager here !
		require_once dirname(__FILE__).'/../../users/lib/usersdatamanager.class.php';
		$udm = UsersDataManager :: get_instance();

        if (!$udm->is_username_available($parsed_login))
        {
        	$user = $udm->retrieve_user_by_username($parsed_login);

            if ($user->get_auth_source() == PLATFORM_AUTH_SOURCE)
            {
                //the authentification of this user is managed by Dokeos itself

                $password = trim(stripslashes($password));

                // determine if the password needs to be encrypted before checking
                // $userPasswordCrypted is set in an external configuration file

                if ($userPasswordCrypted) $password = md5($password);

                // check the user's password

                if ($password == $user->get_password() && (trim($login) == $user->get_username()))
                {
                    $_uid = $user->get_user_id();

                    api_session_register('_uid');                    
                }
                else // abnormal login -> login failed
                {
                    $loginFailed = true;
                    api_session_unregister('_uid');
                    header('Location: index.php?loginFailed=1');
                    exit;
                }

                if ($_uid != $user->get_creator_id())
                {
                    //first login for a not self registred
                    //e.g. registered by a teacher
                    //do nothing (code may be added later)
                }
            }
            else // no standard Dokeos login - try external authentification
            {
                 /*
                  * Process external authentication
                  * on the basis of the given login name
                  */
                 $loginFailed = true;  // Default initialisation. It could
                                       // change after the external authentication
                 $key = $user->get_auth_source();

                /* >>>>>>>>>>>>>>>> External authentication modules <<<<<<<<<<<<<<<< */
				// see claro_main.conf.php to define these
                include_once($extAuthSource[$key]['login']);
                /* >>>>>>>>>>>>>>>> External authentication modules <<<<<<<<<<<<<<<<<< */
            }
//    	    if(!empty($_SESSION['request_uri']))
//    	    {
//      	        $req = $_SESSION['request_uri'];
//      	        unset($_SESSION['request_uri']);
//      	        header('location: '.$req);
//    	    }
//    	    else
//    	    {
//    	    	header('location: '.api_get_path(WEB_PATH).'index.php');
//    	    }
        }
        else // login failed, mysql_num_rows($result) <= 0
        {
            $loginFailed = true;  // Default initialisation. It could
                                  // change after the external authentication

            /*
             * In this section:
             * there is no entry for the $login user in the Dokeos
             * database. This also means there is no auth_source for the user.
             * We let all external procedures attempt to add him/her
             * to the system.
             *
             * Process external login on the basis
             * of the authentication source list
             * provided by the configuration settings.
             * If the login succeeds, for going further,
             * Dokeos needs the $_uid variable to be
             * set and registered in the session. It's the
             * responsability of the external login script
             * to provide this $_uid.
             */

            if (is_array($extAuthSource))
            {
                foreach($extAuthSource as $thisAuthSource)
                {
                    include_once($thisAuthSource['newUser']);
                }
            } //end if is_array($extAuthSource)

        } //end else login failed
    }
}
?>