<?php
/**
 * @author Patrick Pollet
 * @version $Id: config_avancee.php 715 2009-04-20 11:27:24Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */


//ini_set('error_reporting',0);

$chemin = '../..';
$chemin_commun = $chemin."/commun";

require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres

require_login("P"); //PP
if (!  is_admin (false,$CFG->universite_serveur))
            erreur_fatale("err_droits");





require_once( $chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates
require_once($CFG->chemin_commun."/pear/HTML_TreeMenu/TreeMenu.php");

$tpl = new C2IPopup(  );	//cr�er une instance
//inclure d'autre block de templates

$liste=<<<EOL

 <div class="information">
     {msg_info_configuration_avancee2}
 </div>
 {resultats_op}
 <form name="monform" id="monform" method="post" action="config_avancee2.php">
<div class="gauche">
<input type="hidden" name="doit" value="1" />
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}"/>
<!-- END BLOCK : id_session -->
{ici}
</div>
<div class="centre">
{bouton:annuler} {bouton:enregistrer}
</div>
</form>
EOL;

$tpl->assignInclude("corps",$liste,T_BYVAR);	// le template g�rant la liste des qcms


$options=array (
    "corps_byvar"=>$liste
);

$tpl->prepare($chemin,$options);

$tpl->assign("resultats_op","");
$doit=optional_param("doit",0,PARAM_INT);
if ($doit) {
    require_once($CFG->chemin."/commun/lib_rapport.php");
      $resultats=array(); //�tats des op�rations
    $cles=required_param("cles",PARAM_RAW);
    $olds=required_param("olds",PARAM_RAW);
    //print_r($cles);
    //print_r($olds);
    foreach ($cles as $key=>$value) {
        if ($CFG->$key !=$value) {
        //print ("$key chang�e de {$CFG->$key} en $value <br/>");
         set_ok ("$key changée de {$CFG->$key} en $value",$resultats);
        set_config('',$key,$value);
        }
    }
    if (count($resultats))
        $tpl->assign("resultats_op",print_details($resultats));

}



$CFG->utiliser_validation_js=1;
add_javascript($tpl,$CFG->chemin_commun."/pear//HTML_TreeMenu/TreeMenu.js");


$tpl->traduit("_ROOT.titre_popup","configuration_avancee" );

$menu=config_en_menu();
$treeMenu = & new HTML_TreeMenu_DHTML($menu, array('images' =>$CFG->chemin_theme . "/images/treemenu",
 'defaultClass' => 'treeMenuDefault'));

 $tpl->assign("ici",$treeMenu->toHTML());


$tpl->gotoBlock("_ROOT");

//$items=array();

//$items[]=get_menu_item_legende("config_avancee");

//print_menu($tpl,"_ROOT.menu_niveau2",$items);

//print_bouton_fermer($tpl);
$tpl->print_boutons_fermeture();
$tpl->printToScreen();


									//affichage
?>

