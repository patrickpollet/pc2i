<?php

/**
 * @author Patrick Pollet
 * @version $Id: lib_resultats.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 *
*/
//pour acc�s a la classe resultat et noteuse
require_once($CFG->chemin_commun."/noteuse.class.php");

//options de consolidations rev 937
define('CONSO_RIEN', 0);
define('CONSO_DERNIER', 1);
define('CONSO_MEILLEUR', 2);
define('CONSO_MEILLEUR_DOM', 3);
define('CONSO_MEILLEUR_ITEM', 4);

if (is_admin()) {   //utilisateur courant uniquement
    maj_bd_resultats();
}

function maj_bd_resultats () {
	global $CFG,$USER;
	/*************************************************************************************************
 //rev 1025 date d�but passage = date 1ere fois qu'il a coch� un truc (peut pas mieux)
	$update=! mysql_column_exists("date_debut","resultatsexamens");
	if ($update) {
		mysql_add_column("date_debut","INT(10)  default '00'","resultatsexamens");
        //calcul pour les examens pass�s
		$sql=<<<EOS
			SELECT login, examen, min( ts_date) as date_debut
			FROM {$CFG->prefix}resultats
			WHERE 1
			GROUP BY login, examen
EOS;
			$lignes=get_records_sql($sql);
			foreach ($lignes as $ligne) {
				$ligne->examen=str_replace('.','_',$ligne->examen); //argh point dans resultats tiret bas dans resultatsexamens
				update_record("resultatsexamens",$ligne,'login','examen',1);
			}
	}
	*************************************************************************************************/
}


/**
 * met en forme les r�sultats recus de la noteuse
 * remplace tous les anciens include (action_passage, action_normale et les templates associ�s)
 */
function affiche_resultats ($res,$resu_notmin,$avecParcours) {
    global $CFG,$USER;
    $fiche=<<<EOF


<table class="resultats" width="90%">
  <tbody>
     <tr>
      <th width="632"  >{referentiels}</th>
      <th width="119"  >{score}</th>
      <!-- START BLOCK : colonne_parcours -->
      <th width="119"  >{parcours}</th>
       <!-- END BLOCK : colonne_parcours -->
     </tr>
<!-- START BLOCK : ligne_r -->
    <tr class="{paire_impaire}">
        <td>{ref} : {domaine}</td>
        <td class="{class_couleur} droite">{score}</td>
         <!-- START BLOCK : ligne_parcours -->
         <td    class="taille1"><input type="checkbox" name="{parc}" value="mise_parc" {checked} /></td>
         <!-- END BLOCK : ligne_parcours -->
    </tr>
<!-- END BLOCK : ligne_r -->
  </tbody>
</table>
<hr/>

<p>{dh_ip_passage}  {duree_passage}</p>
<p>{domaines_a_revoir} : {domaines_ar} </p>
<p>{preconisations}</p>




EOF;

$tpl = new SubTemplatePower( $fiche,T_BYVAR);
$tpl->prepare($CFG->chemin);
if ($avecParcours)  $tpl->newBlock("colonne_parcours");
// on arrondi � 0 si c'est inf�rieur
$score_global = max(0, round($res->score_global, 2));
$arrondi=$CFG->nombre_decimales_score;
//$tpl->assignGlobal("scoreg", $score_global . " %");

$tpl->assignGlobal("scoreg",sprintf("%.{$arrondi}f",$score_global)." %");

$domaines_ar="";
$tab_domaines_ar=array();
$compteur_ligne=0;

$resu_notmin=$resu_notmin?$resu_notmin:50; // rev 874 attention en certification c'est 0
foreach ($res->tabref_score as $ref=>$score) {
	$ligne_r=get_referentiel($ref);
	$tpl->newBlock("ligne_r");
    $tpl->setCouleurLigne($compteur_ligne);
	$tpl->assign("ref", $ligne_r->referentielc2i);
	$tpl->assign("domaine", $ligne_r->domaine);
    /**
     * rev 843 ATTENTION PIEGE
     * si appel�e suite au passage res contient les r�sultats de la noteuse
     * donc avec uniquement les referentiels test�s...
     * si appel�e en consultation ult�rieure , certains scores sont VIDES
     * (non relus en BD mais initialis�s a VIDE par relit_resultats
     */


    if ($score) { //pas de questions dessus possible pour un QCM par domaine
		// $tpl->assign("score", $score." %");
        $tpl->assign("score",sprintf("%.{$arrondi}f",$score)." %");
		if ($score < $resu_notmin) {
			//$tpl->assign("checked", "checked");
			$domaines_ar .= " " . $ligne_r->referentielc2i;
			$tpl->assign("class_couleur", "rouge");
			$tab_domaines_ar[$ligne_r->referentielc2i] = 1;
		} else {
			//$tpl->assign("checked", "unchecked");
			$tab_domaines_ar[$ligne_r->referentielc2i] = 0; //PP rev 1.3.1
			if ($score < 70)
				$tpl->assign("class_couleur", "orange");
			else
				$tpl->assign("class_couleur", "vert");
		}
        if ($avecParcours) {
        $tpl->newBlock("ligne_parcours");
        $tpl->setChecked($score < $resu_notmin,"checked");
        }
	}
	else {
		$tpl->assign("score","");
        $tpl->assign("class_couleur", "");
	}

   $compteur_ligne++;
}

//passage anonyme ou par un prof ?
if (empty($res->ip_max)) $res->ip_max=$USER->ip;
if (empty($res->ts_date_max)) $res->ts_date_max=time();
if (empty($res->ts_date_min)) $res->ts_date_min=time();


if (!empty($res->origine)) $res->ip_max=$res->origine; // cas des scanners



$tpl->assignGlobal ("dh_ip_passage",traduction("info_examen_passe_le",false,userdate($res->ts_date_max),$res->ip_max));

if (!empty($CFG->afficher_temps_passage) && $res->ts_date_max>$res->ts_date_min)
    $tpl->assignGlobal("duree_passage",traduction("info_duree_passage",false,format_time($res->ts_date_max-$res->ts_date_min,'',false)));
else $tpl->assignGlobal("duree_passage",'');

    // rev 1.5 une fonction dans lib_resultat en attendant des textes en BD
 $tpl->assignGlobal("preconisations",get_preconisations($score_global));
 $tpl->assignGlobal("domaines_a_revoir",traduction("domaines_a_revoir"));
 $tpl->assign ("_ROOT.domaines_ar",$domaines_ar);


    return $tpl->getOutputContent();
}

/**
 * V 1.5 calcule les scores ET les m�morise dans quatre tables de la BD dans tous les cas
 * sauf le cas mode=QCM_TEST (un prof teste son examen) et QCM_PASSAGE (tirage lors du passage)
 * @return une instance des resulats pour afficher ces r�sultats si n�cessaire
 *
 */
function note_examen ($idex,$idexe,$mode,$login,$questions,$reponses,$nbquestions,$nbreponses){
    global $CFG, $USER;
    $etudiant=get_compte($login); // important !!!!
    if ($mode==QCM_NORMAL)
        $noteuse = new noteuse($idex, $idexe);
    else
        $noteuse = new noteuseALaVolee ($questions,$reponses);
    $res = $noteuse->note_etudiant($etudiant);

    if ($mode !=QCM_TEST && $res->score_global !=-1)
    //pas en mode test et si a au moins repondu � une !
    // en mode  tirage lors du passage on ne conserve donc que le dernier ?
      if ($mode !=QCM_PASSAGE) // rev 868 a revoir ...
    	enregistre_resultats($idex,$idexe,$login,$res);
    return $res;

}

/**
 * renvoie les r�sultats d'un �tudiant depuis la BD
 * ou renote si n�cessaire
 * @return un objet au meme format que celui renvoy� par noteuse
 * @see enregistre_resulats
 */
function get_resultats($idex,$idexe,$login,$renote=false) {
   global $CFG;
   $cle=$idexe."_".$idex;
   //rev 819 faux pour un pool !!!
   //if ($ret=get_record("resultatsexamens","examen='".$cle."' and login='".addslashes($login)."'",false)) {
   if (compte_passages($idex,$idexe,$login)>=1) {
        return relit_resultats($idex,$idexe,$login);
   }
   if ($renote)
        $res=note_examen($idex,$idexe,QCM_NORMAL,$login,false,false,false,false);
   else {
      $ret=new resultat(null);  //important
      $ret->examen=$idexe.".".$idex; //cl� r��lle
      /* fait dans le constructeur de la classe resultat
      $referentiels=get_referentiels();  //une ligne vide pour export apogee qui veut 9"DEF" ...
      foreach($referentiels as $rowr) {
            $ret->tabref_score[$rowr->referentielc2i] = 0;
        }
      //$ret->nbreponses=0;  ne plus utiliser car inconnu en relecture depuis la BD !

       */
      $ret->score_global=-1;
       return $ret; //pas pass� ou pas not�
   }
  return $res;
}

/**
 * renvoie juste le score, ip, date
 * rev 809  fonctionne aussi pour les pools ...
 */
function get_resultats_examen($idex,$idexe,$login) {

    $ex=get_examen($idex,$idexe);
    if ($ex->est_pool) {
        $fils=liste_groupe_pool ($idex,$idexe);
        // cherche le 'bon fils'
        foreach ($fils as $f) {
            $cle=$f->id_etab."_".$f->id_examen;
            if ($ret=get_record("resultatsexamens","examen='".$cle."' and login='".addslashes($login)."'",false))
            return $ret;
        }
        return false; //pas trouv�
    }

	$cle=$idexe."_".$idex;
	return get_record("resultatsexamens","examen='".$cle."' and login='".addslashes($login)."'",false);

}

/**
 * revision 937
 */
function get_resultats_consolides($login,$typep,$consolid) {
	global $USER;

	$sav=$USER->type_plateforme;
	$USER->type_plateforme=$typep;

	$examens=get_examens_inscrits($login,'ts_datedebut desc',false);
	$res=array();
	foreach($examens as $examen) {
		$idq=$examen->id_examen;
		$ide=$examen->id_etab;
		$tmp=get_resultats($idq,$ide,$login,false);
		if ($tmp->score_global !=-1)  // l'a passe ???
			$res[]=$tmp;
	}
	$USER->type_plateforme=$sav;
	//pas de consolidation si un seul
	if (count($res)<2)
		return $res;
	switch ($consolid) {

		case CONSO_RIEN:
			return $res;
			break;

		case CONSO_DERNIER :
			//pas vraiment correct on devrait regarder la date de fin de passage ($res->ts_date_max)
			//return array($res[0]); //le dernier pass� (voir le crit�re de tri)
			$i=0;
			$date=0;
			foreach ($res as $ex) {
				if ($ex->ts_date_max > $date) {
					$dernier=$i;
					$date=$ex->ts_date_max;
				}
				$i++;
			}
			return array($res[$dernier]);
			break;

		case CONSO_MEILLEUR :
			$i=0;
			$score=-999;
			foreach ($res as $ex) {
				if ($ex->score_global > $score) {
					$meilleur=$i;
					$score=$ex->score_global;
				}
				$i++;
			}
			return array($res[$meilleur]);
			break;

			case CONSO_MEILLEUR_DOM :

			$best = new resultat(null);
			foreach($res as $ex) {
				foreach ($ex->tabref_score as $ref => $note)
				if (!isset( $best->tabref_score[$ref]))
				      $best->tabref_score[$ref]=$note;
				else if ($note > $best->tabref_score[$ref])
				    $best->tabref_score[$ref]=$note;
			}

			return array($best);

			break;
			case CONSO_MEILLEUR_ITEM :
			$best = new resultat(null);
			foreach($res as $ex) {
				foreach ($ex->tabcomp_score as $ref => $note)
				if (!isset( $best->tabcomp_score[$ref]))
				      $best->tabcomp_score[$ref]=$note;
				else if ($note > $best->tabcomp_score[$ref])
				    $best->tabcomp_score[$ref]=$note;
			}
			return array($best);
			break;

			default :
			return $res;
		break;

	}


}

/**
 * renvoie les r�sultats d'un �tudiant depuis la BD
 * @return un objet au meme format que celui renvoy� par noteuse
 * LES DOMAINES NON TESTES ont un score VIDE (pas zero)
 * pour affichage ou export CORRECT
 *
        $this->etudiant=$etudiant;
        $this->tabref_score=array();
        $this->tabcomp_score=array();
        $this->tab_points=array();
        $this->score_global=0;        score global  -1 pas pass�
        $this->score_global_na=0;
        $this->score_brut=0;
        $this->score_brut_na=0;
        $this->nb_reponses=0;
        $this->heure_max = "";
        $this->ip_max = "";
        $this->origine = "";          vide, qcmdirect, simulation ...
        $this->examen="";             cl� de l'examen REEL (peut �tre different de idexe.$idex si pool)
        $this->ts_date_max=0;
 *
 * @see enregistre_resultats  et classe resultat
 */
function relit_resultats($idex,$idexe,$login) {
    global $CFG;

    $examen=get_examen($idex,$idexe);
    if (!$examen->est_pool)
        return __relit_resultats($idex,$idexe,$login);
    $fils=liste_groupe_pool($idex,$idexe);
    foreach ($fils as $f) {
        $ret=__relit_resultats($f->id_examen,$f->id_etab,$login);
        if ($ret->score_global !=-1)
              return $ret; //pass� et  not�
    }
    $ret=new resultat(null);  //important
    $ret->score_global=-1;
    // rev 847 pour un examen par domaine il faut que les autres domaines ait une note vide
    // pour export apog�e ou csv !!!
    // donc on met tout � vide et certains scores seront r�cup�r�s de la BD
    $referentiels=get_referentiels();  //une ligne vide pour export apogee qui veut 9"DEF" ...
    foreach($referentiels as $rowr) {
            $ret->tabref_score[$rowr->referentielc2i] = "";  //pas 0 VIDE !!!!
    }
    return $ret; //pas pass� ou pas not�
}

/**
 * fonction priv�e appel�e soit pour un examen normal soit pour le bon membre d'un pool
 */
function __relit_resultats($idex,$idexe,$login) {
    global $CFG;

    $ret=new resultat(null);  //important pour export (tout est initailis�)
    // rev 847 pour un examen par domaine il faut que les autres domaines ait une note vide
    // pour export apog�e ou csv !!!
    // donc on met tout � VIDE et certains scores seront r�cup�r�s de la BD
    // charge � l'afficheur/exporteur  de g�rer ce cas !
    $referentiels=get_referentiels();  //une ligne vide pour export apogee qui veut 9"DEF" ...
    foreach($referentiels as $rowr) {
            $ret->tabref_score[$rowr->referentielc2i] = "";  //pas 0 VIDE !!!!
    }
   // pour l'instant on ne le fait pas pour les comp�tences qui l'on n'�value pas

    $ret->examen=$idexe.".".$idex; //cl� r��lle
    $cle=$idexe."_".$idex;
    $critere="examen='".$cle."' and login='".addslashes($login)."'";  // rev 984
    if($tmp=get_record("resultatsexamens",$critere,false)){
            $ret->ip_max=$tmp->ip_max;
            $ret->ts_date_max=$tmp->date;
            $ret->ts_date_min=$tmp->date_debut; //rev 1027

            $ret->score_global=$tmp->score;
            $ret->origine=$tmp->origine;
            $ret->examen=$idexe.".".$idex;  // rev 937 pour consolidation
    } else {
        $ret->score_global=-1; return $ret; //pas pass� ou pas not�
    }
    $tmp=get_records("resultatsreferentiels",$critere,"referentielc2i",false);
    //print_r($tmp);
    foreach ($tmp as $score)
        $ret->tabref_score[$score->referentielc2i]=$score->score;

    $tmp=get_records("resultatscompetences",$critere,"competence",false);
    foreach ($tmp as $score)
        $ret->tabcomp_score[$score->competence]=$score->score;

  $tmp=get_records("resultatsdetailles",$critere,"question",false);
    foreach ($tmp as $score)
        $ret->tab_points[$score->question]=$score->score;

  return $ret;
}
/**
 * enregistre les r�sultats de la noteuse qui n'a �value QUE les domaines/comp�tences
 * TESTEES
 */
function enregistre_resultats($idex,$idexe,$login,$res) {
	purge_resultats_inscrit($idex,$idexe,$login);

    if ($res->score_global==-1) return; //pas pass�

	$cle=$idexe."_".$idex;
	$now=time();  //php5 ne plus utiliser mktime() capricieux !
	$ligne=new StdClass();
	//partie commune
	$ligne->login=$login;
	$ligne->examen=$cle;
	//TODO en ajax (enregistrer le timestamp et le g�rer dans noteuse) fait
	$ligne->date=$res->ts_date_max;
    $ligne->date_debut=$res->ts_date_min;
    
	$ligne->score=$res->score_global;
	$ligne->ip_max=$res->ip_max;
    $ligne->origine=$res->origine; //qcmdirect ou vide si passage
    $ligne->drapeau=0; // rev 980 pb avec MySQL sous Windows ?
	insert_record("resultatsexamens",$ligne,false,'id',true);
    unset($ligne->ip_max); //existe pas dans les autres tables !
	unset($ligne->score);
    unset($ligne->origine);
    unset($ligne->date_debut);


	foreach ($res->tabref_score as $ref=>$score) {
		$ligne->referentielc2i=$ref;
		$ligne->score=$score;
		insert_record("resultatsreferentiels",$ligne,false,'id',true);
	}
	unset($ligne->referentielc2i);

	foreach ($res->tabcomp_score as $comp => $score) {
		$ligne->competence=$comp;
		$ligne->score=$score;
		insert_record("resultatscompetences",$ligne,false,'id',true);
	}
	unset($ligne->competence);

	foreach ($res->tab_points as $question=>$score) {
		$ligne->question=$question;
		$ligne->score=$score;
		insert_record("resultatsdetailles",$ligne,false,'id',true);
	}
    //print("<br/> ENRE=".print_r($ligne,true)."<br/>");

}

/**
 * rev 976
 * compare deux algos de calculs de scores
 * pour test AMC et autres scanners optiques
 */
function meme_resultats($res1,$res2) {
	if ($res1->score_global !=$res2->score_global) return false;

	foreach ($res1->tabref_score as $ref=>$score)
		if ($score != $res2->tabref_score[$ref]) return false;

	foreach ($res1->tabcomp_score as $comp=>$score)
		if ($score != $res2->tabcomp_score[$comp]) return false;

	foreach ($res1->tab_points as $q=>$score)
		if ($score != $res2->tab_points[$q]) return false;

	return true;



}


/**
 * purge l'histo d'un candidat enregistr� via ajax pendant son passage
 * TODO arghh la cl� est ide.idq pas ide_idq  a changer
 */

function purge_historique ($idex,$idexe,$login) {
    delete_records("resultats","examen='" . $idexe . "." . $idex . "' and login='" . addslashes($login)."'" ); // rev 984
}

/**
 * rev 819 gere les pools
 * renvoie les r�sulats enregist�s par ajax pour un examen et un login
 *
 */

function get_historique ($idex,$idexe,$login) {
      $examen=get_examen($idex,$idexe);
    if (!$examen->est_pool)
        return get_records("resultats","examen='" . $idexe . "." . $idex . "' and login='" . addslashes($login)."'" );  // rev 984

    $fils=liste_groupe_pool($idex,$idexe);
    foreach ($fils as $f) {
        $ret= get_records("resultats","examen='" . $f->id_etab . "." . $f->id_examen . "' and login='" . addslashes($login)."'" ); // rev 984
        if (count($ret))
              return $ret; //pass�
    }
    return array(); //rat�
}



/**
 * ne purge pas les infos enregistr�es part ajax pour pouvoir le renoter ...
 * et qu'il puisse le repasser
 */

function purge_resultats_inscrit($idex,$idexe,$login,$avecHistorique=false){

	$cle=$idexe."_".$idex;
    $critere="examen='".$cle."' and login='".addslashes($login)."'";  // rev 984
	delete_records("resultatsexamens",$critere,true);
	delete_records("resultatscompetences",$critere,true);
	delete_records("resultatsdetailles",$critere,true);
	delete_records("resultatsreferentiels",$critere,true);
if ($avecHistorique)
    purge_historique($idex,$idexe,$login);
}

function purge_resultats_examen($idex,$idexe){

    $cle=$idexe."_".$idex;
    $critere="examen='".$cle."'";
    delete_records("resultatsexamens",$critere,true);
    delete_records("resultatscompetences",$critere,true);
    delete_records("resultatsdetailles",$critere,true);
    delete_records("resultatsreferentiels",$critere,true);
}

/**
 * renoter tout un examen
 * pas utile en V1.5
 * recherche les r�ponses enregistr�s par ajax dans la bd (c2iresultats)
 */

function renoter_examen ($idq,$ide) {
    $examen=get_examen($idq,$ide);
    $inscrits=get_inscrits($idq,$ide);  //seulement les comptes qui existent toujours
    if (count($inscrits)) {
        purge_resultats_examen($idq,$ide);
        foreach ($inscrits as $inscrit)
            note_examen($idq,$ide,QCM_NORMAL,$inscrit->login,false,false,false,false);
    }
}

/*
*	Fonction de calcul des points d'un �tudiant par question
*	$B: nbre bonnes r�ponses
*	$M: nbre mauvaises r�ponses
*  $X: nbre de r�ponses coch�es par l'�tudiant dans les bonnes
*	$Y: nbre de r�ponses coch�es par l'�tudiant dans les mauvaises
*	return int
*pas encore utilis�e
*/
function __score_question($B,$M,$X,$Y){
	if ($B > 0)	{
		if ($M > 0)
			$nbpoints = ($X / $B) - ($Y / $M);
		else
			$nbpoints = ($X / $B);
	}
	elseif ($Y == 0)
	$nbpoints = 1;
	else   {
		if ($M > 0)
			$nbpoints = -($Y / $M);
		else
			$nbpoints = 1;
	}
	$nbpoints = round($nbpoints, 2);
	return $nbpoints;
}


function get_preconisations ($score_global) {
    global $CFG;
     $arrondi=$CFG->nombre_decimales_score;

     //rev 978 d�but gestion encodage UTF8
     $filename=$CFG->chemin."/langues/preconisations_".$CFG->langue;
     if ($CFG->unicodedb)
        $filename .='_utf8';

   include ($filename.".php");

     $score_global=sprintf("%.{$arrondi}f %%",$score_global);
     $score=traduction("votre_score",true,$score_global);

//warning eclipse normal ($preconisations definie dans le include)
   foreach ($preconisations as $barre=>$p) {
        if  ($score_global <$barre)
           return $score.". ".$p;
   }
   return $score;
}



/**
 * templates des resultats mis en "global" car utilis�s par 2 fonctions
 */
$modeleComplets=<<<EOS
    <div class="gauche titre1">examen {ide}.{idq} : {nb_passages}</div><br />


<div id="menu2">
{menu_niveau2}
</div>
      <table width="100%" id="sortable"  class="listing">

        <thead>
          <tr  {bulle:astuce:msg_tri_colonnes}>
            <th class="bg">{t_numetud}</th>
            <th class="bg">{t_login}</th>
            <th class="bg">{t_nom}</th>
            <th class="bg">{t_prenom}</th>
             <th class="bg">{t_examen}</th>
            <th class="bg">{t_score}</th>
             <th class="bg nosort" style="width:30px;" > {t_action} </th>
<!-- START BLOCK : e_score_q -->
            <th class="bg">{t_score_quest}</th>
<!-- END BLOCK : e_score_q -->
<!-- START BLOCK : e_score_r -->
            <th class="bg">{t_score_ref}</th>
<!-- END BLOCK : e_score_r -->
            <th class="bg">{t_date}</th>
<!-- START BLOCK : e_duree_passage -->
            <th class="bg">{t_duree}</th>
<!-- END BLOCK : e_duree_passage -->

            <th class="bg">{t_ip}</th>
            
</tr>
</thead>
<tbody>
<!-- START BLOCK : question -->
          <tr class="{paire_impaire}">

            <td class="rouge1">{numetudiant}
            <!-- START BLOCK : icone_erreur -->
                &nbsp;<img src="{chemin_images}/i_non_valide.gif" width="17" height="17" title="{passage_suspect}" alt="{passage_suspect}"/>
            <!-- END BLOCK : icone_erreur -->
            </td>
<td  class="taille1">{login}</td>
            <td>{nom}</td>
            <td>{prenom}</td>
            <td>{examen}</td>
            <td>{score}</td>
             <td>{icones_actions} </td>
<!-- START BLOCK : score_q -->
            <td>{squest}</td>
<!-- END BLOCK : score_q -->
<!-- START BLOCK : score_r -->
            <td>{sref}</td>
<!-- END BLOCK : score_r -->
            <td>{date}</td>
<!-- START BLOCK : duree_passage -->
            <td>{duree}</td>
<!-- END BLOCK : duree_passage -->
            <td>{ip}</td>
            
          </tr>
<!-- END BLOCK : question -->
</tbody>

        </table>



EOS;

    $modeleRefs=<<<EO2
<div class="centre titre1">examen {ide}.{idq} : {nb_passages}</div><br />

<div id="menu2">
{menu_niveau2}
</div>

 <table width="100%" id="sortable"  class="listing"  >
  <thead>
          <tr  {bulle:astuce:msg_tri_colonnes}>

           <th class="bg">{t_numetud}</th>
            <th class="bg">{t_login}</th>
            <th class="bg">{t_nom}</th>
            <th class="bg">{t_prenom}</th>
            <th class="bg">{t_examen}</th>

            <th class="bg">{t_score}</th>
              <th class="bg nosort" style="width:30px;" > {t_action} </th>
<!-- START BLOCK : e_score_r -->
            <th class="bg">{t_score_ref}</th>
<!-- END BLOCK : e_score_r -->



</tr>
</thead>
<tbody>
<!-- START BLOCK : question -->
          <tr class="{paire_impaire}">
            <td class="rouge1">{numetudiant}
            <!-- START BLOCK : icone_erreur -->
                &nbsp;<img src="{chemin_images}/i_non_valide.gif" width="17" height="17" title="{passage_suspect}" alt="{passage_suspect}"/>
            <!-- END BLOCK : icone_erreur -->
            </td>
            <td>{login}</td>
            <td>{nom}</td>
        <td>{prenom}</td>
        <td>{examen}</td>

            <td>{score}</td>
              <td> {icones_actions}</td>
<!-- START BLOCK : score_r -->
            <td class="histogramme">
            <img src="{chemin_images}/histo.png" height="{height}" width="15" title="{score}" border="1" alt="" />

            </td>
<!-- END BLOCK : score_r -->
         
          </tr>
<!-- END BLOCK : question -->
        </tbody>

      </table>


EO2;



/** retourne un tableau html des resultats de l'examen $ide.$idq selon le mode d'affichage
complets ; synthetiques ; referentiel

VERSION 1.5 on relit les tables c2iresultats*
TODO gerer les pools. Il faut le faire ici et pas en appelant cette focntion pour chaque membre
car elle renvoie TOUT l'HTML (entetes, liens excel et imprimer...) pour chaque examen

*/
function return_tableau_resultats_examen($ide, $idq ,$affichage="complets",$type=0,$retour_fiche=1){

    global $CFG;
    global $modeleComplets,$modeleRefs;


    $CSV_SEP=$CFG->csv_separateur;
    $arrondi=$CFG->nombre_decimales_score;
    $chemin=$CFG->chemin;
    $rowe=get_examen($idq,$ide);

    // V 1.5 22/05/2009 svn 807 ...
    // les entetes �tant les memes on peut les mettre a la suite des uns des autres
    // sauf pour les d�taill�s !!!
    if ($rowe->est_pool && $affichage !='complets')
	    $examens= liste_groupe_pool($idq, $ide);
    else
	    $examens=array($rowe); //un seul


    // initialisation
    $nb_inscrits=0;
    $compteur_ligne = 0;
    //entete de base
    // PP ajout code examen a chaque ligne pour faciliter import MySQL
    $entete_csv=array("t_numetud","t_login","t_nom","t_prenom","t_examen","t_score");
    $ligne_csv=array("numetudiant","login","nom","prenom","examen");
    $ch_fp=ligne_to_csv($entete_csv,false);
    $cle=$ide."_".$idq;  //id examen

    switch ($affichage) {
        default :
            return "";
            break;

        case "complets" :
//gros pb dans le cas d'un pool
// les questions ne sont pas les m�mes pour chaque membre !!!
//donc les entetes doivent �tre differentes !!!
//pour l'instant cette focntion est donc appel�e pour chaque membre uniquement en cas de r�sultats d�taill�s

	        $tpl = new SubTemplatePower( $modeleComplets,T_BYVAR);
	        $tpl->prepare($chemin);

            if ($CFG->afficher_temps_passage)
                $tpl->newBlock('e_duree_passage');

	        $tpl->gotoBlock("_ROOT");

		    $filename="resultats_".$ide."_".$idq.".csv";


	        $fp = fopen("{$CFG->chemin_ressources}/csv/".$filename, "w");


	         if ($CFG->export_ods) {
	            require_once($CFG->chemin_commun.'/OOo/odslib.class.php');
	            $filename_ods="resultats_".$ide."_".$idq.".ods";
	            /// Creating a workbook
	            $workbook = new MoodleODSWorkbook("-");
	            /// Send HTTP headers
	            $workbook->send($filename_ods);
	            /// Creating the first worksheet
	            $myods = & $workbook->add_worksheet(traduction("resultats_complets"));
	            $row=0;
	            $col=0;
	            foreach($entete_csv as $e) {
		            $myods->write_string($row, $col++,traduction($e,false));
	            }


            }


                //attention � l'ordre !!! (non m�lang�es et par ordre de cl� comme viendront les r�sultats
                //
		        $resq=get_questions($idq,$ide,false,"id_etab,id");
		        foreach($resq as $rowq) {
			        $tpl->newBlock("e_score_q");
			        $tpl->assign("t_score_quest",$rowq->id_etab.".".$rowq->id);
			        $qid=$rowq->id_etab.".".$rowq->id; //PP sans le "q" le "." perturbe openoffice !
			        $ch_fp .= $CSV_SEP.$qid;
			        if ($CFG->export_ods)
			          	 $myods->write_string($row, $col++,$qid);

		        }

            $referentiels=get_referentiels();
            foreach($referentiels as $rowr) {
                $tpl->newBlock("e_score_r");
                $tpl->assign("t_score_ref",$rowr->referentielc2i);
                $ch_fp .= $CSV_SEP.$rowr->referentielc2i;
                if ($CFG->export_ods)
                	$myods->write_string($row, $col++,$rowr->referentielc2i);

            }


            $ch_fp .= $CSV_SEP.(to_csv(traduction("heure_validation")));
            if ($CFG->afficher_temps_passage)
                $ch_fp .= $CSV_SEP.(to_csv(traduction("t_duree")));
            $ch_fp .= $CSV_SEP.(to_csv(traduction("ip")));

            fputs($fp, $ch_fp."\n");

            if ($CFG->export_ods) {
                	$myods->write_string($row, $col++,traduction("heure_validation"));
                     if ($CFG->afficher_temps_passage)
                            $myods->write_string($row, $col++,traduction("t_duree"));
                	$myods->write_string($row, $col++,traduction("ip"));
    				$row=1;
    				$col=0;
    		}

            if ($res= get_inscrits($idq,$ide)) {
                $nb_inscrits +=count($res);  // ajouter en cas de pool rev 809
                foreach($res as $ligne) {
                    $cp=compte_passages($idq,$ide,$ligne->login);
                    if ($type==0 && $cp==0) continue;
                    if ($type==1 && $cp >0) continue;

                    $tpl->newBlock("question");
                    $tpl->assign("n",$compteur_ligne);
                    $tpl->setCouleurLigne($compteur_ligne);
                    $ligne->examen=$ide.".".$idq ; //ajout� au CSV  et a l'�cran en 1.5  (cas des pools)
                    $tpl->assignObjet($ligne);

                    $ch_fp=ligne_to_csv($ligne_csv,$ligne);

                    if($CFG->export_ods) {
			            	foreach($ligne_csv as $a){
			            		$myods->write_string($row, $col++,$ligne->$a);
			            	}
			            }

                   //calculs
                    $res=get_resultats($idq,$ide,$ligne->login,false);

                    $score_global=sprintf("%.{$arrondi}f",$res->score_global)." %";
                    $ch_fp .= $CSV_SEP.$score_global;
                     if($CFG->export_ods) {
			            		$myods->write_number($row, $col++,$score_global);

			            }


                    $tpl->assign("score",$score_global);
                    if ($res->origine)
                        $res->ip_max=$res->origine; //ecran et csv
                    $tpl->assign("ip",$res->ip_max);
 					$heuref=userdate($res->ts_date_max,'strftimedatetimeshort');
                    $tpl->assign("date",$heuref);
                    $items=array();
                    $items[]=new icone_action('consulter',"consulterItem('{$ligne->login}',$idq,$ide)");
                    print_icones_action($tpl,'icones_actions',$items,'actions_'.$compteur_ligne);

                    if ($CFG->afficher_temps_passage) {
                        $duree=format_time($res->ts_date_max-$res->ts_date_min,'',false);
                        $tpl->newBlock("duree_passage");
                        $tpl->assign("duree",$duree);



                    }

                    //d�tail par question
// bug Rennes 2 26/06/2009 les scores ne reviennent pas dans l'ordre des entetes tri� par idetab.idquestion
//Array ( [1.1128] => -0.25 [1.13] => 1.00 [1.130] => 0.00 [1.148] => -0.50 [1.507] => 0.17 [1.637] => -0.25 [1.87] => 0.75 [69.1298] => -0.67 )
//print_r($res->tab_points);
	                  //  foreach ($res->tab_points as $pts) {

                      foreach ($resq as $q) {
                            $cleq= $q->id_etab.".".$q->id;
                            $pts=$res->tab_points[$cleq];

		                    $tpl->newBlock("score_q");
		                    //$pts=sprintf("%.2f",$pts);
		                    $pts=sprintf("%.{$arrondi}f",$pts);
		                    $tpl->assign("squest",$pts);
		                    $ch_fp.=$CSV_SEP.$pts;            //pb openoffice qui convertit en date si 1 ???

		                    if($CFG->export_ods) {
					            $myods->write_number($row, $col++,$pts);

				            }
                    }


                    foreach ($res->tabref_score as $s) {
                        $tpl->newBlock("score_r");
                        if (!empty($s))  // rev 843 pour qcm par domaine (PAS de NOTE =BLANC!)
                            $s=sprintf("%.{$arrondi}f",$s)." %";

                        $tpl->assign("sref",$s);
                        $ch_fp.=$CSV_SEP.$s;
                         if($CFG->export_ods) {
					            $myods->write_number($row, $col++,$s);

				            }
                    }


                     $ch_fp.=$CSV_SEP.$heuref;
                          if ($CFG->afficher_temps_passage)
                                   $ch_fp.=$CSV_SEP.$duree;
                        $ch_fp.=$CSV_SEP.$res->ip_max;
                        fputs($fp, $ch_fp."\n");

                     if($CFG->export_ods) {
			            		$myods->write_string($row, $col++,$heuref);
                                 if ($CFG->afficher_temps_passage)
                                    $myods->write_string($row, $col++,$duree);
			            		$myods->write_string($row, $col++,$res->ip_max);


			            }

                    // passage a la ligne suivante
                    $compteur_ligne ++;
                     $row++; //ods
			         $col=0; //ods
                }
            }

            fclose($fp);
              if ($CFG->export_ods)
				$full_filename_ods=$workbook->close(); // fait le zip et dire ou il l'a mis

            break;

        case "synthetiques" :
            //idem sans le d�tail des questions
            //les entetes �tant les memes on peut les mettre a la suite des uns des autres si pool
            $tpl = new SubTemplatePower( $modeleComplets,T_BYVAR);
            $tpl->prepare($chemin);

             if ($CFG->afficher_temps_passage)
                $tpl->newBlock('e_duree_passage');

            $tpl->gotoBlock("_ROOT");
            $filename="synthetiques_".$ide."_".$idq.".csv";

            if ($CFG->export_ods) {
	            require_once($CFG->chemin_commun.'/OOo/odslib.class.php');
	            $filename_ods="synthetiques_".$ide."_".$idq.".ods";
	            /// Creating a workbook
	            $workbook = new MoodleODSWorkbook("-");
	            /// Send HTTP headers
	            $workbook->send($filename_ods);
	            /// Creating the first worksheet
	            $myods = & $workbook->add_worksheet(traduction("resultats_synthetiques"));
	            $row=0;
	            $col=0;
	            foreach($entete_csv as $e) {
		            $myods->write_string($row, $col++,traduction($e,false));
	            }
            }


            $fp = fopen("{$CFG->chemin_ressources}/csv/".$filename, "w");

            $referentiels=get_referentiels();
            foreach($referentiels as $rowr) {
                $tpl->newBlock("e_score_r");
                $tpl->assign("t_score_ref",$rowr->referentielc2i);
                $ch_fp .= $CSV_SEP.$rowr->referentielc2i;

                if ($CFG->export_ods)
                	$myods->write_string($row, $col++,$rowr->referentielc2i);


            }

            $ch_fp .= $CSV_SEP.(to_csv(traduction("heure_validation")));
            if ($CFG->afficher_temps_passage)
                $ch_fp .= $CSV_SEP.(to_csv(traduction("t_duree")));

            $ch_fp .= $CSV_SEP.(to_csv(traduction("ip")));


            fputs($fp, $ch_fp."\n");

    		if ($CFG->export_ods) {
                	$myods->write_string($row, $col++,traduction("heure_validation"));
                    if ($CFG->afficher_temps_passage)
                            $myods->write_string($row, $col++,traduction("t_duree"));
                	$myods->write_string($row, $col++,traduction("ip"));

    				$row=1;
    				$col=0;
    		}


            foreach($examens as $exam) {

	            if ($res= get_inscrits($exam->id_examen,$exam->id_etab)) {
		            $nb_inscrits +=count($res); // ajouter en cas de pool rev 809
		            foreach($res as $ligne) {
			            $cp=compte_passages($exam->id_examen,$exam->id_etab,$ligne->login);
			            if ($type==0 && $cp==0) continue;
			            if ($type==1 && $cp >0) continue;

			            $tpl->newBlock("question");
			            $tpl->assign("n",$compteur_ligne);
			            $tpl->setCouleurLigne($compteur_ligne);
			            $ligne->examen=$exam->id_etab.".".$exam->id_examen; //ajout� au CSV  et a l'�cran en 1.5  (cas des pools)
			            $tpl->assignObjet($ligne);

			            $ch_fp=ligne_to_csv($ligne_csv,$ligne);

			            if($CFG->export_ods) {
			            	foreach($ligne_csv as $a){
			            		$myods->write_string($row, $col++,$ligne->$a);
			            	}
			            }


			            //calculs
			            $res=get_resultats($exam->id_examen,$exam->id_etab,$ligne->login,false);

			            $score_global=sprintf("%.{$arrondi}f",$res->score_global)." %";
			            $ch_fp .= $CSV_SEP.$score_global;

			            if($CFG->export_ods) {
			            		$myods->write_number($row, $col++,$score_global);

			            }

			            $tpl->assign("score",$score_global);
			            if ($res->origine) //qcmdirect ou autre �cran et csv
				            $res->ip_max=$res->origine;
			            $tpl->assign("ip",$res->ip_max);
			            $heuref=userdate($res->ts_date_max,'strftimedatetimeshort');
			            $tpl->assign("date",$heuref);
			            
			            $items=array();
			            $items[]=new icone_action('consulter',"consulterItem('{$ligne->login}',$idq,$ide)");
			            print_icones_action($tpl,'icones_actions',$items,'actions_'.$compteur_ligne);
			             
			            
			            
			            if ($CFG->afficher_temps_passage) {
				            $duree=format_time($res->ts_date_max-$res->ts_date_min,'',false);
				            $tpl->newBlock("duree_passage");
				            $tpl->assign("duree",$duree);
			            }
			            
			           
				            foreach ($res->tabref_score as $s) {
				            $tpl->newBlock("score_r");
				            if (!empty($s))  // rev 843 pour qcm par domaine (PAS de NOTE =BLANC!)
					            $s=sprintf("%.{$arrondi}f",$s)." %";
				            $tpl->assign("sref",$s);
				            $ch_fp.=$CSV_SEP.$s;
				            if($CFG->export_ods) {
					            $myods->write_number($row, $col++,$s);

				            }


			            }

			            $ch_fp.=$CSV_SEP.$heuref;
                          if ($CFG->afficher_temps_passage)
                                   $ch_fp.=$CSV_SEP.$duree;
                        $ch_fp.=$CSV_SEP.$res->ip_max;
			            fputs($fp, $ch_fp."\n");


			             if($CFG->export_ods) {
			            		$myods->write_string($row, $col++,$heuref);
                                  if ($CFG->afficher_temps_passage)
                                    $myods->write_string($row, $col++,$duree);
			            		$myods->write_string($row, $col++,$res->ip_max);


			            }

			            // passage a la ligne suivante
			            $compteur_ligne ++;
			            $row++; //ods
			            $col=0; //ods
		            }
	            }
            }

            fclose($fp);

            if ($CFG->export_ods) {
				$full_filename_ods=$workbook->close(); // fait le zip et dire ou il l'a mis
}

            break;
        case "referentiel" :
            //*******************************
            // referentiel
            //*******************************
            DEFINE('TAILLE_BARRE',50); //taille des barres verticales

            $tpl = new SubTemplatePower( $modeleRefs,T_BYVAR);
            $tpl->prepare($chemin);
            //emission des entetes des tables HTML et CSV
            //$referentiels=$noteuse->liste_referentiels();
             $referentiels=get_referentiels();
            foreach($referentiels as $rowr) {
                $tpl->newBlock("e_score_r");
                $tpl->assign("t_score_ref",$rowr->referentielc2i);
            }

            $filename=false; // pas de csv

            foreach($examens as $exam) {

	            if ($res= get_inscrits($exam->id_examen,$exam->id_etab)) {
		            $nb_inscrits +=count($res);  // ajouter en cas de pool rev 809
		            foreach($res as $ligne) {

			            // V 1.41
			            $cp=compte_passages($exam->id_examen,$exam->id_etab,$ligne->login);
			            if ($type==0 && $cp==0) continue;
			            if ($type==1 && $cp >0) continue;

			            $tpl->newBlock("question"); // une ligne d'examen (template inspire de celui des questions, d'o� le nom)
			            $tpl->assign("n",$compteur_ligne);
			            $tpl->setCouleurLigne($compteur_ligne);
			            $ligne->examen=$exam->id_etab.".".$exam->id_examen;
			            $tpl->assignObjet($ligne);
			            $items=array();
			            $items[]=new icone_action('consulter',"consulterItem('{$ligne->login}',$idq,$ide)");
			            print_icones_action($tpl,'icones_actions',$items,'actions_'.$compteur_ligne);
			             
			            $res=get_resultats($exam->id_examen,$exam->id_etab,$ligne->login,false);

			            $score_global=sprintf("%.{$arrondi}f",$res->score_global)." %";
			            $tpl->assign("score",$score_global);
			            //sont deja en %
			            foreach ($res->tabref_score as $ref=>$s) {
				            //  print "$ref $s<br/>";
				            $tpl->newBlock("score_r");

				            // $tpl->newBlock("histogramme");
				            if ($s>=0)  // pas d'histo n�gatif et rien si pas de note VIDE
					            $tpl->assign("height",$s*TAILLE_BARRE /100);
				            else
					            $tpl->assign("height",1);
				            $tpl->assign("score",$s." %");
				            $tpl->assign("score_ref",sprintf("%.0f%%",$s));
			            }
			            $compteur_ligne ++;
		            }
	            }
            }
            $tpl->assignGlobal("max_height",TAILLE_BARRE);  //taille des barres

            //*******************************
            // fin referentiel
            //*******************************
            break;

    }

    $tpl->assign("_ROOT.ide",$ide);
    $tpl->assign("_ROOT.idq",$idq);
    // V 1.41
    switch ($type) {
            case 0: $ch= $compteur_ligne ." ".traduction("passages")." / ".$nb_inscrits." ".traduction("inscrits"); break;
            case 1: $ch= $compteur_ligne ." ".traduction("non_passes")." / ".$nb_inscrits." ".traduction("inscrits"); break;

            default: $ch= $nb_inscrits." ".traduction( "inscrits"); break;

    }
     $tpl->assignGlobal('nb_passages',$ch);


    $items=array();

// v 1.5 menu de niveau 2 standard (cf weblib)
$tpl->gotoBlock("_ROOT");
//$items[]=get_menu_item_criteres();
if( $filename) {
    $items[]=get_menu_item_csv($filename);
    if ($CFG->export_ods)
		$items[]=get_menu_item_ods($filename_ods,'tmp/ods');
}
$items[]=get_menu_item_imprimer();
//$items[]=get_menu_item_legende("resultats");  rev 984 pas de l�gende dans cet �cran, pas d'icones dans les listes'

print_menu($tpl,"menu_niveau2",$items);


    return $tpl->getOutputContent();

}


/**
 * met en forme les r�sultats recus de la noteuse
 * a partir du template definis pour l'examen
 */
function affiche_template_resultats ($note_inscrit,$resu_notmin) {
    global $CFG,$USER;
	$texte_resultats = $resu_notmin->template_resultat;

	$arrondi=$CFG->nombre_decimales_score;
	$score_global = max(0, round($note_inscrit->score_global, 2));
	$score_global =  sprintf("%.{$arrondi}f",$score_global)." %";


	$texte_resultats = str_replace("[[ScoreGlobal]]", $score_global, $texte_resultats);
	$note = $note_inscrit->tabref_score;

	$domaines_ar = "";

	$refs = get_referentiels();

	foreach ($refs as $domaine) {
		$ref = $domaine->referentielc2i;
		$texte_resultats = str_replace("[[Domaine$ref]]", $domaine->referentielc2i." : ".$domaine->domaine, $texte_resultats);
        $score = sprintf("%.{$arrondi}f",$note[$ref])." %";

		$texte_resultats = str_replace("[[Score$ref]]", $score, $texte_resultats);
		if ($note[$ref] !=-1) { //pas de questions dessus possible ???
			if ($note[$ref] < $resu_notmin->resultat_mini) {
				$domaines_ar .= " " . $domaine->referentielc2i;
			}
		}
	}

	$texte_resultats = str_replace("[[Domaine_a_revoir]]", $domaines_ar, $texte_resultats);
	$texte_resultats = str_replace("[[Date_du_jour]]", userdate($note_inscrit->ts_date_max), $texte_resultats);
	$texte_resultats = str_replace("[[Client]]", $USER->ip, $texte_resultats);
	$matches = preg_split("/##(.*)##/i", $texte_resultats, -1, PREG_SPLIT_DELIM_CAPTURE);
	$fiche = "";
	$x= 0;
	for ($i = 0;  $i < count($matches); $i++){
		$val = $matches[$i];
		if (preg_match("/score_global/i", $val, $m, PREG_OFFSET_CAPTURE) || (isset($exist) && !(preg_match("/FIN SI/i", $val, $m, PREG_OFFSET_CAPTURE)))){
			$val = str_replace(" score_global", $note_inscrit->score_global, str_replace("%", "", $val));
			$val = str_replace("&lt;", "<", $val);
			$val = str_replace("&gt;", ">", $val);
			$val = str_replace("&amp;", "&", $val);
			$val = str_replace("SI", "", $val);
			$test = "if ($val) return 1; return 0;";
			$exist = @eval($test);
			if ($exist){
				$fiche .= $matches[$i+1];
				$i++;
			}
		}
		elseif (preg_match("/FIN SI/i", $val, $m, PREG_OFFSET_CAPTURE)){
			unset($exist);
		}
		else{
			$fiche .= $val;
		}
	}
	$tpl = new SubTemplatePower( $fiche,T_BYVAR);
	$tpl->prepare($CFG->chemin);
    	return $tpl->getOutputContent();
}



/**
 * fonction de tests de la PF
 */

 function simule_un_passage ($id_examen, $id_etab,$login) {
     global $REMOTE_ADDR,$CFG;
     
     $output="";
     //rev 948
     if (compte_passages($id_examen,$id_etab,$login) >0) {
        // echo "$login deja passé $id_examen $id_etab<br/>";
         return;
     }

    

    $cle=$id_etab.".".$id_examen;
     $output = "simulation passage de ".$login." a ".$cle."<br/>";

    delete_records("resultats", "login='".addslashes($login)."' and examen='$cle'");  // rev 984
    $questions=get_questions($id_examen,$id_etab,true,false);  // desordre

    $ligne=new StdClass();
    $ligne->login=$login;
    $ligne->examen=$cle;
    $ligne->origine='simulation';
    $ligne->ts_date=time();
    $ligne->ip=$REMOTE_ADDR;
    foreach ($questions as $q) {
        $ligne->question=$q->id_etab.".".$q->id;
        $reponses=get_reponses($q->id,$q->id_etab, true);  //desordre
        $ligne->reponse=$reponses[0]->num;   // prend la 1ere comme r�ponse �tudiant
       insert_record("resultats",$ligne,false);
    }
     $res=note_examen($id_examen,$id_etab,QCM_NORMAL,$login,false,false,false,false);
     return $output;
 }


/**
 * attention pas pour un pool
 */

function simule_passage ($id_examen, $id_etab) {

    $examen=get_examen($id_examen,$id_etab);
    if ($examen->est_pool) simule_passage_pool($id_examen,$id_etab);
    else {
        $inscrits=get_inscrits($id_examen,$id_etab);
        foreach( $inscrits as $i)
            simule_un_passage($id_examen,$id_etab,$i->login);
    }
}

function simule_passage_pool ($id_examen, $id_etab) {
    $examen=get_examen($id_examen,$id_etab);
    if (! $examen->est_pool) simule_passage($id_examen,$id_etab);
    $inscrits=get_inscrits($id_examen,$id_etab);
    $fils=liste_groupe_pool($id_examen,$id_etab);

    $nb_par_fils=floor(count($inscrits) / count($fils));
    $i=0;
    foreach($fils as $f) {
      //vire les anciennes inscriptions aux fils (on est en test !)
        $critere =<<<EOS
            id_examen=$f->id_examen
                and id_etab=$f->id_etab
EOS;
        delete_records("qcm",$critere,false);
        $tags='simulation passage pool '.time();

        for ($num=0;$num<$nb_par_fils;$num++) {
            print "inscription de ".$inscrits[$i]->login." a ".$f->id_etab.".".$f->id_examen."<br/>";
            inscrit_candidat($f->id_examen,$f->id_etab,$inscrits[$i]->login,$tags);
            simule_un_passage($f->id_examen,$f->id_etab,$inscrits[$i]->login);
            $i++;
        }

    }

}



function compte_simulations ($id_examen, $id_etab) {
	$examen=get_examen($id_examen,$id_etab);
	if ($examen->est_pool) {
		$fils=liste_groupe_pool($id_examen,$id_etab);
		$n=0;
		foreach($fils as $f) {
			$n += compte_simulations($f->id_examen,$f->id_etab);
		}
		return $n;
	}
	$cle=$id_etab."_".$id_examen;
	$critere="examen='".$cle."' and origine ='simulation' ";
	$n=count_records('resultatsexamens',$critere,false);
	//print $n;
	return $n;
}

function annule_simulation ($id_examen, $id_etab) {
	$examen=get_examen($id_examen,$id_etab);
    if ( $examen->est_pool){
    	$fils=liste_groupe_pool($id_examen,$id_etab);
		foreach($fils as $f) {
    	 	annule_simulation($f->id_examen,$f->id_etab);
		}
		return;
    }
    $cle=$id_etab."_".$id_examen;
    $critere="examen='".$cle."' and origine ='simulation' ";

    $passages=get_records('resultatsexamens',$critere,false);
    foreach ($passages as $p) {
	    // il n'y a pas de champ origine dans ces trois tables !' TODO v1.6-
	    $critere2="examen='".$cle."' and login='".addslashes($p->login)."' "; // rev 984
	    delete_records("resultatscompetences",$critere2,true);
	    delete_records("resultatsdetailles",$critere2,true);
	    delete_records("resultatsreferentiels",$critere2,true);
    }

    delete_records("resultatsexamens",$critere,true);

    $cle=$id_etab.".".$id_examen;
    $critere="examen='".$cle."' and origine ='simulation' ";
    delete_records("resultats",$critere,true);
}







if (0) {
    set_time_limit(0);
    simule_passage(342,65);
   // simule_passage_pool(318,1);
}

if (0) {
 for ($i=10; $i<100; $i=$i+10)
 print("<br/>".get_preconisations($i));
}
