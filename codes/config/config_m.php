<?php

/**
 * @author Patrick Pollet
 * @version $Id: config_m.php 1221 2011-03-15 15:50:51Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	Page de modification d'un champ de configuration
//
////////////////////////////////

/**
 * ----------------REVISIONS----------------------
v 1.1 : PP 17/10/2006
           ajout des trois attributs LDAP pour les membres d'un groupe et l'id unique
v 1.41 les valeurs par défaut sont TOUTES en base ...voir lib_ldap.php/maj_bd_ldap()
      l'action de validation est faite ici (plus de script action.php)
v 1.5 utilisation de validation.js pour certaines valeurs sensibles
*/


$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");					//fichier de paramètres

require_login('P'); //PP

$elt=required_param("elt",PARAM_ALPHAEXT);  //lettres et _ (nom de l'item a modifier)
$doit=optional_param("doit","",PARAM_INT);  //(validation ?)
$valeur=optional_param("valeur","",PARAM_RAW);

v_d_o_d("config");

//commence par les items de CFG
$estCFG=false;
if (substr($elt,0,4)=="CFG_")  {// option de CFG pas e c2ietablissement
      $nomParam=$elt;  //nom complet a renvoyer
    $elt=substr($elt,4);
    $item=get_config_item($elt,true); // erreur fatale si inconnu
    if (!$item->modifiable)
        erreur_fatale("err_config_item",$elt);
    $estCFG=true;

    //valeur actuelle
    $valeurParam=$item->valeur;
    $valDefaut=$item->defaut;
    $etiquette=$item->description;
    $valid=$item->validation;
    $title=""; //TODO (validation.js)
}

// selection de l'établissement de la personne connectée (pour l'insertion d'item)
$id_etab =$USER->id_etab_perso;   // V 1.5

//validation
if (!empty($doit)) {
    //problème avec les valeurs vides ....gere par validation.js si nécessaire
    //$valeur=required_param("valeur",PARAM_CLEAN);  //valeur de l'item
	/////////////////////////////////////////
	if ($elt == "param_nb_aleatoire") {
		$tmpval = $valeur;

        //revision 932 (c2ims : nb questions= multiple nombre referentiel)
        $refs=get_referentiels(); //trié par referentiel

		$tmpvalmodulo = $tmpval % count($refs);
		if ($tmpvalmodulo != 0) $valeur= $valeur - $tmpvalmodulo;


	}
	switch($elt){
		case "param_nb_items" :
		case "param_nb_aleatoire" :
		case "param_nb_experts" :
		case "param_nb_qac" :
		case "param_langue" :
		case "param_ldap" :
		case "base_ldap" :
		case "rdn_ldap" :
		case "passe_ldap" :
		case "url":
		case "ldap_group_class":
		case "ldap_group_attribute":
		case "ldap_id_attribute":

		case "ldap_login_attribute" :
		case "ldap_nom_attribute" :
		case "ldap_prenom_attribute" :
        case "ldap_mail_attribute" :
		case "ldap_champs_recherche":
		case "ldap_version":
        case "ldap_user_type":   // rev 1022

			// modification de l'item  TODO set_field !!!!
			$sql = "update {$CFG->prefix}etablissement set `".$elt."`='".$valeur."' where id_etab=".$id_etab.";";
			$res = ExecRequete ($sql);

			//tracking :
			espion2("configuration", $elt, $valeur);
			break;


		default :
			//config
			if ($estCFG) {


				set_config("pfc2i",$elt,$valeur);
				//tracking :
				espion2("configuration", $elt, $valeur);
				break;
			}
			else erreur_fatale ("err_config_parametre_inconnu", $elt);
	}
   // if ($CFG->W3C_strict)  rev 981
      ferme_popup("configuration2.php",true);
	// else ferme_popup("configuration.php",true);
}

require_once( $chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates

$tpl = new C2IMiniPopup( );	//créer une instance

//inclure d'autre block de templates
$fiche=<<<EOF
<script type="text/javascript">
//<![CDATA[
function val_defaut(){
		validator.reset(); //annuler erreur
        document.form.valeur.value=document.form.defaut.value;
}
//]]>
</script>

<div id="saisie_minipopup">
<form action="config_m.php" id="monform" name="form" method="post">
    <div id="etiquette">
        {etiquette}
    </div>

   <hr/>
    <div class="centre">
<!-- START BLOCK : champ_texte -->
            <input name="valeur" type="text" size="40" value="{valeur_champ}" class="saisie {valid}" title="{title}" />
<!-- END BLOCK : champ_texte -->
<!-- START BLOCK : champ_liste -->
             {select_valeur}
<!-- END BLOCK : champ_liste -->

    </div>
<div class="centre">
{bouton:annuler}
&nbsp;
<input type="button" value="{bouton_defaut}" class="saisie_bouton"
    onclick="val_defaut();" />
&nbsp;
{bouton:enregistrer}
<input name="elt" type="hidden" value="{nom_champ}" />
<input name="defaut"    type="hidden" value="{valeur_defaut}" />

<!-- START BLOCK : id_session -->
 <input name="{session_nom}"    type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->
 <input name="etablissement"    type="hidden" value="{etablissement}" />
 <input name="doit"    type="hidden" value="1" />
</div>


</form>
</div>
EOF;

$tpl->assignInclude("contenu",$fiche,T_BYVAR);	// le template gérant la configuration

$tpl->prepare($chemin);

$ligne=get_etablissement($id_etab); // V 1.5 fatale si inconnu

$tpl->traduit("titre_popup","modif_config");

$tpl->assign("etablissement" , $ligne->id_etab);

// valeurs par défaut et validation




//a on commencé par $CFG au début (elt=CFG_xxxxx);

if (!$estCFG) {
	$nomParam="";
	$valid="";
	$title="";
	$resultat0 = ExecRequete ("describe {$CFG->prefix}etablissement" ); //fatale si erreur
	while($ligne0 = LigneSuivante ($resultat0)){

		if ($ligne0->Field==$elt) {
			switch($ligne0->Field){
				case "param_nb_items" :
				case "param_nb_aleatoire" :
				case "param_nb_experts" :
				case "param_nb_qac" :
					$valid="required validate-digits";
					$title= "js_valeur_numerique_attendue";
					break;

				case "param_langue" :
					$valid="required validate-alpha";
					$title="js_valeur_alpha_attendue";
					break;

				case "url" :
					$valid="required validate-path";
					$title="js_valeur_url_incorrecte";
					break;
				case "param_ldap" :
				case "base_ldap" :
				case "rdn_ldap" :
				case "passe_ldap" :
					break;


				case "ldap_group_class" :
				case "ldap_group_attribute" :
				case "ldap_id_attribute" :
				case "ldap_login_attribute" :
				case "ldap_nom_attribute" :
				case "ldap_prenom_attribute" :
				case "ldap_mail_attribute" :
					$valid="validate-alphanum"; //rev 899 certains attributs ldap eont de chiffres !
					$title="js_valeur_alphanumerique_attendue";
					break;

				case "ldap_champs_recherche":
                case "ldap_user_type":   // rev 1022
					break;
			}

			$nomParam = $ligne0->Field;
			$valDefaut = $ligne0->Default;
			//valeur actuelle
			$valeurParam=$ligne->$nomParam;
            if ($nomParam != 'param_nb_aleatoire')
			 $etiquette=traduction ("config_".$nomParam);
            else {
                 $refs=get_referentiels(); //trié par referentiel
             $etiquette=traduction("config_param_nb_aleatoire", true, count($refs));
            }
			break; //sortie de boucle while
		}
	}
}
//on demande une valeur inconnue
if (empty($nomParam))
    erreur_fatale("!!!err_config_parametre_inconnu",$elt);



$CFG->utiliser_validation_js=1;


$tpl->assign("etiquette" ,$etiquette);
$tpl->assign ("elt",$elt);
$tpl->assignGlobal("nom_champ" , str_replace('"', "&quot;", $nomParam));
$tpl->assignGlobal("valeur_defaut" , str_replace('"', "&quot;", $valDefaut));
//a gerer pour d'autres type d'input un jour


switch($elt){
	case "param_langue":
		$tpl->newBlock("champ_liste");
		break;
	case "ldap_user_type":
        $tpl->newBlock("champ_liste");
        print_select_from_table($tpl,"select_valeur",
           get_ldap_annuaires(),"valeur","","","id","texte","",$ligne->ldap_user_type);

        break;
	default:
		$tpl->newBlock("champ_texte");
        $tpl->assign("valeur_champ" , str_replace('"', "&quot;", $valeurParam));
	    $tpl->assign("valid",$valid); //classe de validation
	    $tpl->assign("title",traduction($title)); //message d erreur
	break;
}


$tpl->printToScreen();										//affichage
?>