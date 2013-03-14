<?php

/**
 * @author Patrick Pollet
 * @version $Id: config_m.php 1221 2011-03-15 15:50:51Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	Page de modification d'un champ de configuration du LDAP
//	Pierre Raynaud
//	pierre.raynaud@u-clermont1.fr
//
////////////////////////////////


/*
* Pour la description des différentes méthodes de la classe TemplatePower,
* il faut se reférer à http://templatepower.codocad.com/
*/
//******** Pour chaque page $chemin représente le path(chemin) de script dans le site (à la racine)
//******** ---------------- $chemin_commun représente le path des utilitaires dont on aura besoin
//******** ---------------- $chemin_images représente le path des images
$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");					//fichier de paramètres
require_once($chemin_commun."/lib_ldap.php");


require_login('P'); //PP
v_d_o_d("config");

$elt=required_param("elt",PARAM_ALPHAEXT);  //lettres et _ (nom de l'item a modifier)
$doit=optional_param("doit","",PARAM_INT);  //(validation ?)
$noms=optional_param("noms",array(),PARAM_RAW);
$champs=optional_param("champs",array(),PARAM_RAW);

$id_etab =$USER->id_etab_perso;

//la validation est traitée ici (plus de script action.php)
if ($doit) {
	if ($elt == 'ldap_champs_recherche'){  //pas d'autre cas
		print_r($noms);
		print_r($champs);

		// On supprime la liste des champs
		delete_records("ldap","modifiable='OUI' AND id_etab = $id_etab");

		$ligne=new StdClass;
		$ligne->id_etab=$id_etab;

		for ($i=0;$i<count($noms);$i++){
			$ligne->nom_champ=$noms[$i];
			$ligne->champ_LDAP=$champs[$i];
			$ligne->ordre=$i;
			$ligne->modifiable='OUI'; //compat V 1.4
			insert_record("ldap",$ligne,false,false);
		}
	}
	//tracking :
	espion2("configuration", "champs recherche LDAP",get_champs_recherche_ldap($id_etab));
     if ($CFG->W3C_strict)
      ferme_popup("../config/configuration2.php",true);
    else ferme_popup("../config/configuration.php",true);

}


require_once( $chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates

$tpl = new C2IMiniPopup( );	//créer une instance
//inclure d'autre block de templates

//TRES IMPORTANT
// dans les templates inline IL FAUT un marqueur CDATA !!! et pas de commentaires du type //
$fiche=<<<EOF
<script type="text/javascript">
//<![CDATA[
	function ajouter(elem)	{
	var copier = elem.cloneNode(true);
	copier.getElementsByTagName("input")[0].value="";
	copier.getElementsByTagName("input")[1].value="";
	elem.parentNode.appendChild(copier);
}

function supprimer(elem){
	if (elem.parentNode.childNodes.length > 3)
		elem.parentNode.removeChild(elem);
}

//]]>
</script>


<form action="config_m.php" name="monform" id="monform" method="post">
<table border="1"  width="100%">
    <thead>
    <tr>
        <th>+ / -</th>
        <th class="taille2">{ldap_nom_champ_bd}<br/><span class="commentaire1">(ex:prenom)</span></th>
        <th class="taille2">{ldap_nom_attribut}<br/><span class="commentaire1">(ex:givenName)</span></th>
    </tr>
    </thead>
    <tbody>
<!-- START BLOCK : nouvelle_ligne -->
          <tr>
            <td width="38">
            <img src="{chemin_images}/i_nouveau.gif"  alt="{alt_ajouter}" title="{alt_ajouter}"
                 onclick="ajouter(this.parentNode.parentNode)" />
           <img src="{chemin_images}/i_non_valide_a.gif"  alt="{alt_supprimer}" title="{alt_supprimer}"
                onclick="supprimer(this.parentNode.parentNode)" />
                </td>
            <td  >
        <input name="noms[]" type="text" size="15" value="{nom_champ}" class="required" title="{js_valeur_manquante}" />
        </td>
            <td>
        <input name="champs[]" type="text" size="15" value="{champ_LDAP}" class="required" title="{js_valeur_manquante}" />
        </td>
          </tr>
<!-- END BLOCK : nouvelle_ligne -->
        </tbody>
        </table>
<div class="centre">
        {bouton:annuler} &nbsp; &nbsp; {bouton:enregistrer}
</div>


<!-- START BLOCK : nom_item -->
    <input name="item" type="hidden" value="{nom_champ}" />
<!-- END BLOCK : nom_item -->
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->
<input name="etablissement" type="hidden" value="{etablissement}" />
<input name="doit" type="hidden" value="1" />
<input name="elt" type="hidden" value="{elt}" />

</form>

EOF;




if ($elt=='ldap_champs_recherche')
$tpl->assignInclude("contenu",$fiche,T_BYVAR);
else
//$tpl->assignInclude("contenu",$chemin."/templates/config_m.html");	// le template gérant la configuration
   erreur_fatale ("err_config_ldap_item",$elt);

$tpl->prepare($chemin);

$tpl->assign("etablissement" , $id_etab);


//print_bouton_annuler($tpl);
//print_bouton_enregistrer($tpl);


$tpl->assign("titre_popup" , traduction("modif_config").'<br/>'.traduction("ldap_synchro"));


$CFG->utiliser_validation_js=1;


// On ne traite que  la gestion des champs de recherche

$champs_ldap = get_champs_recherche_ldap($id_etab);

	foreach ($champs_ldap as $ligne)	{
		$tpl->newBlock("nouvelle_ligne");
        $tpl->assignObjet($ligne);
        /**
		$tpl->assign("nom_champ" , str_replace('"', "&quot;", "ldap_champs_recherche_nom"));
		$tpl->assign("valeur_nom_champ" , str_replace('"', "&quot;", $ligne->nom_champ));
		$tpl->assign("champ_LDAP" , str_replace('"', "&quot;", "ldap_champs_recherche_ldap"));
		$tpl->assign("valeur_champ_LDAP" , str_replace('"', "&quot;", $ligne->champ_LDAP));
        **/
	}

$tpl->assign("_ROOT.elt" , str_replace('"', "&quot;", $elt));


$tpl->printToScreen();										//affichage
?>