<?php

/**
 * @author Patrick Pollet
 * @version $Id: action.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	Action (ins�rer / mettre � jour l'item / attacher un document)
//	� la sortie, si un document est attach� on retourne � l'ajout (en mode modification)
//	sinon on affiche la fiche de l'item et la page appelante (liste d'items) est recharg�e
//  rev 1.41 utilise $CFG->chemin_ressources


////////////////////////////////


$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres

require_login('P'); //PP

$id=required_param("id",PARAM_INT);
$url_retour=optional_param("url_retour","",PARAM_CLEAN);
$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT); //important de le retrouver



// rev 836 gestion de annuler en cas de duplication
$annuler=optional_param("bouton_annuler","",PARAM_CLEAN);
if ($annuler) {
    if ($dupliquer=optional_param("dupliquer",0,PARAM_INT) || $CFG->utiliser_editeur_html) {
             supprime_question($id, $ide);
    }
    ferme_popup("liste.php?".$url_retour,true);
}



$supps=optional_param("supps",array(),PARAM_RAW);
$descs=optional_param("descs",array(),PARAM_RAW);
//les noms de fichiers sont dans $_FILES (file1, file2 ... plus facile qu'un tableau)

//donn�es recues du formulaire
$data=array('titre','referentielc2i','alinea');

//ini_set('error_reporting',E_ALL|E_STRICT);
$ligne=new StdClass();

foreach ($data as $champ)
    $ligne->$champ=required_param($champ,PARAM_CLEAN);


// rev 836  sur nationale seulement

// rev 836 sur la nationale on peut/doit changer directement la famille valid�e
if ($CFG->universite_serveur==1 && is_super_admin()) {
    if ($famille=optional_param("famille",0,PARAM_INT))
        $ligne->id_famille_validee=$famille;  //sinon touche pas !
}
else {
    $ligne->famille_proposee=optional_param("famille_proposee","",PARAM_RAW);
     if ($famille=optional_param("famille",0,PARAM_INT))
        $ligne->id_famille_proposee=$famille; //sinon touche pas !
}




//les reponses
// les réponses requises
$reponses=array();
for ($r=1; $r <=$CFG->nombre_reponses_mini; $r++) {
    $rep=new StdClass();
    //partie commune
    $rep->id_etab=$ide;
    $rep->reponse=required_param("reponse_".$r,PARAM_RAW);
    $rep->bonne=optional_param("bonne_".$r,'NON',PARAM_ALPHA);
    $rep->commentaires=optional_param("comm_".$r,'',PARAM_RAW);
    $reponses[]=$rep;

}
// les facultatives
for ($r=$CFG->nombre_reponses_mini+1; $r <=$CFG->nombre_reponses_maxi; $r++) {
	if ($tmp=optional_param("reponse_".$r,"",PARAM_RAW)) {
		$rep=new StdClass();
		$rep->id=$id;
		$rep->id_etab=$ide;
		$rep->reponse=$tmp;
		$rep->bonne=optional_param("bonne_".$r,'NON',PARAM_ALPHA);
		$rep->commentaires=optional_param("comm_".$r,'',PARAM_RAW);
        $reponses[]=$rep;
	}
}

$ligne->tags=optional_param("tags",'',PARAM_CLEAN);


if ( $id == "-1" ){ // ajout de l'item
    v_d_o_d("qa"); //PP
    $ligne->id_etab=$ide; // capital (recu de formulaire);
    $ligne->ts_datecreation=$ligne->ts_datemodification=time();

    $ligne->auteur=get_fullname($USER->id_user);
    $ligne->auteur_mail=get_mail($USER->id_user);
    $ligne->etat=QUESTION_NONEXAMINEE;
    $ligne->positionnement=$ligne->certification='NON';
    if ($USER->type_plateforme=="positionnement"){
         $ligne->positionnement='OUI';
    } else {
        $ligne->certification='OUI';
    }
	 $id=insert_record("questions",$ligne,true,"id");



    //tracking :
	espion3("ajout", "question", $ide.".".$id,$ligne); //rev 922
}
else { // modification de l'item
	if (isset($duplication))
         v_d_o_d("qd");
    //rev 843 un expert peut modifier une question sur la nationale ...
	else if ($CFG->universite_serveur == 1) {
             $nbvalid = nb_validations($id, $ide);
            if ($nbvalid > 0) {
                if (! is_super_admin()) {
                    erreur_fatale("err_modif_question_validee",$nbvalid);
                }
            } else if (! a_capacite("qv",1)
                       && ! a_capacite("qm"))  // sur CETTE question, pas sur 1 !!!
                            erreur_fatale("err_droits");

        } else v_d_o_d("qm");

	$ligne->id=$id; //important !
    $ligne->id_etab=$ide; // capital (recu de formulaire)
    $ligne->ts_datemodification=time();
    // eexmple d'usage d'update_record avec deux cl�s identifiant le bon record ...
     update_record("questions",$ligne, 'id','id_etab');
	//tracking :
	espion3("modification","question", $ide.".".$id,$ligne);
}

// supprimer les anciennes r�ponses (on ne peut pas modifier une question qui est d�j� utilis�e donc pas de probl�me de cl�)
delete_records("reponses","id=".$id." and id_etab=".$ide);

// insertion des réponses non vides
foreach ($reponses as $reponse) {
        $reponse->id=$id; //important !
    	insert_record ("reponses",$reponse,false,'num');
}

// rev 980 ce code n'est pas execut� si l'�diteur HTML est activ�. En effet dans ce cas
// les ressources images ont d�ja �t� copi�es en place et les lines cr��s.

/**
$dest=$CFG->chemin_ressources."/questions/".$ide."_".$id;
cree_dossier_si_absent($dest);
$dest=$dest."/documents";
cree_dossier_si_absent($dest);
**/
$dest=get_document_location($id,$ide,true);

// gestion des documents

//print_r($supps);
//print_r($descs);
//print"<br/>";



for ($i=1 ; $i<=$CFG->max_documents_par_question ; $i++){
    $doc=new StdClass();  //le reconstruire a chaque fois car update_record efface les cl�s ...
    $doc->id=$id;
    $doc->id_etab=$ide;
    $doc->id_doc=$i;

    $cle="id=".$id." and id_etab=".$ide." and id_doc=".$i; //cl� triple !!!

	$fich = isset($_FILES["file".$i]) ? $_FILES["file".$i]['name']:""; //nom du fichier
    // dans ajout.php on a forc� l'indice par  name="supps[{n}] ou name="descs[{n}]"
    $sup=isset($supps[$i]) ? $supps[$i]:"";
    $desc=isset($descs[$i]) ? $descs[$i]:"";

    if ($sup){ // document � supprimer
	    if ($doc=get_record("questionsdocuments",$cle,false)) { //si existe
		    delete_records("questionsdocuments",$cle);
		    @unlink($dest."/".$i.".".$doc->extension);
	    }
	    continue; //ignore le reste
    }

	if ($fich){ // document � ajouter
	   //$auto=explode(".",$fich);
	   //$extension=$auto[sizeof($auto)-1];
       $extension = pathinfo($fich,PATHINFO_EXTENSION);
       if (isgoodfile($fich)) {  //verif nom et extension
			    delete_records("questionsdocuments",$cle); //vire un �ventuel ancien
                $doc->description=$desc;  //si vide lib_question mettra Document xxx
                $doc->extension=$extension;
                insert_record("questionsdocuments",$doc,false,'rien');
                //ceci ecrasera l'eventuel ancien (meme nom 1.xxx)
            	//copy ($_FILES["file".$i]['tmp_name'],$dest."/".$i.".".$extension);
                // a tester  SECURIT�
                /*** ne gere pas le renommage qui est n�cessaire encore ..
                $fichier_garde=upload_file("file".$i,$dest."/".$i.".".$extension, $CFG->max_taille_fichiers_uploades,array());
                if (!$fichier_garde)
                     erreur_fatale("err_upload_fichier",$_FILES["file".$i]["tmp_name"]);
                **/
                if (is_uploaded_file($_FILES["file".$i]['tmp_name']))
                    copy ($_FILES["file".$i]['tmp_name'],$dest."/".$i.".".$extension);
                else
                    erreur_fatale("err_upload_fichier",$_FILES["file".$i]["tmp_name"]);


		} else erreur_fatale("err_type_de_fichier_interdit",$fich." ".typeMime($fich));
	}
    //la mise � jour de la description ne marche pas 3 criteres requis et update_record n'en a que 2 permis!
    //TODO ajouter un� cl� autonum a questionsdocuments et virer la cl� triple ...
    elseif ($desc) {
	    if ($doc=get_record("questionsdocuments",$cle,false)) { //si existe
		    $doc->description=$desc;
		    //update_record("questionsdocuments",$doc,'id','id_etab',false);
		    delete_records("questionsdocuments",$cle);
		    insert_record("questionsdocuments",$doc,false,'rien');
	    }
    }
}

// rev 987 pour etre en accord avec le texte d'aide , mais est-ce bien raisonnable ?
if (config_nb_experts()==0) {
    valide_question ($id,$ide);
}

/////////////////////////////////////////////
// transformation de la question en xml
/////////////////////////////////////////////
if ($CFG->generer_xml_qti) {
    require_once ($CFG->chemin_commun."/lib_xml.php");
    to_xml($id, $ide);
}
/////////////////////////////////////////////

// url de la fiche de l'item

$url_retour=optional_param("url_retour","",PARAM_CLEAN);

if ($CFG->montrer_fiche_apres_modification)
    redirect("fiche.php?idq=".$id."&ide=".$ide,"liste.php",$url_retour);
else
    ferme_popup("liste.php?".$url_retour,true);

?>
