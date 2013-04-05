<?php
/**
 * @author Patrick Pollet
 * @version $Id: lib_questions.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/*
 * bibliotheque de manipulations de l'entit� question et ses r�ponses
 */

define ('QUESTION_CERTIFICATION'  ,1);
define ('QUESTION_POSITIONNEMENT'  ,2);

define ('QUESTION_NONEXAMINEE'  ,0);
define ('QUESTION_VALIDEE'  ,1);
define ('QUESTION_REFUSEE'  ,-1);
define ('QUESTION_TOUTE'  ,255);





 if (is_admin()) {   //utilisateur courant uniquement
    maj_bd_questions();
 }

 function maj_bd_questions () {
	 global $CFG,$USER;


 }


/**
 * fonction utilis�e en synchronisation locale-nationale
 *
 */

function get_toutes_questions ($validee=true,$tri=false) {
      global $USER,$CFG;

    // rev 851 la nationale envoie ses valid�es pour le bon type
    //MAIS  comme certaines questions sont maintenant pour les 2 PF
    // une locale doit renvoyer TOUTES ses questions pour �viter
    // des erreurs de cl�s dupliqu�es
    if ($CFG->universite_serveur==1)
      $criteres=$USER->type_plateforme."='OUI'";
    else
      $criteres="1";

      if ($validee) $criteres.=' and etat='.QUESTION_VALIDEE;
      if (!$tri) $tri="id_etab,id";
      $res = get_records("questions",$criteres,$tri);
      foreach ($res as $num=>$q) {
        $res[$num]->qid=$q->id_etab.".".$q->id;
      }
      return $res;
}

/**
 * renvoie la liste des questions locales susceptibles d'�tre envoy�es � la nationale
 * apparu rev svn 919 '
 */
function get_questions_locales($validee=false,$tri=false) {
	global $CFG,$USER;

	$criteres="id_etab=".$USER->id_etab_perso." and ts_dateenvoi=0";

	if ($validee) $criteres.=' and etat='.QUESTION_VALIDEE;

	 if (!$tri) $tri="id_etab,id";
      $res = get_records("questions",$criteres,$tri);
      foreach ($res as $num=>$q) {
        $res[$num]->qid=$q->id_etab.".".$q->id;
      }
      return $res;
}


/**
 * @param int $id_question num�ro de la question
 * @param int $id_etab num�ro de l'�tablissement
 * @param string $die   si vrai declenche une erreur fatale en cas d'erreur sinon return null
 * @return objet        la ligne extraite de la BD ou null en cas d'erreur (et $die=0)
 * rev 979 ajout s�curisatio des param�tres comme cette fonction est un point de passage oblig�

 */
function get_question ($id_question,$id_etab,$die=1) {

	return get_record("questions","id=".(int)$id_question." and id_etab=".(int)$id_etab,$die,"err_question_inconnu",$id_etab.".".$id_question);
}


/**
 * renvoie une question identifi�e par son identifiant national
 */
function get_question_byidnat($id_nat,$die=1) {
	$tmp=explode(".", $id_nat);
    if (sizeof($tmp) == 2)
       return get_question($tmp[1],$tmp[0],$die);
    $tmp=explode("_", $id_nat); //rev 1012 considerer aussi ca (web service)
    if (sizeof($tmp) == 2)
       return get_question($tmp[1],$tmp[0],$die);
    else if ($die) return get_question(-1,-1,true);
    else return false;
}


/**
 * @param int id_exam ide de l'examen n� auto en BD locale'
 * @param int id_etab id de l'�tablissement cr�ateur (N� auto sur la base nationale)'
 * @param boolean melange renvoie les questions m�lang�es al�atoirement ou pas
 * @param string tri  cri�re de tri (incompatible avec m�lang�)
 * @param int die drapeau usuel d'erreur fatale ou non
 * @param strin errMsg1 description
 * @param string errMsg2 description
 * en principe ca n'est pas un drame si un examen n'a pas encore de question
 * sauf si on demande ses notes ou ses r�sultats
 */

function get_questions($id_examen,$id_etab,$melange=true,$tri=false,$die=0,$errMsg1="",$errMsg2="") {
	global $CFG;

   if (!$tri) $tri='id_etab,id';  // important pour la g�n�ration vers les lecteurs optiques. Un ordre DOIT etre impos�

	$sql =<<<EOS
SELECT {$CFG->prefix}questions.*
FROM {$CFG->prefix}questionsexamen,{$CFG->prefix}questions
WHERE {$CFG->prefix}questionsexamen.id={$CFG->prefix}questions.id
and {$CFG->prefix}questionsexamen.id_etab={$CFG->prefix}questions.id_etab
and id_examen=$id_examen and id_examen_etab=$id_etab
EOS;
	if ($melange) $sql .=" order by RAND()";
	else $sql .=" order by $tri";

	return get_records_sql($sql,$die,$errMsg1,$errMsg2);
}

/**
*  renvoie les r�ponses de la question $id_question, propos� par l'�tablissement $id_etab
*  remelang�es ou non selon $melange
*  @param id_examen
*  @param id_etab
*  @param melange   boolean
* @param int die  declenche ou non une erreur fatale : d�faut oui (question sans r�ponses)

*/
function get_reponses ($id_question,$id_etab,$melange=0,$die=1) {

/*	$sort=$melange ? 'RAND()':'num';
BUG id_etab DOIT etre utilis� car l'autoincrement est PAR BD locale
	$res= get_records('reponses','id',$id_question,$sort);
*/
	global $CFG;
	$sort=$melange ? 'RAND()':'num';
	$sql=<<<EOS
SELECT * FROM {$CFG->prefix}reponses
WHERE id=$id_question and id_etab=$id_etab
order by $sort
EOS;
	return get_records_sql($sql,$die,"err_question_sans_reponse",$id_etab.".".$id_question);
}

function get_reponse_vide(){
	$ret= new StdClass();
	$ret->reponse="";
	$ret->bonne='NON';
	return $ret;
}

function get_document_vide(){
	$ret= new StdClass();
	$ret->extension=$ret->description="";
	$ret->url="";
	$ret->id_doc=0;
	return $ret;
}

/**
 * renvoie les document associes a une question
 * ajoute au tableau d'objet un URL calcul�e selon le type de document
 * et etant compte du chemin des ressources (via send_document.php)
 */

function get_documents ($id_question,$id_etab,$die=0 ) {
	global $CFG;
	$res=get_records("questionsdocuments","id=" . $id_question . " and id_etab=".$id_etab ,"id_doc",$die);
	$i=0;
	foreach ($res as $doc) {
       // print_r($doc);
		$i++;
		//url de base du document
		$url=$CFG->chemin_commun."/send_document.php?ide=" . $id_etab . "&amp;idq=" . $id_question . "&amp;idf=" . $doc->id_doc . "." . $doc->extension;
		$nom_doc=$doc->description? $doc->description : traduction("document")." ".$i;
		switch ($doc->extension) { // image ou autre
			case "jpg" :;
			case "jpeg" :;
			case "gif" :;
			case "png" :;
				$doc->url= "<img src='".$url."&amp;type=image' class='document' alt='".$nom_doc."' title='".$nom_doc."'/>";
				break;
	        case 'htm':;
	        case "html":;
	            $onclick=" onclick=\"openPopup('".$url."&amp;type=doc','','".$CFG->largeur_popups."','".$CFG->hauteur_popups."');\"";
	            $doc->url= "<a href ='javascript:void(0);' class='document'".$onclick." alt=\"".$nom_doc."\">".$nom_doc."</a>";
	            break;
			default :
				$doc->url= "<a href ='".$url."&amp;type=doc' class='document' alt=\"".$nom_doc."\">".$nom_doc."</a>";
				break;
		}
	}
	return $res;

}

/**
 * rev 977 affiche le libellé du domaine selon la version du referentiel
 */
function get_domaine_traite ($question) {
    global $CFG;
    $V1=$question->referentielc2i.'.'.$question->alinea;
    return $V1;
}

function get_question_referentiel ($question) {
    global $CFG;
     return get_referentiel($question->referentielc2i);
}

function get_question_alinea ($question) {
    global $CFG;
    return get_alinea($question->alinea,$question->referentielc2i);
}

// rev 977
/**
 * deprecie
 * 
 * Enter description here ...
 * @param unknown_type $question
 * @return StdClass
 */
function get_item_old_domaine($question) {
    $ret=new StdClass();
    $ret->referentielc2i=$ret->alinea=$ret->domaine=$ret->aptitude=$ret->competence='';
    if ($ref=get_record("referentiel","referentielc2i='".$question->referentielc2i."'",false))
        if ($al=get_record("alinea","alinea=".$question->alinea,false)) {
            $ret->referentielc2i=$ref->referentielc2i;
            $ret->alinea=$al->alinea;
            $ret->domaine=$ref->domaine;
            $ret->aptitude=$al->aptitude;
            $ret->competence=$ref->referentielc2i.'.'.$al->alinea;
        }
     return $ret;
}



/**
 * renvoie les examens utilisant cette question
 * pb possible avec les questions qui sont des deux types !
 */

function get_examens_question ($idq,$ide,$tri='id_examen_etab,id_examen') {
    global $CFG;
    //rev 977 seulement pour les examens de ce referentiel
    //return get_records("questionsexamen","id=" . $idq . " and id_etab=" .$ide,$tri,false);
    $sql=<<<EOS
        select QE.*
        from {$CFG->prefix}questionsexamen QE,{$CFG->prefix}examens E
        where QE.id=$idq and QE.id_etab=$ide
        and QE.id_examen=E.id_examen and QE.id_examen_etab=E.id_etab
EOS;
    return get_records_sql($sql,false);

}


function get_questions_auteur($email) {
    global $CFG;
      if (empty($email)) return array(); // pas moyen de le trouver
      //rev 924 notez le distinct pour les auteurs ayant plusieurs comptes
      // avec le meme mail, par exemple sur la nationale !
    $sql=<<<EOS
    select distinct Q.id,Q.id_etab
    from {$CFG->prefix}questions Q inner join {$CFG->prefix}utilisateurs P
    on Q.auteur_mail=P.email
      where P.email='$email'
    order by Q.id_etab,Q.id

EOS;
    $ret=get_records_sql($sql,false);
    //print_r($ret);
   return $ret;
}

/**
 * renvoie vrai si une question est utilis�e dans un examen quelconque
 * donc non supprimable
 * @param $idq $ide ids de la question
 * @param $idex, $idexe ids de l'examen, si vide n'importe lequel
 * @return boolean
 */
function est_utilise_examen($idq,$ide,$idex=false,$idexe=false)  {

	if (! $idex || ! $idexe) {
	//	$tmp=get_examens_question($idq,$ide);
	//	if ($tmp) return count($tmp);
	//	else return false;
    //rev 977 get_examens_question ne renvoie que les examens du referentiel courant (V1 ou V2)
     return count_records("questionsexamen","id=" . $idq . " and id_etab=" .$ide,false);

	} else {  // dans un examen sp�cifique
        return count_records("questionsexamen",
           "id=$idq and id_etab=$ide and id_examen=$idex and id_examen_etab=$idexe",false);

	}
}




/**
 * ne DOIT PAS etre utilis�e dans un examen
 */
function supprime_question($idq,$ide) {
    global $USER,$CFG;
    v_d_o_d("qs");
        $ligne=get_question ($idq,$ide);
        // suppression des r�ponses
        delete_records("reponses","id=".$idq. " and id_etab=".$ide);
        // suppression des validations
        delete_records("questionsvalidation","id=" . $idq . " and id_etab=" .$ide );
        //suppression des documents   rev 1075
        delete_records("questionsdocuments","id=" . $idq . " and id_etab=" .$ide );
        // suppression de la question
       delete_records("questions"," id=" . $idq . " and id_etab=" . $ide );
        // suppression du dossier contenant les �l�ments de la question
        $dossier_a_supp = $CFG->chemin_ressources."/questions/" . $ide . "_" . $idq;
        supprimer_dossier($dossier_a_supp);
        //tracking :
        espion3("suppression", "question",$ide . "." . $idq, $ligne);
}

/**
 * version 1.5
 */
function copie_question ($copie_id,$copie_ide) {
	global $USER,$CFG;
	v_d_o_d("qd");
	$ide=$USER->id_etab_perso;

	// s�lection des valeurs de la question � dupliquer
	$ligne=get_question($copie_id,$copie_ide );
	$ligne->titre = traduction ("copie_de")." ".$ligne->titre;

	$ligne->id_etab=$ide; //on se l'approprie
	$ligne->auteur=get_fullname($USER->id_user);
	$ligne->auteur_mail=get_mail($USER->id_user);

	$ligne->ts_datecreation=$ligne->ts_datemodification=time();
    $ligne->etat=QUESTION_NONEXAMINEE;
      //rev 978 RAZ date de remont�e des stats � la nationale
    $ligne->ts_dateenvoi=0;
    $ligne->ts_dateutilisation=0;

	// on indique a insert_record de renvoyer le nouvel id ET de virer la cle id avant (autonum)...
	$idq=insert_record("questions",$ligne,true,'id',true); //,"err_duplication",$copie_ide.".".$copie_id);


	// r�ponses existantes :
	$reponses=get_reponses($copie_id,$copie_ide,false);
	foreach($reponses as $ligne_r) {
		$ligne_r->id=$idq;
		$ligne_r->id_etab=$ide;
		insert_record("reponses",$ligne_r,true,'num',true);// ,"err_duplication",$copie_ide.".".$copie_id);
	}

	// normalemnt ne doit pas exister ?
	if (!is_dir($CFG->chemin_ressources."/questions/" . $ide . "_" . $idq)) {
		mkdir($CFG->chemin_ressources."/questions/" . $ide . "_" . $idq);
	}
	if (!is_dir($CFG->chemin_ressources."/questions/" . $ide . "_" . $idq . "/documents")) {
		mkdir($CFG->chemin_ressources."/questions/" . $ide . "_" . $idq . "/documents");
	}
	// documents existants:
	$docs=get_documents($copie_id, $copie_ide);
	foreach($docs as $rowd) {
		copy(   $CFG->chemin_ressources."/questions/" . $copie_ide . "_" . $copie_id . "/documents/" . $rowd->id_doc . "." . $rowd->extension,
			$CFG->chemin_ressources."/questions/" . $ide . "_" . $idq . "/documents/" . $rowd->id_doc . "." . $rowd->extension);
		$rowd->id=$idq;
		$rowd->id_etab=$ide;
		unset($rowd->url); //attention champ ajout� par get_documents pas en BD !
		//ici pas d'autonum encore ...
		insert_record("questionsdocuments",$rowd,false,"",true);//,"err_duplication",$copie_ide.".".$copie_id);
	}
	/////////////////////////////////////////////
	// transformation de la question en xml
	/////////////////////////////////////////////
	if ($CFG->generer_xml_qti) {
		require_once ($CFG->chemin_commun."/lib_xml.php");
		to_xml($idq, $ide);
	}
    //rev 978 n'�tait pas fait
    espion2("duplication","question",$copie_ide.".".$copie_id."->".$ide.".".$idq);
	return $idq;
}



/**
 * renvoie le liste des avis de validation de la question (oui ou non)
 */
function get_question_avis_validations ($idq,$ide,$tri=false,$die=0) {
	if (!$tri)  $tri="ts_date desc";   // v 1.5 timestamp
	return get_records("questionsvalidation","id=".$idq." and id_etab=".$ide,$tri,$die);
}

/**
 * renvoie MON avis sur la question  (il ne peut y en avoir qu'un que l'on modifie au fur et a mesure)
 */

function get_question_mon_avis_validation ($idq,$ide,$login=false) {
	global $USER;
	if (!$login) $login=$USER->id_user;
	return get_record("questionsvalidation","id=".$idq." and id_etab=".$ide." and login='".addslashes($login)."'",false);
}

/**
 * retourne le nombre de personnes ayant VALIDES la  question ide.idq.
 */
function nb_validations($idq,$ide){
	return count_records("questionsvalidation","id=".$idq." and id_etab=".$ide." and validation='OUI'",false,false);
}

/**
 * retourne le nombre d'avis sur la question positifs ou non
 */
function nb_avis($idq,$ide){
	return count_records("questionsvalidation","id=".$idq." and id_etab=".$ide,false,false);
}


/**
 * dans l'attente d'un changement de structure de la BD (valid�e sans acent) ou drapeau)
 * essayer de passer par ici le plus possible
 */
function est_validee ($question) {
    return $question->etat==QUESTION_VALIDEE;
}

function est_refusee ($question) {
    return $question->etat==QUESTION_REFUSEE;
}

function valide_question ($idq,$ide,$die=1) {
	global $CFG;
	$ligne=new StdClass();
	$ligne->id=$idq;
	$ligne->id_etab=$ide;
	$ligne->etat=QUESTION_VALIDEE;

	$ligne->ts_datemodification=time();

	//rev 940 toute question de cert. valid�e st dispo en positionnement sur la Nationale
	if ($CFG->universite_serveur==1) {
		$oldq=get_question($idq,$ide);
		if ($oldq->certification=='OUI')
		$ligne->positionnement='OUI';
	}
	update_record("questions",$ligne,"id","id_etab",$die,"err_validation_question",$ide.".".$idq);
	espion3("validation","question",$ide.".".$idq,$ligne);
}

/**
 * �tait dans un script codes/questions/invalider.php inclus jusqu'en V1.5'
 */
function invalide_question ($idq,$ide,$die=1) {
	global $CFG;
	// suppression des validations en v 1.4 ca c'�tait violent !!!'

    // rev 843 cette suppression n'est plus faite !
    // rev 940 il faut le faire ...
    delete_records("questionsvalidation", "id=".$idq." and id_etab=".$ide);

	 //set_field("questions","etat","refus�e","id=$idq and id_etab=$ide");
	 //set_field("questions","validation","NON","id=$idq and id_etab=$ide");

	$ligne=new StdClass();
	$ligne->id=$idq;
	$ligne->id_etab=$ide;

	$ligne->etat=QUESTION_REFUSEE;
	$ligne->ts_datemodification=time();

	/**
	//rev 940 toute question de cert. invalid�e n'est plus dispo en positionnement sur la Nationale
    // annul� rev 978, en effet si on invalide une question C+P d�ja valid�e les locales
    // l'utilisant en positionnement ne seront pas notifi�es lors d'une synchro...
    // cas de la question 1.998
    if ($CFG->universite_serveur==1) {
		$oldq=get_question($idq,$ide);
		if ($oldq->certification=='OUI')
		$ligne->positionnement='NON';
	}
    **/
	update_record("questions",$ligne,"id","id_etab",$die); //,"err_validation_question",$ide.".".$idq);
	espion3("invalidation","question",$ide.".".$idq,$ligne);
}


function filtre_question ($idq,$ide) {
    global $CFG;
    v_d_o_d("qs");
    $sql=<<<EOS
        update {$CFG->prefix}questions
        set est_filtree=not est_filtree
            where id=$idq and id_etab=$ide
EOS;
        $res = ExecRequete ($sql);
        //tracking
        $tmp=get_question($idq,$ide);
        if ($tmp->est_filtree)
        	espion3("filtrage","question",$ide.".".$idq,$tmp);
        else 	
			espion3("defiltrage","question",$ide.".".$idq,$tmp);
}



/**
 * renvoie sous une forme pr�te � �tre affich�e les avis d'experts sur la question'
 * appel�e par qustions/commentaires.php et questions/valider.php
 */

function print_commentaires ($idq,$ide) {

	global $CFG;

	$fiche=<<<EOF
<table class="fiche" width="90%">
<thead>
<tr>
<th class="bg" colspan="2">{form_avis_experts} </th>
</tr>
</thead>
 <tbody>

<!-- START BLOCK : expert -->
          <tr>
            <th>{form_expert}</th>
            <td  >{validateur}</td>
          </tr>

          <tr>
            <th>{form_remarques}</th>
            <td  >{remarques}</td>
          </tr>
          <tr>
            <th>{form_modifications}</th>
            <td  >{modifications}</td>
          </tr>
          <tr>
            <th>{form_avis}</th>
            <td  >{avis}</td>
          </tr>
          <tr>
            <th>{form_date_avis}</th>
            <td  >{date}</td>
          </tr>
<!-- END BLOCK : expert -->

<!-- START BLOCK : no_results -->
<tr class="information">
<td colspan="2">
		{msg_pas_davis}
</td>
</tr>
<!-- END BLOCK : no_results -->

</tbody>
</table>
EOF;

$tpl= new SubTemplatePower($fiche,T_BYVAR);    //cr�er une instance
$tpl->prepare($CFG->chemin);

$res=get_question_avis_validations($idq,$ide,"ts_date" ,false);
if ($res)
	foreach ($res as $ligne) {
	$tpl->newBlock("expert");
	$tpl->assignObjet($ligne);
	if($compte=get_compte($ligne->login,false))
		$tpl->assign("validateur", cree_lien_mailto($compte->email,_regle_nom_prenom ($compte->nom,$compte->prenom)));
	else $tpl->assign ("validateur",traduction ("validateur_inconnu"));
	$tpl->setConditionalValue($ligne->validation == 'OUI',"avis",traduction("alt_valide"),traduction("alt_non_valide"));
	$tpl->assign("date", userdate($ligne->ts_date));
}

else
	$tpl->newblock("no_results");
	return $tpl->getOutputContent();
}


function get_famille($id,$die=1) {
    return get_record('familles', 'idf='.$id,$die,"err_famille_inconnu" ,$id);
}


/**
 * retrouve une famille par son nom
 * si le referentiel et l'ainea sont sp�cifi�s, ajoute les aux crit�res
 * important pour v�rifier une famille propos�e par un auteur pour une question ...
 */
function get_famille_par_nom ($nom,$ref=false,$alinea=false,$die=false) {

	if ($ref && $alinea) $where =" and referentielc2i='".$ref."' and alinea=".$alinea;

	return get_record("familles","famille='".$nom."'".$where,$die);

}


/**
 * retourne comme une chaine pr�te a etrre affich�e
 * le domaine d'une famille (selon version du referentiel)
 */
function get_referentiel_famille($famille) {
    global $CFG;
    $ret=$famille->referentielc2i.'.'.$famille->alinea;
    return $ret;
}


/**
 * renvoie une famille vide (pr�te a remplir un formaulaire)
 */
function get_famille_vide() {
    global $CFG;
    $ligne=new StdClass();
    $ligne->idf=$ligne->ordref=0;
    $ligne->famille="";
    $ligne->referentielc2i=$ligne->alinea="";
    $ligne->mots_clesf=$ligne->mots_cles_manquants="";
    $ligne->commentaires="";
    return $ligne;
}

/**
 * rev 1041 appel�e sur la nationale en validation et en suivi des familles
 * @param  $ligne : les infos recues du formaulaire
 */
function ajoute_famille ($ligne,$die) {
    global $USER,$CFG;

    // gestion de l'ordre des familles
   $maxof=0;
   if ($res=get_record_sql("select max(ordref) as mordre from {$CFG->prefix}familles where referentielc2i='{$ligne->referentielc2i}' and alinea='{$ligne->alinea}'",false))
           $maxof=$res->mordre;

    $ligne->ordref=$maxof+1;
    $ligne->ts_datecreation=$ligne->ts_dateutilisation=time();

    if ($cpt=get_compte($USER->id_user)) {
        $ligne->auteur=_regle_nom_prenom ($cpt->nom,$cpt->prenom);
        $ligne->auteur_mail=$cpt->email;
    }

    if ($idf=insert_record("familles",$ligne,true,'idf',$die))
        espion3("ajout","famille",$idf,$ligne);
    else espion2("err_ajout_famille","famille",$ligne->famille);
    return $idf;
}


/**
 * rev 841 informations suppl pour une famille
 * appel�e par ajax en mise � jour lors du changement de famille
 * et par la fiche d'une question ...
 */
function get_infos_famille ($fam) {
    if (empty($fam->commentaires)) $fam->commentaires=traduction("msg_pas_de_commentaires_famille"). " ".$fam->idf;
    if (empty($fam->mots_clesf)) $fam->mots_clesf=traduction("msg_pas_de_mots_cle_famille"). " ".$fam->idf;
  //return "(".$fam->mots_clesf.")<br/>".$fam->commentaires ;
  return $fam->commentaires."<br/>(".$fam->mots_clesf.")" ; //rev 842 commentaires avant mot-cl�s
}


//rev 1040 si die=0, situation bloquante pour les nouvelles plate-formes
function get_familles($tri = 'idf',$die=0) {
    global $CFG;
     $criteres='';
    return get_records('familles', $criteres, $tri,0,0,$die,"err_pas_de_familles","???");
}


/**
 * renvoie la liste des familles associ�es � un couple refrentiel/alinea
 */

function get_familles_associees ($ref,$alinea,$tri="ordref,famille",$die=0) {
    global $CFG;
    return get_records("familles","referentielc2i='" . $ref. "' and alinea=" . $alinea, $tri,0,0,$die);
    
}


function get_familles_bytags($tags,$sort='',$page=0, $recordsperpage=-1, &$totalcount=null) {
    return search_table_bytags('familles',$tags, $sort, $page, $recordsperpage,$totalcount);
}


/**
 * rev 984 renvoie la liste des familles/th�mes trait�es par un examen
 * @param string $liste  liste des domaines trait�s (s�parateur virgule)
 * @param string $tri  ordre de tri (peut �tre rand()
 * @param int  $die
 * @return array() tableau des th�mes
 */
function get_familles_liste ($liste='',$tri='',$die=0) {
	global $CFG;
	// cas de c2iexamen.referentielc2i vide ou valeur par d�faut (-1)
	if (empty($liste) || $liste==-1) return get_familles($tri,$die);  //toutes
	$refs=explode(',',$liste);
	//reconstitue comme une liste SQL ex 'A1'  ou 'A1','A2'
	$refs_string=  "'". implode("', '", $refs) ."'";
    return get_records('familles','referentielc2i in ('. $refs_string.')', $tri,0,0,$die,"err_pas_de_familles","????");
}


/**
 * met a jour la famille valid�e d'une question'
 */
function mise_a_jour_famille_validee ($idq,$ide,$idf,$die=1) {
	global $CFG;

	if (!$famille=get_famille($idf,false)) return false; // famille perdue ????
	if (!$question=get_question($idq,$ide,false)) return false; // question perdue ????
	$ligne=new StdClass();
	$ligne->id=$idq;
	$ligne->id_etab=$ide;
	$ligne->id_famille_validee=$idf;

	
	if (empty($question->referentielc2i)) {
		$ligne->referentielc2i=$famille->referentielc2i;
		$ligne->alinea=$famille->alinea;
	}
	
	if (update_record("questions",$ligne,"id","id_etab",$die,"err_maj_famille_question",$ide.".".$idq."->".$idf)) {
		espion2("maj_famille","question",$ide.".".$idq."->".$idf);
		return true;
	} else return false;
}



function get_referentiel($id_referentielc2i,$die=1) {
    global $CFG;
    return get_record('referentiel', "referentielc2i='$id_referentielc2i'" ,$die,"err_referentiel_inconnu" ,$id_referentielc2i);
}

function get_referentiels($tri = 'referentielc2i',$die=1) {
    global $CFG;
    return get_records('referentiel', '', $tri,0,0,$die,"err_pas_de_referentiels","????");
}


/**
 * rev 944 retrouve les refentiels de la liste $liste
 * utilis� pour des qcm par comp�tence(s)
 * ex get_referentiels_liste('A1,A2','referentielc2i',1)
 */
function get_referentiels_liste ($liste='',$tri='',$die=1) {
    global $CFG;
    // cas de c2iexamen.referentielc2i vide ou valeur par d�faut (-1)
    if (empty($liste) || $liste==-1) return get_referentiels($tri,$die);
    $refs=explode(',',$liste);
    //reconstitue comme une liste SQL ex 'A1'  ou 'A1','A2'
    $refs_string=  "'". implode("', '", $refs) ."'";
    return get_records('referentiel','referentielc2i in ('. $refs_string.')', $tri,0,0,$die,"err_pas_de_referentiels","????");
}


function get_alinea($id_alinea,$id_ref,$die=1) {
    global $CFG;
    return get_record('alinea', "referentielc2i='$id_ref' and alinea='$id_alinea'" ,$die,"err_alinea_inconnu" ,$id_ref."_".$id_alinea);
}

/**
 * rev 1041 facilite le suivi des alineas sur la nationale
 */
function get_alinea_byid($id,$die=1) {
    global $CFG;
    return get_record('alinea', "id='$id'" ,$die,"err_alinea_inconnu" ,$id);
}


/**
 * rev facilite le suivi des alineas sur la nationale
 */
function get_alinea_byidnat($id_nat,$die=1) {
    global $CFG;
    $tmp=explode(".", $id_nat);
    if (sizeof($tmp) == 2)
       return get_alinea($tmp[1],$tmp[0],$die);
    $tmp=explode("_", $id_nat); //rev 1012 considerer aussi ca (web service)
    if (sizeof($tmp) == 2)
       return get_alinea($tmp[1],$tmp[0],$die);
    else if ($die) return get_alinea(-1,-1,true);
    else return false;
}

/**
* renvoie les alineas d'un referentiel ou tous les alineas class�s par referentiel
*/

function get_alineas($id_referentiel, $tri ='',$die=1) {

    global $CFG;
    $table= 'alinea';
    if (empty($tri)) $tri='referentielc2i,alinea';
	if (empty($id_referentiel)) $where ="";
	else $where= " WHERE referentielc2i = '$id_referentiel'";
    $sql =<<<EOS
SELECT id,concat(referentielc2i,'.',alinea) as id_nat,
       alinea,referentielc2i,aptitude FROM {$CFG->prefix}$table
$where
order by $tri
EOS;

    return get_records_sql($sql,$die, "err_pas_de_alineas",$id_referentiel);
}

/**
 * renvoie un tableau d'objet (id,texte) tr a etre inser� dans un select par print_select_from_table
 * les valeurs sont excatement ce qui est en BD
 */

function get_etats_validation() {
	global $CFG,$USER;
        $ret= array();
    $ret[]=new option_select (QUESTION_TOUTE,traduction ('alt_toutes'));
    $ret[]=new option_select (QUESTION_NONEXAMINEE,traduction ('alt_non_examinee')); //defaut en positionnement local
   //jamais utilis� dans le code mais existe dans l'enum dans le SQL de c2iquestions ????
   // $ret[]=new option_select ('en attente',traduction ('alt_attente'));
    $ret[]=new option_select (QUESTION_REFUSEE,traduction ('alt_non_valide'));
    $ret[]=new option_select (QUESTION_VALIDEE,traduction ('alt_valide'));
    return $ret;
}



/**
 * version 2.0 maintenant que l'état n'est plus codé en dur dans la BD
 * renvoie une chaine traduite selon l'état de la question
 * @param $question une ligne en BD
 * @return string
 */

function get_etat_validation($etat,$uc_first=false) {
    if ($etat == QUESTION_VALIDEE)
    return traduction ('alt_valide',$uc_first);
    else if ($etat == QUESTION_REFUSEE)
    return traduction ('alt_non_valide',$uc_first);
    else if ($etat == QUESTION_NONEXAMINEE)
    return traduction ('alt_non_examinee',$uc_first);
    else if ($etat == QUESTION_TOUTE)
    return traduction ('alt_toutes',$uc_first);
    else 
     return ('???');
    
}

/**
 * renvoie les stats sur une question comme un objet contenant
 * nb, mini,maxi et moyenne
 * si jamais evalu�e renvoie un objet avec nb=0
 * @param idq ide  ids de la question
 * @param debut, fin fourchette (optionnelle) des dates d'examen l'utilisant
 * TODO ne fait pas la difference entre �valuation en positionnement ou en certification
 * dans le cas ou une question est utilis�e dans les deux modes
 */

function get_stats_question ($idq,$ide,$debut=false,$fin=false) {
	global $CFG,$USER;
	$cle=$ide.".".$idq;


    $table="{$CFG->prefix}resultatsdetailles R";
    $where="R.question='$cle' ";

    if (!$debut) $debut=1;  // il y a tr�s longtemps ...
    if (!$fin) $fin=time();
	$where .=" and R.date >=$debut and R.date <= $fin";
	$sql=<<<EOS
	select count(R.score) as nb, avg(R.score) as moyenne,stddev(R.score) as stddev, min(R.score) as mini, max(R.score)as maxi
	from $table
	where $where
	group by R.question
EOS;
   // print($sql);
	if (!$ret= get_record_sql($sql,false))  {
		$ret=new StdClass();
		$ret->nb=0;
        $ret->nb_examen=0;
		$ret->mini=$ret->maxi=$ret->moyenne=$ret->stddev=""; //facilite l'affichage ;-)'
	}else {
		  $ret->moyenne=sprintf("%.2f",$ret->moyenne);
		  $ret->stddev=sprintf("%.2f",$ret->stddev);

        $sql2=<<<EOS
        select distinct(R.examen) as nb_examen
        from $table
        where $where
EOS;
        $ret2=get_records_sql($sql2,true);
       // print_r($ret2);
        $ret->nb_examen=count($ret2);


         if ($CFG->calcul_indice_discrimination) { //TODO
         	$ret->idisc=0;
         	$ret->cdisc=0;
         	$ret->idq=$idq;
         	$ret->ide=$ide;
         	$ret->id=$cle;
         	calcul_stats_avancees($ret);
         }


    }
	return $ret;
}


/**
 * renvoie les stats sur un referentiel comme un objet contenant
 * nb, mini,maxi et moyenne
 * si jamais evalu�e renvoie un objet avec nb=0
 * @param idr  id du referentiel
 * @param debut, fin fourchette (optionnelle) des dates d'examen l'utilisant
 * rev 820 Fait  la difference entre �valuation en positionnement ou en certification
 * rev 820 ajout du nombre d'examens concern�s (en plus du nombre d'�tudiants test�s)
 */

function get_stats_referentiel ($idr,$debut=false,$fin=false) {
    global $CFG,$USER;
    $table="{$CFG->prefix}resultatsreferentiels R,{$CFG->prefix}examens E";
    $where="R.referentielc2i='$idr' and concat(E.id_etab,'_',E.id_examen)=R.examen and $USER->type_plateforme='OUI' ";
    if (!$debut) $debut=1;  // il y a tr�s longtemps ...
    if (!$fin) $fin=time();
    $where .=" and R.date >=$debut and R.date <= $fin";

    $sql=<<<EOS
    select count(R.score) as nb, avg(R.score) as moyenne, stddev(R.score) as stddev,min(R.score) as mini, max(R.score)as maxi
    from $table
    where $where
    group by R.referentielc2i
EOS;
   // print($sql);
    if (!$ret= get_record_sql($sql,false))  {
        $ret=new StdClass();
        $ret->nb=0;
        $ret->nb_examen=0;
        $ret->mini=$ret->maxi=$ret->moyenne=$ret->stddev=""; //facilite l'affichage ;-)'
    } else {
    	 $ret->moyenne=sprintf("%.2f",$ret->moyenne);
		  $ret->stddev=sprintf("%.2f",$ret->stddev);
        $sql2=<<<EOS
        select distinct(R.examen) as nb_examen
        from $table
        where $where
EOS;
        $ret2=get_records_sql($sql2,true);
       // print_r($ret2);
        $ret->nb_examen=count($ret2);

    }
    return $ret;
}

/**
 * renvoie les stats sur une comptetence comme un objet contenant
 * nb, mini,maxi et moyenne
 * si jamais evalu�e renvoie un objet avec nb=0
 * @param idr  id du referentiel idal id de l'alinea
 * @param debut, fin fourchette (optionnelle) des dates d'examen l'utilisant
 * rev 820  fait la difference entre �valuation en positionnement ou en certification
 * rev 820 ajout du nombre d'examens concern�s (en plus du nombre d'�tudiants test�s)
 */

function get_stats_competence ($idr,$idal,$debut=false,$fin=false) {
    global $CFG,$USER;
    $cle=$idr.".".$idal;
    $table="{$CFG->prefix}resultatscompetences R,{$CFG->prefix}examens E";
    $where="R.competence='$cle'  and concat(E.id_etab,'_',E.id_examen)=R.examen and $USER->type_plateforme='OUI'";
    if (!$debut) $debut=1;  // il y a tr�s longtemps ...
    if (!$fin) $fin=time();
    $where .=" and R.date >=$debut and R.date <= $fin";

    $sql=<<<EOS
    select count(R.score) as nb, avg(R.score) as moyenne, stddev(R.score) as stddev,min(R.score) as mini, max(R.score)as maxi
    from $table
    where $where
    group by R.competence
EOS;
   // print($sql);
    if (!$ret= get_record_sql($sql,false))  {
        $ret=new StdClass();
        $ret->nb=0;
         $ret->nb_examen=0;
        $ret->mini=$ret->maxi=$ret->moyenne=$ret->stddev=""; //facilite l'affichage ;-)'
    }else {
    	 $ret->moyenne=sprintf("%.2f",$ret->moyenne);
		  $ret->stddev=sprintf("%.2f",$ret->stddev);
        $sql2=<<<EOS
        select distinct(R.examen) as nb_examen
        from $table
        where $where
EOS;
        $ret2=get_records_sql($sql2,true);
       // print_r($ret2);
        $ret->nb_examen=count($ret2);

    }
    return $ret;
}

/**
 * calcul des statistiques pour une question
 * code tr�s inspir� de Moodle mod/quiz/ranalysis/report.php
 * on utilise un objet plutot qu'un tableau'
 */

function calcul_stats_avancees(&$ret) {
	global $CFG;

	$ret->attemptscores=array();
	   //liste des copies utilisant cette question par ordre de score decroissant
	$ret->questionscores=array();
	// liste des scores obtenus pour cette question dans chaque copie
	$idq=$ret->idq;
	$ide=$ret->ide;
	$cleq=$ret->id;

   //oblig� de passer par c2iquestionsexman car certains ont pu ne pas r�pondre
   // rev 977 seulement pour cette version du referentiel
    $sql=<<<EOS
    select R.*
    from {$CFG->prefix}questionsexamen Q,{$CFG->prefix}resultatsexamens R,{$CFG->prefix}examens E
    where Q.id=$idq and Q.id_etab=$ide
    and Q.id_examen_etab=E.id_etab
    and Q.id_examen=E.id_examen
    and concat(Q.id_examen_etab,'_',Q.id_examen)=R.examen
    order by R.score DESC;
EOS;

    $sql=<<<EOS
    select R.*
    from {$CFG->prefix}questionsexamen Q,{$CFG->prefix}resultatsexamens R
    where Q.id=$idq and Q.id_etab=$ide
    and concat(Q.id_examen_etab,'_',Q.id_examen)=R.examen
    order by R.score DESC;
EOS;

   $copies=get_records_sql($sql);
   $ret->nb_copies=count($copies);
	foreach($copies as $copie) {
		$ret->attemptscores[$copie->id]=$copie->score;
		 //note de cette question dans cet examen pour ce candidat identifi� par autonum id
        $copie->login=addslashes($copie->login); // rev 984
    	$sql=<<<EOS
   	 		select score
    		from {$CFG->prefix}resultatsdetailles RD
   			 where RD.login='$copie->login'
    		and RD.question='$cleq'
    		and RD.examen='$copie->examen'
EOS;
   		 $score=get_record_sql($sql,false); //n'a pas r�pondu a cette question ?'
   		 if ($score)
		 	$ret->questionscores[$copie->id]=$score->score;
		 else $ret->questionscores[$copie->id]=0;	//rev 921

	}
    // warning PHP sur min/max si rien
    if (empty($ret->attemptscores))
        $ret->attemptscores=array(0);
	$ret->top = max($ret->attemptscores); //pourrait �tre $attemptscores[0]
    $ret->bottom = min($ret->attemptscores); //pourrait �tre le dernier de $attemptscores
    $ret->gap = ($ret->top - $ret->bottom)/3;
    $ret->top -=$ret->gap;
    $ret->bottom +=$ret->gap;

	//print_r($attemptscores);
	//print "<br/>";
	//print_r($questionscores);
	report_question_stats($ret);
}

/**
 * code Moodle (seul changement =$q est un objet, pas un tableau)
 */
function report_question_stats(&$q) {
        $q->qstats = array();
        $qid = $q->id;
        $q->top_scores = $q->top_count = 0;
        $q->bottom_scores = $q->bottom_count = 0;
        foreach ($q->questionscores as $aid => $qrow){
           // if (isset($qrow[$qid])){
           //     $qstats[] =  array($attemptscores[$aid],$qrow[$qid]);
           		$q->qstats[]=array($q->attemptscores[$aid],$qrow);
                if ($q->attemptscores[$aid]>=$q->top){
                   // $top_scores +=$qrow[$qid];
                    $q->top_scores +=$qrow;
                    $q->top_count++;
                }
                if ($q->attemptscores[$aid]<=$q->bottom){
                    //$bottom_scores +=$qrow[$qid];
                     $q->bottom_scores +=$qrow;
                    $q->bottom_count++;
                }
           // }
        }
       // print_r($qstats);
        $n = count($q->qstats);
        if ($n==0) return $q;
        $sumx = stats_sumx($q->qstats, array(0,0));
        $sumg = $sumx[0];
        $sumq = $sumx[1];
        $sumx2 = stats_sumx2($q->qstats, array(0,0));
        $sumg2 = $sumx2[0];
        $sumq2 = $sumx2[1];
        $sumxy = stats_sumxy($q->qstats, array(0,0));
        $sumgq = $sumxy[0];

        $q->count = $n;
        //doit �te �gal � la moyenne calcul�e par sql
        $q->facility = $sumq/$n;
        //doit etre egal � stddev calcul� en sql qui le calcule en non biais� (/N) et pas (N-1)
        if ($n<2) {
            $q->qsd = sqrt(($sumq2 - $sumq*$sumq/$n)/($n));// ecart type question
            $q->gsd = sqrt(($sumg2 - $sumg*$sumg/$n)/($n)); // ecart type toutes copies
        } else {
            $q->qsd = sqrt(($sumq2 - $sumq*$sumq/$n)/($n-0));  //pourquoi �tait $n-1 ???
            $q->gsd = sqrt(($sumg2 - $sumg*$sumg/$n)/($n-0));
        }
        $q->idisc=sprintf("%.2f", ($q->top_scores - $q->bottom_scores)/max($q->top_count, $q->bottom_count, 1));
        $div = $n*$q->gsd*$q->qsd;
        if ($div!=0) {
            $q->cdisc= ($sumgq - $sumg*$sumq/$n)/$div;
             $q->cdisc=sprintf("%.2f",$q->cdisc);
        } else {
            $q->cdisc = -999;
        }

        return $q;
    }


function stats_sumx($data, $initsum){
    $accum = $initsum;
    foreach ($data as $v) {
        $accum[0] += $v[0];
        $accum[1] += $v[1];
    }
    return $accum;
}

function stats_sumx2($data, $initsum){
    $accum = $initsum;
    foreach ($data as $v) {
        $accum[0] += $v[0]*$v[0];
        $accum[1] += $v[1]*$v[1];
    }
    return $accum;
}

function stats_sumxy($data, $initsum){
    $accum = $initsum;
    foreach ($data as $v) {
        $accum[0] += $v[0]*$v[1];
        $accum[1] += $v[1]*$v[0];
    }
    return $accum;
}


 /*
  * fonction non utilis�e car diff�rente de celle utilis�e par Moodle
  *
  *
  * Bonjour,

Voici notre m�thode utilis�e � Saint-Etienne pour calculer l'indice de
discrimination :

coeff= Bi*100/T - Mi*100/T

T = le tiers des copies (nb copies/3)

Bi = nombre de r�ponses ayant obtenu la note maximale pour la question i
parmi les tiers des meilleurs copies

Mi = nombre de r�ponses ayant obtenu la note maximale pour la question
i �parmi le tiers des moins bonnes copies

Remarques :
Il faut avoir au moins 12 copies pour appliquer cette formule.
Cette formule est particuli�rement adapt�e pour �les questions �qui �
n'ont pas de note �interm�diaire entre �la �note �max �et �la �note �mini.

--
Cordialement,

***************************************************
David Boit
DSI : P�le Production TICE et Services
Universit� Jean Monnet Saint-Etienne
T�l : 04 77 42 1609
  */
function get_indice_discrimination ($idq, $ide) {
    global $CFG;

    //liste des copies utilisant cette question par ordre de score decroissant

    $cleq=$ide.".".$idq;
    $sql=<<<EOS
    select R.*
    from {$CFG->prefix}questionsexamen Q,{$CFG->prefix}resultatsexamens R
    where Q.id=$idq and Q.id_etab=$ide
    and concat(Q.id_examen_etab,'_',Q.id_examen)=R.examen
    order by R.score DESC;
EOS;

   $res=get_records_sql($sql);
   $T=count($res)/3;
   if ($T*3 <12) return ""; //moins de 12 copies
   $Bi=$Mi=0;
   $N=0;
   foreach ($res as $copie) {
    //note de cette question dans cet examen pour ce candidat
    $copie->login=addslashes($copie->login); // rev 984
    $sql=<<<EOS
    select score
    from {$CFG->prefix}resultatsdetailles RD
    where RD.login='$copie->login'
    and RD.question='$cleq'
    and RD.examen='$copie->examen'
EOS;
    $score=get_record_sql($sql);
    if ($score->score==1) {  // score maxi
        if ($N <$T) $Bi++;    // premier tier
        else if ($N>$T*2) $Mi++; //dernier tier
    }
    $N++;

   }
   //print "$Bi $Mi $T $N<br/>";

   return sprintf("%.2f",$Bi*100/$T -$Mi*100/$T)."<br/>". "Bi=$Bi  Mi=$Mi";


}






function affiche_texte_question ($texte) {
    return  affiche_texte($texte);
}


function affiche_texte_reponse ($texte) {
    return  affiche_texte($texte);
}



function get_questions_bytags($tags,$sort='',$page=0, $recordsperpage=-1, &$totalcount=null) {
    return search_table_bytags('questions',$tags, $sort, $page, $recordsperpage,$totalcount);
}





function evt_question_modification ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_question_ajout ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_question_suppression ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_question_envoi ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_question_validation ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}

function evt_question_invalidation ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}

function evt_question_filtrage ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}

function evt_question_defiltrage ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}

function evt_question_duplication ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}

function evt_question_importation ($data) {
	dump_event(__FUNCTION__,$data);
	return true;
}

function evt_question_feedback ($data) {
	dump_event(__FUNCTION__,$data);
	return true;
}


function evt_famille_modification ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_famille_ajout ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_famille_suppression ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}

if (0)
{
//print get_indice_discrimination (1785,1);
print_r(get_referentiels_liste(-1));
print '<br/>';
print_r(get_referentiels_liste());
print '<br/>';
print_r(get_referentiels_liste('A2'));
print '<br/>';
print_r(get_referentiels_liste('B1,B3'));

}
if (0) {
  print ('<pre>');
    print_r(get_familles_liste('','',0));
    print ('<br/>');
    print_r(get_familles_liste('D1','',0));
    print ('<br/>');
    print_r(get_familles_liste('D1','rand()',0));
    print ('</pre>');
}
