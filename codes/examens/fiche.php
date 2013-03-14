<?php

/**
 * @author Patrick Pollet
 * @version $Id: fiche.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	Fiche d'examen
// rev 1.5 fortement simplifi�e (templet inline)
//   desctivation des itms de menus impossibles a ce moment
//

// rev 1.5 977 beaucoup d'options non relevantes retir�es pour un examen anonyme
////////////////////////////////

$fiche=<<<EOF

<!-- START BLOCK : rafraichi_liste -->
<script type="text/javascript">
        if (window.opener)
            window.opener.location.href='liste.php?{url_retour}';
</script>

<!-- END BLOCK : rafraichi_liste -->

<div id="fiche">
<div id="xx">
	<ul id="tabs">
		<li> <a class="active_tab" 	href="#general">{general} </a></li>
<!-- START BLOCK : options_tab -->
			<li> <a class="" 			  href="#options">{options}</a> </li>
<!-- END BLOCK : options_tab -->
<!-- START BLOCK : pool_tab -->
			<li> <a class="" 			 href="#pools">{pool}</a> </li>
<!-- END BLOCK : pool_tab -->
			<li> <a class="" 			 href="#questions">{questions}</a> </li>
			<li> <a class="" 			 href="#refs">{referentiels}</a> </li>
<!--START BLOCK : famille_tab -->
            <li> <a class=""             href="#familles">{familles}</a> </li>
<!--END BLOCK : famille_tab -->

<!--START BLOCK : apercus_tab -->
			<li> <a class="" 			 href="#apercus">{apercus}</a> </li>
<!--END BLOCK : apercus_tab -->
<!--START BLOCK : inscriptions_tab -->
			<li> <a class="" 			 href="#inscriptions" >{inscriptions}</a> </li>
<!--END BLOCK : inscriptions_tab -->
<!--START BLOCK : optiques_tab -->
			<li> <a class=""			 href="#optique">{lecture_optique} </a></li>
<!--END BLOCK : optiques_tab -->
<!--START BLOCK : resultats_tab -->
			<li> <a class=""			 href="#resultats">{resultats} </a></li>
<!--END BLOCK : resultats_tab -->
<!--START BLOCK : template_resultats_tab -->
			<li> <a class=""			 href="#template_resultats">{template_resultats} </a></li>
<!--END BLOCK : template_resultats_tab -->
<!--START BLOCK : admin_tab -->
			<li> <a class=""			 href="#admin">{administration} </a></li>
<!--END BLOCK : admin_tab -->
	</ul>
</div>

<div class="panel" id="general">
	<table class="fiche">
		<tbody>
 		 <tr>
            <th width="30%">{form_id}</th>
            <td>{id_etab}.{id_examen}</td>
        </tr>
        <tr>
            <th>{form_libelle}</th>
            <td>{nom_examen}</td>
		</tr>
 <tr>
            <th>
               {form_typep} </th>
            <td >{typep}</td>
          </tr>


<!-- START BLOCK : examen_anonyme -->
 <tr>
    <th>{form_examen_anonyme}</th>
        <td>{examen_anonyme}</td>
 </tr>
<!-- END BLOCK : examen_anonyme -->
      <tr>
            <th>
             {form_date_de_creation}</th>
            <td>{date_creation}</td>
          </tr>
         <tr>
            <th>
             {form_date_de_modification}</th>
            <td>{date_modification}</td>
          </tr>
           <tr>
            <th>
             {form_date_de_envoi_stats}</th>
            <td>{date_envoi}</td>
          </tr>
          <tr>
            <th>
             {form_debut_examen}</th>
            <td>{date_examen}</td>
          </tr>
		  <tr>
            <th>
             {form_fin_examen}</th>
            <td>{date_examen_fin}</td>
          </tr>
		  <tr>
            <th>
           {form_mot_passe_examen}</th>
            <td>{mot_de_passe}</td>
          </tr>
<!-- START BLOCK : restrictions_ip -->          
          <tr>
            <th>
           {form_restriction_ip}</th>
            <td>{subnet}</td>
          </tr>
<!-- END BLOCK : restrictions_ip -->             
          
            <tr>
            <th>
           {form_tirage}</th>
            <td>{type_tirage} <i>{algo_tirage}</i></td>
          </tr>
		  <tr>
            <th>
           {form_ordre_q}</th>
            <td>{ordre_q}</td>
          </tr>
		  <tr>
            <th>
           {form_ordre_r}</th>
            <td>{ordre_r}</td>
          </tr>

<!-- START BLOCK : limitedureepassage -->
<tr>
<th>{form_duree_limite}
</th>
<td>
{ts_dureelimitepassage}
</td>
</tr>
<!-- END BLOCK : limitedureepassage -->

<!-- START BLOCK : chrono -->
          <tr>
            <th>
           {form_affiche_chrono}</th>
            <td>{affiche_chrono}</td>
          </tr>
<!-- END BLOCK : chrono -->

<!-- START BLOCK : seuil -->
		  <tr>
		  <th>{note_mini}</th>
		  <td>{resultat_mini} % </td>
		  </tr>
<!-- END BLOCK : seuil -->

<!-- START BLOCK : correction -->
		  <tr>
            <th>
           {form_correction}</th>
            <td>{corr}</td>
          </tr>
<!-- END BLOCK : correction -->


<!-- START BLOCK : envoi_resultat -->
		  <tr>
            <th>
           {form_envoi_resultat}</th>
            <td>{envoi_resultat}</td>
          </tr>
<!-- END BLOCK : envoi_resultat -->

  <tr>
      <th> {form_auteurs} {form_nom_coll}</th>
      <td>{auteur} <ul style="display:inline;">{consulter_fiche}</ul></td>
  </tr>
  <tr>
    <th>{universite} </th>
    <td>{nom_univ}  <ul style="display:inline;"> {consulter_fiche_u}</ul></td>
  </tr>

  <!-- START BLOCK : tags -->
          <tr>
            <th>
               {form_tags} </th>
            <td >{tags}
            <br/>
              <span class="commentaire1">{info_tags}</span></td>
          </tr>
<!-- END BLOCK : tags -->
</tbody>
</table>
</div>

<!-- START BLOCK : options -->
<div class="panel" id="options" >
	<table class="fiche">
    	<tbody>
        <tr>
            <th>
             {options}</th>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <th>
             {form_ref_c2i}</th>
            <td >{referentielc2i}</td>
          </tr>
          <tr>
            <th>
             {form_alinea}</th>
            <td >{alinea}</td>
          </tr>
		   <tr>
            <th>
             {form_langue}</th>
            <td >{langue}</td>
          </tr>
		</tbody>
	</table>
</div>
<!-- END BLOCK : options -->

 <!-- START BLOCK : pool -->
<div class="panel" id="pools" >
  <TABLE class="fiche">
 	<tbody>
          <tr>
            <th width='40%' >{form_pool}<br/>
            <span class="commentaire1">{commentaire_pool}</span></th>
            <td>
            {form_est_un_pool} : <img src="{chemin_images}/case1.gif" > <br /><br />
			{form_nb_questions_pool} :{nbqp} <br/>
			{form_nb_groupes_pool} : {nbgp}  <br/>
            {form_nb_questions_groupe_pool} : {nbqgp}  <br/>
            <hr/>
            <div id="liste_fils">
			<!-- START BLOCK : liste_groupes -->
					{form_liste_groupes} :
                    <ul>
					<!-- START BLOCK : groupe -->
						<li class="menu_niveau2_item">{nom}   {consulter_fiche}  {modifier_fiche} {supprimer_fiche}  </li><br/>
					<!-- END BLOCK : groupe -->
                    </ul>
			<!-- END BLOCK : liste_groupes -->
			<!-- START BLOCK : creer_groupes -->
                    <form id="monform">
                    <input type="hidden" name="idq" value="{idq}"/>
                    <input type="hidden" name="ide" value="{ide}"/>
                    <input type="hidden" name="url_retour" value="{url_retour}"/>

                     <!-- START BLOCK : id_session -->
                        <input name="{session_nom}" type="hidden" value="{session_id}"/>
                     <!-- END BLOCK : id_session -->
                    {bouton}
                    </form>

			<!-- END BLOCK : creer_groupes -->
			<span class="rouge1">{pool_manque_question}</span>
            </div>
			</td>
         </tr>

</tbody>
</table>
			</div>
<!-- END BLOCK : pool -->

<!-- START BLOCK : pool_fils -->
<div class="panel" id="pools" >
 <table class="fiche">
 	<tbody>
       <tr>
          <th width="40%" >{form_pool}<br/>
         <span class="commentaire1">{commentaire_pool}</span></th>
         <td>
            {form_est_un_groupe} : <img src="{chemin_images}/case1.gif" ><br /><br />
			{form_nb_questions_pool} :{nbqp} <br/>
			{form_nb_groupes_pool} : {nbgp}  <br/>
			<!-- START BLOCK : liste_groupes_fils -->
                    {form_pool_pere} : {nom_pere}  {consulter_fiche}  {modifier_fiche}
                    <hr/>
					{form_liste_groupes} :
                    <ul>
					<!-- START BLOCK : groupe_fils -->
						<li class="menu_niveau2_item">{nom}  {consulter_fiche} {modifier_fiche} </li><br/>
					<!-- END BLOCK : groupe_fils -->
                    </ul>
			<!-- END BLOCK : liste_groupes_fils -->

			</td>
         </tr>
</tbody>
</table>
</div>
<!-- END BLOCK : pool_fils -->

<div class="panel" id="questions" >
	<table class="fiche" width="90%">
		<tbody>
          <tr>
            <th class="bg" colspan="2">{nbQuestions}</th>
          </tr>
          <tr>
            <th> {form_tirage}</th>
            <td>{type_tirage} <i>{algo_tirage}</i><br/>
<!-- START BLOCK : modift -->
				<a href="{url_selection}"><b>{modifier_tirage}</b></a>
<!-- END BLOCK : modift -->
				<span class="commentaire1">{pas_modif}</span>
			</td>
        </tr>
<!-- START BLOCK : rep -->
          <tr>
	   <th class="{style}" title="{obsolete}">
             <span class="bordeau1"> {r} </span>&nbsp;({refer}) <ul style='display:inline;'> {consulter_fiche} </ul>{etat}</th>
            <td>{val}&nbsp;</td>
          </tr>
<!-- END BLOCK : rep -->
	</tbody>
	</table>
</div>

<div class="panel" id="refs" >
	<table class="fiche" width="90%">
		<tbody>
        <tr>
           	 <th class="bg" colspan="3">{questions_par_ref}<br/>{referentiels_traites}</th>
          </tr>
<!-- START BLOCK : q_ref -->
          <tr>
            <th>{r}</th>
            <td>{domaine}</td>
            <td>{val}</td>
          </tr>
<!-- END BLOCK : q_ref -->
		</tbody>
	</table>


    <table class="fiche" width="90%">
          <tbody>
           <tr>
             <th class="bg" colspan="3">{questions_par_comp}<br/></th>
          </tr>
<!-- START BLOCK : q_comp -->
          <tr class="{paire_impaire}">
            <th>{id_comp}</th>
            <td>{aptitude}</td>
            <td>{val}</td>
          </tr>
<!-- END BLOCK : q_comp -->
        </tbody>
    </table>

</div>

<!-- START BLOCK : familles -->
<div class="panel" id="familles" >




   <b>{questions_par_famille}</b><br/>
    <table class="listing" width="90%"  id="sortable">
        <thead>
        <tr {bulle:astuce:msg_tri_colonnes}>
            <th class="bg">{t_id}</th>
            <th class="bg">{t_titre}</th>
            <th class="bg">{t_referentiel}</th>
            <th class="bg">{t_nb}</th>
          </tr>
          </thead>
          <tbody>
<!-- START BLOCK : q_fam -->
          <tr class="{paire_impaire}">
            <td>{id_f}</td>
            <td>{nom_f}</td>
            <td>{ref_f}</td>
            <td>{val}</td>
          </tr>
<!-- END BLOCK : q_fam -->
        </tbody>
    </table>
</div>



<!-- END BLOCK : familles -->


<!-- START BLOCK : apercus -->
 <div class="panel" id="apercus" >
	<table class="fiche" width="90%">
 		<tbody>
            <tr>
           		<th class="bg_examen">{apercus}</th>
    		</tr>
    		<tr>
        		<td>
            		{comme_candidat}<br/>

            		{version_imprimable} <br/>

            		{version_corrigee} <br/>

            		{version_corrigee_imprimable} <br/>

            		<hr/>
            		{passer_examen} <br/>
        		</td>
     		</tr>
		</tbody>
 	</table>
 </div>
<!-- END BLOCK : apercus -->

<!-- START BLOCK : inscriptions -->
<div class="panel" id="inscriptions" >
	<table class="fiche" width="90%">
 		<tbody>
 			<tr>
        		<th class="bg_examen">{inscriptions}</th>
    		</tr>
    		<tr>
            	<td>{nombre_inscrits} {nbInscrits}</td>
        	</tr>
    		<tr>
        		<td>
          			{liste_inscrits} <br/>
                	{liste_inscrits_np} <br/>
                    {liste_emargement_odt} <br/>
        		</td>
    		</tr>
    		<tr>
        		<th class="bg_examen">{nouvelles_inscriptions}</th>
    		</tr>
    		<tr>
               <td>

    			<fieldset>
    		<legend>{comptes_manuels} </legend>
    		 <div class="commentaire1">{info_inscriptions} </div>
            {inscription_manuelle} <br/>
    		{inscriptions_massives_csv}
    		</fieldset>
    		<fieldset>
    		<legend>{comptes_ldap} </legend>
            <div class="commentaire1">{info_inscriptions_ldap}</div>
            {inscriptions_groupe_ldap} <br/>
            {recherches_ldap} <br/>
    		</fieldset>
            <fieldset>
            <legend>{convocations} </legend>
             {convocations_mail} <br/>
            </fieldset>
            </td>
    		</tr>
 		</tbody>
 	</table>
 </div>
<!-- END BLOCK : inscriptions -->

<!-- START BLOCK : resultats -->
<div class="panel" id="resultats" >
	<table class="fiche" width="90%">
		<tbody>
			<tr>
  				<th class="bg_examen">{resultats}</th>
			</tr>
		  <tr>
           <td>{nombre_passages} {nbPassages}<br/>
           <!-- START BLOCK : stats -->
               {form_stats}
                   <b>{t_nb} </b>: {nb}
                   <b>{t_mini} </b>: {mini}
                   <b>{t_maxi} </b>: {maxi}
                   <b>{t_moyenne} </b>: {moyenne}
                    <b>{t_ec} </b>: {stddev}
             <!-- END BLOCK : stats -->
            </td>
          </tr>
  		<tr>
    		<td>
  				{resultats_complets} <br/>
   				{resultats_synthetiques}<br/>
   				{resultats_par_domaine}<br/>
	  			<!-- START BLOCK : export_bd_mysql -->
         				<a href="javascript:void(0);"
           					onclick="openPopup('{url_export_bd_mysql}','mp','{lp}','{hp}')">
                       		{export_bd_mysql}
                       	</a>
           				<br />
				<!-- END BLOCK : export_bd_mysql -->
				<!-- START BLOCK : no_export_bdmysql -->
        					{export_bd_mysql}<br/>
				<!-- END BLOCK : no_export_bdmysql -->
   				{reponses_par_etudiant}<br/>
       			<!-- START BLOCK : export_apogee -->
          				<a href="javascript:void(0);"
           					onclick="openPopup('{url_export_apogee}','mp','{lp}','{hp}')">
                       		{resultats_apogee}
                       	</a>
         				<br />
				<!-- END BLOCK : export_apogee -->
				<!-- START BLOCK : no_export_apogee -->
        					{resultats_apogee}<br/>
				<!-- END BLOCK : no_export_apogee -->
				<!-- START BLOCK : export_xml -->
         				<a href="{url_resultats_export_xml}">
                       		{resultats_export_xml}
                       	</a>
           				<br />
				<!-- END BLOCK : export_xml -->
				<!-- START BLOCK : no_export_xml -->
        					{resultats_export_xml}<br/>
				<!-- END BLOCK : no_export_xml -->

                <!-- START BLOCK : archiver_examen -->
                        <hr/>
                        <a href="{url_archiver_examen}">
                            {archiver_examen}
                        </a>

                <!-- END BLOCK : archiver_examen -->


			</td>
		</tr>
	</tbody>
  </table>
</div>
<!-- END BLOCK : resultats -->

<!-- START BLOCK : optiques -->
<div class="panel" id="optique" >
	<table class="fiche" width="90%">
		<tbody>
			<tr>
   				<th class="bg_examen">{lecture_optique}</th>
			</tr>
			<tr>
    			<td>
		            {fiche_reponses_qcm_pdf}  {fiche_reponses_qcm_odt}  {fiche_reponses_qcm_doc}
       				<hr/>
       				{generation_qcm_direct}<br />
       				{generation_qcm_direct_avec_referentiel}<br />
       				{generation_AMC}<br />
                    {generation_AMC_V2}<br />
       				<hr/>
                    {recuperation_lecture_optique} <br/>
	   			<!-- START BLOCK : recup_optique -->
       				<a href="javascript:void(0);"
          					onclick="openPopup('{url_xmlqcm}','','{lp}','{hp}')">
                       		{recuperation_lecture_optique}
                   	</a>
       				<br />
				<!-- END BLOCK : recup_optique -->
				<!-- START BLOCK : no_recup_optique -->
         					{recuperation_lecture_optique}
				<!-- END BLOCK : no_recup_optique -->
            	</td>
    		</tr>
 		</tbody>
 	</table>
 </div>

<!-- END BLOCK : optiques -->

<!-- START BLOCK : template_resultats -->
<div class="panel" id="template_resultats" >
	<table class="fiche" width="90%">
		<tbody>
			<tr>
   				<th class="bg_examen">{libl_template_resultats}</th>
			</tr>
			<tr>
    			<td>
      				{edit_template_resultats}<br />
       				{reinit_template_resultats}<br />
            	</td>
    		</tr>
 		</tbody>
 	</table>
 </div>

<!-- END BLOCK : template_resultats -->

<!-- START BLOCK : admin -->
<div class="panel" id="admin" >
	<table class="fiche" width="90%">
		<tbody>
			<tr>
   				<th class="bg_examen">{administration}</th>
			</tr>
			<tr>
    			<td>
      				{supprimer_inscrits}<br />
       				{supprimer_inscrits_np}<br />
       				<hr/>
       				{purger_resultats}<br />
					{simuler_passage}<br />
					{annuler_simuler_passage}<br/>
                    <hr/>
                    {exporter_examen}<br/>
                    {archiver_examen}<br/>
                    {verouiller_examen}
            	</td>
    		</tr>
 		</tbody>
 	</table>
</div>
<!-- END BLOCK : admin -->

</div>


EOF;


$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres
require_once($chemin_commun."/lib_ldap.php");
require_once($chemin_commun."/lib_resultats.php");

require_login("P"); //PP


//rev 981 simplification avec le parametre id complet
if ($id=optional_param('id','',PARAM_CLE_C2I)) {
	$ligne=get_examen_byidnat ($id);
	$idq=$ligne->id_examen;
	$ide=$ligne->id_etab;
} else {
	$idq=required_param("idq",PARAM_INT);
	$ide=required_param("ide",PARAM_INT);
	$ligne=get_examen($idq,$ide);
}
v_d_o_d("el");

$refresh=optional_param("refresh",0,PARAM_INT); //rafraichir la liste des exaemns sous-jacente qui a ouvert le popup


//parametres de reaffichage de la liste sous jacente
$url_retour=optional_param("url_retour","",PARAM_RAW);
if ($url_retour)  //garde adresse de rafraichissement de la liste des examens
    var_register_session("liste_examens", $url_retour);

// un "plugin" d'inscription a tourn�. rafraichir la liste
//inutile ?
$maj_liste=optional_param("maj_liste",0,PARAM_INT);

$questions=get_questions($idq,$ide,false,'referentielc2i,alinea,id',false,"",""); //liste des questions dans cet ordre

$nbQuestions=count($questions);

$nbPassages=compte_passages($idq,$ide);
// rev 1022
// attention avec les examens import�s AMC
// get_passages renvoie 0 ! (on n'a pas l'info sur les cases coch�es)
//$passages=get_passages($idq,$ide);
//$nbPassages=count($passages); //inclus les r�sultats non enregistr�s

$nbInscrits=compte_inscrits($idq,$ide);




require_once( $chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates

$tpl = new C2IPopup();	//cr�er une instance
//inclure d'autre block de templates
$tpl->assignInclude("corps",$fiche,T_BYVAR);

   $CFG->utiliser_tables_sortables_js=1;

$tpl->prepare($chemin);

$tpl->assign("_ROOT.titre_popup",traduction("fiche_examen")."<br/>". nom_complet_examen($ligne));

$CFG->utiliser_prototype_js=1;
$CFG->utiliser_fabtabulous_js=1;

//definition des onglets et du contenu

$a_options=false;
$a_pools = ( $USER->type_plateforme=='certification' ||$CFG->pool_en_positionnement )&& ($ligne->est_pool || $ligne->pool_pere !=0);
$a_inscriptions = $ligne->est_pool || $ligne->pool_pere==0;
//$a_resultats    = ! $ligne->est_pool && $nbPassages>0;
$a_resultats    =  $nbPassages>0; // rev 808 25/05/2009 ok pour un pool
$est_anonyme=$ligne->anonyme;

$a_apercus= ! $ligne->est_pool && ($nbQuestions >0 ||  $ligne->type_tirage==EXAMEN_TIRAGE_PASSAGE);
$a_optiques= ! $ligne->est_pool && $nbQuestions >0  && ! $est_anonyme ;



 $peutInscrire= teste_droit("em") &&  etat_examen($ligne) !=-1  && !$est_anonyme; // rev 977 pas d'inscription si anonyme !

if ($a_pools){
	$tpl->newBlock ("pool_tab");

	//2 cas pool_pere ou pool_fils !

	if ($ligne->est_pool) { //pere
		$tpl->newBlock ("pool");
		$a_des_groupes = 0; // passe � 1 si le pool a des d�j� des groupes d�finis
		$groupes = liste_groupe_pool($idq, $ide);
		if (sizeof($groupes)>0) $a_des_groupes = 1;

		//// gestion du pool de questions
		//
		$tpl->assign("commentaire_pool",traduction("commentaire_existe_pool"));
		$tpl->assign("nbqp", $ligne->nb_q_pool);
		$tpl->assign("nbgp", $ligne->pool_nb_groupes);
        //rev 944
        $tpl->assign("nbqgp", $ligne->nbquestions?$ligne->nbquestions:config_nb_aleatoire($ligne->id_etab));

		if ($a_des_groupes == 1){
			// affichage de la liste
			$tpl->assign('pool_manque_question', '');
			$tpl->newBlock('liste_groupes');
			foreach($groupes as  $groupe){
				$tpl->newBlock('groupe');
				$tpl->assign('nom', $groupe->nom_examen);
				//TODO DROITS et ajouter item de suppression
				if ($peutInscrire)
					print_menu_item($tpl,"modifier_fiche",get_menu_item_modifier("ajout.php?idq={$groupe->id_examen}&amp;ide={$groupe->id_etab}"));

				print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("fiche.php?idq={$groupe->id_examen}&amp;ide={$groupe->id_etab}"));
				/**
				if ($peutInscrire && teste_droit("es")) //PAS FINI
					print_menu_item($tpl,"supprimer_fiche",get_menu_item_supprimer("fiche.php?idq={$groupe->id_examen}&amp;ide={$groupe->id_etab}",''));
				**/

			}
		}
		else {
			// donner la possibilit� de les cr�er si le nombre de questions s�lectionn�es dans le pool correspond � ce qui pr�vu
			// v�rification du nombre de questions associ�es � l'examen
			$nbqeff = $nbQuestions;   //nbqe($idq,$ide);
			// afin de g�n�rer les examens s'il manque quelques questions dans le pool :
			// initialement : if ($nbqeff < $ligne->nb_q_pool){
			if ($nbqeff < 45){
				$tpl->assign('pool_manque_question',traduction('pool_manque_question').' : '.$nbqeff.'/'.$ligne->nb_q_pool);
			}
			else {
				// affichage du bloc mise en place appel Ajax
				$tpl->assign('pool_manque_question', '');
				$tpl->newBlock('creer_groupes');
                $tpl->assign("idq",$idq);
                $tpl->assign("ide",$ide);
                // 1er appel c'est url_retour envoy� pat liste
                // apr�s qq navigation dans les inscriptions ...
                $tpl->assign("url_retour",$url_retour?$url_retour:var_get_session("liste_examens"));
                form_session($tpl);
                //TODO rafraichir la liste des examens en dessous !
                $onClick=<<<EOC
                javascript:majDiv("liste_fils","$CFG->chemin_commun/ajax/cree_groupes_pool.php",false,"monform");

EOC;
               print_bouton ($tpl,"bouton","generer_groupes",$onClick);
             }
		}
	}else if ($ligne->pool_pere) {  // est un fils
		//afficher un lien vers le p�re et les "freres" ....'
		$tpl->newBlock ("pool_fils");
		//chercher le p�re et les fr�res
		$groupes = liste_groupe_pool($ligne->pool_pere, $ide);

		$pere=get_examen($ligne->pool_pere,$ide);
		$tpl->assign("nbqp", $pere->nb_q_pool);
        $tpl->assign("nbgp", $pere->pool_nb_groupes);
        $tpl->newBlock('liste_groupes_fils');

        $tpl->assign('nom_pere', $pere->nom_examen);
		print_menu_item($tpl,"modifier_fiche",get_menu_item_modifier("ajout.php?idq={$ligne->pool_pere}&amp;ide={$ligne->id_etab}"));
        print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("fiche.php?idq={$ligne->pool_pere}&amp;ide={$ligne->id_etab}"));

		foreach($groupes as  $groupe){
			if ($groupe->id_examen !=$ligne->id_examen) {
				$tpl->newBlock('groupe_fils');
				$tpl->assign('nom', $groupe->nom_examen);
				print_menu_item($tpl,"modifier_fiche",get_menu_item_modifier("ajout.php?idq={$groupe->id_examen}&amp;ide={$groupe->id_etab}"));
				print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("fiche.php?idq={$groupe->id_examen}&amp;ide={$groupe->id_etab}"));

			}
		}
	}
}


$tpl->gotoBlock("_ROOT");
if ($a_options) {
    $tpl->newBlock ("options_tab");
    $tpl->newBlock ("options");
    $ligne->referentielc2i = traduction("tous");
    $ligne->alinea = traduction("tous");
    if ($ligne->langue=="") $ligne->langue = traduction("tous");
}



$tpl->gotoBlock("_ROOT");
//les balises ont les memes noms que les attributs !
// rev 977

$tpl->assignObjet($ligne);

//retouches
$tpl->assign("type_tirage",type_tirage($ligne)); // rev 874 traduction possible valeurs en BD en fran�ais

$tpl->assign('ordre_q',ordre_affichage($ligne->ordre_q));
$tpl->assign('ordre_r',ordre_affichage($ligne->ordre_r));


$tpl->assign("date_creation",userdate($ligne->ts_datecreation,'strftimedatetimeday'));
$tpl->assign("date_modification",userdate($ligne->ts_datemodification,'strftimedatetimeday'));
$tpl->assign("date_envoi",userdate($ligne->ts_dateenvoi,'strftimedatetimeday')); // rev 921
$tpl->assign("date_examen",userdate($ligne->ts_datedebut,'strftimedatetimeday'));
$tpl->assign("date_examen_fin",userdate($ligne->ts_datefin,'strftimedatetimeday'));


// rev 980
$tpl->assignGlobal('algo_tirage',algo_tirage($ligne));



$tpl->assign("nom_examen",affiche_texte($ligne->nom_examen));

//rev 977 on peut acc�der � la fiche d'un examen de certification en positionnemment
// via les stats ou la fiuche d'uen question, donc il faut donner le type de PF
$typep="";
if ($ligne->certification=="OUI")
    $typep.=" ".traduction ("certification");
if ($ligne->positionnement=="OUI")
    $typep .=" ".traduction ("positionnement");
 $tpl->assign("typep",$typep);


//rev 944
if ($ligne->referentielc2i !=-1)
    $tpl->assign("referentiels_traites",traduction("referentiels_traites",false,$ligne->referentielc2i));
else
        $tpl->traduit("referentiels_traites","touts_referentiels_traites");

$ligne->auteur=applique_regle_nom_prenom($ligne->auteur); // rev 841

$tpl->assign("auteur",cree_lien_mailto($ligne->auteur_mail,$ligne->auteur));
if ($cpt=get_compte_byemail($ligne->auteur_mail)) //pb en test sur prope car beaucoup de compte ont mon mail ;-))
    print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("{$CFG->chemin}/codes/acces/personnel/fiche.php?id=".$cpt->login));
else
   $tpl->assign("consulter_fiche","");

$tpl->assign("nom_univ",nom_univ($ligne->id_etab ));
print_menu_item($tpl,"consulter_fiche_u",get_menu_item_consulter("{$CFG->chemin}/codes/acces/etablissement/fiche.php?idq=".$ligne->id_etab));

//rev 986 restriction adresses IP
if ($CFG->restrictions_ip) {
    $tpl->newBlock('restrictions_ip');
    $liste=get_ips_liste($ligne->subnet);
    if (empty($liste)) 
        $tpl->traduit('subnet','touts_ips');
    else {
        $ips='';
        foreach ($liste as $ip) {
            $ips .=$ip->nom.' (<i>'.$ip->adresses.'</i>)'.'<br/>';
        }
        $tpl->assign('subnet',$ips);
    }
}

if ($USER->type_plateforme=="positionnement") {
    $tpl->newBlock("seuil");
    $tpl->assign("resultat_mini",$ligne->resultat_mini);
    $tpl->newBlock("correction");
    $tpl->setConditionalvalue($ligne->correction==1,"corr",traduction("oui"),traduction ("non"));
    $tpl->newBlock("envoi_resultat");
    $tpl->setConditionalvalue($ligne->envoi_resultat==1,"envoi_resultat",traduction("oui"),traduction ("non"));
    $tpl->newBlock("examen_anonyme");
    $tpl->setConditionalvalue($ligne->anonyme==1,"examen_anonyme",traduction("oui"),traduction ("non"));

}
// rev 978
$tpl->newBlock('limitedureepassage');
$tpl->assign("ts_dureelimitepassage",$ligne->ts_dureelimitepassage?$ligne->ts_dureelimitepassage.' mn':traduction('non'));

//rev 945

if (!$CFG->pas_de_timer) {
     $tpl->newBlock("chrono");
     $tpl->setConditionalvalue($ligne->affiche_chrono==1,"affiche_chrono",traduction("oui"),traduction ("non"));
}


$tpl->gotoBlock("_ROOT");

if ($a_inscriptions) {
    $tpl->newBlock ("inscriptions_tab");
    $tpl->newBlock ("inscriptions");
    $tpl->assign("nbInscrits",$nbInscrits);
    $tpl->setConditionalValue(etat_examen($ligne)==-1, "info_inscriptions",traduction ("info_inscriptions_non"),traduction("info_inscriptions_oui"));

    // 19/05/2009 meme un admin d'une composante ne peut pas inscrire � des examens de la composante sup�rieure



    $LDAPok=auth_ldap_init($ide);  // pas d'erreur fatale si annuaire HS
    $tpl->setConditionalValue($LDAPok,"info_inscriptions_ldap",traduction ("info_inscriptions_ldap_oui"),traduction("info_inscriptions_ldap_non"));

    $tpl->setConditionalValue(etat_examen($ligne)==-1, "info_inscriptions",traduction ("info_inscriptions_non"),traduction("info_inscriptions_oui"));

    $tpl->assign("inscription_manuelle",
                    cree_lien_conditionnel( p_session("inscrits_existants2.php?idq=".$idq."&amp;ide=".$ide."&amp;retour_fiche=1"),
                                            "inscription_manuelle",
                                            $peutInscrire ));

    $tpl->assign("inscriptions_massives_csv",
                    cree_lien_conditionnel( p_session("inscrits_csv.php?idq=".$idq."&amp;ide=".$ide."&amp;retour_fiche=1"),
                                            "inscriptions_massives_csv",
                                             $peutInscrire));
     $tpl->assign("convocations_mail",
                    cree_lien_conditionnel( p_session("convocations_mail.php?idq=".$idq."&amp;ide=".$ide."&amp;retour_fiche=1"),
                                            "convocations_mail",
                                            $peutInscrire && $nbInscrits>0));
    $tpl->assign("recherches_ldap",
                    cree_lien_conditionnel( p_session("inscrits_ldap.php?idq=".$idq."&amp;ide=".$ide."&amp;retour_fiche=1"),
                                            "recherches_ldap",
                                            $peutInscrire && $LDAPok));
      $tpl->assign("inscriptions_groupe_ldap",
                    cree_lien_conditionnel( p_session("inscrits_groupe_ldap.php?idq=".$idq."&amp;ide=".$ide."&amp;retour_fiche=1"),
                                            "inscriptions_groupe_ldap",
                                            $peutInscrire && $LDAPok));


    $tpl->assign("liste_inscrits",
                    cree_lien_conditionnel(p_session("liste_inscrits.php?retour_fiche=1&amp;ide=".$ide."&amp;idq=".$idq),
                                            "liste_inscrits",
                                            $nbInscrits>0 && a_capacite("etl",$ide))); // rev 839
   $tpl->assign("liste_inscrits_np",
                    cree_lien_conditionnel(p_session("liste_inscrits.php?retour_fiche=1&amp;ide=".$ide."&amp;idq=".$idq."&amp;type=1"),
                                            "liste_inscrits_np",
                                            $nbInscrits-$nbPassages>0 && a_capacite("etl",$ide))); // rev 839
   $tpl->assign("liste_emargement_odt",
                    cree_lien_conditionnel(p_session("liste_emargement.php?retour_fiche=1&amp;ide=".$ide."&amp;idq=".$idq),
                                            "liste_emargement_odt",
                                            $nbInscrits>0 && a_capacite("etl",$ide) && !$est_anonyme)); // rev 1005

}



if ($a_resultats) {
	$tpl->newBlock ("resultats_tab");
	$tpl->newBlock ("resultats");
	$tpl->assign("nbPassages",$nbPassages);


	$tpl->assign("reponses_par_etudiant",
		cree_lien_conditionnel(p_session("resultats/reponse_par_etudiant2.php?retour_fiche=1&amp;idq=".$idq."&amp;ide=".$ide),
			"reponses_par_etudiant",
			$nbPassages>0 && !$est_anonyme)); //implement� pour un pool
	$tpl->assign("resultats_complets",
		cree_lien_conditionnel(p_session("resultats/resultats.php?retour_fiche=1&amp;affichage=complets&amp;idq=".$idq."&amp;ide=".$ide),
			"resultats_complets",
			$nbPassages>0 ));  //moyennement bien implement� pour un pool
	$tpl->assign("resultats_synthetiques",
		cree_lien_conditionnel(p_session("resultats/resultats.php?retour_fiche=1&amp;affichage=synthetiques&amp;idq=".$idq."&amp;ide=".$ide),
			"resultats_synthetiques",
			$nbPassages>0));
	$tpl->assign("resultats_par_domaine",
		cree_lien_conditionnel(p_session("resultats/resultats.php?retour_fiche=1&amp;affichage=referentiel&amp;idq=".$idq."&amp;ide=".$ide),
			"resultats_par_domaine",
			$nbPassages>0));
	if ($nbPassages>0 && teste_droit("em") && !$est_anonyme){  // implement� pour un pool
		$tpl->newBlock("export_apogee");
		$tpl->assignUrl("url_export_apogee","export_apogee.php?retour_fiche=0&amp;idq=".$idq."&amp;ide=".$ide);
	} else $tpl->newBlock("no_export_apogee");

	if ($nbPassages>0 && $exportDB && teste_droit("em") && $ligne->pool_pere==0 ) {  //non proposer pour un pool fils , passer par le père
		$tpl->newBlock("export_bd_mysql");
		$tpl->assign("url_export_bd_mysql",p_session("resultats/resultats.php?retour_fiche=0&amp;affichage=extdb&amp;idq=".$idq."&amp;ide=".$ide));
	} else
		$tpl->newBlock("no_export_bdmysql");

	if ($nbPassages>0 && teste_droit("em")) {   //implement� pour un pool
		$tpl->newBlock("export_xml");
		$tpl->assign("url_resultats_export_xml",p_session("resultats/export_xml.php?idq=".$idq."&amp;ide=".$ide));
	} else
		$tpl->newBlock("no_export_xml");
    // rev 973
    if ($nbPassages>0 && teste_droit("em")) {   //implement� pour un pool
        $tpl->newBlock("archiver_examen");
        $tpl->assign("url_archiver_examen",p_session("admin/archiver_examen.php?eid=".$ide.".".$idq));
    }

   $stats=get_stats_examen($idq,$ide);
    if ($stats->nb) {
        $tpl->newBlock("stats");
        $tpl->assignObjet($stats);
        $tpl->assign("moyenne",sprintf("%.2f",$stats->moyenne));
    }
}

if ($a_apercus) {  //non permis pour un pool
    $tpl->newBlock ("apercus_tab");
    $tpl->newBlock ("apercus");
    $tpl->assign("comme_candidat",
                    cree_lien_conditionnel(p_session("comme_candidat.php?retour_fiche=1&amp;idq=".$idq."&amp;ide=".$ide),
                                            "comme_candidat",
                                            $nbQuestions>0 || $ligne->type_tirage==EXAMEN_TIRAGE_PASSAGE));

    $tpl->assign("version_imprimable",
                    cree_lien_conditionnel(p_session("version_imprimable.php?retour_fiche=1&amp;idq=".$idq."&amp;ide=".$ide),
                                            "version_imprimable",
                                            $nbQuestions>0 ));  // donc jamais en mode tirage lors du passage

     //if ($CFG->prof_peut_passer_qcm) {
        $tpl->assign("passer_examen",
                    cree_lien_conditionnel(p_session("comme_candidat.php?retour_fiche=1&amp;idq=".$idq."&amp;ide=".$ide."&amp;mode=".QCM_TEST),
                                            "passer_examen",$nbQuestions>0 || $ligne->type_tirage==EXAMEN_TIRAGE_PASSAGE));
     //}
      $tpl->assign("version_corrigee",
                    cree_lien_conditionnel(p_session("comme_candidat.php?retour_fiche=1&amp;idq=".$idq."&amp;ide=".$ide."&amp;mode=".QCM_CORRIGE),
                                            "version_corrigee",
                                            $nbQuestions>0 )); // donc jamais en mode tirage lors du passage

      $tpl->assign("version_corrigee_imprimable",
                    cree_lien_conditionnel(p_session("version_imprimable.php?retour_fiche=1&amp;idq=".$idq."&amp;ide=".$ide."&amp;mode=".QCM_CORRIGE),
                                            "version_corrigee_imprimable",
                                            $nbQuestions>0 ));  // donc jamais en mode tirage lors du passage

}

if ($a_optiques) {   //non permis pour un pool
   $tpl->newBlock ("optiques_tab");
   $tpl->newBlock ("optiques");
   $tpl->assign("generation_qcm_direct",
                    cree_lien_conditionnel(p_session("exportQCMDirect.php?export=QCMdirect&amp;idq=".$idq."&amp;ide=".$ide,1),
                                            "generation_qcm_direct",
                                            $nbQuestions>0));
   $tpl->assign("generation_qcm_direct_avec_referentiel",
                    cree_lien_conditionnel(p_session("exportQCMDirect.php?export=QCMdirect&amp;montre_ref=1&amp;idq=".$idq."&amp;ide=".$ide,1),
                                            "generation_qcm_direct_avec_referentiel",
                                            $nbQuestions>0));

	// rev 964
	if ($CFG->export_AMC) {
	 $tpl->assign("generation_AMC",
                    cree_lien_conditionnel(p_session("export_autoMC.php?type=1&amp;idq=".$idq."&amp;ide=".$ide,1),
                                            "generation_AMC",
                                            $nbQuestions>0));
     $tpl->assign("generation_AMC_V2",
                    cree_lien_conditionnel(p_session("export_autoMC.php?type=2&amp;idq=".$idq."&amp;ide=".$ide,1),
                                            "generation_AMC_V2",
                                            $nbQuestions>0));

    } else {
         $tpl->assign("generation_AMC","");
          $tpl->assign("generation_AMC_V2","");
    }

    $tpl->assign("fiche_reponses_qcm_doc",
                    cree_lien_conditionnel("FicheLectureOptiqueC2i-1.doc",
                                            "fiche_reponses_qcm_doc",
                                            $nbQuestions>0));
    $tpl->assign("fiche_reponses_qcm_odt",
                    cree_lien_conditionnel("FicheLectureOptiqueC2i-1.odt",
                                            "fiche_reponses_qcm_odt",
                                            $nbQuestions>0));

     $tpl->assign("fiche_reponses_qcm_pdf",
                    cree_lien_conditionnel(//p_session("fiche_reponse.php?idq=".$idq."&ide=".$ide,1),
                                            "FicheLectureOptiqueC2i-1.pdf",
                                            "fiche_reponses_qcm_pdf",
                                            $nbQuestions>0));
 /*
   if ($nbQuestions>0 &&$peutInscrire && $nbInscrits>0) {
        $tpl->newBlock("recup_optique");
        $tpl->assignUrl("url_xmlqcm","import_optique.php?idq=".$idq."&ide=".$ide,1);
   } else $tpl->newBlock("no_recup_optique");
 */
 /* revision 1016 29/01/2010
  * si l'examen est membre d'un pool, il n'a pas d'inscrit car justement
  * les vrais futurs inscrits sont dans le fichier recu du logiciel de lecture optique !!!
  *
  */
 $tpl->assign("recuperation_lecture_optique",
        cree_lien_conditionnel(p_session("import_optique.php?retour_fiche=1&amp;idq=".$idq."&amp;ide=".$ide),
            "recuperation_lecture_optique",
            $nbQuestions>0 &&teste_droit("em") && ($nbInscrits>0 || $ligne->pool_pere!=0)));



    $tpl->assign("fiche_reponses_qcm_doc",
                    cree_lien_conditionnel("FicheLectureOptiqueC2i-1.doc",
                                            "fiche_reponses_qcm_doc",
                                            $nbQuestions>0));
}

// affichage de l'onglet template resultat

// uniquement en postionnement rev 964
if ($USER->type_plateforme=="positionnement") {
   $tpl->newBlock ("template_resultats_tab");
   $tpl->newBlock ("template_resultats");
   $tpl->assign("edit_template_resultats",
                    cree_lien_conditionnel(p_session("template_resultat.php?retour_fiche=1&amp;affichage=referentiel&amp;idq=".$idq."&amp;ide=".$ide),
                                            "libl_template_resultats",
                                            teste_droit("em")));

   $tpl->assign("reinit_template_resultats",
                    cree_lien_conditionnel(p_session("reinit_template_resultat.php?retour_fiche=1&amp;affichage=referentiel&amp;idq=".$idq."&amp;ide=".$ide),
                                            "reinit_template_resultats",
                                            teste_droit("em") && $ligne->template_resultat !=""));
}

// affichage des questions :

$tpl->gotoBlock("_ROOT");

$tab_nbq_ref = array();
// rev 928 26/12/2010
$tab_nbq_fam = array();
// rev980
$tab_nbq_comp=array();

/*
$competences=get_alineas('');
foreach ($competences as $comp) {
    $tab_nbq_comp[$comp->referentielc2i.".".$comp->alinea]=0;
}
*/


//rev 944 affichage du nombre de questions pr�vues (cas du tirage lors du passage ou pool par competence)
if ($ligne->type_tirage ==EXAMEN_TIRAGE_PASSAGE) {
    $nbq=$ligne->nbquestions ? $ligne->nbquestions : config_nb_aleatoire($ide);
    $tpl->assign("nbQuestions",$nbq." ".traduction ("questions",false));
     $tpl->assign("questions_par_ref",traduction("pas_modifier_tirage_passage"));
}
else {
    if ($ligne->est_pool) {
        //rev 944 le nombre de question de chaque 'membre'
        $nbq=$ligne->nbquestions ? $ligne->nbquestions : config_nb_aleatoire($ide);
        $tpl->assign("nbQuestions",
        $nbQuestions. " ".traduction ("questions",false).
        '<br/>'.traduction ("nb_questions_par_enfant",false,$nbq)
        );

    }
    else
        $tpl->assign("nbQuestions",$nbQuestions." ".traduction ("questions",false));
}

$tpl->assign("_ROOT.pas_modif","");
//  si c'est un pool ayant d�j� des groupes attribu�s

if ($ligne->pool_pere) $tpl->assign("_ROOT.pas_modif",traduction("pas_modifier_tirage_pool_groupe"));
else {
	if ($ligne->est_pool && $a_des_groupes == 1) $tpl->assign("_ROOT.pas_modif",traduction("pas_modifier_tirage_pool"));

	else if (etat_examen($ligne)==1) { //PP uniquement si a venir...
       // attention aux composantes (is_admin() de CET etablissement !
		if (is_admin(false,$ide)  || (teste_droit("em") && $USER->id_etab_perso==$ligne->id_etab)) {
			if ($ligne->type_tirage !=EXAMEN_TIRAGE_PASSAGE){
				$tpl->newBlock("modift");
				$tpl->assign("url_selection",p_session("selection.php?idq=".$idq."&amp;ide=".$ide."&amp;retour_fiche=1"));
				//$tpl->assign("modifier_tirage",traduction("modifier_tirage"));
			} else $tpl->assign("_ROOT.pas_modif",traduction("pas_modifier_tirage_passage"));
		} else $tpl->assign("_ROOT.pas_modif",traduction("pas_modifier_tirage_droits"));
	}
	else {
       if ($ligne->type_tirage !=EXAMEN_TIRAGE_PASSAGE)  // rev 868 message �tait faux dans le cas tirage lors du passage (disait en cours !)
		  $tpl->assign("_ROOT.pas_modif",traduction("pas_modifier_tirage"));
       else
        $tpl->assign("_ROOT.pas_modif",traduction("pas_modifier_tirage_passage"));
	}
}


foreach($questions as $question) {
    	$tpl->newBlock("rep");
	if ($question->etat==QUESTION_REFUSEE) {
		$tpl->assign("style","rouge");
		$tpl->traduit("obsolete","alt_non_valide");
	}else  {
        if ($question->etat==QUESTION_VALIDEE) {
		$tpl->assign("style","vert");
		$tpl->traduit("obsolete","alt_valide");
        } else {
            $tpl->assign("style","orange");
        $tpl->traduit("obsolete","alt_non_examinee");
        }
	}

	$tpl->assign("r",$question->id_etab.".".$question->id);
    $tpl->assign("etat",$question->etat);


	//ajout PP popup de consultation
	 print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../questions/fiche.php?idq=".$question->id."&amp;ide=".$question->id_etab));

	//end PP
	$tpl->assign("refer",get_domaine_traite($question));
	$tpl->assign("val",affiche_texte_question($question->titre));

    $ref= $question->referentielc2i;
    $al= $question->alinea;

    if (!array_key_exists($ref,$tab_nbq_ref)){
		$tab_nbq_ref[$ref] = 0;
	}
	$tab_nbq_ref[$ref] ++;

    $comp=$ref.".".$al;
    if (!array_key_exists($comp,$tab_nbq_comp)){
        $tab_nbq_comp[$comp] = 0;
    }
    $tab_nbq_comp[$comp] ++;


    $fam=$question-> id_famille_validee;
    if (!array_key_exists($fam,$tab_nbq_fam)){
        $tab_nbq_fam[$fam] = 0;
    }
    $tab_nbq_fam[$fam] ++;

}



ksort($tab_nbq_ref);


// affichage de la r�partition des questions par r�f�rentiel :
foreach ($tab_nbq_ref as $ref => $val){
	$tpl->newBlock("q_ref");
	$tpl->assign("r",$ref);
    if ($referentiel=get_referentiel($ref,false))
        $tpl->assign ('domaine',$referentiel->domaine);
    else
        $tpl->assign ('domaine','');
	$tpl->assign("val",$val);
}


ksort($tab_nbq_comp);

    // affichage de la r�partition des questions par comp�tsnce
    $compteur_ligne=0;
    foreach ($tab_nbq_comp as $comp => $val){
        $tpl->newBlock("q_comp");
        $tpl->setCouleurLigne($compteur_ligne++);
        $tpl->assign("id_comp",$comp);
        if ($competence=get_alinea_byidnat($comp,false))
            $tpl->assign('aptitude',$competence->aptitude);
        else
             $tpl->assign('aptitude','');
        $tpl->assign("val",$val);
    }


// rev 948 onglet addmin (tests et actions diverses)

if (is_admin(false,$ide) && $CFG->onglet_admin_examen) {
	$tpl->gotoBlock("_ROOT");
	   $tpl->newBlock ("admin_tab");
   $tpl->newBlock ("admin");

    $tpl->assign("supprimer_inscrits",
                    cree_lien_conditionnel(p_session("admin/admin.php?action=si&amp;retour_fiche=1&amp;eid=".$ide.".".$idq),
                                            "supprimer_inscrits",
                                            $nbInscrits>0 ));
     $tpl->assign("supprimer_inscrits_np",
                    cree_lien_conditionnel(p_session("admin/admin.php?action=sinp&amp;retour_fiche=1&amp;eid=".$ide.".".$idq),
                                            "supprimer_inscrits_np",
                                            $nbInscrits>0 && $nbPassages < $nbInscrits));
      $tpl->assign("purger_resultats",
                    cree_lien_conditionnel(p_session("admin/admin.php?action=pur&amp;retour_fiche=1&amp;eid=".$ide.".".$idq),
                                            "purger_resultats",
                                            $nbPassages >0));
        $tpl->assign("simuler_passage",
                    cree_lien_conditionnel(p_session("admin/admin.php?action=sim&amp;retour_fiche=1&amp;eid=".$ide.".".$idq),
                                            "simuler_passage",
                                            $nbPassages < $nbInscrits && $ligne->type_tirage !=EXAMEN_TIRAGE_PASSAGE));

	   $tpl->assign("annuler_simuler_passage",
                    cree_lien_conditionnel(p_session("admin/admin.php?action=asim&amp;retour_fiche=1&amp;eid=".$ide.".".$idq),
                                            "annuler_simuler_passage",
                                            compte_simulations($idq,$ide) >0 ));
      if ($CFG->autoriser_export)
         $tpl->assign("exporter_examen",
                    cree_lien_conditionnel(p_session("admin/export_examen.php?eid=".$ide.".".$idq),
                                            "exporter_examen",
                                             true));
      else $tpl->assign("exporter_examen",''); // ne pas montrer

       $tpl->assign("archiver_examen",
            cree_lien_conditionnel(p_session("admin/archiver_examen.php?eid=".$ide.".".$idq),
                                            "archiver_examen",
                                             true));
     if ($ligne->verouille)
         $tpl->assign("verouiller_examen",
            cree_lien_conditionnel(p_session("admin/admin.php?action=deverouille&amp;retour_fiche=1&amp;eid=".$ide.".".$idq),
                                            "deverouiller_examen",
                                             true));
        else
         $tpl->assign("verouiller_examen",
            cree_lien_conditionnel(p_session("admin/admin.php?action=verouille&amp;retour_fiche=1&amp;eid=".$ide.".".$idq),
                                            "verouiller_examen",
                                             true));

}

// rev 978 26/12/2010
if (($USER->type_plateforme=="positionnement"  && $CFG->montrer_stats_par_famille_pos) ||
        ($USER->type_plateforme=="certification"  && $CFG->montrer_stats_par_famille_cert)) {


    $tpl->newBlock('famille_tab');
    $tpl->newBlock('familles');
    ksort($tab_nbq_fam);

    // affichage de la r�partition des questions par r�f�rentiel :
    $compteur_ligne=0;
    foreach ($tab_nbq_fam as $fam => $val){
        $tpl->newBlock("q_fam");
        $tpl->setCouleurLigne($compteur_ligne++);
        $tpl->assign("id_f",$fam);
        if ($famille=get_famille($fam,false)) {
            $tpl->assign("nom_f",$famille->famille);
            $tpl->assign("ref_f",get_referentiel_famille($famille));
        }
        else {
            $tpl->assign("nom_f",'');
            $tpl->assign("ref_f",'');
        }
        $tpl->assign("val",$val);
    }




}

if ($CFG->activer_tags_examen) {
    $tpl->newBlock('tags');
    $tpl->assign('tags',$ligne->tags);
}



$tpl->print_boutons_fermeture();

if ($refresh)  {  //retour d'une op�ration modifiant cette liste  (inscriptions, pools ...)
	if($url_retour=var_get_session("liste_examens")) {
		$tpl->newBlock("rafraichi_liste");
		$tpl->assign("url_retour",$url_retour);
	}
}

$tpl->printToScreen();	//affichage
?>
