<?php


/**
 * @author Patrick Pollet
 * @version $Id: liste.php 1304 2012-09-19 13:19:08Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres
require_login("P");
v_d_o_d("ql");


//crit�res de recherche
$id_rech = optional_param("id_rech", "", PARAM_CLE_C2I);

$referentielc2i = optional_param("referentielc2i", "", PARAM_ALPHANUM);
$alinea = optional_param("alinea", 0, PARAM_INT);


$famille = optional_param("famille", 0, PARAM_INT);
$titre_rech = optional_param("titre_rech", '', PARAM_CLEAN);
$famille_rech = optional_param("famille_rech", '', PARAM_CLEAN);
$filtre_univ = optional_param("filtre_univ", '', PARAM_INT);


//par défaut seulement les validées en certif

if ($USER->type_plateforme == 'certification') {
    // $filtre_valid = optional_param("filtre_valid",QUESTION_VALIDEE, PARAM_INT);
    // v 2 seuls les experts validateurs peuvent voir les non validées
    if (a_capacite("qv") || empty($CFG->seulement_validees_liste)) {
    	$filtre_valid = optional_param("filtre_valid",QUESTION_VALIDEE, PARAM_INT);
    } else 
    	$filtre_valid = QUESTION_VALIDEE ; //valeur forcée
}
else 
    $filtre_valid = optional_param("filtre_valid",QUESTION_TOUTE, PARAM_INT);



$tri = optional_param("tri", "", PARAM_INT);

// rev 982 et suivante simplifiaction des liens emis sur les icones d'action'
$action=optional_param('action','',PARAM_ALPHA);
if ($action) {
	//print_r($_POST);
	$id_action=required_param('id_action',PARAM_CLE_C2I);
}



require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPrincipale(); //cr�er une instance
//inclure d'autre block de templates




$liste =<<<EOL


<div id="criteres">
<form id="form_criteres" method="post" action="liste.php">
<div>

          {select_filtre_univ}

          {t_id} : <input class="saisie" name="id_rech" size="5" value="{id_rech}"/>

          | {t_titre} : <input class="saisie" name="titre_rech" size="7" value="{titre_rech}"/>


     {select_referentielc2i} {select_alinea} | {select_famille} |

  {t_famille} : <input class="saisie" name="famille_rech" size="7" value="{famille_rech}"/>
  <!-- START BLOCK : filtre_etat -->
   | {t_etat} : {select_filtre_valid}
 <!-- END BLOCK : filtre_etat -->
 
   <!-- START BLOCK : filtrage -->
   | {t_filtre} :  <input type="checkbox" class="saisie" name="filtrage" value="{filtrage}"/>
   <!-- END BLOCK : filtrage -->


            {boutons_criteres}
<input name="tri" type="hidden" value="{tri}" />
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}"/>
<!-- END BLOCK : id_session -->
</div>
</form>

<!-- START COMMENT -->
pour une liste de questions quand on demande tout afficher il faut
demander TOUTES les questions en certification et pas les valid�es
d ou cette version sp�ciale de clear_criteres() qui remplace
celle de commun/js/script.js voir en bas l'appel a get_menu_item_tout_afficher'

rev 981 et ult�rieure : les javascript dans dans un document php inclus par INCLUDEBLOCK SCRIPT
<!-- END COMMENT -->

</div>

<!-- INCLUDESCRIPT BLOCK : ./actions_js.php -->


<div id="menu2">
{menu_niveau2}
</div>
<div id="infos">
{nb_items}
</div>
<!-- INCLUDE BLOCK : multip_haut -->

 <div id="erreurMsg"> </div>
<table id="liste">
<thead>
<tr>

<!-- START BLOCK : col_e -->
    <th class="bg">&nbsp;</th>
<!-- END BLOCK : col_e -->
    <th class="bg"><a href="{url_id}"    title="{alt_tri}">{t_id}</a>{tri_id}</th>
    <th class="bg"><a href="{url_titre}" title="{alt_tri}">{t_titre}</a>{tri_titre}</th>
    <th class="bg"><a href="{url_auteur}" title="{alt_tri}">{t_auteur}</a>{tri_auteur}</th>
    <th class="bg"><a href="{url_referentiel}" title="{alt_tri}">{t_referentiel}</a>{tri_referentiel}</th>
    <th class="bg"><a href="{url_alinea}" title="{alt_tri}">{t_alinea}</a>{tri_alinea}</th>
<!-- START BLOCK : t_ancien_domaine -->
            <th class="bg">
               <a href="{url_anciendomaine}" title="{alt_tri}">{t_ancien_domaine}</a>{tri_anciendomaine} </th>
<!-- END BLOCK : t_ancien_domaine -->

	<th class="bg"><a href="{url_famille}" title="{alt_tri}">{t_famille_ordre}</a>{tri_famille}</th>
    <th class="bg"> <a href="{url_date}" title="{alt_tri}">{t_date}</a>{tri_date}</th>

    <th class="bg" style="width:{largeur_td_action}px;">{t_actions}</th>



</tr>
</thead>
<tbody>

<!-- START BLOCK : question -->
 <tr title="{title_id}"  class="{paire_impaire}">
            <td class="{style}" title="{obsolete}" >{id}</td>
<!-- START BLOCK : editable -->            
            <td class="editable {barree}"
          ondblclick="inlineMod('{id}',this,'titre','TexteMultiNV','{ajax_modif}');"
                            >{titre}</td>
 <!-- END BLOCK : editable -->  
 <!-- START BLOCK : non_editable -->           
           <td class="{barree}">{titre}</td>
 <!-- END BLOCK : non_editable -->
           
            <td>{auteur}</td>
            <td>{ref}</td>
            <td>{alinea}</td>
 <!-- START BLOCK : ligne_ancien_domaine -->
            <td>
              <s> {ancien_domaine}</s>
          </td>
 <!-- END BLOCK : ligne_ancien_domaine -->
            <td>{ordref}</td>
            <td>{date}
<!-- START BLOCK : image_nouv -->
            <br/>
            <img src="{chemin_images}/nouveau.gif" alt="{alt_nouveau}" title="{alt_nouveau}"/>
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
		{msg_pas_de_questions}
</td>
</tr>
<!-- END BLOCK : no_results -->
        </tbody>
      </table>

<!-- INCLUDE BLOCK : multip -->

{form_actions}

EOL;

$options = array (
	"liste" => 1,
	"corps_byvar" => $liste
);

$tpl->prepare($chemin, $options);

$CFG->utiliser_inlinemod_js=1;
/////////////////////////
// affichage du menu menu

print_menu_haut($tpl, "q");

////////////////////////////////////////////
////////////////////////////////////////////////

$colspan = 8+1;
//affichage des ent�tes de colonnes selon droits
//$tpl->newblock("col_c");
// si droit de modifier
$peutModifier = a_capacite("qm");
$peutSupprimer = a_capacite("qs");
$peutDupliquer = false;


if ($peutModifier) {
	//$tpl->newblock("col_m");
	//$colspan++;
}
// si droit de supprimer
If ($peutSupprimer) {
	//$tpl->newblock("col_s");
	// v 1.41 la suppression se fait ici !

   //question a supprimer
    $supp_idq = optional_param("supp_idq", "", PARAM_INT);
    $supp_ide = optional_param("supp_ide", "", PARAM_INT);
	if ($supp_idq && $supp_ide) { //
		if ($USER->id_etab_perso == $supp_ide || is_admin(false, $supp_ide)) //double check
			supprime_question($supp_idq, $supp_ide);
	} else
	    // rev 981
    	if ($action=="supprimer") {
    		$q=get_question_byidnat($id_action); //controle existence
    		if ($USER->id_etab_perso == $q->id_etab || is_admin(false, $q->id_etab)) //double check
    			supprime_question ($q->id,$q->id_etab);
    	}
	//$colspan++;
}
// si droit de dupliquer
// pb avec les composantes d'ou le test sans etab ??? '
if (a_capacite("qd")) {
	if ($CFG->peut_dupliquer_question) {
	//	$tpl->newblock("col_d");
		$peutDupliquer = true;
	//	$colspan++;
	}
}

if ($USER->type_plateforme == "certification") {
    $largeur_td_action=260;
}else
    $largeur_td_action=200;





//////////////////////////////////////////////
//  	droit d'invalider des questions
//
//	sur la plateforme nationale seul l'administrateur g�n�ral peut
//	invalider des questions
//	(pas les admin locaux de cette plateforme)
//
//	sur une plateforme locale, l'admin local peut les invalider
//   mais PAS un admin de composante
//

// V 1.5
$ligne = get_etablissement($USER->id_etab_perso); //fatale si inconnu ...

$peutInvalider = 0;
if ($USER->type_plateforme == "certification") { //pas encore en positionnement
	$peutInvalider = ($CFG->universite_serveur == 1 && is_super_admin()) || (is_admin(false, $CFG->universite_serveur)); //une locale, pas composante
}
if ($peutInvalider == 1) {
	//$tpl->newblock("col_iv");
	//$colspan++;
	//si invalidation demand�e
	// v 1.5 la manip se fait ici !
    //question a invalider
    $inval_idq = optional_param("inval_idq", "", PARAM_INT);
    $inval_ide = optional_param("inval_ide", "", PARAM_INT);
	if ($inval_idq && $inval_ide) {
		//print "$inval_idq";
		invalide_question($inval_idq, $inval_ide);
	} else
	 // rev 981
    	if ($action=="invalider") {
    		$q=get_question_byidnat($id_action); //controle existence
    		invalide_question ($q->id,$q->id_etab);
    	}

}

// rev 972
// on n'utilise pas le droit invalider car uniquement en certification
$peutFiltrer=$peutSupprimer && (($USER->type_plateforme=="certification" && $CFG->peut_filtrer_question_cert) ||
             ($USER->type_plateforme=="positionnement" && $CFG->peut_filtrer_question_pos));
if ($peutFiltrer) {
    //$colspan++;
    //$tpl->newblock("col_f");
    /** code simplifi� rev 981*/

    $filtre_id=optional_param("filtre_id",0,PARAM_INT);
    $filtre_ide=optional_param("filtre_ide",0,PARAM_INT);

    if ($filtre_id && $filtre_ide) {
        filtre_question ($filtre_id,$filtre_ide);
    } else
        // rev 981
    	if ($action=="filtrer") {
    		$q=get_question_byidnat($id_action); //controle existence
    		filtre_question ($q->id,$q->id_etab);
    	}
    // TODO $tpl->newBlock("filtrage");

}// else $largeur_td_action -= 20;


$tpl->assignGlobal('largeur_td_action',$largeur_td_action);

require_once ($CFG->chemin_commun . "/trieuse.class.php");
require_once ($CFG->chemin_commun . "/chercheuse.class.php");

////////////////////////////////////////////////////
// crit�res de recherche
////////////////////////////////////////////////////
$chaine_critere_recherche = "";
$critere_recherche = "$USER->type_plateforme='OUI'";
if ($id_rech) { //deja filtr� par optional_param
	$id0 = explode(".", $id_rech);
	if (sizeof($id0) == 2) {
		if ($critere_recherche != "")
			$critere_recherche .= " and ";
		$critere_recherche .= "id_etab='" . $id0[0] . "' and id='" . $id0[1] . "'";
		if ($chaine_critere_recherche != "")
			$chaine_critere_recherche .= "&amp;";
		$chaine_critere_recherche .= "id_rech=" . $id_rech;
	} else {
		if ($critere_recherche != "")
			$critere_recherche .= " and ";
		$critere_recherche .= "id=-3000"; // question impossible
	}
}

if ($referentielc2i) {
    $critere_recherche = concatAvecSeparateur($critere_recherche, "Q.referentielc2i='" . $referentielc2i . "'", " and ");
    $chaine_critere_recherche = concatAvecSeparateur($chaine_critere_recherche, "referentielc2i=" . $referentielc2i, "&amp;");
}

if ($alinea) {
   $critere_recherche = concatAvecSeparateur($critere_recherche, "Q.alinea='" . $alinea . "'", " and ");
   $chaine_critere_recherche = concatAvecSeparateur($chaine_critere_recherche, "alinea=" . $alinea, "&amp;");
}


if ($famille) {
	$critere_recherche = concatAvecSeparateur($critere_recherche, "id_famille_validee='" . $famille . "'", " and ");
	$chaine_critere_recherche = concatAvecSeparateur($chaine_critere_recherche, "famille=" . $famille, "&amp;");
}

if ($titre_rech) {
	$critere_recherche = concatAvecSeparateur($critere_recherche, "titre like '%" . $titre_rech . "%'", " and ");
	$chaine_critere_recherche = concatAvecSeparateur($chaine_critere_recherche, "titre_rech=" . urlencode($titre_rech), "&amp;");
}

// CM
if ($famille_rech) {
	$critere_recherche = concatAvecSeparateur($critere_recherche, "famille like '%" . $famille_rech . "%'", " and ");
	$chaine_critere_recherche = concatAvecSeparateur($chaine_critere_recherche, "famille_rech=" . urlencode($famille_rech), "&amp;");
}

if ($filtre_univ) {
	$critere_recherche = concatAvecSeparateur($critere_recherche, "id_etab='" . $filtre_univ . "'", " and ");
	$chaine_critere_recherche = concatAvecSeparateur($chaine_critere_recherche, "filtre_univ=" . $filtre_univ, "&amp;");

}


if ($filtre_valid != QUESTION_TOUTE) {
	$filtre = "etat=" . $filtre_valid;
	$critere_recherche = concatAvecSeparateur($critere_recherche, $filtre, " and ");
	$chaine_critere_recherche = concatAvecSeparateur($chaine_critere_recherche, "validation=" . get_etat_validation($filtre_valid), "&amp;");
} else
	$chaine_critere_recherche = concatAvecSeparateur($chaine_critere_recherche, "validation=" . get_etat_validation(QUESTION_TOUTE), "&amp;");


$url = "liste.php?";
$url = concatAvecSeparateur($url, $chaine_critere_recherche, "");

/////////////////////////////////////////////
// crit�res de tri
/////////////////////////////////////////////

$trieuse = new trieuse($tpl, "", $url, $tri);
$trieuse->addColonne("id", "", "id_etab asc,id asc", "id_etab desc,id desc");
$trieuse->addColonne("titre", "titre");
$trieuse->addColonne("auteur", "auteur");


$trieuse->addColonne("referentiel", "", "referentielc2i asc, alinea asc", "referentielc2i desc, alinea desc");
$trieuse->addColonne("alinea", "", "alinea asc, referentielc2i asc", "alinea desc,referentielc2i asc");

$trieuse->addColonne("famille", "", "Q.id_famille_validee asc, F.ordref asc", "Q.id_famille_validee desc, F.ordref desc");

$trieuse->addColonne("date", "ts_datemodification");

$trieuse->setTriDefaut("date", false);

$critere_tri = $trieuse->getCritereSQL();

$trieuse->printToScreen(); //entetes

/////////////////////////////////////////
// gestion de la multipagination
/////////////////////////////////////////
//$indice_ecart est trouv� dans c2i_params//lien de mp : les deux (recherche et tri)
$url_multipagination = concatAvecSeparateur($url, $trieuse->getParametreTri(), "&amp;");



// compter les questions de type type_p (positionnement / certification)

$nbTotal = count_records("questions", $USER->type_plateforme . "='OUI'");

// CM ajout c2ifamilles
$requete_num = "SELECT Q.*  FROM {$CFG->prefix}questions Q " .
"left outer join {$CFG->prefix}familles F on F.idf =Q.id_famille_validee " .
"where " . $critere_recherche;

$indice_max = count_records_sql($requete_num);

// affichage du nombre questions valid�es

if ($USER->type_plateforme == "certification") {
	$requete_num .= " and etat=".QUESTION_VALIDEE;
	$nb_questions_validees = count_records_sql($requete_num);
	$tpl->assign("_ROOT.nb_items", $chaine_critere_recherche . "<br/>" . $indice_max . "/" . $nbTotal . " " . traduction("questions") .
	"<br/>($nb_questions_validees " . traduction("validees") . ")");

} else
	$tpl->assign("_ROOT.nb_items", $chaine_critere_recherche .
	"<br/>" . $indice_max . "/" . $nbTotal . " " . traduction("questions"));

///////////////////////////////////////////////
// gestion des retours vers cette page � partir d'une popup sans perte des crit�res
$url_retour = $chaine_critere_recherche;
$url_retour = concatAvecSeparateur($url_retour, "indice_deb=" . $indice_deb, "&amp;");
$url_retour = concatAvecSeparateur($url_retour, "indice_ecart=" . $indice_ecart, "&amp;");
$url_retour = concatAvecSeparateur($url_retour, $trieuse->getParametreTri(), "&amp;");

print_form_actions ($tpl,'form_actions',$url_retour,'liste.php');

$url_retour = urlencode($url_retour);
///////////////////////////////////////////////

////////////////////////////////////////////////////
// requete de s�lection des questions � afficher
////////////////////////////////////////////////////
// CM ajout c2ifamilles   que fait ce select dans une page de pr�sentation !!!!
$requete = "SELECT Q.*,F.ordref FROM {$CFG->prefix}questions Q " .
"left outer join {$CFG->prefix}familles F on F.idf = Q.id_famille_validee " .
"where " . $critere_recherche .
" ORDER BY " . $critere_tri . " limit " . $indice_deb . "," . $indice_ecart . ";";

$lignes = get_records_sql($requete, false);

if (count($lignes)) {
	$tmp = get_referentiels();
	$array_ref = array ();
	$array_ref_alin = array ();

    //dans l'attente toutes questions mises � jour
    $array_ref['']='???';
    $array_ref_alin['_0']='???';

	foreach ($tmp as $ref) {
		$array_ref[$ref->referentielc2i] = clean($ref->domaine);
        // rev 1041 sur les nouvelles nationales, peut ne pas encore y avoir d'alinea
		$alineas = get_alineas($ref->referentielc2i,'referentielc2i,alinea',false);
		foreach ($alineas as $al)
			$array_ref_alin[$ref->referentielc2i .
			"_" . $al->alinea] = clean($al->aptitude);

	}
	$tmp = get_familles();
	$array_familles = array ();
	$array_familles_ordre = array ();
	foreach ($tmp as $fam) {
		$array_familles[$fam->idf] = clean($fam->famille);
		$array_familles_ordre[$fam->idf] = $fam->ordref;
	}
}

$compteur_ligne = 0;
$nb_experts = config_nb_experts();
foreach ($lignes as $ligne) {

	// v�rification de l'utilisation de cet item dans un examen
	$nb = est_utilise_examen($ligne->id, $ligne->id_etab); //tests documents
	// rev 977 simplification des tests
	$droitLocal=is_super_admin() || ($USER->id_etab_perso == $ligne->id_etab || is_admin(false, $ligne->id_etab));
	
	// rev 982  parametre unique des actions
	$idnat=$ligne->id_etab.'.'.$ligne->id;

	$tpl->newBlock("question");
	$tpl->setCouleurLigne($compteur_ligne);


	$tpl->assign("id", $idnat);
	$tpl->assign("title_id", nom_univ($ligne->id_etab)); // ajout SB
	
	// rev 2.0 édition inline des libellés
	if ($peutModifier) {
		if (($nb == 0 && !est_validee($ligne)) || is_super_admin()) { //jamais une validée
			if ($droitLocal || ($CFG->universite_serveur == 1 && a_capacite("qv", 1))) //rev 818 un expert peut modifier une question sur la nationale ...
			{
				$tpl->newBlock('editable');
				$tpl->assign("id",$ligne->id);
			}	
			else
				$tpl->newBlock('non_editable');
		} else $tpl->newBlock('non_editable');
	} else $tpl->newBlock('non_editable');
		
	$tpl->assign("titre", affiche_texte_question($ligne->titre));	
	if ($ligne->est_filtree)$tpl->assign("barree","barree");
	else  $tpl->assign("barree","");
	
	
	$tpl->gotoBlock('question'); //important
	
	
	//rev 944 couleurs selon �tat
	if ($ligne->etat == QUESTION_REFUSEE) {
		$tpl->assign("style", "rouge");
		$tpl->traduit("obsolete", "alt_non_valide");
	} else {
		if ($ligne->etat == QUESTION_VALIDEE) {
			$tpl->assign("style", "vert");
			$tpl->traduit("obsolete", "alt_valide");
		} else {
			$tpl->assign("style", "orange");
			$tpl->traduit("obsolete", "alt_non_examinee");
		}
	}

	$tpl->assign("ref", "<span title=\"" . $array_ref[$ligne->referentielc2i] . "\">" . $ligne->referentielc2i . "</span>");
	$tpl->assign("alinea", "<span title=\"" . $array_ref_alin[$ligne->referentielc2i . "_" . $ligne->alinea] . "\">" . $ligne->alinea . "</span>");

	//rev 940 cas de famille supprim�e directement ,devrait �tre execptionnel vire une notice PHP
	if ($ligne->id_famille_validee != 0 && !empty ($array_familles[$ligne->id_famille_validee]))
		$tpl->assign("ordref", "<span title=\"" . $array_familles[$ligne->id_famille_validee] . "\">" .$ligne->id_famille_validee.'('.$array_familles_ordre[$ligne->id_famille_validee] .')'. "</span>");
	else
		$tpl->assign("ordref", "?");

	$tpl->assign("date", userdate($ligne->ts_datemodification, 'strftimedateshort'));

	if (trim($ligne->auteur) == "")
		$ligne->auteur = traduction("SDTICE");

	$ligne->auteur = applique_regle_nom_prenom($ligne->auteur); // rev 841

	if ($CFG->afficher_lien_mail_liste_questions) // rev 839 KS
		$tpl->assign("auteur", cree_lien_mailto($ligne->auteur_mail, $ligne->auteur));
	else
		$tpl->assign("auteur", $ligne->auteur);

	// question r�cente
	if ($USER->derniere_connexion <= $ligne->ts_datecreation)
		$tpl->newBlock("image_nouv");

    // debut code revis� theme v15
    $items=array();

  
    if ($USER->type_plateforme == "certification") {
        $nb_avis = 0;
        // gestion de l'affichage de l'ic�ne de validation
        switch ($ligne->etat) {
            case  QUESTION_VALIDEE :
                $image_v = "valide";
                break;
            case QUESTION_REFUSEE:
                $image_v = "non_valide";
                break;
            case QUESTION_NONEXAMINEE :
                //$image_v = "non_examinee";

            default :
                $image_v = "attente";
            $nb_avis = nb_avis($ligne->id, $ligne->id_etab);
            break;
        }

        $icone= new icone_action ($image_v);
        if ($nb_avis)
            $stats_avis = traduction("nombre_davis_sur", false, $nb_avis, $nb_experts);
        else
            $stats_avis = "";

        $icone->alt = traduction("alt_" . $image_v) . " " . $stats_avis;

        if (($CFG->universite_serveur == 1 && a_capacite("qv", 1)) || a_capacite("qv", $ligne->id_etab)) {
            if ($ligne->etat == QUESTION_NONEXAMINEE)
                $icone->js="validerItem('$idnat')";
            else
                $icone->js="commenterItem('$idnat')";

        }

        $items[]=$icone;
    }

    $items[]=new icone_action('consulter',"consulterItem('$idnat')");

    if ($peutModifier) {
        if (($nb == 0 && !est_validee($ligne)) || is_super_admin()) { //jamais une valid�e
            if ($droitLocal || ($CFG->universite_serveur == 1 && a_capacite("qv", 1))) //rev 818 un expert peut modifier une question sur la nationale ...
            {
                $items[]=new icone_action('modifier',"modifierItem('$idnat')");

            } else {
                $items[]=new icone_action();
            }
        } else
                $items[]=new icone_action();
    }

    // si droit de dupliquer et permis sur la PF
    if ($peutDupliquer) {
        $items[]=new icone_action('dupliquer',"dupliquerItem('$idnat')");
    }

    if ($peutInvalider) {
        if ($ligne->etat != QUESTION_REFUSEE) {
            $items[]=new icone_action('invalider',"invaliderItem('$idnat')");

        } else
            $items[]=new icone_action();
    }

    // si droit de supprimer et question non valid�e ou non utilis�e dans un examen
    if ($peutSupprimer) {
        if ($nb == 0 && !est_validee($ligne) || is_super_admin()) {
            if ($droitLocal) {
                $items[]=new icone_action('supprimer',"supprimerItem('$idnat')" );

            } else
                $items[]=new icone_action();
        } else
            $items[]=new icone_action();
    }


    if ($peutFiltrer) {
        if (!$ligne->est_filtree)  {
            $items[]=new icone_action('filtrer',"filtrerItem('$idnat')");

        } else {
            $items[]=new icone_action('defiltrer',"filtrerItem('$idnat')");
        }

    } else  $items[]=new icone_action();
    
    // rev 986 commentaires par mail si la question est nationale
   // if ($ligne->id_etab==1 && !empty($CFG->adresse_feedback_questions)) {
    if (!empty($CFG->adresse_feedback_questions)) {
                $items[]=new icone_action('commenter_email',"commenterEmailItem('$idnat')");
        }
    
    else  $items[]=new icone_action();
    
    

    $tpl->newBlock ('icones_actions');
    print_icones_action($tpl,'icones_actions',$items,'actions_'.$compteur_ligne);

    /**/
    // passage � la ligne suivante
    $compteur_ligne++;
}

if ($compteur_ligne == 0) {
	$tpl->newBlock("no_results");
	$tpl->assign("colspan", $colspan);
}
///////////////////////////////////
//
//	gestions de l'affichage des crit�res de recherche
//
///////////////////////////////////
// g�n�ration des listes d�roulantes

$tpl->gotoBlock("_ROOT");
print_boutons_criteres($tpl);

//la liste des familles a �t� renseign�e au d�but ...
$tpl->assign("tri", $tri);
$tpl->assign("id_rech", $id_rech);
$tpl->assign("titre_rech", $titre_rech);
$tpl->assign("famille_rech", $famille_rech);

print_selecteur_ref_alinea_famille($tpl, "form_criteres", "select_referentielc2i", "saisie", "style=\"width:200px\"", //select referentiel
"select_alinea", "saisie", "style=\"width:100px\"", //select alinea
"select_famille", "saisie", "style=\"width:100px\"", //select famille
false, false, false, //input famille
$referentielc2i, $alinea, $famille, false, //valeurs actuelles
false);  // rev 977 recherche questions orphelines
//liste d�roulante des universit�s
$ets = get_etablissements('nom_etab');
// rev 839 ajout N� etablissement
foreach ($ets as $et) {
	$et->nom_etab = sprintf("%s (%s)", $et->nom_etab, $et->id_etab);
}

print_select_from_table($tpl, "_ROOT.select_filtre_univ", $ets, "filtre_univ", null, "style=\"width:100px\"", "id_etab", "nom_etab", traduction("universite"), $filtre_univ);

//liste d�roulante des �tats de questions
// seuls les experts validateurs peuvent voir les non validées
if (a_capacite("qv") || empty($CFG->seulement_validees_liste)) {
	$tpl->newBlock ('filtre_etat');
	print_select_from_table($tpl, "select_filtre_valid", get_etats_validation(), "filtre_valid", null, "", "id", "texte", traduction("alt_validation"), $filtre_valid);
}

$tpl->assignGlobal("ajax_modif",$chemin_commun."/ajax/modif_question.php");

$items = array ();
if (a_capacite("qa")) {
	$items[]=new icone_action('nouveau',"doPopup('ajout.php?id=-1')",'nouvelle_question');
}

$items[] = get_menu_item_criteres();
if ($chaine_critere_recherche)
	$items[] = get_menu_item_tout_afficher("clear_criteres_questions();");

if (a_capacite("qa")) {
	$items[] = new icone_action( 'import', "doMiniPopup('import_questions.php?')",'import_questions');
}
$items[] = new icone_action('export',"doPopup('export_questions.php?')",'export_questions');

$items[] = new icone_action('bilan', "doPopup('bilan_questions.php?')",'bilan_questions');

if (a_capacite("qv")) // rev 911 deplcament stats questions ici
	$items[] = new icone_action( 'bilan', "doPopup('../config/statistiques/liste.php?')", 'statistiques');

// rev 1023 'url' chagn� en 'return' pour IE qui n'aime pas un popup avec redirection
$items[] = array (
	'action' => 'export',
	'return' => "export_referentiel_xml.php?url_retour=" . $url_retour,
	'texte' => 'export_ref'
);
$items[] = get_menu_item_legende("question");

print_menu($tpl, "_ROOT.menu_niveau2", $items);



$tpl->printToScreen(); //affichage
?>