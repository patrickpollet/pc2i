<?php
/**
 * @author Patrick Pollet
 * @version $Id: remontee_examens.php 1231 2011-03-25 15:24:21Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres
require_once($chemin_commun."/lib_sync.php");

require_login("P"); //PP
if (!is_admin(false,$CFG->universite_serveur)) erreur_fatale("err_acces");

 set_time_limit(0); //important

$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT); //�tab de l'examen, d�faut = ici '
$login_nat=optional_param("login_nat","",PARAM_RAW);
$pass_nat=optional_param("pass_nat","",PARAM_RAW);
$test=optional_param("test",0,PARAM_INT);
$a_envoyer=optional_param("examens",array(),PARAM_RAW);

//v_d_o_d("config"); //apres lecture $ide

require_once( $chemin."/templates/class.TemplatePower.inc.php");    //inclusion de moteur de templates
$tpl = new C2IPopup(  );	//cr�er une instance
//inclure d'autre block de templates

$forme1=<<<EOF

<div id="maj_ajax">
<form class="normale" id="monform" method="post" action="remontee_examens.php">
<fieldset>
<legend>{remontee_examens} </legend>

<div class="commentaire1">{info_remontees_examens} </div>
<p class="double">
<label for="login_nat">{identifiant_national} </label>
<input type="text" name="login_nat" id="login_nat" value="" size="20"/>
</p>
<p class="double">
<label for="pass_nat">{mot_de_passe_national}  </label>
<input type="password" name="pass_nat" id="pass_nat" value="" size="20"/>
</p>
<p class="double">
<label for="test"> {sync_0}</label>
<input type ="checkbox" value="1" name="test" id="test" checked="checked" />
</p>

<input name="ide" type="hidden" value="{ide}"/>

<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}"/>
<!-- END BLOCK : id_session -->

<table width="100%" class="listing" id="sortable" >
  <thead>
    <tr {bulle:astuce:msg_tri_colonnes}>
      <th  class="bg"> {t_id} </th>

      <th  class="bg"> {t_titre}</th>
      <th  class="bg"> {t_auteur}</th>
          <th  class="bg"> {t_date}</th>
    <th  class="bg"> {t_nb}</th>
        <th  class="bg"> {t_type}</th>
      <th  class="bg nosort"> {t_envoyer}</th>
  </tr>
</thead>
<tfoot>
<tr>
<td colspan="7" > {nb} {examens} </td>
</tr>
</tfoot>
<tbody>

<!-- START BLOCK : ligne_q -->
<tr class="{paire_impaire}">
<td>{id}</td>
<td>{libelle} <ul style="display:inline;">{consulter_fiche} </ul></td>
<td>{auteur}</td>
<td>{dated}</td>
<td>{nbi}</td>

<td>{pf}</td>

<td>
<input type ="checkbox" value="1" name="examens[{id}]" checked="checked" />
</td>

</tr>
<!-- END BLOCK : ligne_q -->

<!-- START BLOCK : no_results -->
<tr class="information">
<td colspan ="7">
		{msg_pas_de_examen}
</td>
</tr>
<!-- END BLOCK : no_results -->
</tbody>

</table>

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

 	$c2i=connect_to_nationale();
    try {
        $lr=$c2i->login($login_nat,$pass_nat);
        $tpl->assignInclude("corps",$forme2,T_BYVAR);
        $tpl->prepare($chemin);
        $resultats=envoi_mes_examens($c2i,$lr,$ide,$test,$a_envoyer);
        $c2i->logout($lr->getClient(),$lr->getSessionKey());
        set_ok(traduction("info_deconnecte_nationale",false,$CFG->adresse_pl_nationale),$resultats);
        if (count($resultats))
        $tpl->assign("resultats_op",print_details($resultats));
        else $tpl->assign("resultats_op","");

$tpl->assign("_ROOT.titre_popup",traduction("remontee_examens"));

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

$tpl->assign("_ROOT.titre_popup",traduction("remontee_examens"));
$tpl->assign("ide", $ide);

$CFG->utiliser_tables_sortables_js=1;

$examens=get_examens_locaux($ide,false);

$compteur_ligne = 0;
foreach ($examens as $e) {
	$cptp = compte_passages($e->id_examen, $e->id_etab);
	if (!$cptp) continue; //aucun passage
	$tpl->newBlock("ligne_q");
	$tpl->setCouleurLigne($compteur_ligne);
	$tpl->assign("id",$e->id);
	$tpl->assign ("libelle",clean($e->nom_examen,70));
	$tpl->assign ("auteur", cree_lien_mailto($e->auteur_mail,$e->auteur));
	$tpl->assign("dated",userdate($e->ts_datedebut,'strftimedatetimeshort'));
	$tpl->assign("nbi",$cptp);


	$type="";
	if ($e->certification=="OUI") $type.="C";
	if ($e->positionnement=="OUI") $type.="P";
	$tpl->assign("pf",$type);
	print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../examens/fiche.php?idq=".$e->id_examen."&amp;ide=".$e->id_etab));
	$compteur_ligne ++;
}

$tpl->gotoBlock("_ROOT");

$tpl->assign("nb",$compteur_ligne);
if ($compteur_ligne==0)
	$tpl->newBlock("no_results");
$tpl->print_boutons_fermeture();


$tpl->printToScreen();										//affichage
?>

