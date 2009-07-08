<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of create_userclass
 *
 * @author pieter
 */
require_once '/var/www/dokeoslink/common/global.inc.php';
require_once "/var/www/dokeoslink/user/lib/user.class.php";

class CreateUser {
    function CreateUser(){
       // $prop = array(4,"test","test","test","test","platform",1,"test@hotmail.com","","23616",NULL,NULL,"english",209715200,300,20,NULL,0,0,1247036923,1);
    

  $prop = Array
(
    'user_id' => 4,
    'lastname' => 'test',
    'firstname' => 'test',
    'username' => 'test',
    'password' => 'd033e22ae348aeb5660fc2140aec35850c4da997',
    'auth_source' => 'platform',
    'email' => 'webmaster@localhost.localdomain',
    'status' => 1,
    'admin' => 1,
    'phone' => NULL,
    'official_code' => '23616',
    'picture_uri' => NULL,
    'creator_id' => NULL,
    'language' => 'english',
    'disk_quota' => 209715200,
    'database_quota' => 300,
    'version_quota' => 20,
    'theme' => NULL,
    'activation_date' => 0,
    'expiration_date' => 0,
    'registration_date' => 1247036923,
    'active' => 1
);

   


        $user = new User();
        $user->set_default_properties($prop);
        dump($user->get_default_properties());
        $udm = UserDataManager::get_instance();
        $udm->create_user($user);

       
        echo einde;
    }


}


?>
