<?php


/**
 * @author Patrick Pollet
 * @version $Id: choix_purger.php 715 2009-04-20 11:27:24Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */


////////////////////////////////
//
//	Page de modification d'un champ de configuration
//
////////////////////////////////
/*----------------REVISIONS----------------------
v 1.5 : PP 24/02/2009
  template et action.php rappatrié ici

-----------------------------------------------*/

$chemin = '../../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");					//fichier de paramètres

require_login('P'); //PP
$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT);
v_d_o_d("config");




$elt=optional_param("elt","date_t",PARAM_ALPHAEXT);  //lettres et _ (nom de l'item a modifier)
$doit=optional_param("doit","",PARAM_INT);  //(validation ?)

//validation  (ancien code de action.php)
if (!empty($doit)) {
    //problème avec les valeurs vides ....
    $valeur=required_param("date_debut",PARAM_CLEAN);  //valeur de l'item

    $debut=mon_strtotime($valeur);


  //  print($valeur. " ".$debut); die();
    if ( is_admin()){
      $sql = "delete from {$CFG->prefix}tracking where date<='$debut'";
      $res = ExecRequete ($sql);
}
else
    erreur_fatale("err_droits");    //////////////

   ferme_popup("",false); //pas besoin de rafraichir
}


require_once( $chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates

$tpl = new C2IMiniPopup( );	//créer une instance

//inclure d'autre block de templates
$fiche=<<<EOF

<form  class="normale" action="choix_purger2.php" name="form" method="post">
<table >
    <tr>
        <td width="19" class="gauche"><img
            src="{chemin_images}/ii_config.gif" alt="configuration"
            width="19" height="19" /></td>
        <td class="gauche">{etiquette}</td>
    </tr>
    <tr>

        <td class="gauche" colspan="2">
<p class="double">
   <label for="f_date_debut">{t_date} : </label>

 <input type="text" name="date_debut" id="f_date_debut" size="30" readonly="readonly" value="{date_debut}" />
 <img src="{chemin_images}/calendar.gif" id="f_trigger_debut" style="cursor: pointer; border: 1px solid red;" title="{alt_choix_date_debut}"
      onmouseover="this.style.background='red';" onmouseout="this.style.background=''" alt=""/>

<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_debut",     // id of the input field
        ifFormat       :    "{jscalendar_if}",      // format of the input field
        date         :"{date_debut}",
          showsTime      :    true,            // will display a time selector
        button         :    "f_trigger_debut",  // trigger for the calendar (button ID)
        align          :    "Tl",           // alignment (defaults to "Bl")
        singleClick    :    false,
         weekNumbers    : true       // Show week numbers
    });
</script>
</p>
       </td>
    </tr>
</table>
<div class="centre">

<input name="annuler" type="button" class="saisie_bouton" id="annuler"
    onclick="window.close();" value="{bouton_annuler}" />
&nbsp;

<input type="submit" value="{bouton_envoyer}"  class="saisie_bouton" />
<input name="elt" type="hidden" value="{nom_champ}" />

<!-- START BLOCK : id_session -->
 <input name="{session_nom}"    type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->
 <input name="etablissement"    type="hidden" value="{etablissement}" />
 <input name="doit"    type="hidden" value="1" />
 </div>
</form>
EOF;

$tpl->assignInclude("contenu",$fiche,T_BYVAR);
$tpl->prepare($chemin);

$CFG->utiliser_js_calendar=1;   //forcé (est à 0 dans config pour accelerer les pages)

$tpl->traduit("titre_popup" ,"purger_tracking");


$tpl->gotoBlock("_ROOT");
$tpl->assign("etablissement" ,$ide);

$debut =mktime(0, 0, 0, date("m")-1, date("d"), date("y"));

//dates dans les input fields et dans le calendrier
$dd=strftime(traduction("jscalendar_if"),$debut);

$tpl->assign("date_debut",strftime(traduction("jscalendar_if"),$debut));

//$tpl->assign("etiquette" , ucfirst($textes_langues["purger_tracking_datant"]));
$tpl->traduit("etiquette","purger_tracking_jusquau");




$tpl->printToScreen();										//affichage
?>