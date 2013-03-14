<?php
/**
 * @author Patrick Pollet
 * @version $Id: synchro_nationale.php 1224 2011-03-16 11:53:54Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";

require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres
require_once($chemin_commun."/lib_sync.php");

require_login("P"); //PP




 set_time_limit(0); //important

$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT); //�tab de l'examen, d�faut = ici '

$login_nat=optional_param("login_nat","",PARAM_RAW);
$pass_nat=optional_param("pass_nat","",PARAM_RAW);

$options=optional_param("option",array(),PARAM_RAW);

v_d_o_d("config"); //apres lecture $ide

require_once( $chemin."/templates/class.TemplatePower.inc.php");    //inclusion de moteur de templates
$tpl = new C2IPopup(  );	//cr�er une instance
//inclure d'autre block de templates

$forme1=<<<EOF

<div id="maj_ajax">
<form class="normale" id="monform" method="post" action="synchro_nationale.php">
<fieldset>
<legend>{synchronisation_plateforme} </legend>

<div class="commentaire1">{info_synchronisation_plateforme} </div>
<p class="double">
<label for="login_nat">{identifiant_national} </label>
<input type="text" name="login_nat" id="login_nat" value="" size="20"/>
</p>
<p class="double">
<label for="pass_nat">{mot_de_passe_national}  </label>
<input type="password" name="pass_nat" id="pass_nat" value="" size="20"/>
</p>


<input name="ide" type="hidden" value="{ide}"/>
<input type="hidden" name="retour_fiche" value="{retour_fiche}" />
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->

<!-- START BLOCK : option -->
<p class="double">
<label for="option_{num}">{val_option} </label>
<input type ="checkbox" value="1" name="option[{num}]" {checked} id="option_{num}" />
</p>

<!-- END BLOCK : option -->

<div class="centre">

{bouton:ok}
</div>

</fieldset>

</form>
</div>

EOF;

$forme2=<<<EOF
{resultats_op}
EOF;



if ($login_nat && $pass_nat) {

    require_once ($chemin."/commun/lib_rapport.php");
//print_r($options); //die();
   $c2i=connect_to_nationale();
    try {
        $lr=$c2i->login($login_nat,$pass_nat);
       // print_r($lr);
        $tpl->assignInclude("corps",$forme2,T_BYVAR);
        $tpl->prepare($chemin);
        $resultats=synchro_nationale($c2i,$lr,$options);
        $c2i->logout($lr->getClient(),$lr->getSessionKey());
        set_ok(traduction("info_deconnecte_nationale",false,$CFG->adresse_pl_nationale),$resultats);
        if (count($resultats))
        $tpl->assign("resultats_op",print_details($resultats));
        else $tpl->assign("resultats_op","");

$tpl->assign("_ROOT.titre_popup",traduction("synchronisation_plateforme"));

$tpl->gotoBlock("_ROOT");
$tpl->print_boutons_fermeture();
        $tpl->printToScreen();
        die();
    } catch (Exception $e) {
        print_r($e);
    }
}




$tpl->assignInclude("corps",$forme1,T_BYVAR);
$tpl->prepare($chemin);

$tpl->assign("_ROOT.titre_popup",traduction("synchronisation_plateforme"));
$tpl->assign("ide", $ide);

// rev 977 le referentile  peut encore �voluer

$tpl->newBlock("option");
$tpl->assign ("num",0);
$tpl->assign ("val_option",traduction("sync_0"));
$tpl->setChecked (true,"checked");
    
$tpl->newBlock("option");
$tpl->assign ("num",6);
$tpl->assign ("val_option",traduction("sync_6"));
$tpl->setChecked (true,"checked");

for ($i=1; $i <=15; $i++) {
  if ($i==6) continue; //synchro referentiel d�ja affich�
  if ($i==3 && ! $CFG->utiliser_notions_parcours) continue;  //cette PF n'utilise pas les notions
  if (est_traduite("sync_$i")) {
    $tpl->newBlock("option");
    $tpl->assign ("num",$i);
    $tpl->assign ("val_option",traduction("sync_".$i));
    $tpl->setChecked (true,"checked");
}

}


$tpl->gotoBlock("_ROOT");
$tpl->print_boutons_fermeture();


$tpl->printToScreen();										//affichage
?>

