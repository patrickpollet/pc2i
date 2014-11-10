<?php


/**
 * @author Patrick Pollet
 * @version $Id: fiche.php 1231 2011-03-25 15:24:21Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
////////////////////////////////
//
//	Fiche d'etablissemnt
//
////////////////////////////////

$fiche =<<<EOF
<table class="fiche">
        <tbody>
          <tr>
            <th>{form_libelle}</th>
            <td width="50%">{nom_etab}</td>
          </tr>
          <tr>
            <th>{form_type_p}</th>
            <td>
            	<img src="{chemin_images}/case{ch_p}.gif" alt=""/>{positionnement}
            	&nbsp;
     			<img src="{chemin_images}/case{ch_c}.gif" alt=""/>{certification}
     			&nbsp;
     			<img src="{chemin_images}/case{ch_a}.gif" alt=""/>{anonyme}
            </td>
		  </tr>

		  <tr>
            <th>{form_nat_loc}</th>
            <td>
            <img src="{chemin_images}/case{ch_l}.gif" alt=""/>{p_locale}
            &nbsp;
            <img src="{chemin_images}/case{ch_n}.gif" alt=""/>{p_nationale}
            </td>
		  </tr>
		  <tr>
            <th>{form_nb_telec}</th>
            <td>{nb_telechargements}</td>
		  </tr>
		  <tr>
            <th>{form_nb_items}</th>
            <td>{param_nb_items}</td>
		  </tr>
		  <tr>
            <th>{form_nb_aleatoire}</th>
            <td>{param_nb_aleatoire}</td>
		  </tr>
		  <tr>
            <th>{form_nb_experts}</th>
            <td>{param_nb_experts}</td>
		  </tr>
		  <tr>
            <th>{form_langue}</th>
            <td>{param_langue}</td>
		  </tr>
<!--  rev 928 vir�
		    <tr>
            <th>{form_nbqar}</th>
            <td>{nbqar}</td>
          </tr>
-->
		    <tr>
            <th>{form_nbcandidats}</th>
            <td>{nb_candidats} <ul style="display:inline;"> {consulter_candidats} </ul></td>
          </tr>
            <tr>
            <th>{form_nbpersonnels}</th>
            <td>{nb_personnels} <ul style="display:inline;"> {consulter_personnels}</ul> </td>
          </tr>
            <tr>
            <th>{form_nbexams}</th>
            <td>
            {positionnement} : {nb_examens_p} <ul style="display:inline;"> {consulter_examens_p} </ul><br/>
            {certification} : {nb_examens_c} <ul style="display:inline;">{consulter_examens_c} </ul>
            </td>
          </tr>
            <tr>
            <th>{form_nbquestions}</th>
            <td>{nb_questions} <ul style="display:inline;">{consulter_questions}</ul> </td>
          </tr>
             <tr>
            <th>{form_parents}</th>
            <td>
<!-- START BLOCK : parent -->
                <a href="{url}">{nom} </a><br/>
<!-- END BLOCK : parent -->
            </td>
          </tr>


             <tr>
            <th>{form_composantes}</th>
            <td>
<!-- START BLOCK : composante -->
				<a href="{url}">{nom} </a><br/>
<!-- END BLOCK : composante -->
            </td>
          </tr>
        </tbody>
      </table>
      <br/>
 {bouton_fermer}


EOF;

$chemin = '../../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

$idq = required_param("idq", PARAM_INT);
$retour = optional_param("retour","", PARAM_INT);

require_login('P'); //PP
v_d_o_d("config");

$ligne = get_etablissement($idq, true);

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates
$tpl->assignInclude("corps", $fiche, T_BYVAR); // le template g�rant la fiche de l'etablissement

$tpl->prepare($chemin);

if ($ligne->pere==1)
$tpl->assign("_ROOT.titre_popup", traduction("fiche_etablissement") . " " . $idq."<br/>".$ligne->nom_etab);
else
$tpl->assign("_ROOT.titre_popup", traduction("fiche_composante") . " " . $idq."<br/>".$ligne->nom_etab);


$tpl->assign("ch_p", $ligne->positionnement); unset($ligne->positionnement);
$tpl->assign("ch_c", $ligne->certification);unset($ligne->certification);
$tpl->assign("ch_l", $ligne->locale);
$tpl->assign("ch_n", $ligne->nationale);

$tpl->assignObjet($ligne);


if (get_examen_anonyme()) $tpl->__assign("ch_a","1");
else  $tpl->__assign("ch_a","0");

$nb=count_records("inscrits",'etablissement='.$idq);
$tpl->assign("nb_candidats",$nb);
if ($nb)
  print_menu_item($tpl,"consulter_candidats",get_menu_item_consulter("../etudiant/liste.php?idq=".$idq));
else
  $tpl->assign('consulter_candidats','<li class="menu_niveau2_item"></li>');

$nb=count_records("utilisateurs",'etablissement='.$idq);
$tpl->assign("nb_personnels",$nb);
if ($nb)
  print_menu_item($tpl,"consulter_personnels",get_menu_item_consulter("../personnel/liste.php?idq=".$idq));
else
  $tpl->assign('consulter_personnels','<li class="menu_niveau2_item"></li>');



$nb=count_records("examens",'id_etab='.$idq." and positionnement='OUI'");
$tpl->assign("nb_examens_p",$nb);
if ($nb)
   $tpl->assign('consulter_examens_p','<li class="menu_niveau2_item"></li>');
  //TODO
else
  $tpl->assign('consulter_examens_p','<li class="menu_niveau2_item"></li>');


$nb=count_records("examens",'id_etab='.$idq." and certification='OUI'");
$tpl->assign("nb_examens_c",$nb);
if ($nb)
   $tpl->assign('consulter_examens_c','<li class="menu_niveau2_item"></li>');
  //TODO
else
  $tpl->assign('consulter_examens_c','<li class="menu_niveau2_item"></li>');


$nb=count_records("questions",'id_etab='.$idq);
$tpl->assign("nb_questions",$nb);

if ($nb)
  print_menu_item($tpl,"consulter_questions",get_menu_item_consulter("../../questions/export_questions.php?ide=".$idq));
else
  $tpl->assign('consulter_questions','<li class="menu_niveau2_item"></li>');



$parents=get_parents($idq);
foreach($parents as $comp) {
    $tpl->newBlock("parent");
    $tpl->assign("nom",$comp->nom_etab);
    $tpl->assignURL("url","fiche.php?idq=".$comp->id_etab."&amp;retour=1");
     // print_menu_item($tpl,"url",get_menu_item_consulter("fiche.php?idq=".$comp->id_etab."&retour=1"));
}

$comps=get_composantes($idq);
foreach($comps as $comp) {
	$tpl->newBlock("composante");
	$tpl->assign("nom",$comp->nom_etab);
	$tpl->assignURL("url","fiche.php?idq=".$comp->id_etab."&amp;retour=1");
}

$tpl->gotoBlock("_ROOT");


if ($retour)
 $tpl->assign("bouton_fermer",get_bouton_action("retour_pere","javascript:history.back();"));
else
    $tpl->assign("bouton_fermer","");

$tpl->print_boutons_fermeture();


$tpl->printToScreen(); //affichage
?>

