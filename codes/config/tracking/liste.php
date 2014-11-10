<?php
/**
 * @version $Id: liste.php 1277 2011-11-05 15:11:47Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */


////////////////////////////////
//
//	Affichage du tracking
//
////////////////////////////////

/*----------------REVISIONS----------------------
v 1.1 : SB 17/10/2006 tri par d�faut sur la date desc
V 1.5 : le crit�re de recherche par utilisateur ne pr�sentait pas
           les �tudiants (C2iinscrits))
       : troncature de l'objet a 40 (les logs des erreurs SQL rendant le select trop large)
       : ajout de la plateforme au tracking (voir lib_tracking) et a la liste
       '
------------------------------------------------*/


$chemin = '../../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres
require_login('P'); //PP
v_d_o_d("at");


$login=optional_param("login","",PARAM_CLEAN);
$objet=optional_param("objet","",PARAM_RAW);
$action=optional_param("action","",PARAM_CLEAN);
$etab=optional_param("etab","",PARAM_INT);
$pf=optional_param("pf","",PARAM_CLEAN);
$indice_deb=optional_param("indice_deb",0,PARAM_INT);


// affichage par d�faut des �v�nements les plus r�cents
$tri=optional_param("tri","",PARAM_INT);


require_once( $chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates

$tpl = new C2IPopup(  );	//cr�er une instance
//inclure d'autre block de templates

$liste=<<<EOL
<div id="criteres">
<form name="form_criteres" id="form_criteres" method="post" action="liste.php">

    {select_etablissement} {select_login} {select_action} {select_objet} {select_pf}
            {boutons_criteres}

<input name="tri" type="hidden" value="{tri}" />

<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->

</form>
</div>
<!-- INCLUDE BLOCK : multip_haut -->
<table width="100%">
        <tr>
          <td class="gauche">{menu_niveau2} </td>
          <td class="droite commentaire1">{nb_items}</td>
        </tr>
</table>

<table id="liste">
    <thead>
       <tr>
            <th  class="bg"><a href="{url_login}" title="{alt_tri}">{t_login}</a>{tri_login}</th>
            <th  class="bg"> {t_nom}</th>
            <th  class="bg">{t_etab}</th>
            <th  class="bg"><a href="{url_objet}" title="{alt_tri}">{t_objet}</a>{tri_objet}</th>
            <th  class="bg"><a href="{url_id_objet}" title="{alt_tri}">{t_id_objet}</a>{tri_id_objet}</th>
            <th  class="bg"><a href="{url_action}" title="{alt_tri}">{t_action}</a>{tri_action} </th>
            <th  class="bg"><a href="{url_date}" title="{alt_tri}">{t_date}</a>{tri_date} </th>
            <th  class="bg"><a href="{url_ip}" title="{alt_tri}">{t_ip}</a>{tri_ip} </th>
            <th  class="bg">{plateforme}</th>
      </tr>
   </thead>
   <tbody>
<!-- START BLOCK : ligne -->
          <tr class="{paire_impaire}">
            <td class="rouge1">{login} <ul style="display:inline;">{consulter_fiche}</ul></td>
            <td>{nom}</td>
            <td class="centre" title="{etabnom}">{etab} <ul style="display:inline;">{consulter_fiche_etab}</ul></td>
            <td>{objet}</td>
            <td>{id_objet}</td>
            <td>{action}</td>
            <td>{date}</td>
            <td>{ip}</td>
            <td>{plateforme}</td>

          </tr>
<!-- END BLOCK : ligne -->
<!-- START BLOCK : no_results -->
<tr class="information">
<td colspan ="{colspan}">
        {msg_pas_de_record}
</td>
</tr>
<!-- END BLOCK : no_results -->

        </tbody>
      </table>
<!-- INCLUDE BLOCK : multip -->

EOL;


$options=array (
	"liste"=>1,
	"corps_byvar"=>$liste
);

$tpl->prepare($chemin,$options);

set_time_limit(0);

$colspan=9;

// s�lection du nom de l'�tablissement
$tpl->traduit("_ROOT.titre_popup","tracking");


/**
 * table pour accelerer les recherches des noms d'�tablissement'
 */
$etabli = array();
$res=get_etablissements();
foreach($res as $rowe){
	$etabli[$rowe->id_etab] = $rowe->nom_etab;
}

require_once ($CFG->chemin_commun . "/trieuse.class.php");
////////////////////////////////////////////////////
// crit�res de recherche
////////////////////////////////////////////////////
//$autre_table=",c2iutilisateurs";
$autre_table="";
$chaine_critere_recherche = "";
$critere_recherche = "";

	if ($login) {
        $critere_recherche= concatAvecSeparateur($critere_recherche,"T.login='" . addslashes($login) . "'"," and ");
        $chaine_critere_recherche =concatAvecSeparateur($chaine_critere_recherche, "login=" . $login,"&amp;");
	}

	if ($objet) {
        // troncature a 40 pour affichagde dans le select
        $critere_recherche= concatAvecSeparateur($critere_recherche,"T.objet LIKE  '".$objet."%'"," and ");
        $chaine_critere_recherche =concatAvecSeparateur($chaine_critere_recherche, "objet=".$objet,"&amp;");
	}

	if ($action) {
        $critere_recherche= concatAvecSeparateur($critere_recherche,"T.action='".$action."'"," and ");
        $chaine_critere_recherche =concatAvecSeparateur($chaine_critere_recherche,"action=".$action,"&amp;");
	}

if ($etab){
        $critere_recherche= concatAvecSeparateur($critere_recherche,"T.etablissement=".$etab," and ");
        $chaine_critere_recherche =concatAvecSeparateur($chaine_critere_recherche,"etab=".$etab,"&amp;");
}

if ($pf){
        $critere_recherche= concatAvecSeparateur($critere_recherche,"T.plateforme='".$pf."'"," and ");
        $chaine_critere_recherche =concatAvecSeparateur($chaine_critere_recherche,"pf=".$pf,"&amp;");
}
/////////////////////////////////////////////
// crit�res de tri
/////////////////////////////////////////////
$url = "liste.php?";
$url = concatAvecSeparateur($url, $chaine_critere_recherche, "");



$trieuse = new trieuse($tpl, "", $url, $tri);
$trieuse->addColonne("login", "","T.login asc","T.login desc");
$trieuse->addColonne("objet", "","T.objet asc","T.objet desc");
$trieuse->addColonne("id_objet", "","T.id_objet asc","T.id_objet desc");
$trieuse->addColonne("action", "","T.action asc","T.action desc");
$trieuse->addColonne("date", "T.date");
$trieuse->addColonne("ip", "","T.ip asc","T.ip desc");
$trieuse->setTriDefaut("date", false);

$critere_tri = $trieuse->getCritereSQL();
$trieuse->printToScreen();  //entetes

/////////////////////////////////////////
// gestion de la multipagination
/////////////////////////////////////////
//$indice_ecart est trouv� dans c2i_params//lien de mp : les deux (recherche et tri)
$url_multipagination = concatAvecSeparateur($url, $trieuse->getParametreTri(), "&amp;");


if (isset($num_page)){
	if ($num_page>0){
		$indice_deb = $indice_ecart * ($num_page - 1);
	}
}

// variables utilis�es dans le code de multipagination

$indice_fin=$indice_deb + $indice_ecart - 1; // indice de fin d'affichage

// recherche du nombre de lignes
$indice_max = 0;

$indice_max=count_records ("tracking T" . $autre_table ,$critere_recherche,1);

$nbTotal=count_records("tracking");

$tpl->assign("_ROOT.nb_items",$chaine_critere_recherche."<br/>".$indice_max."/".$nbTotal);

////////////////////////////////////////////////////
// requete de s�lection des examens � afficher
////////////////////////////////////////////////////


$res=get_records("tracking T" . $autre_table ,$critere_recherche,$critere_tri,$indice_deb,$indice_ecart,false);
$compteur_ligne = 0;
foreach($res as $ligne){
	$tpl->newBlock("ligne");
	$tpl->setCouleurLigne($compteur_ligne);
	//$tpl->assign("nom",strtoupper($ligne->nom)." ".ucfirst($ligne->prenom));
	// plus de jointure avec c2iyutilisateurs, donc on doit aller chercher dans les 2 tables
	$tpl->assign("nom",cree_lien_mailto(get_mail($ligne->login),get_fullname($ligne->login)));
	/*
	 $tpl->assign("etab",$etabli[$ligne->login]['nome']);
	 $tpl->assign("etabnom",str_replace('"',"&quot;",$etabli[$ligne->login]['nome']));
	 */
	$sonEtab=etab($ligne->login);
	if (isset($etabli[$sonEtab])) {
		$tpl->assign("etab",$sonEtab);
		$tpl->assign("etabnom",str_replace('"',"&quot;",$etabli[$sonEtab]));
		 print_menu_item($tpl,"consulter_fiche_etab",get_menu_item_consulter("../../acces/etablissement/fiche.php?idq=".$sonEtab));
	} else {
		$tpl->assign("etab","????");
		$tpl->traduit("etabnom","etablissement_supprime");
		$tpl->assign("consulter_fiche_etab","<li class=\"menu_niveau2_item\"></li>"); //W3C ul ne peut �tre vide

	}

	$tpl->assign("login",$ligne->login);
	if ($ligne->login) {
        // rev 944 attention a bien choisir une fiche utilisateur ou candidat ...
		if ($cpt=get_compte($ligne->login,false)) {
			if ($cpt->type_user=='P')
				print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../../acces/personnel/fiche.php?id=".$ligne->login));
			else
				print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../../acces/etudiant/fiche.php?id=".$ligne->login));

		}else $tpl->assign("consulter_fiche","");
	}
	else $tpl->assign("consulter_fiche","");

	//$tpl->assign("typeu",$ligne->type_utilisateur);
	$tpl->assign("action",$ligne->action);
	$tpl->assign("objet",$ligne->objet);
	$tpl->assign("id_objet",$ligne->id_objet);
    $tpl->assign("date",userdate($ligne->date,'strftimedatetimeshort'));
    $tpl->assign("ip",$ligne->ip);
    $tpl->assign("plateforme",$ligne->plateforme);


	// passage � la ligne suivante
	$compteur_ligne ++;
}

if ($compteur_ligne==0){
    $tpl->newBlock("no_results");
    $tpl->assign( "colspan",$colspan);
}
///////////////////////////////////
//
//	gestions de l'affichage des crit�res de recherche
//
///////////////////////////////////
// g�n�ration des listes d�roulantes

$tpl->gotoBlock("_ROOT");
print_boutons_criteres($tpl);

// rev 940 ajout N� etablissement
//liste d�roulante des universit�s
$ets=get_etablissements('nom_etab');
foreach ($ets as $et) {
    $et->nom_etab=sprintf("%s (%s)",$et->nom_etab,$et->id_etab);
}

print_select_from_table($tpl,"select_etablissement",$ets,"etab","saisie","style='width:120px'","id_etab","nom_etab",traduction("etablissement"),$etab);


// utilisateurs  ET inscrits
// rev 1.5O il faut aussi pouvoir filter sur les inscrits

$query="select distinct login from {$CFG->prefix}tracking order by login";
$res = get_records_sql($query,false);
foreach($res as $ligne) {
    $ligne->info=$ligne->login." (".get_fullname($ligne->login,false).")";
}
print_select_from_table($tpl,"select_login",$res,"login","saisie","style='width:120px'","login","info",traduction("utilisateur"),$login);


// objets
$query="select distinct objet from {$CFG->prefix}tracking order by objet;";
$res = get_records_sql($query,false);
foreach($res as $ligne) {
    //rev 1.5 avec les logs des erreurs SQL ... le select peut devenir trop large ...
    //attention lors de la recherche ...
    $ligne->objet=substr($ligne->objet,0,40);
}
print_select_from_table($tpl,"select_objet",$res,"objet","saisie","style='width:120px'","objet","objet",traduction("objet"),$objet);

// actions
$query="select distinct action from {$CFG->prefix}tracking order by action;";
$res = get_records_sql($query,false);

print_select_from_table($tpl,"select_action",$res,"action","saisie","style='width:300px'","action","action",traduction("action"),$action);


// actions
$query="select distinct plateforme from {$CFG->prefix}tracking order by plateforme;";
$res = get_records_sql($query,false);

print_select_from_table($tpl,"select_pf",$res,"pf","saisie","style='width:120px'","plateforme","plateforme",traduction("plateforme"),$pf);


$tpl->gotoBlock("_ROOT");
//critere de tri dans la forme "criteres"
$tpl->assign("tri",$tri);

//////////////////////////////////////////////////////////////////////////////////////////////////////
// url de la nouvelle question (dans un javascript, d'ou le second parametre)
$items=array();
$items[]=get_menu_item_criteres();
if ($chaine_critere_recherche)
    $items[]=get_menu_item_tout_afficher();

//$items[]=get_menu_item_legende("tracking");

print_menu($tpl,"_ROOT.menu_niveau2",$items);

$tpl->print_boutons_fermeture();


$tpl->printToScreen();										//affichage
?>

