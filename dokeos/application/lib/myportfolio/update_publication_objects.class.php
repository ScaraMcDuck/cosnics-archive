<?php
/*
 * This script has to run every night to check if there are any changes made in the r&d database
 */

require_once '/var/www/dokeoslink/common/global.inc.php';
require_once '/var/www/dokeoslink/repository/lib/learning_object/rdpublication/rdpublication.class.php';
require_once '/var/www/dokeoslink/application/lib/myportfolio/rdpublication_publication.class.php';
require_once '/var/www/dokeoslink/repository/lib/learning_object_form.class.php';
require_once '/var/www/dokeoslink/application/lib/myportfolio/data_manager/database.class.php';
require_once '/var/www/dokeoslink/repository/lib/data_manager/database.class.php';

class UpdatePublicationObjects {

    private $object;

    function update_object(){

        $offcode = 23616;
        $rend = array();
        $query = "select * from r2d2_cur.publication p INNER JOIN r2d2_cur.publication_member pm on p.pub_id = pm.pub_id where pm.person_id='".$offcode."' order by p.pub_date DESC, pub_type ASC";

        $result=mysql_query($query);
        if($result && (mysql_num_rows($result) != 0))
        {
            while($row=mysql_fetch_row($result))

            {
               $pub_id = $row[0];
              // dump($pub_id);
               $LCMSquery = "select * from repository_learning_object join repository_rdpublication on repository_learning_object.id = repository_rdpublication.id where ref_id = ".$pub_id;
               $resultLCMS=mysql_query($LCMSquery);

               //publicaties vervangen die reeds bestaan maar gewijzigd zijn


               if($resultLCMS && (mysql_num_rows($resultLCMS) != 0))
                {
                    $rowLCMS=mysql_fetch_row($resultLCMS);
                    $gelijk = true;
                    $querytit = "select title from r2d2_cur.publication_abstract where pub_id='".$row[0]."'";
                    $res2=mysql_query($querytit);
                    $title= mysql_fetch_row($res2);
                    if ( $rowLCMS[5] != $title[0]) {
                        $gelijk = false;
                        //echo "titel fout <br>";
                        echo $rowLCMS[0];
                        $uquery = "UPDATE repository_learning_object SET title = '".$title[0]."' where id = ".$rowLCMS[0];
                        mysql_query($uquery);
                    }
                    if ( $rowLCMS[6] != $row[4]){
                        $gelijk = false;
                        //echo "descr fout <br>";
                        $uquery = "UPDATE repository_learning_object SET description = '".$row[4]."' where id = ".$rowLCMS[0];
                        mysql_query($uquery);
                    }
                    


                    
                } 
                
                // nieuwe publicaties toevoegen 
                
                else {


                $object = new Rdpublication();

                $query = "select title from r2d2_cur.publication_abstract where pub_id='".$row[0]."'";
                $res2=mysql_query($query);
                $title= mysql_fetch_row($res2);

                $object->set_title($title);
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
                echo "publicatie met nummer '".$row[0]."' wordt toegevoegd <br>";
                
                }
        }
        }
        //echo "einde<br><br><br>";

       //Publicaties verwijderen die niet meer in de r&d staan

        $query = "select * from repository_rdpublication";


        $result=mysql_query($query);
        
        if($result && (mysql_num_rows($result) != 0))
        {
            while($row=mysql_fetch_row($result))

            {
               echo $row[1].'<br>';
               $rdquery = "select * from r2d2_cur.publication where pub_id = ".$row[1];
                $resultrd=mysql_query($rdquery);
               
               if($resultrd && (mysql_num_rows($resultrd) == 0))
                {
                    $rowRD=mysql_fetch_row($resultrd);
                    
                    echo "publicatie met nummer '".$row[0]."' wordt verwijderd <br>";
                       /* $pmdm = DatabasePortfolioDataManager :: get_instance();
                        $object = $pmdm->retrieve_rdpublication($row[0]);
                        dump($object);
                        $object->delete();*/
                    
                        $rmdm = DatabaseRepositoryDataManager :: get_instance();

                        $rdpublication = $rmdm->retrieve_learning_object($row[0], 'rdpublication');

                       /**** kan niet gans verwijderen in repository **/
                       // $rmdm->delete_learning_object($rdpublication);

                        //$object =$pdm->retrieve_rdpublication($row[0]);
                        
                    

                 /* $dquery = "DELETE FROM repository_rdpublication where ref_id = ".$row[1];

                  $dquery = "DELETE FROM repository_learning_object where id =".$row[0];

                  $dquery = "DELETE FROM repository_learning_object where id =".$row[0];*/
                }
            }
        }


        
    }


    


}

set_time_limit(100);
//$aantal = CreatePublicationObjects::count_publications();
UpdatePublicationObjects::update_object();

?>
