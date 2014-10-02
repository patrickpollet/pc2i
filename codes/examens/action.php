<?php
/////////
/**
 * @author Patrick Pollet
 * @version $Id: action.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 *////////////////////////
//
//	Action (ins�rer / mettre � jour l'item / attacher un document)
//	� la sortie, si un document est attach� on retourne � l'ajout (en mode modification)
//	sinon on affiche la fiche de l'item et la page appelante (liste d'items) est recharg�e
//
////////////////////////////////
/*----------------REVISIONS----------------------
v 1.1 : PP 17/10/2006
     modification algo du calcul du nombre de questions al�atoires
        -�tape 1: nombre de question par ref= (nb questions � tirer / nb de referentiel) arrondi
	          � l'entier inf�rieur
	-�tape 2: tirage al�atoire du reste sans referentiel
------------------------------------------------*/
// CNB 2/06/2007 54-57, 62-65
//******** Pour chaque page $chemin repr�sente le path(chemin) de script dans le site (� la racine)
//******** ---------------- $chemin_commun repr�sente le path des utilitaires dont on aura besoin
//******** ---------------- $chemin_images repr�sente le path des images
$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres


require_login("P");

$id=required_param("id",PARAM_INT);

$url_retour=optional_param("url_retour","",PARAM_CLEAN);
$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT); //important de le retrouver


// rev 836 gestion de annuler en cas de duplication
$annuler=optional_param("bouton_annuler","",PARAM_CLEAN);
if ($annuler) {
    if ($dupliquer=optional_param("dupliquer",0,PARAM_INT)) {
             supprime_examen($id, $ide);
    }
    ferme_popup("liste.php?".$url_retour,true);
}


//donn�es recues du formulaire
$data=array('nom_examen','type_tirage','mot_de_passe','ordre_q','ordre_r','resultat_mini','date_fin','date_debut');

//ini_set('error_reporting',E_ALL|E_STRICT);
$ligne=new StdClass();

foreach ($data as $champ)
	$ligne->$champ=required_param($champ,PARAM_CLEAN);



//cas different des cases � cocher
$ligne->anonyme=optional_param("anonyme",0,PARAM_INT);
$ligne->correction=optional_param("correction",0,PARAM_INT);
$ligne->envoi_resultat=optional_param("envoi_resultat",0,PARAM_INT); // rev 835
$ligne->affiche_chrono=optional_param("affiche_chrono",0,PARAM_INT); // rev 945

$ligne->est_pool=optional_param("est_pool",0,PARAM_INT);
if ($ligne->est_pool) {
	$ligne->nb_q_pool=required_param("nb_q_pool",PARAM_INT);
	$ligne->pool_nb_groupes=required_param("pool_nb_groupes",PARAM_INT);
}
//rev 944 retour des qcm par competences rev 985 meme en certification
if (($USER->type_plateforme == "positionnement" && $CFG->autoriser_qcm_par_domaine_en_positionnement)
  || ($USER->type_plateforme == "certification" && $CFG->autoriser_qcm_par_domaine_en_certification)) {
	$refs_traites=optional_param("referentielc2i",array(-1),PARAM_RAW);
	//print_r($refs_traites);
	$ligne->referentielc2i=implode(',',$refs_traites);
    $ligne->nbquestions=optional_param("nbquestions",config_nb_aleatoire($ide),PARAM_INT);
    //calcul du nombre de ref. retenus
    $refs=get_referentiels_liste($ligne->referentielc2i);
    //arrondi au multiple inf�rieur
    $tmpvalmodulo = $ligne->nbquestions % count($refs);
    if ($tmpvalmodulo != 0) $ligne->nbquestions= $ligne->nbquestions - $tmpvalmodulo;
}


// rev 978
$ligne->ts_dureelimitepassage=optional_param("ts_dureelimitepassage",0,PARAM_INT);
if ($ligne->ts_dureelimitepassage) $ligne->affiche_chrono=1;

$ligne->tags=optional_param("tags",'',PARAM_CLEAN);

// rev 986
if ($CFG->restrictions_ip) {
    $ips=optional_param("subnet",array(-1),PARAM_RAW);
//print_r($refs_traites);
    $ligne->subnet=implode(',',$ips);
}

 /**
 * recupere la date renvoy�e par jscalendar
 * et la convertit
 * a adapter si on change le format d'affichage du calendrier dans ajout.php !
 */

function jscalendar_to_mysql ($ligne) {

	//$tmp=explode(" ",$ligne->date_debut); //enleve le nom du jour devant


	//la fonction strtotime semble tr�s capricieuse renvoie de temps en temps une valeur vide ????
	//$ligne->ts_datedebut=strtotime($tmp[1]. " " .$tmp[2]);//timestamp
	$ligne->ts_datedebut=mon_strtotime($ligne->date_debut);

    unset($ligne->date_debut);   //ne pas l'envoyer a insert_record !!!'

	//$tmp=explode(" ",$ligne->date_fin);

	//$ligne->ts_datefin=strtotime($tmp[1]. " " .$tmp[2]);
	$ligne->ts_datefin=mon_strtotime($ligne->date_fin);

    unset($ligne->date_fin);  //ne pas l'envoyer a insert_record !!!'
	return $ligne;
}


//print_r($ligne);
$ligne=jscalendar_to_mysql($ligne);
//print_r($ligne);



if ($id == "-1") { // ajout de l'item
    v_d_o_d("ea");
    //oublie pas le nom et le mail si nouveau ! attention a auteur_mail !!!!
    $ligne->auteur=get_fullname($USER->id_user,false);
    $ligne->auteur_mail=get_mail($USER->id_user,false);
    $ligne->id_etab=$ide;
    $ligne->ts_datecreation=$ligne->ts_datemodification=time();
    $ligne->positionnement=$ligne->certification='NON';
    if ($USER->type_plateforme=="positionnement"){
    	 $ligne->positionnement='OUI';
    } else {
    	$ligne->certification='OUI';
    }
     $ligne->template_resultat='';     // rev 957  notice php et pb mysql UPMC

    $id=insert_record("examens",$ligne,true,'id');
    espion3("ajout","examen",$ide . "." . $id,$ligne);
    $pool_pere =0;

} else { // modification de l'item
    if (isset ($duplication))
        v_d_o_d("ed");
    else
        v_d_o_d("em"); //PP

        $oldexamen=get_examen($id,$ide); //rev 944 anciennes valeurs
        $oldrefs=get_referentiels_liste($oldexamen->referentielc2i);
        //tableau des anciens referentiels trait�s
        $oldrefsrefs=array();
        foreach($oldrefs as $key=>$value)
            $oldrefsrefs[$value->referentielc2i]=1;

        $ligne->id_etab=$ide; // capital (recu de formulaire)
        $ligne->id_examen=$id; // ne pas oublier !!!!!
        $ligne->ts_datemodification=time();
       // exemple d'usage d'update_record avec deux cl�s identifiant le bon record ...
		update_record("examens",$ligne, 'id_etab','id_examen');

		// rev 944 si a chang� entre aleatoire et passage il faut virer les eventuelles questions
		// pb si certains �tudiants l'ont d�ja pass� !
		if ($ligne->type_tirage == EXAMEN_TIRAGE_PASSAGE) {
			// suppression des attachements de questions
			delete_records('questionsexamen','id_examen='.$id." and id_examen_etab=".$ide);

			// suppression des inscriptions a l'examen  non !
			//delete_records('qcm','id_examen='.$id." and id_etab=".$ide);

			$critere=$id.".".$ide;  // critere unique examen dans les tables suivantes
			// suppression des �VENTuelles r�ponses des �tudiants  a cet examen" .
			delete_records("resultats","examen=".$critere);

			//suppression des r�sultats (scores, par competence et detailles)
			require_once($CFG->chemin_commun."/lib_resultats.php");
			purge_resultats_examen($id,$ide);

		} else { //liste des domaines a elle chang� ?
		    if (($USER->type_plateforme == "positionnement" && $CFG->autoriser_qcm_par_domaine_en_positionnement)
		    || ($USER->type_plateforme == "certification" && $CFG->autoriser_qcm_par_domaine_en_certification)) {
				//nouveaux referentiels trait�s
				$newrefs=get_referentiels_liste($ligne->referentielc2i);
				//tableau des nouveaux referentiels trait�s
				$newrefsrefs=array();
				foreach($newrefs as $key=>$value)
				$newrefsrefs[$value->referentielc2i]=1;
               // print_r($oldrefsrefs);
               // print_r($newrefsrefs);

                foreach($oldrefsrefs as $key=>$value)
                    if (! array_key_exists($key,$newrefsrefs)) {
                         supprime_questions_referentiel_examen ($key,$id,$ide);
                        // print ("suppression questions de ".$key.'<br/>');
                    }

			}
		}



    //tracking :

    $lignetest=get_examen($id,$ide); //relecture
    espion3("modification","examen", $ide . "." . $id,$lignetest);
    $pool_pere = $lignetest->pool_pere;
}
//devient l'unique anonyme de la PF '
if ($ligne->anonyme) {
	set_examen_anonyme ($id,$ide) ;

}

////////////////////////////////////////////////////////////////////
// en cas de tirage al�atoire, s�lection automatique des questions
////////////////////////////////////////////////////////////////////

if (($ligne->type_tirage == EXAMEN_TIRAGE_ALEATOIRE)  && ($pool_pere == 0)) {
    tirage_questions ($id,$ide);
    //die();
}

if ($CFG->montrer_fiche_apres_modification)
    redirect("fiche.php?idq=".$id."&ide=".$ide,"liste.php",$url_retour);
else
    ferme_popup("liste.php?".$url_retour,true);

?>