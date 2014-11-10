<?php

/**
 * @author Patrick Pollet
 * @version $Id: valider.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
ce script passe la validation W3C avec les commentaires <!-- --> dans le javascript en ligne
 on ne PEUT pas mettre un CDATA sinon ne fait pas les assign aux parametres ref, alin et session_id
**/

$chemin = '../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

//rev 981 simplification avec le parametre id complet
if ($id=optional_param('id','',PARAM_CLE_C2I)) {
    $ligne=get_question_byidnat ($id);
    $idq=$ligne->id;
    $ide=$ligne->id_etab;
} else {
    $idq = required_param("idq", PARAM_INT);
    $ide = required_param("ide", PARAM_INT);
    $ligne=get_question($idq,$ide);

}
$doit=optional_param("doit",0,PARAM_INT);  //action ?

$url_retour=optional_param("url_retour","",PARAM_CLEAN);

require_login("P"); //PP

if ($CFG->universite_serveur==1)  capacite_requise("qv",1);  // sur la nationale, un expert
else capacite_requise("qv",$ide);  // sinon un expert local de cet etablissement

/**
 * traitement ici (�tait dans un script validation.php avant v 1.5)
 */



if ($doit) { // action
	$familles=optional_param("famille","",PARAM_ALPHANUM);

	// les deux cases a cocher sont dans un groupe 'validation' donc valeur toujours mise!
	$data=array('remarques','modifications','validation');
	$ligne=new StdClass();
	foreach ($data as $champ)
	$ligne->$champ=required_param($champ,PARAM_CLEAN);

	$ligne->id=$idq;
	$ligne->id_etab=$ide;
	$ligne->ts_date=time();
	$ligne->login=$USER->id_user;
	// rev. 1.5 on igore l'ancien champ date que l'on virera un jour

	if (get_question_mon_avis_validation ($idq,$ide))  // maj de mon ancien avis
		delete_records("questionsvalidation", "id=$idq and id_etab=$ide and login='".addslashes($USER->id_user)."' "); // rev 984
	// faux il faudrait trois criteres a update_record !!!
	//	update_record("questionsvalidation",$ligne ,'id','id_etab',true); //,"err_maj_avis_validation",$ide.".".$idq);


    insert_record("questionsvalidation",$ligne,false,false,true,"err_maj_avis_validation",$ide.".".$idq);

	/////////////////////////////////////////
	// v�rification du nombre de valideurs requis
	/////////////////////////////////////////
	if ($ligne->validation=='NON')
		invalide_question ($idq,$ide);

	else {  //valid�e par moi
		if ($familles){  // rev 1.4
			if ($familles == 'auteur'){

				$question=get_question ($idq,$ide);
				$famille_proposee=trim($question->famille_proposee);  // c'est un texte (pas un id) '
				if ($famille_proposee){
					$fam=get_famille_par_nom($famille_proposee,$question->referentielc2i,$question->alinea);
					if ($fam)
						$famille_choisie=$fam->idf;
					else {  //cr�ation d'une nouvelle famille ... (seulement sur la nationale ????)'
						$fam=new StdClass();
						$fam->referentielc2i=$question->referentielc2i;
						$fam->alinea=$question_>alinea;
						$fam->famille=$famille_proposee;
						// champs ajout�s V 1.5
						$fam->ts_datecreation=time();
						$fam->auteur=$question->auteur;
						$fam->auteur_mail=$question->auteur_mail;
						$famille_choisie=insert_record("familles",$fam,true,'idf',true,"err_creation_famille",$famille_proposee);
					}
				} else
					$famille_choisie = 0;
			} else
				$famille_choisie = intval($familles);
			mise_a_jour_famille_validee($idq,$ide,$famille_choisie);
		}
		// assez d'avis positif (dont le mien)
		if (nb_validations($idq,$ide) >= config_nb_experts()){
			valide_question($idq,$ide);

			if ($CFG->universite_serveur == 1) {
				if ($CFG->generer_xml_qti)  {
					require_once ($CFG->chemin_commun."/lib_xml.php");
					to_xml($id, $id_etab);
				}
			}
		}
	}

	ferme_popup("liste.php?".urldecode($url_retour),true);
}




require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates

$forme=<<<EOF

<!-- START BLOCK : js_familles -->
<script type="text/javascript">
<!--

function aj_famille(){
	var famille = document.monform.nouv_f.value;
	var descf = document.monform.fdesc.value;
    var motsclesf = document.monform.fmotscles.value;


	var request_url = '../../commun/ajax/aj_famille.php?ref={ref}&alin={alin}&{session_name}={session_id}';
	//dbg(request_url);
	new Ajax.Request(request_url,
	{
		method:'post',
		parameters : {'famille' : encodeURI(famille), 'descf' : encodeURI(descf), 'motsclesf':encodeURI(motsclesf) },
		onSuccess: function(transport){
			var retour = transport.responseText.evalJSON();
			if (retour.result == 'ok') {
				var tmpdesc = '';
				if (descf != '') tmpdesc = ' ('+descf+')';
				 document.monform.famille.options[document.monform.famille.options.length]=new Option(famille + tmpdesc,retour.idf);
				 document.monform.nouv_f.value="";
				 document.monform.fdesc.value="";
                 document.monform.fmotscles.value="";
				 document.monform.famille.options.selectedIndex = document.monform.famille.options.length - 1;
                 miseAJourMotsClesFamille();
			}
			else {
				document.monform.nouv_f.value="";
				document.monform.fdesc.value="";
                document.monform.fmotscles.value="";
				alert(retour.result);
			}
		},
		onFailure: function(transport){
			alert("erreur");
		}
	});
}


function test_champs(){

	if (document.monform.nouv_f.value != "") {
		aj_famille();
		return false;
	}
	else if ((\$F('famillev')==0) && (\$F('etatv')=="OUI")){
		alert("Vous devez sélectionner une famille avant de pouvoir valider cette question");
		return false;
	}

	else if (\$F('etatv')=="OUI"){
		return confirm('Vous avez demandé de valider la question. Est-ce bien cela ?');
	}
	else return confirm('Vous avez demandé de non-valider la question. Est-ce bien cela ?');

	return true;
}
-->
</script>
<!-- END BLOCK : js_familles -->
<script type="text/javascript">
//<![CDATA[
function miseAJourMotsClesFamille() {
     majDiv("mots_clesf","{$CFG->chemin_commun}/ajax/get_mots_cles_familles.php",null,"monform");
}
 setTimeout('miseAJourMotsClesFamille()',3);
//]]>
</script>

<!-- START BLOCK : js_sans_familles -->
<script type="text/javascript">
//<![CDATA[
function test_champs(){

	if (\$F('etatv')=="OUI"){
		return confirm('Vous avez demandé de valider la question. Est-ce bien cela ?');
	}
	else return confirm('Vous avez demandé de non-valider la question. Est-ce bien cela ?');

    return true;
}

//]]>

</script>
<!-- END BLOCK : js_sans_familles -->

<!-- avis pr�c�dents -->
{ici}

<!-- START BLOCK : form_validation -->

<form name="monform" id="monform" action="valider.php" method="post" onsubmit="return test_champs();">
<table class="fiche" width="90%">
<thead>
<tr><th class="bg" colspan="2">{form_votre_avis} </th></tr>
</thead>
<tbody>
          <tr>
           <th>{form_remarques}</th>
            <td><textarea name="remarques" rows="3" cols="65" class="required">{remarques}</textarea></td>
          </tr>
          <tr>
           <th>
              {form_modifications}</th>
            <td><textarea name="modifications" rows="3" cols="65" class="required">{modifications}</textarea></td>
          </tr>
<!-- START BLOCK : famille -->
          <tr>
            <th>{form_famille_validee}</th>
            <td>
            <div class="normale">
            <p class="double">
			<label for="fprof">{form_famille_proposee} : </label>
			<input type="text" name="fprof" id="fprof" value="{famille_proposee}" size="65" readonly="readonly"/>
            <br/> <span id="mots_clesfp" class="commentaire1">{mots_cles_famille_proposee}</span>
			</p>
			<p class="double">
			<label for="famillev">{form_famille_validee} : </label>
			   <select size="1" name="famille"  class="saisie" style="width:420px" id="famillev" onchange='miseAJourMotsClesFamille();'>
                <option value="0">-- {famille} ---</option>
				<!-- START BLOCK : conserver -->
				<option value="{id}" {selected}>-- Conserver la proposition de l auteur ---</option>
				<!-- END BLOCK : conserver -->
<!-- START BLOCK : selected_famille -->
                <option value="{idf}" {selected}>{famille}</option>
<!-- END BLOCK : selected_famille -->
            </select>
             <br/><span id="mots_clesf" class="commentaire1">{mots_cles_famille_validee}</span>
            </p>
			<p class="double">
			<b>Nouveau thême pour le domaine {ref} - compétence {alin} :</b>
			</p>
			<p class="double">
			<label for="nouv_f">Intitulé : </label>
			<input type="text" class="saisie"  name="nouv_f" id="nouv_f" size="60"/>
			</p>
			<p class="double">
			<label for="fdesc">Description : </label>
			<input type="text" name="fdesc" id="fdesc" class="saisie" size="60" />
			</p>
            <p class="double">
            <label for="fmotscles">Mots clés : </label>
            <input type="text" name="fmotscles" id="fmotscles" class="saisie" size="60" />
            </p>
			<p class="simple">
			<input type="button" name="vnf" value="ajouter" class="saisie"
			onclick="aj_famille();"/>
			</p>
			</div>

			</td>
          </tr>
<!-- END BLOCK : famille -->
          <tr>
            <th>{form_avis}</th>
            <td><input id="etati" name="validation" type="radio" value="NON" {checkedi}/>{alt_non_valide}
			<br/><input id="etatv" name="validation" type="radio" value="OUI" {checkedv}/>{alt_valide}</td>
          </tr>

 </tbody>
 </table>
     <div class="centre">
      {bouton:annuler}  &nbsp; {bouton:enregistrer}

<input name="idq" type="hidden" value="{idq}"/>
<input name="ide" type="hidden" value="{ide}"/>
<input name="doit" type="hidden" value="1"/>
<input type="hidden" name="url_retour" value="{url_retour}"/>

<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}"/>
<!-- END BLOCK : id_session -->
</div>

</form>
<!-- END BLOCK : form_validation -->
EOF;


$tpl->assignInclude("corps", $forme ,T_BYVAR);
$tpl->prepare($chemin);

$ligne=get_question($idq,$ide);

$CFG->utiliser_prototype_js=1;  //forc� pour jajax

$link= "<ul style=\"display:inline;\">".print_menu_item(false, false,get_menu_item_consulter("fiche.php?idq=$idq&amp;ide=$ide" ))."</ul>";

$tpl->assign("_ROOT.titre_popup", traduction( "validation_question")." ".$ide.".".$idq."<br/>".
                clean(affiche_texte_question($ligne->titre),75)." ".$link);

$tpl->assign("ici",print_commentaires($idq,$ide));  //anciens commentaires (dont le mien ...)

$nb_avis=nb_avis($idq,$ide);

//echo $nb_avis."/".config_nb_experts();

if ($nb_avis  < config_nb_experts()) {


    $tpl->newblock("form_validation");
    $tpl->assign("idq", $idq);
    $tpl->assign("ide", $ide);
    $tpl->assign("url_retour", $url_retour);
    $mon_avis=get_question_mon_avis_validation($idq,$ide);  //mon ancien avis si il existe
    if (!$mon_avis) {
    	$mon_avis=new StdClass();
    	$mon_avis->remarques=$mon_avis->modifications="";
    	$mon_avis->validation="OUI"; // rev 839 valid�e par d�faut
    }
   $tpl->assignObjet($mon_avis);
   $tpl->setChecked($mon_avis->validation=='OUI',"checkedv","checkedi");

   if ($CFG->universite_serveur == 1) {    //uniquement sur la nationale
       $ligneq = get_question($idq,$ide);

       $ref= $ligneq->referentielc2i;
       $alin=$ligneq->alinea;

       $famille_proposee = trim($ligneq->famille_proposee); // plus de proposition nouvelle par l'auteur
       $famille_proposee_idf = $ligneq->id_famille_proposee;
       $tpl->newBlock("js_familles");
       $tpl->assign("ref", $ref);
       $tpl->assign("alin", $alin);
       $tpl->assign("session_name", session_name());
       $tpl->assign("session_id", session_id());

       $tpl->newBlock("famille");
       $fam_prop=get_famille_vide();
       if ($famille_proposee_idf>0 && $test=get_famille($famille_proposee_idf,false)) {
           $fam_prop=$test;
       }

       $tpl->assign("famille_proposee", $fam_prop->famille);
       $tpl->assign ("mots_cles_famille_proposee",get_infos_famille($fam_prop));
       $tpl->assign("ref", $ref);
       $tpl->assign("alin", $alin);
       if ($famille_proposee != "") {
           $tpl->newBlock("conserver");
           if ($famille_proposee_idf > 0) {
               $tpl->assign("id", $famille_proposee_idf);
               $tpl->assign ("selected","selected"); // la selectionne alors rev 839
           }
           else
               $tpl->assign("id", "auteur"); //interdi en v 1.5
       }
       $familles=get_familles_associees( $ref,$alin);


       foreach($familles as $lignef) {
           $tpl->newBlock("selected_famille");
           $tpl->assign("idf", $lignef->idf);

           $motsClesF = trim($lignef->mots_clesf);
           if ($motsClesF != "")
               $motsClesF = ' (' . $motsClesF . ')';
           /*
            $descF = trim($lignef->commentaires);
            if ($descF != "")
            $descF = ' (' . $descF . ')';
            */
           $tpl->assign("famille", str_replace('"', "&quot;", $lignef->famille . $motsClesF ));
           if ($ligneq->id_famille_validee == $lignef->idf)
               $tpl->assign("selected", " selected=\"selected\" ");
           else $tpl->assign("selected","");
       }
   } else {
       $tpl->newBlock("js_sans_familles");
   }
} else {

    $tpl->print_boutons_fermeture();

}

$tpl->printToScreen(); //affichage
?>