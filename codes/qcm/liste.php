<?php

/**
 * @version $Id: liste.php 1266 2011-09-20 13:40:42Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
////////////////////////////////
//
//	liste des examens actifs pour l'�tudiant connect�
//
////////////////////////////////

/*----------------REVISIONS----------------------
v 1.1 : PP 16/10/2006
      test via require_login("E")
      pas de loupe  si termin�
      Dans le cas d'un ENT, les liens Retour et Quitter n'ont pas de raison d'�tre ...
------------------------------------------------*/

$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";

require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

require_login("E"); //PP



//$indice_deb=optional_param("indice_deb",0,PARAM_INT);
$tri=optional_param("tri","",PARAM_INT);  //critere de tri par d�faut

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

$tpl = new C2IPrincipale(); //cr�er une instance
//inclure d'autre block de templates

$liste=<<<EOL


<!-- INCLUDESCRIPT BLOCK : ./actions_js.php -->

<div id="menu2">
{menu_niveau2}
</div>
<div id="infos">
{nb_items}
</div>

<!-- INCLUDE BLOCK : multip_haut -->
      <table id="liste">
        <thead>
          <tr>
            <th    class="bg"><a href="{url_id}" title="{alt_tri}">{t_id}</a>{tri_id}</th>
            <th   class="bg"><a href="{url_titre}" title="{alt_tri}">{t_titre}</a>{tri_titre}</th>
            <th class="bg"> <a href="{url_dateh}" title="{alt_tri}">{t_dateh}</a>{tri_dateh}</th>
            <th class="bg"><a href="{url_auteur}" title="{alt_tri}">{t_auteur}</a>{tri_auteur}</th>
            <th class="bg"> <a href="{url_date}" title="{alt_tri}">{t_date}</a>{tri_date}</th>

 		<th class="bg" style="width:50px;">{t_actions}</th>

</tr>
</thead>
<tbody>

<!-- START BLOCK : question -->
          <tr  class="{paire_impaire}">
            <td>{id}</td>
            <td>{quest}</td>
            <td class="centre">{dateh}<br/><span class="commentaire1">{heures}<br/>
              </span>
            <br/>
            <img src="{chemin_images}/{image_dateh}.gif" alt="{alt_image_dateh}" title="{alt_image_dateh}" width="99" height="17" />
            </td>

            <td>{auteur}</td>
            <td class="centre">{date}
<!-- START BLOCK : image_nouv -->
            <br/>
            <img src="{chemin_images}/nouveau.gif" alt="{alt_nouveau}" title="{alt_nouveau}" width="59" height="17" />
<!-- END BLOCK : image_nouv -->
            </td>
<!-- START BLOCK : icones_actions -->
          <td>
          {icones_actions}
          </td>
<!-- END BLOCK : icones_actions -->
          </tr>
<!-- END BLOCK : question -->



<!-- START BLOCK : no_results -->
<tr class="information">
<td colspan ="{colspan}">
		{msg_pas_de_qcm}
</td>
</tr>

<!-- END BLOCK : no_results -->
</tbody>
</table>

<!-- INCLUDE BLOCK : multip -->

{form_actions}
EOL;


$options=array (
    "liste"=>1,
    "corps_byvar"=>$liste
);

$tpl->prepare($chemin,$options);

/////////////////////////
// affichage du menu menu
print_menu_haut($tpl,"qcm");

$tpl->gotoBlock("_ROOT");


////////////////////////////////////////////////
//affichage des entêtes de colonnes selon droits


require_once ($CFG->chemin_commun . "/trieuse.class.php");
require_once ($CFG->chemin_commun . "/chercheuse.class.php");

////////////////////////////////////////////////////
// crit�res de recherche
//mes qcm dans mon type de plateforme et seulement les normaux et les pools
////////////////////////////////////////////////////
$chaine_critere_recherche = "";
$critere_recherche = "{$USER->type_plateforme}='OUI' and login='".addslashes($USER->id_user)."'";  // rev 984
$critere_recherche.=" and E.id_examen=I.id_examen and E.id_etab=I.id_etab and E.pool_pere=0";


/////////////////////////////////////////////
// crit�res de tri
/////////////////////////////////////////////
$url = "liste.php?";
$url = concatAvecSeparateur($url, $chaine_critere_recherche, "");

//////
// crit�res de tri
/////////////////////////////////////////////

$trieuse = new trieuse($tpl, "", $url, $tri);
$trieuse->addColonne("id", "", "E.id_etab asc,E.id_examen asc", "E.id_etab desc,E.id_examen desc");
$trieuse->addColonne("auteur", "E.auteur");
$trieuse->addColonne("titre", "E.nom_examen");
$trieuse->addColonne("date", "E.ts_datemodification");
$trieuse->addColonne("dateh", "E.ts_datedebut");

$trieuse->setTriDefaut("dateh", false);

$critere_tri = $trieuse->getCritereSQL();

$trieuse->printToScreen();  //entetes
/////////////////////////////////////////
// gestion de la multipagination
/////////////////////////////////////////
//lien de mp : les deux (recherche et tri)

$url_multipagination = concatAvecSeparateur($url, $trieuse->getParametreTri(), "&");



///////////////////////////////////////////////
// gestion des retours vers cette page � partir d'une popup sans perte des crit�res
// en principe jamais
$url_retour = $chaine_critere_recherche;
$url_retour=concatAvecSeparateur($url_retour,"indice_deb=" . $indice_deb,"&");
$url_retour=concatAvecSeparateur($url_retour,"indice_ecart=" . $indice_ecart,"&");
$url_retour=concatAvecSeparateur($url_retour, $trieuse->getParametreTri(),"&");
print_form_actions ($tpl,'form_actions',$url_retour,'liste.php');
$url_retour=urlencode($url_retour);
///////////////////////////////////////////////


// recherche du nombre de lignes
//petit pb afiche le nb de  QCMS auquel il est inscrit (et pas ceux affich�es)
// corrig�  rev 872 (voir apr�s la boucle)
/**
$indice_max =count_records ("examens E,{$CFG->prefix}qcm I",$critere_recherche);
$tpl->assign("_ROOT.nb_items", $indice_max . " " . traduction ("qcms"));
**/

////////////////////////////////////////////////////
// requete de s�lection des examens � afficher
////////////////////////////////////////////////////

$lignes=get_records("examens E,{$CFG->prefix}qcm I",$critere_recherche,$critere_tri,$indice_deb,$indice_ecart);


$compteur_ligne = 0;

foreach ($lignes as $ligne) {
	if (compte_passages($ligne->id_examen,$ligne->id_etab,$USER->id_user)==0) {
		$tpl->newBlock("question"); // une ligne d'examen (template inspir� de celui des questions, d'o� le nom)
		$tpl->setCouleurLigne($compteur_ligne);

		$tpl->assign("id", $ligne->id_etab . "." . $ligne->id_examen);
		$tpl->assign("quest", affiche_texte($ligne->nom_examen));

        if ($CFG->afficher_lien_mail_liste_qcm)  // rev 809 KS
            $tpl->assign("auteur", cree_lien_mailto($ligne->auteur_mail,$ligne->auteur));
        else
             $tpl->assign("auteur",$ligne->auteur);
        $tpl->assign("date",userdate($ligne->ts_datecreation,'strftimedateshort'));
        $tpl->assign("dateh",userdate($ligne->ts_datedebut,'strftimedatetimeshort'));
        $tpl->assign("heures",userdate($ligne->ts_datefin,'strftimedatetimeshort'));

      //icone etat de passage
        $etat_date_passage=image_etat_examen ($ligne);

//comme on n'affiche pas les pool fils il faut ajuster l'icone si un fils est potentiellement 'passable

		if (($ligne->est_pool == 1) && ($etat_date_passage != "en_cours")) {
			$groupes = liste_groupe_pool($ligne->id_examen, $ligne->id_etab);
			foreach ($groupes as $groupe)
						if (examen_en_cours($groupe)) {
							$etat_date_passage = "en_cours";
							break; // c'est bon il y en a au moins un '
						}
		}

		$tpl->assign("image_dateh", "e_" . $etat_date_passage);
		$tpl->assign("alt_image_dateh", traduction($etat_date_passage));

        // examen r�cent
        if ($USER->derniere_connexion <= $ligne->ts_datemodification)
            $tpl->newBlock("image_nouv");

 $items=array();
 if ($etat_date_passage == "en_cours") {
    $items[]=new icone_action('consulter',"consulterItem({$ligne->id_examen},{$ligne->id_etab})",
     'menu_vide',traduction( 'alt_passerqcm'));
 } else  $items[]=new icone_action();

	$tpl->newBlock ('icones_actions');
    print_icones_action($tpl,'icones_actions',$items,'actions_'.$compteur_ligne);


		//end PP


		// passage � la ligne suivante
		$compteur_ligne++;
	}
}

if ($compteur_ligne==0) {
	$tpl->newBlock ("no_results");
	$tpl->assign("colspan",7);

}


$tpl->gotoBlock("_ROOT");
$tpl->assign("_ROOT.nb_items", $compteur_ligne . " " . traduction ("qcms"));
$items=array();

//$items[]=get_menu_item_criteres();
$items[]=get_menu_item_legende("qcm");

print_menu($tpl,"_ROOT.menu_niveau2",$items);



$tpl->printToScreen(); //affichage
?>

