<?php
/*
 * with this script its possible to read in the r&d database and import it in
 * the Lcms database


 */

require_once '/var/www/dokeoslink/common/global.inc.php';
require_once '/var/www/dokeoslink/repository/lib/learning_object/rdpublication/rdpublication.class.php';
require_once '/var/www/dokeoslink/application/lib/myportfolio/rdpublication_publication.class.php';
require_once '/var/www/dokeoslink/repository/lib/learning_object_form.class.php';
require_once 'create_user.class.php';



class CreatePublicationObjects {

    private $object;

    function create_object(){

        //dit is enkel voor frederik zijn publicaties
        // om alle publicaties toe te voegen moeten we die offcode weglaten
        $offcode = 23616;
        
        $query = "select * from r2d2_cur.publication p INNER JOIN r2d2_cur.publication_member pm on p.pub_id = pm.pub_id where pm.person_id='".$offcode."' order by p.pub_date DESC, pub_type ASC";

        $result=mysql_query($query);
        if($result && (mysql_num_rows($result) != 0))
        {
            while($row=mysql_fetch_row($result))

            {
                $object = new Rdpublication();

                $query = "select title from r2d2_cur.publication_abstract where pub_id='".$row[0]."'";
                $res2=mysql_query($query);
                $title= mysql_fetch_row($res2);
                $object->set_title($title[0]);
                echo $object->get_title();
                $object->set_description($row[4]);
                $object->set_pub_type("pub");
                $object->set_owner_id($row[10]);
                $object->set_ref_id($row[0]);
                $object->set_parent_id(0);
                $object->create();


                $publication =  new RdpublicationPublication();

                $publication->set_publisher($row[10]);
                //$publication->set_published(234567);
                $publication->set_rdpublication($object->get_object_number());
                $publication->create();
                echo "pub toegevoegd";
            }
        }
    }

    function count_publications(){



        $query = "select count(1) from r2d2_cur.publication p" ;

        $result=mysql_query($query);

        if($result && (mysql_num_rows($result) != 0))
        {

            $row=mysql_fetch_row($result);

            return $row[0];

        }
    }

    function create_dummy_object(){
         $offcode = 4;
         $ref_id =132352;

       // $query = "select * from r2d2_cur.publication p INNER JOIN r2d2_cur.publication_member pm on p.pub_id = pm.pub_id where pm.person_id='".$offcode."' order by p.pub_date DESC, pub_type ASC";

        //$result=mysql_query($query);
        //if($result && (mysql_num_rows($result) != 0))
        //{
            //while($row=mysql_fetch_row($result))

            //{
                $object = new Rdpublication();

              //  $query = "select title from r2d2_cur.publication_abstract where pub_id='".$row[0]."'";
               // $res2=mysql_query($query);
                $title= 'rdpublicatie titel';
                $object->set_title($title);

                $object->set_description('dit is een descriptie van een rdpublicatie');
                $object->set_pub_type("pub");
                $object->set_owner_id($offcode);
                $object->set_ref_id($ref_id);
                $object->set_parent_id(0);
                $object->create();


                $publication =  new RdpublicationPublication();

                $publication->set_publisher($offcode);
                //$publication->set_published(234567);
                $publication->set_rdpublication($object->get_object_number());
                $publication->create();
                echo "pub toegevoegd";
            }
        



}

set_time_limit(100);
//$aantal = CreatePublicationObjects::count_publications();
//CreatePublicationObjects::create_object();
CreatePublicationObjects::  create_dummy_object();
new CreateUser();
?>
