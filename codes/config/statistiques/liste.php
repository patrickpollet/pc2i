<?php

/**
 * @author Patrick Pollet
 * @version $Id: liste.php 1198 2011-01-26 17:09:46Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '../../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres

require_login('P'); //PP
v_d_o_d("qv");  //rev 911

require_once( $chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates
require_once($chemin_commun."/lib_resultats.php");
$tpl = new C2iPopup(  );	//cr�er une instance
//inclure d'autre block de templates

$fiche=<<<EOF

<form name="form_criteres" class="normale" method="get" action="liste.php">
<p class="double">
  <label for="type_stats">{type_statistiques} : </label>
				<select name="type_stats" id="type_stats" class="saisie">
				    <option value="util_ex" {util_ex}>{stat_util_ex}</option>
                    <option value="util_q" {util_q}>{stat_util_q}</option>
                    <option value="util_ref" {util_ref}>{stat_util_ref}</option>
					<option value="util_comp" {util_comp}>{stat_util_comp}</option>

                    <option value="nb_cand" {nb_cand}>{stat_nb_cand}</option>
					<option value="rep_q" {rep_q}>{stat_rep_q}</option>
					<option value="domaine" {domaine}>{stat_domaine}</option>
                    <option value="comp" {comp}>{stat_comp}</option>
				</select>
			 {bouton:ok}
</p>

<p class="double">
   <label for="f_date_debut">{form_debut_examens} : </label>

 <input type="text" name="date_debut" id="f_date_debut" size="30" readonly="readonly" value="{date_debut}" />
 <img src="{chemin_images}/calendar.gif" id="f_trigger_debut" style="cursor: pointer; border: 1px solid red;" title="{alt_choix_date_debut}"
      onmouseover="this.style.background='red';" onmouseout="this.style.background=''" alt=""/>

<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_debut",     // id of the input field
        ifFormat       :    "{jscalendar_if}",      // format of the input field
        date         :"{date_debut}",
          showsTime      :    true,            // will display a time selector
        button         :    "f_trigger_debut",  // trigger for the calendar (button ID)
        align          :    "Tl",           // alignment (defaults to "Bl")
        singleClick    :    false,
         weekNumbers    : true       // Show week numbers
    });
</script>
</p>
<p class="double">
  <label for="f_date_fin">{form_fin_examens} : </label>

<input type="text" name="date_fin" id="f_date_fin" size="30" readonly="readonly" value="{date_fin}" />
 <img src="{chemin_images}/calendar.gif" id="f_trigger_fin" style="cursor: pointer; border: 1px solid red;" title="{alt_choix_date_fin}"
      onmouseover="this.style.background='red';" onmouseout="this.style.background=''" alt=""/>

<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_fin",     // id of the input field
        ifFormat       :    "{jscalendar_if}",      // format of the input field
         date         :"{date_fin}",
          showsTime      :    true,            // will display a time selector
        button         :    "f_trigger_fin",  // trigger for the calendar (button ID)
        align          :    "Tl",           // alignment (defaults to "Bl")
        singleClick    :    false,
        weekNumbers    : true       // Show week numbers
    });
</script>
</p>

<input name="debut_tous" type="hidden" value="{debut_tous}" />
<input name="fin_tous" type="hidden" value="{fin_tous}" />
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}"/>
<!-- END BLOCK : id_session -->

</form>

<table width="100%">
   <tr>
     <td>{menu_niveau2}</td>
  </tr>
</table>

<!-- START BLOCK : stat -->
<table width="100%" class="listing" id="sortable" >
<thead>
        <tr {bulle:astuce:msg_tri_colonnes}>
<!-- START BLOCK : entete -->
     <!-- START BLOCK : e_id -->
         <th class="bg"> {quoi}  </th>
      <!-- END BLOCK : e_id -->

<th class="bg">{valeur}&nbsp;</th>
<!-- END BLOCK : entete -->
</tr>
</thead>
<tfoot>
<!-- START BLOCK : ligne_total -->
          <tr class='{paire_impaire}'>
          <!-- START BLOCK : id_total -->
          <td> {id} </td>
            <!-- END BLOCK : id_total -->
<!-- START BLOCK : case_total -->
            <td class="centre">{valeur}</td>
<!-- END BLOCK : case_total -->
          </tr>
<!-- END BLOCK : ligne_total -->

<tr><td colspan="{colspan}">{nb} {quoi}</td> </tr>
</tfoot>
<tbody>
<!-- START BLOCK : ligne -->
          <tr class='{paire_impaire}'>
          <!-- START BLOCK : id -->
          <td> {id}</td><td> {libelle} <ul style="display:inline;">{consulter_fiche} </ul></td>
            <!-- END BLOCK : id -->
<!-- START BLOCK : case -->
            <td class="centre">{valeur}</td>
<!-- END BLOCK : case -->
          </tr>
<!-- END BLOCK : ligne -->
</tbody>

</table>
<!-- END BLOCK : stat -->



<!-- START BLOCK : util_q -->
<table width="100%" class="listing" id="sortable" >
  <thead>
    <tr {bulle:astuce:msg_tri_colonnes}>
      <th  class="bg"> {t_id} </th>
      <th    class="bg"> {t_referentiel}</th>
      <th    class="bg"> {t_titre}</th>
        <th   class="bg">  {t_examen}  </th>
        <th   class="bg">  {t_nb}  </th>
        <th   class="bg">  {t_mini}  </th>
        <th   class="bg">  {t_maxi}  </th>
        <th   class="bg">  {t_moyenne}  </th>
         <th   class="bg">  {t_ec}  </th>
         <!-- START BLOCK : entete_stats2 -->
         <th   class="bg">  {t_idisc}</th>
         <th   class="bg">  {t_cdisc}  </th>
          <!-- END BLOCK : entete_stats2 -->
  </tr>
</thead>
<tfoot>
<tr>
<td colspan="{colspan}" > {nb} {questions} </td>
</tr>
</tfoot>

<tbody>
<!-- START BLOCK : ligne_q -->
<tr class="{paire_impaire}">
<td>{id}</td>
<td>{ref_alin}</td>
<td>{libelle} <ul style="display:inline;">{consulter_fiche} </ul></td>
<td class="droite">{nb_examen}</td>
<td class="droite">{nb}</td>
<td class="droite">{mini}</td>
<td class="droite">{maxi}</td>
<td class="droite">{moyenne}</td>
<td class="droite">{stddev}</td>
 <!-- START BLOCK : stats2 -->
<td class="droite">{idisc}</td>
<td class="droite">{cdisc}</td>
 <!-- END BLOCK : stats2 -->

</tr>
<!-- END BLOCK : ligne_q -->
</tbody>
</table>
<!--END BLOCK : util_q -->


<!-- START BLOCK : util_ex -->
<table width="100%" class="listing" id="sortable" >
  <thead>
    <tr {bulle:astuce:msg_tri_colonnes}>
      <th  class="bg"> {t_id} </th>
      <th    class="bg"> {t_titre}</th>
        <th   class="bg">  {t_nbq}  </th>
        <th   class="bg">  {t_nb}  </th>
        <th   class="bg">  {t_mini}  </th>
        <th   class="bg">  {t_maxi}  </th>
        <th   class="bg">  {t_moyenne}  </th>
        <th   class="bg">  {t_ec}  </th>
     </tr>
</thead>

<tfoot>
<tr>
<td colspan="8" > {nb} {examens} </td>
</tr>
</tfoot>

<tbody>
<!-- START BLOCK : ligne_ex -->
<tr class="{paire_impaire}">
<td>{id}</td>
<td>{libelle} <ul style="display:inline;"> {consulter_fiche}</ul></td>
<td class="droite">{nbq}</td>
<td class="droite">{nb}</td>
<td class="droite">{mini}</td>
<td class="droite">{maxi}</td>
<td class="droite">{moyenne}</td>
<td class="droite">{stddev}</td>

</tr>
<!-- END BLOCK : ligne_ex -->
</tbody>
</table
>
<!--END BLOCK : util_ex-->

<!-- START BLOCK : util_ref -->
<table width="100%" class="listing" id="sortable" >
  <thead>
    <tr {bulle:astuce:msg_tri_colonnes}>
      <th  class="bg"> {t_id} </th>
      <th    class="bg"> {t_titre}</th>
       <th   class="bg">  {t_examen}  </th>
        <th   class="bg">  {t_nb}  </th>
        <th   class="bg">  {t_mini}  </th>
        <th   class="bg">  {t_maxi}  </th>
        <th   class="bg">  {t_moyenne}  </th>
         <th   class="bg">  {t_ec}  </th>
</tr>
</thead>
<tfoot>
<tr>
<td colspan="8" > {nb} {referentiels} </td>
</tr>
</tfoot>

<tbody>
<!-- START BLOCK : ligne_ref -->
<tr class="{paire_impaire}">
<td>{id}</td>
<td>{libelle} </td>
<td class="droite">{nb_examen}</td>
<td class="droite">{nb}</td>
<td class="droite">{mini}</td>
<td class="droite">{maxi}</td>
<td class="droite">{moyenne}</td>
<td class="droite">{stddev}</td>

</tr>
<!-- END BLOCK : ligne_ref -->
</tbody>
</table
>
<!--END BLOCK : util_ref-->



EOF;

/**
 * elkement d'un histogramme
 */
class item {
        var $val=0;
        var $nb=0;

        function item ($val,$nb) {
            $this->val=$val;
            $this->$nb=$nb;
        }
}

/**
 * renvoie un histogramme vide pr�t � l'emploi
 * @param min valeur mini
 * @param mwx valeur maxi
 * @param delta increment
 * param prec  pr�cision d'affichage (nb de d�cimales)
 */
function mk_tranches($min,$max,$delta,$prec){
    $vals=array();
    for ($x=$min; $x<=$max; $x +=$delta)
        $vals[]=new item(sprintf("%0.{$prec}f",$x),0);

    return $vals;
}

/**
 * remise a zero d'un histogramme (passage a la ligne suivante)
 */

function raz($tranches) {
    foreach ($tranches as $tranche)
        $tranche->nb=0;
    return $tranches;
}


$type_stats=optional_param("type_stats","",PARAM_ALPHAEXT);
$date_debut=optional_param("date_debut","",PARAM_RAW);
$date_fin=optional_param("date_fin","",PARAM_RAW);



$tpl->assignInclude("corps",$fiche,T_BYVAR);
$tpl->prepare($chemin);

$tpl->traduit("_ROOT.titre_popup","statistiques");

set_time_limit(0);

$CFG->utiliser_js_calendar=1;   //forc� (est � 0 dans config pour accelerer les pages)
$CFG->utiliser_tables_sortables_js=1;





//limtes des dates par defaut
$sql=<<<EOS
	select min(ts_datedebut) as min, max(ts_datefin) as max
	from {$CFG->prefix}examens
	where $USER->type_plateforme='OUI'
EOS;

if($res=get_record_sql($sql,false)) {
	//print_r($res);
	if (!$date_debut)
		$debut=$res->min?$res->min:1;
	else
		$debut=mon_strtotime($date_debut);
	if (!$date_fin)
		$fin=$res->max?$res->max:time();
	else
		$fin=mon_strtotime($date_fin)+60; //ajoute 1 minute date de fin (pour examen des stats)
}else { //jamais atteint si pas d'examen $res->min et $res->max sont vides...
	$debut=$fin=time(); //pas d'examen;
}



//en vue trdauction
$stats=array("util_ex","util_q","util_ref","util_comp","nb_cand","rep_q","domaine","comp");

// W3C selectionner le bon
foreach ($stats as $tmp)
    $tpl->assign("_ROOT.".$tmp,"");
if ($type_stats)
    $tpl->assign("_ROOT.".$type_stats," selected=\"selected\" ");

//TODO en timestamp
//print "$debut $fin $date_debut $date_fin";
//dates dans les input fields et dans le calendrier
$dd=strftime(traduction("jscalendar_if"),$debut);
$df=strftime(traduction("jscalendar_if"),$fin);

$tpl->assign("date_debut",strftime(traduction("jscalendar_if"),$debut));
$tpl->assign("date_fin",strftime(traduction("jscalendar_if"),$fin));


$critere_date_ex= "ts_datedebut >=$debut and ts_datefin <=$fin";   //dans les examens
$critere_date="date >=$debut and date <=$fin";                     //dans les resultats

$filename=false;  // rev 820 export tableur
$typepf=$USER->type_plateforme;
$CSV_SEP=$CFG->csv_separateur;

 espion2("statistiques",'stat_'.$type_stats,"");

switch($type_stats){



	case "util_ex" :
		//TODO timestamp
		$examens=  get_records("examens",$critere_date_ex. " and $USER->type_plateforme='OUI'");
		//print_r($examens);
        $compteur_ligne=0;
		$tpl->newBlock("util_ex");
        $filename="stats_util_examens_".$debut."_".$fin.".csv";
        $fp = fopen("{$CFG->chemin_ressources}/csv/".$filename, "w");
        $entete_csv=array("t_id","t_titre","t_nbq","t_nb","t_mini","t_maxi","t_moyenne","t_ec");


        $ligne_csv=array("nbq","nb","mini","maxi","moyenne","stddev");
        $ligne_cvt=array(true,true,true,true,true,true); //conversion point virgule pour OO
        $ch_fp=ligne_to_csv($entete_csv,false);
        fwrite($fp,$ch_fp."\n");



        if ($CFG->export_ods) {
        	$filename_ods="stats_util_examens_".$debut."_".$fin.".ods";
	        require_once($CFG->chemin_commun.'/OOo/odslib.class.php');
	        /// Creating a workbook
	        $workbook = new MoodleODSWorkbook("-");
	        /// Send HTTP headers
	        $workbook->send($filename_ods);
	        /// Creating the first worksheet
	        $myods = & $workbook->add_worksheet("");
	        $row=0;
	        $col=0;
	        foreach($entete_csv as $e) {
		        $myods->write_string($row, $col++,traduction($e,false));
	        }
	        $row=1;

        }

		foreach($examens as $ligne){

        	$ide = $ligne->id_etab;
			$idq = $ligne->id_examen;

            $stats=get_stats_examen($idq,$ide);  //pas besoin de dates ici
            //print_r($stats);
			if ($stats->nb) {
				$tpl->newBlock("ligne_ex");
				$tpl->setCouleurLigne($compteur_ligne);
				$tpl->assign("id",$ide.".".$idq);
				$tpl->assign ("libelle",clean($ligne->nom_examen,70));
				print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../../examens/fiche.php?idq=".$idq."&amp;ide=".$ide));
                $stats->nbq=nbqe($idq,$ide);
				$stats->moyenne=sprintf("%.2f",$stats->moyenne);

                $tpl->assignObjet($stats);

				$ch_fp=$ide."_".$idq.$CSV_SEP.to_csv( $ligne->nom_examen).$CSV_SEP.ligne_to_csv($ligne_csv,$stats,$ligne_cvt);
                fwrite($fp,$ch_fp."\n");

                if ($CFG->export_ods) {
	                $pos=0;
	                 $myods->write_string($row,$pos++,$ide.".".$idq);
	                 $myods->write_string($row,$pos++,$ligne->nom_examen);
	                foreach($ligne_csv as $num=>$col) {
	                	if ($ligne_cvt[$num])
		                        $myods->write_number($row,$pos++,$stats->$col);
				        else
				                $myods->write_string($row,$pos++,$stats->$col);
	                }
	                $row++;
                }
				$compteur_ligne++;
			}
		}
		$tpl->assign("util_ex.nb",$compteur_ligne);
        fclose($fp);

        if ($CFG->export_ods) {
	        $full_filename_ods=$workbook->close(); // fait le zip et dire ou il l'a mis
        }
		break;



	case "util_ref" :
		$compteur_ligne=0;
		$tpl->newBlock("util_ref");
		$refs=get_referentiels("referentielc2i",false);
        $filename="stats_util_refs_".$debut."_".$fin.".csv";
        $fp = fopen("{$CFG->chemin_ressources}/csv/".$filename, "w");
        $entete_csv=array("t_id","t_titre","t_examen","t_nb","t_mini","t_maxi","t_moyenne","t_ec");
        $ligne_csv=array("nb_examen","nb","mini","maxi","moyenne","stddev");
        $ligne_cvt=array(false,false,true,true,true,true); //conversion point virgule pour OO
        $ch_fp=ligne_to_csv($entete_csv,false);
        fwrite($fp,$ch_fp."\n");

         if ($CFG->export_ods) {
        	$filename_ods="stats_util_refs_".$debut."_".$fin.".ods";
	        require_once($CFG->chemin_commun.'/OOo/odslib.class.php');
	        /// Creating a workbook
	        $workbook = new MoodleODSWorkbook("-");
	        /// Send HTTP headers
	        $workbook->send($filename_ods);
	        /// Creating the first worksheet
	        $myods = & $workbook->add_worksheet("");
	        $row=0;
	        $col=0;
	        foreach($entete_csv as $e) {
		        $myods->write_string($row, $col++,traduction($e,false));
	        }
	        $row=1;

        }



		foreach($refs as $ref){
			$stats=get_stats_referentiel($ref->referentielc2i,$debut,$fin);
			if ($stats->nb) {
				$tpl->newBlock("ligne_ref");
				$tpl->setCouleurLigne($compteur_ligne);
				$tpl->assign("id",$ref->referentielc2i);
				$tpl->assign ("libelle",clean($ref->domaine,70));
                $stats->moyenne=sprintf("%.2f",$stats->moyenne);
				$tpl->assignObjet($stats);
				$compteur_ligne++;
                $ch_fp=$ref->referentielc2i.$CSV_SEP.to_csv($ref->domaine).$CSV_SEP.ligne_to_csv($ligne_csv,$stats,$ligne_cvt);
                fwrite($fp,$ch_fp."\n");


                 if ($CFG->export_ods) {
	                $pos=0;
	                 $myods->write_string($row,$pos++,$ref->referentielc2i);
	                 $myods->write_string($row,$pos++,$ref->domaine);
	                foreach($ligne_csv as $num=>$col) {
	                	if ($ligne_cvt[$num])
		                        $myods->write_number($row,$pos++,$stats->$col);
				        else
				                $myods->write_string($row,$pos++,$stats->$col);
	                }
	                $row++;
                }


			}
		}
		$tpl->assign("util_ref.nb",$compteur_ligne);
         fclose($fp);
          if ($CFG->export_ods) {
	        $full_filename_ods=$workbook->close(); // fait le zip et dire ou il l'a mis
        }
		break;

	case "util_comp" :
		$compteur_ligne=0;
		$tpl->newBlock("util_ref");
		$refs=get_referentiels("referentielc2i",false);
        $filename="stats_util_comps_".$debut."_".$fin.".csv";
        $fp = fopen("{$CFG->chemin_ressources}/csv/".$filename, "w");
        $entete_csv=array("t_id","t_titre","t_examen","t_nb","t_mini","t_maxi","t_moyenne","t_ec");
        $ligne_csv=array("nb_examen","nb","mini","maxi","moyenne","stddev");
        $ligne_cvt=array(false,false,true,true,true,true); //conversion point virgule pour OO
        $ch_fp=ligne_to_csv($entete_csv,false);
        fwrite($fp,$ch_fp."\n");

         if ($CFG->export_ods) {
        	$filename_ods="stats_util_comps_".$debut."_".$fin.".ods";
	        require_once($CFG->chemin_commun.'/OOo/odslib.class.php');
	        /// Creating a workbook
	        $workbook = new MoodleODSWorkbook("-");
	        /// Send HTTP headers
	        $workbook->send($filename_ods);
	        /// Creating the first worksheet
	        $myods = & $workbook->add_worksheet("");
	        $row=0;
	        $col=0;
	        foreach($entete_csv as $e) {
		        $myods->write_string($row, $col++,traduction($e,false));
	        }
	        $row=1;

        }



         foreach($refs as $ref){
	         $alineas=get_alineas($ref->referentielc2i);
	         foreach($alineas as $alinea) {
		         $stats=get_stats_competence($ref->referentielc2i,$alinea->alinea,$debut,$fin);
		         if ($stats->nb) {
			         $tpl->newBlock("ligne_ref");
			         $tpl->setCouleurLigne($compteur_ligne);
			         $tpl->assign("id",$ref->referentielc2i.".".$alinea->alinea);
			         $tpl->assign ("libelle",clean($alinea->aptitude,70));
			         $stats->moyenne=sprintf("%.2f",$stats->moyenne);
			         $tpl->assignObjet($stats);
			         $compteur_ligne++;
			         $ch_fp=$ref->referentielc2i.".".$alinea->alinea.$CSV_SEP.to_csv($alinea->aptitude).$CSV_SEP.ligne_to_csv($ligne_csv,$stats,$ligne_cvt);
			         fwrite($fp,$ch_fp."\n");

			         if ($CFG->export_ods) {
				         $pos=0;
				         $myods->write_string($row,$pos++,$ref->referentielc2i.".".$alinea->alinea);
				         $myods->write_string($row,$pos++,$alinea->aptitude);
				         foreach($ligne_csv as $num=>$col) {
					         if ($ligne_cvt[$num])
						         $myods->write_number($row,$pos++,$stats->$col);
					         else
						         $myods->write_string($row,$pos++,$stats->$col);
				         }
				         $row++;
			         }


		         }
	         }
         }
         $tpl->assign("util_ref.nb",$compteur_ligne);
         $tpl->assign("util_ref.referentiels",traduction("competences"),false);
         fclose($fp);
         if ($CFG->export_ods) {
	         $full_filename_ods=$workbook->close(); // fait le zip et dire ou il l'a mis
         }
         break;



		//////////////////////////////////////////////////////
		// utilisation des questions dans les examens
        // rev 820 filtrage par type de plateforme
		//////////////////////////////////////////////////////

	case "util_q" :
		$filename="stats_questions_".$debut."_".$fin.".csv";
        $fp = fopen("{$CFG->chemin_ressources}/csv/".$filename, "w");
         $entete_csv=array("t_id","t_titre","t_referentiel","t_examen","t_nb","t_mini","t_maxi","t_moyenne","t_ec");
        $ligne_csv=array("nb_examen","nb","mini","maxi","moyenne","stddev");
         $ligne_cvt=array(false,false,true,true,true,true); //conversion point virgule pour OO
         $colspan=9;
        if ($CFG->calcul_indice_discrimination) {
        	$entete_csv[]="t_idisc";
        	$entete_csv[]="t_cdisc";
        	$ligne_csv[]="idisc";
        	$ligne_csv[]="cdisc";
        	$ligne_cvt[]=true;
        	$ligne_cvt[]=true;
        	$colspan+=2;

        }


        $ch_fp=ligne_to_csv($entete_csv,false);
        fwrite($fp,$ch_fp."\n");

         if ($CFG->export_ods) {
        	$filename_ods="stats_questions_".$debut."_".$fin.".ods";
	        require_once($CFG->chemin_commun.'/OOo/odslib.class.php');
	        /// Creating a workbook
	        $workbook = new MoodleODSWorkbook("-");
	        /// Send HTTP headers
	        $workbook->send($filename_ods);
	        /// Creating the first worksheet
	        $myods = & $workbook->add_worksheet("");
	        $row=0;
	        $col=0;
	        foreach($entete_csv as $e) {
		        $myods->write_string($row, $col++,traduction($e,false));
	        }
	        $row=1;

        }




        $tpl->newBlock("util_q");
           if ($CFG->calcul_indice_discrimination) $tpl->newBlock("entete_stats2");
		//questions r�pondues entre ces deux dates
        // rev 977 seulement dans les examens de cette version du referentiel
		$requete=<<<EOS
			select count(score) as nb, question
			from  {$CFG->prefix}resultatsdetailles,{$CFG->prefix}examens E
			where date >=$debut and date <=$fin
            and concat(E.id_etab,'_',E.id_examen)= examen
				group by question
				order by question
EOS;
			$resultats =get_records_sql($requete);
			$compteur_ligne = 0;
			foreach($resultats as $ligne_q){
				list($ide,$idq)=explode(".",$ligne_q->question);
				if ( $q=get_question ($idq,$ide,false)) {  //peut avoir �t� supprim�e ...
					if ($q->$typepf=="OUI") {   // rev 820 uniquement pour cette plateforme
						$tpl->newBlock("ligne_q");
						$tpl->setCouleurLigne($compteur_ligne);
						$tpl->assign("id",$ligne_q->question);
                        $ref_alin=get_domaine_traite($q);  // rev 977
						$tpl->assign ("ref_alin",$ref_alin);
						$tpl->assign ("libelle",clean(affiche_texte_question($q->titre),70));
						print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../../questions/fiche.php?idq=".$idq."&amp;ide=".$ide));
						//entre les bonnes dates
						$stats=get_stats_question($idq,$ide,$debut,$fin);

						$tpl->assignObjet($stats);
						if ($CFG->calcul_indice_discrimination) {
							$tpl->newBlock("stats2");
							$tpl->assign("idisc",$stats->idisc);
							$tpl->assign("cdisc",$stats->cdisc);
							//if ($stats->idisc>1) print_r($stats);
							//print $stats->stddev." ".$stats->qsd."<br/>";
						}
						// renvoie tous les examens l'utilisant pas seulement ceux qui ont �t� pass�s
                        //$liste=get_examens_question ($idq,$ide,false);
						//$tpl->assign("nb_examen",count($liste));

						$compteur_ligne++;
                        $ch_fp=$ide."_".$idq.$CSV_SEP.to_csv($q->titre).$CSV_SEP.$q->referentielc2i.".".$q->alinea.$CSV_SEP.ligne_to_csv($ligne_csv,$stats,$ligne_cvt);
                        fwrite($fp,$ch_fp."\n");

                        if ($CFG->export_ods) {
	                        $pos=0;
	                        $myods->write_string($row,$pos++,$ide."_".$idq);
	                        $myods->write_string($row,$pos++,$q->titre);
	                        $myods->write_string($row,$pos++,$ref_alin);
	                        foreach($ligne_csv as $num=>$col) {
		                        if ($ligne_cvt[$num])
			                        $myods->write_number($row,$pos++,$stats->$col);
		                        else
			                        $myods->write_string($row,$pos++,$stats->$col);
	                        }
	                        $row++;
                        }




					}
				}
            }
			$tpl->assign("util_q.nb",$compteur_ligne);
			$tpl->assign("util_q.colspan",$colspan);

             fclose($fp);
              if ($CFG->export_ods) {
	        $full_filename_ods=$workbook->close(); // fait le zip et dire ou il l'a mis
        }
			break;

			//////////////////////////////////////////////////////
			// nombre de candidats avec leurs notes par tranche
			//////////////////////////////////////////////////////

	case "nb_cand" :
		$filename="stats_cands_".$debut."_".$fin.".csv";
        $fp = fopen("{$CFG->chemin_ressources}/csv/".$filename, "w");



        $compteur_ligne = 0;
		$nb_etud = 0;
		$tranches=mk_tranches(0,100,10,0); //les scores vont de 0 � 100%
		$totaux=mk_tranches(0,100,10,0); // =$tranches NON ! ;
		$tpl->newBlock("stat");
		$tpl->newBlock("entete");
		$tpl->assign("valeur","nombre");
        $tpl->newBlock("e_id");
        $tpl->assign("quoi",traduction("t_id"));
		$tpl->newBlock("e_id");
		$tpl->assign("quoi",traduction("examen"));
		$tpl->assign("valeur","tranche");
        $entete_csv=array("t_id","t_titre","t_nb");
        $ch_fp=ligne_to_csv($entete_csv,false);


         if ($CFG->export_ods) {
        	$filename_ods="stats_cands_".$debut."_".$fin.".ods";
	        require_once($CFG->chemin_commun.'/OOo/odslib.class.php');
	        /// Creating a workbook
	        $workbook = new MoodleODSWorkbook("-");
	        /// Send HTTP headers
	        $workbook->send($filename_ods);
	        /// Creating the first worksheet
	        $myods = & $workbook->add_worksheet("");
	        $row=0;
	        $col=0;
	        foreach($entete_csv as $e) {
		        $myods->write_string($row, $col++,traduction($e,false));
	        }

        }


        for ($i=0; $i<count($tranches)-1;$i++) {
			$tpl->newBlock("entete");
            $v=$tranches[$i]->val."-".$tranches[$i+1]->val."%";
			$tpl->assign("valeur",$v );
            $ch_fp.=$CSV_SEP.$v;
            if ($CFG->export_ods)
            	 $myods->write_string($row, $col++,$v);

		}
        fwrite($fp,$ch_fp."\n");




		//examens entre ces dates
		$examens=  get_records("examens",$critere_date_ex. " and $USER->type_plateforme='OUI' ");
		$nb_etud=0;
		$row=1;
		foreach($examens as $ligne_e) {
			$cle= $ligne_e->id_etab."_".$ligne_e->id_examen;
            $tranches=raz($tranches);
			$scores=get_records("resultatsexamens","examen='$cle' and $critere_date");  //relecture en BD
			if (count($scores) >0) {
				$nb_etud +=count($scores);
				foreach  ($scores as $score) {
					for ($i=0;$i<count($tranches)-1;$i++){
						if ($score->score <= $tranches[$i+1]->val){
							$tranches[$i]->nb++;
							$totaux[$i]->nb++;
							break;
						}
					}
				}


				$tpl->newBlock("ligne");
				$tpl->setCouleurLigne($compteur_ligne);
				$tpl->newBlock("id");
				$tpl->assign ("id",$cle);
				$tpl->assign ("libelle",clean($ligne_e->nom_examen,30));
				print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../../examens/fiche.php?idq=".$ligne_e->id_examen."&amp;ide=".$ligne_e->id_etab));
				$tpl->newBlock("case");
				$tpl->assign("valeur",count($scores));
				$ch_fp=$cle.$CSV_SEP.to_csv($ligne_e->nom_examen).$CSV_SEP.count($scores);
				if ($CFG->export_ods) {
	                        $pos=0;
	                        $myods->write_string($row,$pos++,$cle);
	                        $myods->write_string($row,$pos++,$ligne_e->nom_examen);
	                        $myods->write_number($row,$pos++,count($scores));
				 }

                for ($i=0;$i<count($tranches)-1;$i++){
					$tpl->newBlock("case");
					$tpl->assign("valeur",$tranches[$i]->nb);
                    $ch_fp.=$CSV_SEP.$tranches[$i]->nb;
                    if ($CFG->export_ods) {
                    	 $myods->write_number($row,$pos++,$tranches[$i]->nb);
                    }

				}

                fwrite($fp,$ch_fp."\n");
				$compteur_ligne++;
				$row++;

			}
		}

		//  totaux
		// les mettre dans le tfoot pour les exclure du tri javascript !!!

		$tpl->newBlock("ligne_total");
		$tpl->setCouleurLigne($compteur_ligne);
		$tpl->newBlock("id_total");
		$tpl->assign ("id","totaux");
		$tpl->newBlock("id_total");
		$tpl->assign ("id","");

		$tpl->newBlock("case_total");
		$tpl->assign("valeur",$nb_etud);
		for ($i=0;$i<count($totaux)-1;$i++){
			$tpl->newBlock("case_total");
			$tpl->assign("valeur",$totaux[$i]->nb);
		}


		$tpl->assign("stat.colspan",count($tranches)+2);
		$tpl->assign("stat.nb",$compteur_ligne);
		$tpl->assign("stat.quoi",traduction("examens",false));

         fclose($fp);
          if ($CFG->export_ods) {
	        $full_filename_ods=$workbook->close(); // fait le zip et dire ou il l'a mis
        }
		break;
		//////////////////////////////////////////////////////
		// r�ponses aux questions (par tranche)
        // rev 820 filtrage par plateforme
		//////////////////////////////////////////////////////
	case "rep_q" :

		$compteur_ligne = 0;
		$nb_etud = 0;
        if ($CFG->pas_de_scores_negatifs)
              $tranches=mk_tranches(0,+1,0.1,2);  //les questions sont not�es de 0 �  1
		else
              $tranches=mk_tranches(-1,+1,0.2,2);  //les questions sont not�es de -1 � 1
        $filename="stats_reps_".$debut."_".$fin.".csv";
        $fp = fopen("{$CFG->chemin_ressources}/csv/".$filename, "w");
		$tpl->newBlock("stat");
		$tpl->newBlock("entete");

		$tpl->assign("valeur","nombre");
		$tpl->newBlock("e_id");
		$tpl->assign("quoi",traduction("t_id"));
        $tpl->newBlock("e_id");
        $tpl->assign("quoi",traduction("t_titre"));
         $tpl->newBlock("e_id");
        $tpl->assign("quoi",traduction("t_examen"));
        $entete_csv=array("t_id","t_titre","t_examen","t_nb");
        $ch_fp=ligne_to_csv($entete_csv,false);

         if ($CFG->export_ods) {
        	$filename_ods="stats_reps_".$debut."_".$fin.".ods";
	        require_once($CFG->chemin_commun.'/OOo/odslib.class.php');
	        /// Creating a workbook
	        $workbook = new MoodleODSWorkbook("-");
	        /// Send HTTP headers
	        $workbook->send($filename_ods);
	        /// Creating the first worksheet
	        $myods = & $workbook->add_worksheet(traduction("questions"));
	        $row=0;
	        $col=0;
	        foreach($entete_csv as $e) {
		        $myods->write_string($row, $col++,traduction($e,false));
	        }


        }


        for ($i=0; $i<count($tranches)-1;$i++) {
            $tpl->newBlock("entete");
            $v=$tranches[$i]->val." ".$tranches[$i+1]->val."%";
            $tpl->assign("valeur",$v );
            $ch_fp.=$CSV_SEP.$v;
            if ($CFG->export_ods)
            	 $myods->write_string($row, $col++,$v);
        }


        fwrite($fp,$ch_fp."\n");




		$requete=<<<EOS
			select count(score) as nb, question
			from  {$CFG->prefix}resultatsdetailles,{$CFG->prefix}examens E
			where $critere_date
            and concat(E.id_etab,'_',E.id_examen) = examen
				group by question
				order by question
EOS;
			$resultats =get_records_sql($requete);
			$compteur_ligne = 0;
			$row=1;
			foreach($resultats as $ligne_q){  //pour chaque question pos�e
				$tranches=raz($tranches);
				list($ide,$idq)=explode(".",$ligne_q->question);
				if ( $q=get_question ($idq,$ide,false)) { //rev 921 peut avoir �t� supprim�e
					if ($q->$typepf=="OUI") {   // rev 820 uniquement pour cette plateforme
						$CFG->calcul_indice_discrimination=false; //inutile pas affich� ici rev 921
                        $stats=get_stats_question($idq,$ide,$debut,$fin);
						$tpl->newBlock("ligne");
						$tpl->setCouleurLigne($compteur_ligne);
						$tpl->newBlock("id");
						$tpl->assign ("id",$ligne_q->question);


						$tpl->assign ("libelle",clean(affiche_texte_question($q->titre),30));
						print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../../questions/fiche.php?idq=".$idq."&amp;ide=".$ide));


                        $tpl->newBlock("case");
                        $tpl->assign("valeur",$stats->nb_examen);

						$tpl->newBlock("case");
						$tpl->assign("valeur",$stats->nb);
                        //details
						//$res_scores=get_records("resultatsdetailles","question='$ligne_q->question' and $critere_date");
                        $sql_details=<<<EOS
                            select RD.*
                            from  {$CFG->prefix}resultatsdetailles RD,{$CFG->prefix}examens E
                            where $critere_date
                            and question='{$ligne_q->question}'
                            and concat(E.id_etab,'_',E.id_examen) = examen
EOS;

                        $res_scores=get_records_sql($sql_details,false);


                        $ch_fp=$ligne_q->question.$CSV_SEP.to_csv($q->titre).$CSV_SEP.$stats->nb_examen.$CSV_SEP.$stats->nb;

  						if ($CFG->export_ods) {
				         	$pos=0;
				         	$myods->write_string($row,$pos++,$ligne_q->question);
				         	$myods->write_string($row,$pos++,$q->titre);
				         	$myods->write_number($row,$pos++,$stats->nb_examen);
				         	$myods->write_number($row,$pos++,$stats->nb);
  						}
						foreach($res_scores as $score) {
							for ($i=0;$i<count($tranches)-1;$i++){
								if ($score->score <= $tranches[$i+1]->val){
									$tranches[$i]->nb++;
									break;
								}
							}
						}

						for ($i=0;$i<count($tranches)-1;$i++){
							$tpl->newBlock("case");
							$tpl->assign("valeur",$tranches[$i]->nb);
                            $ch_fp.=$CSV_SEP.$tranches[$i]->nb;
                            if ($CFG->export_ods) {
                    	 		$myods->write_number($row,$pos++,$tranches[$i]->nb);
                   			}
						}

						$compteur_ligne++;
						$row++;
                        fwrite($fp,$ch_fp."\n");
					}
				}

			}
			$tpl->assign("stat.colspan",count($tranches)+3);
			$tpl->assign("stat.nb",$compteur_ligne);
			$tpl->assign("stat.quoi",traduction("questions",false));
             fclose($fp);
              if ($CFG->export_ods) {
	        $full_filename_ods=$workbook->close(); // fait le zip et dire ou il l'a mis
        }
			break;

			//////////////////////////////////////////////////////
			// domaines du r�f�rentiel (par tranche)
			//////////////////////////////////////////////////////

	case "domaine" :

		//les scores par domaine vont de ?? � ??? (-100% +100 % pas sur)

		$compteur_ligne = 0;
		$nb_etud = 0;
            if ($CFG->pas_de_scores_negatifs)
            $tranches=mk_tranches(0,+100,10,0);
		else
            $tranches=mk_tranches(-100,+100,20,0);

        $filename="stats_domaines_".$debut."_".$fin.".csv";
        $fp = fopen("{$CFG->chemin_ressources}/csv/".$filename, "w");
		$tpl->newBlock("stat");
		$tpl->newBlock("entete");

		$tpl->assign("valeur","nombre");
        $tpl->newBlock("e_id");
        $tpl->assign("quoi",traduction("t_id"));
		$tpl->newBlock("e_id");
		$tpl->assign("quoi",traduction("referentiel"));
        $tpl->newBlock("e_id");
        $tpl->assign("quoi",traduction("t_examen"));
        $entete_csv=array("t_id","t_titre","t_examen","t_nb");
        $ch_fp=ligne_to_csv($entete_csv,false);

          if ($CFG->export_ods) {
        	$filename_ods="stats_domaines_".$debut."_".$fin.".ods";
	        require_once($CFG->chemin_commun.'/OOo/odslib.class.php');
	        /// Creating a workbook
	        $workbook = new MoodleODSWorkbook("-");
	        /// Send HTTP headers
	        $workbook->send($filename_ods);
	        /// Creating the first worksheet
	        $myods = & $workbook->add_worksheet(traduction( "referentiels"));
	        $row=0;
	        $col=0;
	        foreach($entete_csv as $e) {
		        $myods->write_string($row, $col++,traduction($e,false));
	        }
        }

		for ($i=0; $i<count($tranches)-1;$i++) {
			$tpl->newBlock("entete");
            $v= $tranches[$i]->val." ".$tranches[$i+1]->val;
			$tpl->assign("valeur", $v);
            $ch_fp.=$CSV_SEP.$v;
            if ($CFG->export_ods)
            	 $myods->write_string($row, $col++,$v);
		}
        fwrite($fp,$ch_fp."\n");

		$referentiels =get_referentiels();
		$compteur_ligne = 0;
		$row=1;
		foreach($referentiels as $ligne_r){  //pour chaque referentiel
			$stats=get_stats_referentiel($ligne_r->referentielc2i,$debut,$fin);  //si evalu�
			if ($stats->nb) {
				$nb_etud=0;
				$tranches=raz($tranches);

				$tpl->newBlock("ligne");
				$tpl->setCouleurLigne($compteur_ligne);
				$tpl->newBlock("id");
				$tpl->assign ("id",$ligne_r->referentielc2i);
				$tpl->assign ("libelle",clean($ligne_r->domaine,60));
                $tpl->assign("consulter_fiche","<li class=\"menu_niveau2_item\"></li>");  //W3C

				$cle=$ligne_r->referentielc2i;
                $critere_pf="concat(E.id_etab,'_',E.id_examen)=R.examen and $USER->type_plateforme='OUI'";
				$res_scores=get_records("resultatsreferentiels R,{$CFG->prefix}examens E","$critere_date and R.referentielc2i='$cle'  and $critere_pf");
				$nb_etud +=count($res_scores);
                $ch_fp=$ligne_r->referentielc2i.$CSV_SEP.to_csv($ligne_r->domaine).$CSV_SEP.$stats->nb_examen.$CSV_SEP.count($res_scores);

                if ($CFG->export_ods) {
				         $pos=0;
				         $myods->write_string($row,$pos++,$ligne_r->referentielc2i);
				         $myods->write_string($row,$pos++,$ligne_r->domaine);
				         $myods->write_number($row,$pos++,$stats->nb_examen);
				         $myods->write_number($row,$pos++,count($res_scores));
                }
				foreach($res_scores as $score) {
					for ($i=0;$i<count($tranches)-1;$i++){
						if ($score->score <= $tranches[$i+1]->val){
							$tranches[$i]->nb++;
							break;
						}
					}
				}
                $tpl->newBlock("case");
                    $tpl->assign("valeur",$stats->nb_examen);
				$tpl->newBlock("case");
				$tpl->assign("valeur",$nb_etud);
				for ($i=0;$i<count($tranches)-1;$i++){
					$tpl->newBlock("case");
					$tpl->assign("valeur",$tranches[$i]->nb);
                     $ch_fp.=$CSV_SEP.$tranches[$i]->nb;
                      if ($CFG->export_ods) {
                    	 $myods->write_number($row,$pos++,$tranches[$i]->nb);
                    }
				}

				$compteur_ligne++;
				$row++;
                 fwrite($fp,$ch_fp."\n");
			}

		}
		$tpl->assign("stat.colspan",count($tranches)+3);
		$tpl->assign("stat.nb",$compteur_ligne);
		$tpl->assign("stat.quoi",traduction("referentiels",false));
         fclose($fp);
          if ($CFG->export_ods) {
	        $full_filename_ods=$workbook->close(); // fait le zip et dire ou il l'a mis
        }



		break;
		//////////////////////////////////////////////////////
		//
		//////////////////////////////////////////////////////

		//////////////////////////////////////////////////////
		// domaines du r�f�rentiel (par tranche)
		//////////////////////////////////////////////////////

	case "comp" :

		//les scores par domaine vont de ?? � ??? (-100% +100 % pas sur)

		$compteur_ligne = 0;
		$nb_etud = 0;

		if ($CFG->pas_de_scores_negatifs)
            $tranches=mk_tranches(0,+100,10,0);
        else
           $tranches=mk_tranches(-100,+100,20,0);
        $filename="stats_comps_".$debut."_".$fin.".csv";
        $fp = fopen("{$CFG->chemin_ressources}/csv/".$filename, "w");
		$tpl->newBlock("stat");
		$tpl->newBlock("entete");

		$tpl->assign("valeur","nombre");
        $tpl->newBlock("e_id");
        $tpl->assign("quoi",traduction("t_id"));
		$tpl->newBlock("e_id");
		$tpl->assign("quoi",traduction("competence"));
          $tpl->newBlock("e_id");
        $tpl->assign("quoi",traduction("t_examen"));
        $entete_csv=array("t_id","t_titre","t_examen","t_nb");
        $ch_fp=ligne_to_csv($entete_csv,false);


 		if ($CFG->export_ods) {
        	$filename_ods="stats_comps_".$debut."_".$fin.".ods";
	        require_once($CFG->chemin_commun.'/OOo/odslib.class.php');
	        /// Creating a workbook
	        $workbook = new MoodleODSWorkbook("-");
	        /// Send HTTP headers
	        $workbook->send($filename_ods);
	        /// Creating the first worksheet
	        $myods = & $workbook->add_worksheet(traduction( "alineas"));
	        $row=0;
	        $col=0;
	        foreach($entete_csv as $e) {
		        $myods->write_string($row, $col++,traduction($e,false));
	        }
        }

		for ($i=0; $i<count($tranches)-1;$i++) {
			$tpl->newBlock("entete");
             $v= $tranches[$i]->val." ".$tranches[$i+1]->val;
            $tpl->assign("valeur", $v);
            $ch_fp.=$CSV_SEP.$v;
            if ($CFG->export_ods)
            	 $myods->write_string($row, $col++,$v);

		}
        fwrite($fp,$ch_fp."\n");



		$referentiels =get_referentiels();
		$compteur_ligne = 0;
		$row=1;
		foreach($referentiels as $ligne_r){  //pour chaque referentiel
			$alineas=get_alineas($ligne_r->referentielc2i);
			foreach($alineas as $alinea) {
				$stats=get_stats_competence($ligne_r->referentielc2i,$alinea->alinea,$debut,$fin); //si �valu�
				if ($stats->nb) {
					$nb_etud=0;
					$tranches=raz($tranches);
					$tpl->newBlock("ligne");
					$tpl->setCouleurLigne($compteur_ligne);
					$tpl->newBlock("id");
					$tpl->assign ("id",$ligne_r->referentielc2i.".".$alinea->alinea);
					$tpl->assign ("libelle",clean($alinea->aptitude,60));
                    $tpl->assign("consulter_fiche","<li class=\"menu_niveau2_item\"></li>");  //W3C
					$cle=$ligne_r->referentielc2i.".".$alinea->alinea;
                    $critere_pf="concat(E.id_etab,'_',E.id_examen)=R.examen and $USER->type_plateforme='OUI'";
					$res_scores=get_records("resultatscompetences R ,{$CFG->prefix}examens E","$critere_date and R.competence='$cle'  and $critere_pf");
					$nb_etud +=count($res_scores);
                    $ch_fp=$ligne_r->referentielc2i.".".$alinea->alinea.$CSV_SEP.to_csv($alinea->aptitude).$CSV_SEP.$stats->nb_examen.$CSV_SEP.count($res_scores);

					  if ($CFG->export_ods) {
				         $pos=0;
				         $myods->write_string($row,$pos++,$ligne_r->referentielc2i.".".$alinea->alinea);
				         $myods->write_string($row,$pos++,$alinea->aptitude);
						 $myods->write_number($row,$pos++,$stats->nb_examen);
						 $myods->write_number($row,$pos++,count($res_scores));
					  }

					foreach($res_scores as $score) {
						for ($i=0;$i<count($tranches)-1;$i++){
							if ($score->score <= $tranches[$i+1]->val){
								$tranches[$i]->nb++;
								break;
							}
						}
					}
                    $tpl->newBlock("case");
                    $tpl->assign("valeur",$stats->nb_examen);
					$tpl->newBlock("case");
					$tpl->assign("valeur",$nb_etud);
					for ($i=0;$i<count($tranches)-1;$i++){
						$tpl->newBlock("case");
						$tpl->assign("valeur",$tranches[$i]->nb);
                        $ch_fp.=$CSV_SEP.$tranches[$i]->nb;
                         if ($CFG->export_ods) {
                    	 $myods->write_number($row,$pos++,$tranches[$i]->nb);
                    }
					}

					$compteur_ligne++;
					$row++;
                     fwrite($fp,$ch_fp."\n");
				}
			}
		}
		$tpl->assign("stat.colspan",count($tranches)+3);
		$tpl->assign("stat.nb",$compteur_ligne);
		$tpl->assign("stat.quoi",traduction("competences",false));
         fclose($fp);

 if ($CFG->export_ods) {
	        $full_filename_ods=$workbook->close(); // fait le zip et dire ou il l'a mis
        }

		break;


}


$items=array();

// v 1.5 menu de niveau 2 standard (cf weblib)
$tpl->gotoBlock("_ROOT");
if( $filename) {
    $items[]=get_menu_item_csv($filename);
    if ($CFG->export_ods && isset($filename_ods))
	$items[]=get_menu_item_ods($filename_ods,'tmp/ods');
}
$items[]=get_menu_item_legende("stats");
    print_menu($tpl,"menu_niveau2",$items);




$tpl->gotoBlock("_ROOT");
$tpl->print_boutons_fermeture();

$tpl->printToScreen();										//affichage
?>

