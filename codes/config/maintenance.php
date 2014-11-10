<?php
/**
 * @author Patrick Pollet
 * @version $Id: maintenance.php 621 2009-04-02 17:31:40Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres
require_once ($CFG->chemin_commun.'/lib_editeur.php');

require_login("P"); //PP
v_d_o_d("config");

$doit=optional_param("doit",0,PARAM_INT);  //action ?
$message=optional_param("message",'',PARAM_RAW);  //action ?

require_once( $chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates

$tpl = new C2IPopup();	//cr�er une instance
$CFG->utiliser_editeur_html=1;

$forme=<<<EOL

<form class="normale" action="maintenance.php" method="post" name="monform" id="monform">
<fieldset>
<legend>{maintenance} </legend>
<input name="doit" type="hidden" value="1"/>


<!-- START BLOCK : active -->
<div class="commentaire1">{info_maintenance_on}</div>
<label for="message">{form_texte_message}</label>
{corps_message}
<!-- END BLOCK : active -->

<!-- START BLOCK : desactive -->
<div class="commentaire1">{info_maintenance_off}</div>
<!-- END BLOCK : desactive -->

<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->
</fieldset>

<div class='centre'>
{bouton:annuler}&nbsp;{bouton:ok}
</div>
</form>
{active_html_editor}
EOL;

// si validé
if ($doit) {
	if ($CFG->mode_maintenance) {
		set_config('pfc2i', 'mode_maintenance', 0);
	} else {
		set_config('pfc2i', 'mode_maintenance',1);
		set_config('pfc2i', 'info_maintenance', $message);
	}
	
	ferme_popup("configuration2.php",true);
}

$tpl->assignInclude("corps",$forme,T_BYVAR);
$tpl->prepare($chemin);

if ($CFG->mode_maintenance)
	$tpl->newBlock('desactive');
else {
	$tpl->newBlock('active');
	$tpl->assign('corps_message',print_textarea($CFG->utiliser_editeur_html,6,60,0,0,"message",
			$CFG->info_maintenance,'saisie required',
		traduction('js_libelle_manquant') ,true,1,0));
}

$tpl->traduit("_ROOT.titre_popup","maintenance" );


$tpl->gotoBlock("_ROOT");

if ($CFG->utiliser_editeur_html) {
	$tpl->assign ("_ROOT.active_html_editor",use_html_editor('message','', '',true) );
} else $tpl->assign ("_ROOT.active_html_editor","");

$tpl->printToScreen();										//affichage
?>

