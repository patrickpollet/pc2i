<?php
/**
 * @author Patrick Pollet
 * @version $Id: maintenance.php 621 2009-04-02 17:31:40Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";

require_once($chemin_commun."/c2i_params.php");					//fichier de paramètres
require_once($chemin_commun."/lib_cron.php");
require_login("P"); //PP
v_d_o_d("config");

require_once( $chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates

$tpl = new C2IPopup(  );	//créer une instance
//inclure d'autre block de templates

$forme=<<<EOL
{menu_niveau2}

<form action="maintenance.php" method="post">
<fieldset>
<legend>{base_de_donnees} </legend>
<label for="purger_candidats">{purger_candidats}</label>
<input type="checkbox" value="1" checked name= "purger_candidats" /> <br/>
<label for="purger_ex_nat">{purger_ex_nat}</label>
<input type="checkbox" value="1" checked name= "purger_ex_nat" /> <br/>
<label for="reparer_xmls">{reparer_xmls}</label>
<input type="checkbox" value="1" checked name= "reparer_xmls" /> <br/>

</fieldset>

<fieldset>
<legend>{ldap} </legend>
<label for="purger_non_ldap">{purger_non_ldap}</label>
<input type="checkbox" value="1" checked name= "purger_non_ldap" /> <br/>
<label for="synchro_ldap">{synchro_ldap}</label>
<input type="checkbox" value="1" checked name= "synchro_ldap" /> <br/>

</fieldset>

<input type="hidden"  name="go" value="1"  />


{bouton:annuler}&nbsp;{bouton:ok}
	</form>
EOL;


//template d'affichage du résultat de l'opération
$fiche_reponse=<<<EOF
<div class="information">{resultats}
<br/>
<div>
<br/>
<!-- START BLOCK : oks -->
<label for "oks">{succes}</label> : <br/>
<textarea id="oks"  rows="6" cols="50" readonly>
{details}
</textarea>
<!-- END BLOCK : oks -->
<!-- START BLOCK : kos -->
<label for "kos">{echecs}</label> : <br/>
<textarea id="kos"  rows="6" cols="50" readonly>
{details}
</textarea>
<!-- END BLOCK : kos -->
</div>
</div>

EOF;


$tpl->assignInclude("corps",$forme,T_BYVAR);
$tpl->prepare($chemin);


$tpl->traduit("_ROOT.titre_popup","maintenance" );




$tpl->gotoBlock("_ROOT");

$items=array();

$items[]=get_menu_item_legende("maintenance");

print_menu($tpl,"_ROOT.menu_niveau2",$items);

$tpl->print_boutons_fermeture();
$tpl->printToScreen();										//affichage
?>

