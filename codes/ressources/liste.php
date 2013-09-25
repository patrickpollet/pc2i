<?php

/**
 * @author Patrick Pollet
 * @version $Id: liste.php 1231 2011-03-25 15:24:21Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf

 */


/*----------------REVISIONS----------------------
v 1.5 version exp�rimentale utilisant les sous templates de la V2
------------------------------------------------*/


$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres


 if (!$CFG->utiliser_notions_parcours)
    erreur_fatale("err_pas_de_notions_parcours_ici");

require_login("P");
v_d_o_d("ql");


//parametres attendus

$id_rech=optional_param("id_rech","",PARAM_INT);  //critere de recherche
$titre_rech=optional_param("titre_rech","",PARAM_CLEAN);  //critere de recherche


$referentielc2i = optional_param("referentielc2i", "", PARAM_ALPHANUM);
$alinea = optional_param("alinea", 0, PARAM_INT);


//$indice_deb=optional_param("indice_deb",0,PARAM_INT);
$tri=optional_param("tri","",PARAM_INT);  //critere de tri (defaut ref/alinea)

//$supp_id=optional_param("supp_id","",PARAM_CLEAN); // suppression

// rev 982 et suivante simplifiaction des liens emis sur les icones d'action'
$action=optional_param('action','',PARAM_ALPHA);
if ($action) {
	//print_r($_POST);
	$id_action=required_param('id_action',PARAM_INT);
}

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

$tpl = new C2IPrincipale(); //cr�er une instance
//inclure d'autre block de templates

$liste=<<<EOL
<div id="criteres">
<form id="form_criteres" method="post" action="liste.php">
<div>
  			{t_id} : <input class="saisie" name="id_rech" size="5" value="{id_rech}" />
            |
            {select_referentielc2i} {select_alinea} |
            {t_titre} : <input class="saisie" name="titre_rech" size="20" value="{titre_rech}" />
            {boutons_criteres}

<input name="tri" type="hidden" value="{tri}" />

<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->
</div>
</form>
</div>


<!-- INCLUDESCRIPT BLOCK : ./actions_js.php -->

<!-- emplacement du menu de niveau 2 -->

<div id="menu2">
{menu_niveau2}
</div>
<div id="infos">
{nb_items}
</div>

<!-- INCLUDE BLOCK : multip_haut -->
<table width="100%" id="liste">
        <thead>
            <tr>
            <th class="bg"><a href="{url_id}"     title="{alt_tri}">{t_id}</a>{tri_id}</th>
            <th class="bg"><a href="{url_titre}"  title="{alt_tri}">{t_titre}</a>{tri_titre}</th>
           
            <th class="bg"><a href="{url_referentiel}"  title="{alt_tri}">{t_referentiel}</a>{tri_referentiel}</th>
            <th class="bg"><a href="{url_alinea}" title="{alt_tri}">{t_alinea}</a>{tri_alinea}</th>



  <th class="bg" style="width:150px;">{t_actions}</th>

<!-- START BLOCK : col_c -->
<th class="bg icone_action">{t_consult}</th>
<!-- END BLOCK : col_c -->

<!-- START BLOCK : col_d -->
            <th class="bg icone_action">{t_dupl}</th>
<!-- END BLOCK : col_d -->
<!-- START BLOCK : col_m -->
            <th class="bg icone_action">{t_modif}</th>
<!-- END BLOCK : col_m -->
<!-- START BLOCK : col_s -->
            <th class="bg icone_action">{t_supp}</th>
<!-- END BLOCK : col_s -->

</tr>
</thead>
<tbody>
<!-- START BLOCK : ligne -->
<tr title="{title_id}" class="{paire_impaire}">
    <td>{id}</td>
    <td class="{barree}"><a href="{URL}" target="_BLANK" title="{origine}">{libelle}</a></td>
 
    <td>{ref}</td>
    <td>{alinea}</td>


<!-- START BLOCK : icones_actions -->
          <td>
          {icones_actions}
          </td>
<!-- END BLOCK : icones_actions -->
<!-- INCLUDE BLOCK : icones_action_liste -->
</tr>
<!-- END BLOCK : ligne -->

<!-- START BLOCK : no_results -->
<tr class="information">
<td colspan ="{colspan}">{msg_pas_de_ressources}</td>
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

////////////////////////
// affichage du menu menu

print_menu_haut($tpl,"n");


////////////////////////////////////////////////
//affichage des ent�tes de colonnes selon droits


$colspan=5;

//$tpl->assign('_ROOT.colspan',$colspan);

//$tpl->newblock("col_c");

$peutModifier = a_capacite("qm");
$peutSupprimer =a_capacite("qs");
$peutDupliquer=$CFG->peut_dupliquer_notion && a_capacite("qd");



// si droit de supprimer
if ($peutSupprimer) {
    // v15 les actions sont gerer en js avec un simpl parametre action
    if ($action=="supprimer")
    	supprime_ressource ($id_action);
    //$colspan++;
    if ($action=='filtrer')
        filtre_ressource ($id_action);
    
}


require_once ($CFG->chemin_commun . "/trieuse.class.php");
require_once ($CFG->chemin_commun . "/chercheuse.class.php");
////////////////////////////////////////////////////
// crit�res de recherche
////////////////////////////////////////////////////


//rev 977 en referentiel version 1, ne pas montrer les notions sp�cifiques V2
 $critere_version="domaine !='' and competence !=0";

$critere_recherche=$critere_version;
$chaine_critere_recherche = "";

if ($id_rech) {
	$critere_recherche= concatAvecSeparateur($critere_recherche,"id=" . $id_rech," and ");
	$chaine_critere_recherche = concatAvecSeparateur($chaine_critere_recherche,"id=" . $id_rech,"&amp;");

}


if ($referentielc2i) {
    $critere_recherche = concatAvecSeparateur($critere_recherche, "domaine='" . $referentielc2i . "'", " and ");
    $chaine_critere_recherche = concatAvecSeparateur($chaine_critere_recherche, "domaine=" . $referentielc2i, "&amp;");
}

if ($alinea) {
    $critere_recherche = concatAvecSeparateur($critere_recherche, "competence='" . $alinea . "'", " and ");
    $chaine_critere_recherche = concatAvecSeparateur($chaine_critere_recherche, "competence=" . $alinea, "&amp;");
}


if ($titre_rech) {
	$critere_recherche= concatAvecSeparateur($critere_recherche,"titre like '%" . $titre_rech . "%'"," and ");
	$chaine_critere_recherche = concatAvecSeparateur($chaine_critere_recherche,"titre_rech=" . urlencode($titre_rech),"&amp;");
}


/////////////////////////////////////////////
// crit�res de tri
/////////////////////////////////////////////

$url = "liste.php?";
$url = concatAvecSeparateur($url, $chaine_critere_recherche, "");

/////////////////////////////////////////////
// crit�res de tri
/////////////////////////////////////////////

$trieuse = new trieuse($tpl, "", $url, $tri);
$trieuse->addColonne("id", "id");
$trieuse->addColonne("titre", "titre");


$trieuse->addColonne("referentiel", "", "domaine asc, competence asc", "domaine desc, competence desc");
$trieuse->addColonne("alinea", "", "competence asc, domaine asc", "competence desc,domaine asc");

$trieuse->setTriDefaut("referentiel", true);


$critere_tri = $trieuse->getCritereSQL();

$trieuse->printToScreen();  //entetes

/////////////////////////////////////////
// gestion de la multipagination
/////////////////////////////////////////
//lien de mp : les deux (recherche et tri)

$url_multipagination = concatAvecSeparateur($url, $trieuse->getParametreTri(), "&amp;");



///////////////////////////////////////////////
// gestion des retours vers cette page � partir d'une popup sans perte des crit�res
$url_retour = $chaine_critere_recherche;
$url_retour=concatAvecSeparateur($url_retour,"indice_deb=" . $indice_deb,"&amp;");
$url_retour=concatAvecSeparateur($url_retour,"indice_ecart=" . $indice_ecart,"&amp;");
$url_retour=concatAvecSeparateur($url_retour, $trieuse->getParametreTri(),"&amp;");

print_form_actions ($tpl,'form_actions',$url_retour,'liste.php');

$url_retour=urlencode($url_retour);
///////////////////////////////////////////////

// recherche du nombre de lignes

// rev 977 ne pas compter les notions sp�cifiques V2 en V1

$nbTotal=count_records("ressources",$critere_version);

$indice_max=count_records ("ressources",$critere_recherche,1);

$tpl->assign("_ROOT.nb_items",$chaine_critere_recherche."<br/>".$indice_max."/".$nbTotal. " ".traduction("ressources"));

$lignes=get_records("ressources",$critere_recherche,$critere_tri,$indice_deb,$indice_ecart);

//mise en cache bulles d'aide sur ref et alineas
if (count($lignes)) {
    $tmp=get_referentiels();
    $array_ref=array();
    $array_ref_alin=array();

    foreach($tmp as $ref) {
        $array_ref[$ref->referentielc2i]=clean($ref->domaine);
        $alineas=get_alineas($ref->referentielc2i,'',false);  //rev 1047 peut ne pas encore en avoir
        foreach($alineas as $al)
        $array_ref_alin[$ref->referentielc2i."_".$al->alinea]=clean($al->aptitude);
    }
}


$compteur_ligne = 0;


foreach ($lignes as $ligne) {
	$idnat=$ligne->id;

	$tpl->newBlock("ligne");

	$tpl->setCouleurLigne($compteur_ligne);
	if ($ligne->filtree)$tpl->assign("barree","barree");
	else  $tpl->assign("barree","");

	$tpl->assign("id", $idnat);


    $tpl->assign("ref", "<span title=\"" .$array_ref[$ligne->domaine] . "\">" . $ligne->domaine . "</span>");
    $tpl->assign("alinea", "<span title=\"". $array_ref_alin[$ligne->domaine . "_" . $ligne->competence] . "\">" . $ligne->competence . "</span>");


	$tpl->assign("libelle", affiche_texte($ligne->titre));
	
    $tpl->assign("title_id",nom_univ($ligne->id_etab));

    $lien=get_lien($ligne);
    $tpl->assign('URL',$lien->URL);
    $tpl->assign('origine',$lien->origine);


	$items=array();
     $items[]=new icone_action('consulter',"consulterItem('$idnat')");
     if ($peutDupliquer) {
     	  $items[]=new icone_action('dupliquer',"dupliquerItem('$idnat')");
     }

     if ($peutModifier) {
     	if (($ligne->modifiable && ($USER->id_etab_perso == $ligne->id_etab || is_admin(false,$ligne->id_etab)))
             || ($CFG->peut_modifier_notion_nationale && $ligne->id_etab==1)
             || is_super_admin()) {
     	 			$items[]=new icone_action('modifier',"modifierItem('$idnat')");
    	 }else  $items[]=new icone_action( );
     }
     
     if ($peutSupprimer) {
     	if(count_parcours_ressource($ligne->id)==0) { //pas utilis�e
	    if (($ligne->modifiable && ( $USER->id_etab_perso == $ligne->id_etab || is_admin(false,$ligne->id_etab)))
              || ($CFG->peut_modifier_notion_nationale && $ligne->id_etab==1)
              || is_super_admin()) {
     	 			$items[]=new icone_action('supprimer',"supprimerItem('$idnat')" );
     		
     	       }else  $items[]=new icone_action( );
        }     
        if (!$ligne->filtree)  {
             $items[]=new icone_action('filtrer',"filtrerItem('$idnat')");          	
        } else {
              $items[]=new icone_action('defiltrer',"filtrerItem('$idnat')");
        }

     }


    $tpl->newBlock ('icones_actions');
    print_icones_action($tpl,'icones_actions',$items,'actions_'.$compteur_ligne);

	$compteur_ligne++;
}
if ($compteur_ligne==0){
	$tpl->newBlock("no_results");
    $tpl->assign("colspan",$colspan);
}


// g�n�ration des criteres de recherche

$tpl->gotoBlock("_ROOT");
print_boutons_criteres($tpl);
$tpl->assign("tri",$tri);

$tpl->assign("id_rech",$id_rech);
$tpl->assign("titre_rech",$titre_rech);


print_selecteur_ref_alinea_famille($tpl,"form_criteres",
                    "select_referentielc2i","saisie","style='width:200px'",  //select referentiel
                    "select_alinea","saisie","style='width:100px'",          //select alinea
                    false,false,false,                                  //select famille aucun
                    false,false,false,                                  //input famille aucun
                    $referentielc2i,$alinea,false,false,         //valeurs actuelles
                    false); 



//menu niveau 2
$items=array();
if (a_capacite("qa")) {
	$items[]=new icone_action('nouveau',"doPopup('ajout.php?id=-1')",'nouvelle_ressource');
}

$items[]=get_menu_item_criteres();

if ($chaine_critere_recherche)
    $items[]=get_menu_item_tout_afficher();

$items[]=get_menu_item_legende("ressource");

print_menu($tpl,"_ROOT.menu_niveau2",$items);

$tpl->printToScreen(); //affichage
?>
