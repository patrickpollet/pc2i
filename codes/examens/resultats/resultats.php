<?php

/**
 * @author Patrick Pollet
 * @version $Id: resultats.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	Affichage des résultats d'examen (complets => par étudiant : score global  + score par question + score par référentiel c2i)
//
////////////////////////////////

/*----------------REVISIONS----------------------
v 1.1 : PP 16/10/2006 ajout de
      - tests divisions par 0
      - lien CSV remplacé par un appel à commun/send_csv.php?idf=... (sécurité)
      - écriture dans le fichier ligne à ligne et plus tout d'un coup à la fin
      (bug Laurence avec plusieurs centaines de lignes ?)

V 1.5 utilise la classe noteuse
------------------------------------------------*/


$chemin = '../../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");	//fichier de param�tres
//require_once($chemin_commun."/dates.php");
//require_once($chemin_commun."/fonctions_resultat.php");
//test version V2
require_once($chemin_commun."/lib_resultats.php");

require_login('P'); //PP



$idq=required_param("idq",PARAM_INT,"");
$ide=required_param("ide",PARAM_INT,"");
$affichage=required_param("affichage",PARAM_ALPHANUM);
$retour_fiche=optional_param("retour_fiche","0",PARAM_INT);

v_d_o_d("el");

$ligne=get_examen($idq,$ide);



require_once( $chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates

$tpl = new C2IPopup(  );	//cr�er une instance
//inclure d'autre block de templates
//$tpl->assignInclude("corps",$chemin."/templates/resultats_examen.html");	// le template g�rant les r�sultats complets et synth�tiques
//ttemplate rappatri� ici
$modele=<<<EOM

 <!-- START BLOCK : stats -->
               <b>{form_stats}</b> :
                   <b>{t_nb} </b>: {nb}
                   <b>{t_mini} </b>: {mini}
                   <b>{t_maxi} </b>: {maxi}
                   <b>{t_moyenne} </b>: {moyenne}
                     <b>{t_ec} </b>: {stddev}

             <!-- END BLOCK : stats -->
<!-- INCLUDESCRIPT BLOCK : ./actions_js_resultats.php -->
{resultats}
<!-- START BLOCK : export -->

<textarea cols="80" rows="25">
{resultats_export}
</textarea>
<!-- END BLOCK : export -->
{form_actions}
EOM;

//pas de multipagination car on g�n�re aussi un  csv
$CFG->utiliser_tables_sortables_js=1;

$tpl->assignInclude("corps",$modele,T_BYVAR);
$tpl->prepare($chemin);

//construction url_retour pour desinsription et ajout doPopup()
print_form_actions ($tpl,'form_actions','','resultats.php');


set_time_limit(0);

$afficheTable=1;
$type=0;
$nomexamen= nom_complet_examen($ligne);

$stats=get_stats_examen($idq,$ide);
if ($stats->nb) {
	$tpl->newBlock("stats");
	$tpl->assignObjet($stats);
	$tpl->assign("moyenne",sprintf("%.2f",$stats->moyenne));
}
switch ($affichage)	{
	default :
		exit;
	case "complets" :
		$tpl->assign("_ROOT.titre_popup",traduction("resultats_complets")."<br/>".$nomexamen);

		break;

	case "synthetiques" :
		$tpl->assign("_ROOT.titre_popup",traduction("resultats_synthetiques")."<br/>".$nomexamen );

		break;

	case "referentiel" :
		$tpl->assign("_ROOT.titre_popup",traduction("resultats_par_domaine")."<br/>".$nomexamen);

		break;
        case "extdb": // BD externe on n'affiche pas
                $tpl->assign("_ROOT.titre_popup",traduction("export_bd_mysql")."<br/>".$nomexamen );
                $afficheTable=0;
                $affichage="synthetiques";
                break;
}
//pas possible en V1.5 (l'option n'est pas offerte si c'est un pool (regression par rapport � la 1.4) !!!
/****
 * de toute facon de code est faux car il envoie x fois la fiche complete evec x entetes, x 'sortie tableur' x 'imprimer' ...
 * TODO le gerer dans lib_resultats.php !!!
 * probleme : les questions ne sont pas les m�mes, donc pas les m�mes entetes pour chacun en mode detaill� !  ...
 */
if ($ligne->est_pool==1 && $affichage=="complets"){  //pas le choix
	$resultats = "";
	$groupes = liste_groupe_pool($idq, $ide);
        // TODO envoyer dans le CSV plus d'info en cas de pool ($ide, $idq ,$indice?)
	foreach ($groupes as $groupe){
		$resultats .= return_tableau_resultats_examen($groupe->id_etab, $groupe->id_examen ,$affichage,$type,$retour_fiche);
	}
}
else
        // pour un pool on se debrouille dans lib_resultats si pas complets !
        // il FAUT donc afficher la colonne de l'examen r��l
        $resultats=return_tableau_resultats_examen($ide, $idq ,$affichage,$type,$retour_fiche);

if ($afficheTable) $tpl->assign("_ROOT.resultats", $resultats);
else {
   //recupere et traite le CSV synthetique juste produit
   $tpl->newBlock ("export");

   $resultats="connexion à la base externe ...";
   $connexion2=Connexion($extDB['user'],$extDB['password'],$extDB['db'],$extDB['host']);
   $resultats .="Ok\n";

   $fname=$CFG->chemin_ressources."/csv/synthetiques_".$ide."_".$idq.".csv";
   $fp = fopen($fname, "r");
   if ($fp) {
     // --- get header (field names) ---
      $header = explode($CFG->csv_separateur, fgets($fp,1024));
      //$resultats .=print_r($header,true)."\n";
      $set="( ";
      $i=0;
      foreach ($extDB['map'] as $colonne)
          if ($colonne)
	      $set .=($i++ ? ",":""). $colonne;

      $extraValues="";
      foreach ($extraColonnes as $colonne=>$valeur) {
	      $set .=",".$colonne;
	      switch ($valeur) {
	      //completer cette liste si necessaire
		      case "TYPEP": $extraValues .=",'".($USER->type_plateforme=='positionnement'?"P":"C")."'"; break;
		      case "ANNEE" :
			      #calcul automatique de l'annee scolaire dans $strCetteAnnee
			      $m= date("m");
			      $a= date("Y");
			      if ($m>=9)   // Septembre et apres
				      $strCetteAnnee=$a;
			      else
				      $strCetteAnnee=$a-1;
			      $extraValues .=$strCetteAnnee;
			      break;
			  case "TSDEBUT": // rev 984
			      $extraValues .=",".$ligne->ts_datedebut; 
			      break;    

		      default: $extraValues .=",''";  // varaible C2I non trouvee, mettre une valeur vide.
	      }
      }
      $set .=")";
      $resultats.=$set;
      $nbOk=0;
      $nbKo=0;
      while (!feof ($fp)) {
                //Note: commas within a field should be encoded as &#44 (for comma separated csv files)
                //Note: semicolon within a field should be encoded as &#59 (for semicolon separated csv files)

                $line = explode($CFG->csv_separateur, fgets($fp,1024));
		//$resultats .=print_r($line,true)."\n";
		if (!$line[0]) continue ; // lignes vides
		$values="(";
		 $i=0;
		 $j=0;
		foreach ($extDB['map'] as $colonne) {
          		if ($colonne)
	      			$values .=($i++ ? ",":"")."'" .trim($line[$j])."'";
			$j++;
		}
		if ($extraValues) $values .=",".$extraValues;
		$values .=")";
		$sql ="INSERT INTO ".$extDB["table"]." $set VALUES  $values ";
		$resultats .="$sql ";
		if (ExecRequete ($sql,$connexion2,0)) {
                   $resultats .=" OK\n";
		   $nbOk++;
		}
		else {
		  $resultats .="KO \n".  mysqli_error($connexion2)."\n";
		  $nbKo++;
		}
      }
      fclose($fp);
      $resultats .=" $nbOk transférés avec succès et $nbKo erreurs\n";
   } else {
       $resultats.=" accés au fichier CSV $fname impossible ??? \n";
   }
   $tpl->assign("resultats_export", $resultats);
}
$tpl->gotoBlock("_ROOT");

$url_retour=($retour_fiche? $CFG->chemin."/codes/examens/fiche.php?ide=" . $ide . "&idq=" . $idq."#resultats": "");
$tpl->print_boutons_fermeture($url_retour);



$tpl->printToScreen();
?>
