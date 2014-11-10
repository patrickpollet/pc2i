<?php

/**
 * @version $Id: reponse_par_etudiant2.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
/*
 * rev 819  gere aussi les examens de type pool (recherche le bon fils )
 */

$fiche=<<<EOF


<form name="go" action="reponse_par_etudiant2.php" method="post">
<input type="hidden" name="idq" value="{idq}" />
<input type="hidden" name="retour_fiche" value="{retour_fiche}" />

<input type="hidden" name="ide" value="{ide}" />

<div class="centre">
<span class="taille2">{message}</span>


{select_login}

<br/> <b>{form_stats} </b> :
        <b>{t_nb} </b>: {nb}
        <b>{t_mini} </b>: {mini}
        <b>{t_maxi} </b>: {maxi}
        <b>{t_moyenne} </b>: {moyenne}
          <b>{t_ec} </b>: {stddev}
        <br/>
</div>
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}"/>
<!-- END BLOCK : id_session -->

<!-- START BLOCK : infos -->
<div id="corps2">
	<div id="xx">
		<ul id="tabs">
			<li> <a class="active_tab" 	href="#scores">{scores} </a></li>
			<li> <a class="" 			href="#corrige">{corrige}</a> </li>
<!-- START BLOCK : parcours_tab -->
            <li> <a class=""            href="#parcours">{parcours}</a> </li>
<!-- END BLOCK : parcours_tab -->
		</ul>

<div class="panel" id="scores">
<span class="titre1">{nom_du_candidat} score global : {scoreg}<br/>
{nom_reel_examen}</span>
<!-- START BLOCK : repasser -->
  <div class="information" >
   {info_repasser_examen}
   <input type="hidden" value="{id_repasser}" name="id_repasser"/>
   <input type="hidden" value="{ide_repasser}" name="ide_repasser"/>
   <input type="hidden" value="{idq_repasser}" name="idq_repasser"/>

   {bouton_repasser}
 </div>
<!-- END BLOCK : repasser -->
{resultats}
<!-- START BLOCK : renoter -->
  <div class="information" >
   {info_notes_manquantes}
    <input type="hidden" value="{id_renoter}" name="id_renoter"/>
   <input type="hidden" value="{ide_renoter}" name="ide_renoter"/>
   <input type="hidden" value="{idq_renoter}" name="idq_renoter"/>
   {bouton_renoter}
 </div>
<!-- END BLOCK : renoter -->
{liste_liens}

</div>
<div class="panel" id="corrige">
{correction}
</div>

<!-- START BLOCK : parcours -->
<div class="panel" id="parcours">
   <div class="titre1">{nom_parcours} </div>
   <div class="droite commentaire1">{nb_items}</div>
        <div id="menuLayer" class="gauche">
            {parcours_ici}
        </div>
</div>
<!-- END BLOCK : parcours -->


</div>
</div>
<!-- END BLOCK : infos -->
</form>

EOF;


$chemin = '../../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres
require_once ($chemin_commun . "/lib_resultats.php"); //n'est pas charg� par c2i_params

$ide=required_param("ide",PARAM_INT);
$idq=required_param("idq",PARAM_INT);
$retour_fiche=optional_param("retour_fiche","0",PARAM_INT);
$id_us=optional_param("id_us","",PARAM_RAW);

//$renoter=optional_param("renoter","",PARAM_RAW);
//$repasser=optional_param("repasser","",PARAM_RAW);

$id_renoter=optional_param("id_renoter","",PARAM_RAW);
$id_repasser=optional_param("id_repasser","",PARAM_RAW);


require_login('P'); //PP
v_d_o_d("el");


$ligne=get_examen($idq,$ide);
// attention
// si on a chang� d'etudiant' id_repasser = le login de l'ancien �tudiant
// il faut donc le comparer au nouvel (id_us) sinon vire les notes du nouveau !!!!

	if ($id_repasser && ( $id_repasser==$id_us) && $CFG->permettre_repasser_examen) {
		//print"$id_us autoris� a repasser son examen<br/>";
		//attention si pool ce peut �tre un examen different du 'p�re'
		$idex=required_param("idq_repasser",PARAM_INT);
		$idexe=required_param("ide_repasser",PARAM_INT);
		purge_resultats_inscrit($idex,$idexe,$id_us);
	}


	if ($id_renoter && ( $id_renoter==$id_us)) {
		//print"$id_us renot�<br/>";
		//attention si pool ce peut �tre un examen different du 'p�re'
		$idex=required_param("idq_renoter",PARAM_INT);
		$idexe=required_param("ide_renoter",PARAM_INT);
		note_examen($idex,$idexe,QCM_NORMAL,$id_us,false,false,false,false);
	}


require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates
$tpl->assignInclude("corps", $fiche,T_BYVAR); //
$tpl->prepare($chemin);

$CFG->utiliser_fabtabulous_js=1;

$tpl->assign("_ROOT.titre_popup", nom_complet_examen($ligne));

// liste des PASSAGES � l'examen
// attention avec un pool
$inscrits=get_passages($idq,$ide);


$tpl->assign("message", traduction("resultats_de"));

$tpl->assign("ide", $ide);
$tpl->assign("idq", $idq);
$tpl->assign("retour_fiche", $retour_fiche);

//pb avec l'anonyme. Ses r�sultats ne sont pas stock�s ...
//maj info affich�es dans le select
foreach ($inscrits as $inscrit) {
    $inscrit->nom_complet=_regle_nom_prenom($inscrit->nom,$inscrit->prenom)." ".$inscrit->numetudiant;
}
print_select_from_table ($tpl,"select_login",       //template et nom balise
                        $inscrits,                  //table des options
                        "id_us",                    // nom du select
                        "saisie",                   //classe
                        "onchange='javascript:go.submit();'",  //attributs supp.
                        "login","nom_complet",       // valeur et texte des options
                        traduction("candidat"),      // texte option neutre
                        $id_us);                     // valeur a selectionner


$tpl->gotoBlock("_ROOT"); //aussi grrr
if ($id_us) {
   // echo $id_us;
	$tpl->newBlock("infos");
	$tpl->assign("nom_du_candidat",get_fullname($id_us));
	$tpl->assignGlobal("scoreg","");
	//l etudiant a t'il passe cet examen ?
	$histo=get_historique($idq,$ide,$id_us);
	$nbrep=count($histo);
	if ($nbrep == 0) {
		$tpl->traduit("resultats", "pas_de_resultats");
		if (!$ligne->est_pool)
            $ret=imprime_examen($idq,$ide,false,false,false,$montre_ref=true,$mode=QCM_CORRIGE);
         else
            $ret=array(traduction("info_pas_corrige_pool"),"");   //pas de corrig� disponible
		$tpl->assign("infos.correction", $ret[0]);
	} else {
		$res=get_resultats($idq,$ide,$id_us,false);  //relire depuis la BD  ou renoter

		if ($res->score_global ==-1) {  // a des r�ponses mais pas de notes ...
           //N� reel examen renvoy� par get_historique !!!
         //print_r($res);
         list($idexe,$idex)=explode(".",$histo[0]->examen);

         	$tpl->traduit("infos.resultats", "pas_de_resultats");
            if (teste_droit("em")) {  // rev 828
			$tpl->newBlock ("renoter");
            $tpl->assign("id_renoter",$id_us);
            $tpl->assign("ide_renoter",$idexe);  //important id reel en cas de pool
            $tpl->assign("idq_renoter",$idex);
			$tpl->assign("bouton_renoter", get_bouton_action("renoter","","","submit" ));
            $tpl->assign ("infos.liste_liens","");
            }
		} else {
         //N� reel examen renvoy� par get_resultats
         //print_r($res);
         list($idexe,$idex)=explode(".",$res->examen);
			$tpl->assignGlobal("scoreg",sprintf("%.{$CFG->nombre_decimales_score}f",$res->score_global)." %");
			$resultats=affiche_resultats($res,$ligne->resultat_mini, false); //avec parcours ????
			$tpl->assign("infos.resultats",$resultats);
			if (teste_droit("em") &&  //rev 828
               $CFG->permettre_repasser_examen && ! is_utilisateur_anonyme($id_us)) {
				if ($res->origine !='amc') {  // rev 1020 amc envoie des notes , pas des cases donc on ne peut pas
					$tpl->newBlock ("repasser");
					$tpl->assign("id_repasser",$id_us);  //important ne le faire au submit que si c'est le m�me candidat '
					$tpl->assign("ide_repasser",$idexe);  //important id reel en cas de pool
					$tpl->assign("idq_repasser",$idex);

					$tpl->assign("repasser.bouton_repasser", get_bouton_action("repasser","","","submit" ));
				}
			}
            if ($CFG->utiliser_notions_parcours && $CFG->creer_parcours_html) {
                $parcHTML=cree_parcours_HTML($ligne,$res);
                $tpl->assign ("infos.liste_liens",$parcHTML);
            } else $tpl->assign ("infos.liste_liens","");


		}
       //en cas de pool attention
       if ($ligne->est_pool) {
        $tpl->assignGlobal("nom_reel_examen",nom_complet_examen(get_examen($idex,$idexe)));
       }else {
        $tpl->assignGlobal("nom_reel_examen","");
       }
		$ret=imprime_examen($idex,$idexe,false,false,false,$montre_ref=true,$mode=QCM_CORRECTION,$id_us);
		$tpl->assign("infos.correction", $ret[0]);


		$avecParcours= $USER->type_plateforme == "positionnement"
			&& !is_utilisateur_anonyme($id_us) && $CFG->utiliser_notions_parcours;
		if ($avecParcours) {
			add_javascript($tpl,$CFG->chemin_commun."/pear//HTML_TreeMenu/TreeMenu.js"); //important !
			$tpl->newBlock("parcours_tab");
			$tpl->newBlock("parcours");
            $cle=$idexe."_".$idex;
            if ($parc=get_record("parcours","examen='$cle' and login='".addslashes($id_us)."'",false)) { // rev 984
             require_once($CFG->chemin_commun."/pear/HTML_TreeMenu/TreeMenu.php");
             $menu=parcours_en_menu($parc->id_parcours);
             $treeMenu = &new HTML_TreeMenu_DHTML($menu, array('images' => $CFG->chemin_commun."/pear/HTML_TreeMenu/images",
                   'defaultClass' => 'treeMenuDefault'));

                $tpl->assign("parcours_ici",$treeMenu->toHTML());

                $tpl->assign("nom_parcours",$parc->titre);
               // $indice_max =count_records ("notionsparcours","id_parcours=$parc->id_parcours");
                $indice_max =count_records ("ressourcesparcours","id_parcours=$parc->id_parcours");
                $tpl->assign("nb_items",$indice_max." ".traduction("ressources"));
            } else {
                $tpl->assign("nom_parcours","");
                $tpl->assign("parcours_ici","<div class='information_gauche'>".traduction ("msg_pas_de_parcours")."</div>");
                $tpl->assign("nb_items","");

            }

		}
	}
}
else {
	//rien
}

$tpl->gotoBlock("_ROOT");
$stats=get_stats_examen($idq,$ide);
	$tpl->assignObjet($stats);
	$tpl->assign("moyenne",sprintf("%.2f",$stats->moyenne));



$url_retour=($retour_fiche? $CFG->chemin."/codes/examens/fiche.php?ide=" . $ide . "&amp;idq=" . $idq."#resultats": "");
$tpl->print_boutons_fermeture($url_retour);


$tpl->printToScreen(); //affichage
?>

