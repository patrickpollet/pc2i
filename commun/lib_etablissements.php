<?php
/**
 * @author Patrick Pollet
 * @version $Id: lib_etablissements.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/*
 * bibliotheque de manipulations de l'entit� etablissement
 * beaucoup de fonctions on un parametre $connexion qui res inutile en v1.5
 * mais est rest� tant que tous les scripts n'ont pas �t� migr�s'
 */

/**
 * partie retouche BD
 * rev 1.4--> 1.41
 */

 // if (is_admin()) {   //lib_etablissements est charg�e AVANt la lecture des droits !
    maj_bd_etablissement();
 //} ;

 function maj_bd_etablissement () {
    global $CFG,$USER;

 }

/**
 * n'�choue pas !'
 */
function nom_univ($id_etab, $connexion=false){
	// retourne le nom de l'�tablissement ayant pour cl� $id_etab
	if ($e=get_etablissement($id_etab,false))
	   return $e->nom_etab;
    else return traduction("err_etablissement_inconnu",false, $id_etab);

}

/**
 * @param string $ide
 * @param string $die 	si vrai declenche une erreur fatale en cas d'erreur sinon return null
 * @return objet 		la ligne extraite de la BD ou null en cas d'erreur (et $die=0)
 */
function get_etablissement ($id,$die=1) {
	return get_record("etablissement",'id_etab='.(int)$id,$die,"err_etablissement_inconnu",$id);
}


function etablissement($login,$die=1){
	// retourne l'�tablissement complet de la personne ayant pour login $login
	if ($ide=etab($login,false,$die))
		return get_etablissement($ide,$die);
	else return false;
}


/**
 * retourne l'universit� (la plateforme) de l'�tablissement $ide
 */

function plateforme($ide, $conn=false){

	while (intval($ide)>0){
        $ligne=get_record("etablissement","id_etab = '".intval($ide)."'",false);
        if ($ligne) {
            if ($ligne->pere ==-1) return 1;
            else  if ($ligne->pere ==1) return $ide;
                else $ide=$ligne->pere;
        }
		else
           erreur_fatale ("err_etab_inconnu", $ide);
	}

}



function etablissement_est_supprimable($ide) {
    global $USER;
    if (($ide==1) || ($ide==$USER->id_etab_perso)) return false;
    return (count_records("utilisateurs","etablissement=".$ide)==0)
              && (count_records("inscrits","etablissement=".$ide)==0)
              && (count_records("questions","id_etab=".$ide)==0)
              && (count_records("examens","id_etab=".$ide)==0)
              && (count_records("etablissement","pere=".$ide)==0);
}


function supprime_etablissement($ide,$test=true) {
    global $USER;
    if (($ide==1) || ($ide==$USER->id_etab_perso)) return false;

    $comps=get_composantes($ide);
    $ok=true;

    foreach ($comps as $comp) {

        $ok = $ok and supprime_etablissement($comp->id_etab,$test);
        if (!$ok) break; // une composante a refus�
    }

    if ($ok) {
         $ok =  etablissement_est_supprimable($ide);

         if ($ok && !$test) {
             delete_records("ldap","id_etab=".$ide);
             $ligne=get_etablissement($ide);
             delete_records("etablissement","id_etab=".$ide,true);
            //tracking :
            espion3("suppression","etablissement",  $ide,$ligne);
         } //else print "ko".$ok;
    }
    return $ok;
}

function get_etablissements($tri='nom_etab') {
        return get_records('etablissement', '1',$tri);
}


/**
 * renvoie les composantes de 1er niveau
 */
function get_composantes($id_pere=1,$tri='nom_etab') {
       return get_records('etablissement','pere='.(int)$id_pere,$tri);
}




/**
 * renvoie les composantes au dessus de ide
 */
function get_parents ($ide) {
    $ret=array();
    $et=get_etablissement($ide);
    while ($et->pere >1) {  //on ne compte pas la nationale comme 'parent'
            $et=get_etablissement($et->pere);
            $ret[]=$et;
    }
    return $ret;
}


function get_etablissements_principaux($tri='nom_etab') {
        return get_composantes (1,$tri);
}


/**
 * rev 841
 * renvoie une liste pertinente d'�tablissements pour des crit�res de recherche dans
 * les liste d'examens (pas celle de questions ou tous les etablissements peuvent participer
 * rev 979 ajout param�tre optionnel id_pere
 */
function get_etablissements_filtre ($tri='nom_etab',$id_pere='') {
    global $CFG;
    if (empty($id_pere)) $id_pere=$CFG->universite_serveur;
    if ($id_pere==1)
        return get_etablissements($tri); //le ministere ET les ets principaux
    else {
        /***
         $linf=$id_pere."000";
         $lsup=$id_pere."999";
         $sql=<<<EOS
         select * from {$CFG->prefix}etablissement
         where id_etab=$id_pere
         or (id_etab >=$linf and id_etab <=$lsup)
         order by $tri
         EOS;
         return get_records_sql($sql);
         **/
        $ret[]=get_etablissement ($id_pere);
        $comps=get_composantes($id_pere,$tri);
        foreach ($comps as $comp) {
            if ($subs=get_etablissements_filtre ($tri,$comp->id_etab))
                $ret=array_merge($ret,$subs);
        }
        return $ret;
    }
}


/**
 * anciennes fonctions version < 1.5
 * le parametre connexion devien optionnel car inutile
 * idem pour etab et login
 */

function config_langue($login, $connexion=false){
	global $CFG;
	// retourne la langue pour l'universit� de l'utilisateur $login
	// non support� tant que la PF n'est pas traduite
	return $CFG->langue;
	/*
	if (! isset( $login) || empty($login)) return 50;
	if ($e=etablissement($login,0))
	   return $e->param_langue;
     else erreur_fatale ("DEV: erreur config_langue pour ".$login);
    */
}


function config_nb_item($login, $connexion=false){
	if (! isset( $login) || empty($login)) return 50;
	if ($e=etablissement($login,0))
	   return $e->param_nb_items;
    else erreur_fatale ("DEV: erreur config_nb_item pour ".$login);

}


/**
function config_nb_aleatoire_14($login, $connexion=false){
	if (! isset( $login) || empty($login)) return 50;
	if ($e=etablissement($login,0))
	   return $e->param_nb_aleatoire;
	else erreur_fatale ("DEV: erreur config_nb_aleatoire pour ".$login);
}
**/
/** rev 944
la fonction config_nb_aleatoire doit recevoir
(comme les autres fonctions config_xxx ) l'�tablissement concern� et pas l'utilisateur.
En effet si un admin change un examen d'une de ses composantes (ou le super-admin un
examen d'un �tablissement) le nb de questions doit �tre celui de l'�tablissement cible
et pas celui de l'utilisateur qui fait la modif ...
*/
function config_nb_aleatoire($etab=false, $connexion=false){
    global $CFG;
    if ( empty($etab)) $etab=$CFG->universite_serveur;
    if ($e=get_etablissement($etab,0))
       return $e->param_nb_aleatoire;
    else erreur_fatale ("DEV: erreur config_nb_aleatoire pour ".$etab);
}

function config_nb_qac($etab=false, $connexion=false){
    global $CFG;
	if ( empty($etab)) $etab=$CFG->universite_serveur;
	if ($e=get_etablissement($etab,0))
	 //return $e->param_nb_qac;   ????
	 return $e->nb_quest_recup;
	 else erreur_fatale ("DEV: erreur config_nb_qac pour ".$etab);
}


function config_nb_experts($etab=false, $connexion=false){
     global $CFG;
	if (empty($etab)) $etab =$CFG->universite_serveur;
    if ($e=get_etablissement($etab,0))
	 return $e->param_nb_experts;
	else erreur_fatale ("DEV: erreur nb_experts ".$etab);
}


function evt_etablissement_modification ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_etablissement_ajout ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_etablissement_suppression ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}
