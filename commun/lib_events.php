<?php
/**
 * @author Patrick Pollet
 * @version $Id: lib_events.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */



 if (is_admin()) {   //utilisateur courant uniquement
    maj_bd_events();
 }

 function maj_bd_events () {
	 global $CFG,$USER;
 
       
      
 }
 
 
 function event_exist ($objet,$action) {
 	//return true;
 	$criteres="objet='$objet' and action='$action'";	
 	$res=get_record('events',$criteres,false);
 	return $res;
 }
 
 function add_event($objet,$action,$script,$fonction,$cron=0,$actif=1,$commentaire='') {
 	$rec=new StdClass();
 	$rec->objet=$objet;
 	$rec->action=$action;
 	$rec->script=$script;
 	$rec->fonction=$fonction;
 	$rec->cron=$cron;
 	$rec->actif=$actif;
 	$rec->commentaire=$commentaire;
 	insert_record('events',$rec);
 	
 	//`objet`, `action`, `script`, `fonction`, `cron`, `actif`, `commentaire`
 }

/**
 * declenche un evenement
 * @param $objet
 * @param $action
 * @param $id
 * @param $data
 * @return boolean
 */
 function event_trigger($objet,$action,$id,$data) {
     // dump_event ($objet.'_'.$action.' : '.$id,$data);
     $criteres="objet='$objet' and action='$action' and actif=1";

     $res=get_records('events',$criteres);
     if (empty($res)) {
         dump_event ("pas d''events pour ".$objet.'_'.$action,null);
         return false;
     } else {
         foreach ($res as $ligne) {
             if ($ligne->cron)
                 ajoute_evt_cron ($ligne,$id,$data);  //traitement differ�
             else {
                 $ligne->status=execute_evt($ligne,$id,$data);      //traitement imm�diat

                 dump_event ('status '.$objet.'_'.$action.' : ',$ligne);
                 //die();
             }
         }
     }
     return true;
 }


/**
 * traitement imm�diat
 * @param $event un record extrait de la table c2ievents
 * @param $id identifiant unique de l'objet concern�
 * @param $data donn�es sp�cifique
 * @return 0 if OK sinon code d'erreur n�gatif
 */
function execute_evt($event,$id,$data) {
    global $CFG;

    if (empty($event->fonction)) return -1; // pas de fonction

    if (is_callable($event->fonction)) {
        // oki, no need for includes

    } else if (file_exists($CFG->dirroot.'/'.$event->script)) {
        include_once($CFG->dirroot.'/'.$event->script);

    } else {
        return -2; // pas de script
    }
      // checks for handler validity
    if (is_callable($event->fonction)) {
        return call_user_func($event->fonction, $data);

    } else {
        return -3; //fonction pas dans le script
    }

}

/**
 * traitement differ�
 * @param $event un record extrait de la table c2ievents
 * @param $id identifiant unique de l'objet concern�
 * @param $data donn�es sp�cifique
 * @return void
 */
function ajoute_evt_cron($event,$id,$data) {
}


function dump_event ($id,$data) {
    global $CFG;
    if (!empty($CFG->debug_events)) {
        $info=$id.' :' .print_r($data,true);
        error_log ($info,3,$CFG->chemin_ressources.'/events.log' );
        //die();
    }

}



?>
