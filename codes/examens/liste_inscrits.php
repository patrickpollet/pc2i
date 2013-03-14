<?php

/**
 * @author Patrick Pollet
 * @version $Id: liste_inscrits.php 1231 2011-03-25 15:24:21Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
////////////////////////////////
//
//      Liste des inscrits a un examen
////////////////////////////////
/**
 * rev 1.41
 * deplacée de codes/acces/etudiants a codes/examen
 * donne la liste des absents si $type=1
 * utilse la globale $CFG->csv_sep
 * affiche et ajoute le mail au CSV (+type d'authentification)')
 */
set_time_limit(0);
/*
* Pour la description des différentes méthodes de la classe TemplatePower,
* il faut se reférer à http://templatepower.codocad.com/
*/
//******** Pour chaque page $chemin représente le path(chemin) de script dans le site (à la racine)
//******** ---------------- $chemin_commun représente le path des utilitaires dont on aura besoin
//******** ---------------- $chemin_images représente le path des images
$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de paramètres
require_once($CFG->chemin_commun.'/lib_csv.php');
require_once($CFG->chemin_commun."/lib_resultats.php");
require_login('P'); //PP



$idq=required_param("idq",PARAM_INT,"");
$ide=required_param("ide",PARAM_INT,"");
// rev 1.41 type de liste_inscrits  0 tous 1 absents 2 presents
$type=optional_param("type",0,PARAM_INT);
$retour_fiche=optional_param("retour_fiche","0",PARAM_INT);

// rev 982 et suivante simplifiaction des liens emis sur les icones d'action'
$action=optional_param('action','',PARAM_ALPHA);
if ($action) {
	//print_r($_POST);
	$id_action=required_param('id_action',PARAM_CLEAN); // atention c'est un login '
}

v_d_o_d("etl"); // droits apres lecture $ide



$ligne=get_examen($idq,$ide);
$nom_examen =  nom_complet_examen($ligne);


$modele=<<<EOM

<!-- START BLOCK : rafraichi_liste -->
<script type="text/javascript">
        if (window.opener)
            window.opener.location.href='liste.php?{url_retour}';
</script>

<!-- END BLOCK : rafraichi_liste -->

<!-- INCLUDESCRIPT BLOCK : ./actions_js_liste_inscrits.php -->

 <div id="menu2">
{menu_niveau2}
</div>
<div id="infos">
{nb}
</div>

<table width="100%" class="listing" id="sortable"  >
  <thead>
    <tr {bulle:astuce:msg_tri_colonnes}>
      <th  class="bg"> {t_id} </th>
      <th    class="bg"> {t_mdp}</th>
      <th    class="bg" width="150"> {t_nom}</th>
      <th    class="bg"> {t_prenom}</th>
      <th   class="bg"> {t_numetud} </th>
      <th   class="bg">  {t_mail}  </th>
    <!--  <th   class="bg">  {t_examen}  </th> -->
      <th   class="bg">  {t_score}  </th>
       <th   class="bg">  {t_date}  </th>
        <th   class="bg">  {t_ip}  </th>
        <th class="bg nosort" style="width:30px;" > {t_action} </th>
    </tr>
</thead>
  <tfoot>
  <tr>
  <td colspan="10"> {nb} {inscrits}</td>
  </tr>
</tfoot>
<tbody>
      <!-- START BLOCK : etud -->
    <tr  class="{paire_impaire}">

      <td>{login}</td>
      <td>{password}</td>
      <td>{nom}</td>
      <td>{prenom}</td>
      <td>{numetudiant}</td>
      <td>{email}</td>
      <!-- <td>{examen}</td> -->
      <td>{score}</td>
       <td>{date}</td>
        <td>{ip_max}</td>
        <td>{icones_actions} </td>
    </tr>
    <!-- END BLOCK : etud -->
</tbody>

</table>
{form_actions}

EOM;

// 1ere ligne csv a traduire
$entete_csv=array("t_examen","t_id","t_nom","t_prenom","t_mdp","t_numetud","t_mail","t_score","t_date","t_ip");
// ligne suivantes les noms des attributs dans $ligne dans cet ordre
$ligne_csv=array("examen","login","nom","prenom","password","numetudiant","email","score","date","ip_max");
// rev 948 conversion numérique du score
$ligne_cvt=array(false,   false, false, false,  false,     false,        false,  true    ,false  , false); //conversion point virgule pour OO

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //créer une instance
//inclure d'autre block de templates


//pas de multipagination car on génére aussi un  csv
$CFG->utiliser_tables_sortables_js=1;

$options=array (
	"liste"=>1,
	"corps_byvar"=>$modele
);

$tpl->prepare($chemin,$options);

add_javascript($tpl,$CFG->chemin_commun."/js/sprintf.js");


//construction url_retour pour desinsription
$url_retour="idq=$idq&amp;ide=$ide&amp;type=$type&amp;retour_fiche=$retour_fiche";
print_form_actions ($tpl,'form_actions',$url_retour,'liste_inscrits.php');

//rev 936
//$id_supp=optional_param("id_supp","",PARAM_RAW);
//if ($id_supp) {

if ($action=='supprimer'){ 
     desinscrit_candidat($idq,$ide,$id_action);
     $tpl->newBlock("rafraichi_liste"); // rev 937
}


// rev. 1.41 $CFG->chemin_ressources contient le chemin complet et a été vérifié (cf lib_fichiers.php)
if ($type == 0) {
	$filename="inscrits_".$ide."_".$idq.".csv" ;
	$filename_ods="inscrits_".$ide."_".$idq.".ods" ;
	$feuille="inscrits";
}
else {
	$filename="absents_".$ide."_".$idq.".csv" ;
	$filename_ods="absents_".$ide."_".$idq.".ods" ;
	$feuille="absents";
}



$mycsv=new CsvExporter($filename,$entete_csv,$ligne_csv,$ligne_cvt);

if ($CFG->export_ods) {
		require_once($CFG->chemin_commun.'/lib_OOo.php');
        $myods= new OdsExporter ($filename_ods,$feuille,$entete_csv,$ligne_csv,$ligne_cvt);
}

    // liste des inscrits à l'examen

	$lignes=get_inscrits($idq,$ide,'nom,prenom');
 	$compteur_ligne= 0;
 	$nb_inscrits=count($lignes);
 	$cle=$ide."_".$idq; //ajoutée au csv
    foreach($lignes as $ligne) {             // V 1.41
        $cp = compte_passages($idq, $ide, $ligne->login);
        if ($type == 1 && $cp > 0)
            continue;
        $tpl->newBlock("etud");
       $tpl->setCouleurLigne($compteur_ligne);

        if ($ligne->auth=='manuel') {
            // rev 944 ne pas montrer les pwd meme md5 des utilisateurs personnels
            if (! $CFG->montrer_password_inscrits || $ligne->genre=='P')
    			$ligne->password='********';
        } else $ligne->password= $ligne->auth;

         if ($res=get_resultats_examen($idq,$ide,$ligne->login)){
         	$ligne->score=$res->score;
           // $ligne->examen=$res->examen;
         	$ligne->ip_max=$res->ip_max;
            //retouche origine notes écran et csv 21/05/2009
            if ($res->origine) {
                  $ligne->ip_max=$res->origine;
            }
         	$ligne->date=userdate($res->date,'strftimedatetimeshort');

         }else {
         	$ligne->score=$ligne->date=$ligne->ip_max=""; //$ligne->examen="";
         }
           $tpl->assignObjet($ligne);
         //lien  mailto a cliquer
         $tpl->assign("email",cree_lien_mailto($ligne->email,$ligne->email));

		$ligne->examen=$cle;
        $mycsv->add_ligne($ligne);

        if ($CFG->export_ods) {
            $myods->add_ligne($ligne);
        }
	             // rev 936  popup de consultation ou desinscription
/*****
        $tpl->newBlockNum("icones_action_liste",$compteur_ligne);
        if ($ligne->score) {
            $tpl->newblockNum("td_consulter_oui",$compteur_ligne);
            $tpl->assignURL("url_consulter","resultats/reponse_par_etudiant2.php?idq=".$idq."&amp;ide=".$ide."&amp;id_us=".$ligne->login."&amp;retour_fiche=0");
        } else {
            $tpl->newblockNum("td_supprimer_oui",$compteur_ligne);
            $tpl->assignURL("url_supprimer","liste_inscrits.php?idq=".$idq."&amp;ide=".$ide."&amp;id_supp=".$ligne->login."&amp;type=".$type."&amp;retour_fiche=".$retour_fiche);
            $tpl->traduit("alt_supprimer","alt_desinscrire");
            $tpl->assign("js_supp", traduction("js_desinscrire_0",false,$ligne->login));
        }
***/
		   // debut code revisé theme v15
    $items=array();
   
     if ($ligne->score) {
     	 	$items[]=new icone_action('consulter',"consulterItem('{$ligne->login}',$idq,$ide)");  
     } else {
     		 $items[]=new icone_action('supprimer',"supprimerItem('{$ligne->login}')",'menu_vide',traduction('alt_desinscrire'));  
     }	
    //  $tpl->newBlock ('icones_actions');
    print_icones_action($tpl,'icones_actions',$items,'actions_'.$compteur_ligne);

        $compteur_ligne++;
    }
$tpl->assign("_ROOT.nb",$compteur_ligne."/".$nb_inscrits);
$mycsv->close();

if ($CFG->export_ods) {
	$filename_ods=$myods->close(); // fait le zip et dire ou il l'a mis
}

    switch ($type) {
            case 0: $ch= $compteur_ligne ." ".traduction("passages")." / ".$nb_inscrits." ".traduction("inscrits"); break;
            case 1: $ch= $compteur_ligne ." ".traduction("non_passes")." / ".$nb_inscrits." ".traduction("inscrits"); break;

            default: $ch= $nb_inscrits." ".traduction("inscrits"); break;

    }
     $tpl->assignGlobal('nb_passages',$ch);


$tpl->gotoBlock("_ROOT");


if ($type == 0) {
   $tpl->assign("_ROOT.titre_popup",$compteur_ligne. " ".traduction("liste_etudiants_inscrits") ."<br/>". $nom_examen);
} else {
    $tpl->assign("_ROOT.titre_popup",$compteur_ligne."/".$nb_inscrits." ".traduction("liste_etudiants_absents") ."<br/>". $nom_examen);

}

$items=array();

// v 1.5 menu de niveau 2 standard (cf weblib)

$items[]=get_menu_item_csv($filename);
if ($CFG->export_ods)
	$items[]=get_menu_item_ods($filename_ods,'tmp/ods');
$items[]=get_menu_item_imprimer();

print_menu($tpl,"_ROOT.menu_niveau2",$items);

$url_retour=($retour_fiche? $CFG->chemin."/codes/examens/fiche.php?ide=" . $ide . "&amp;idq=" . $idq."#inscriptions": "");
$tpl->print_boutons_fermeture($url_retour);


$tpl->printToScreen(); //affichage
?>
