<?php
/**
 * @author Patrick Pollet
 * @version $Id: ajout.php 1312 2012-11-14 12:50:49Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
////////////////////////////////
//
//	Ajout et modification d'item
//	et duplication (ajout par recopie)
//
////////////////////////////////

/*
* Pour la description des diff�rentes m�thodes de la classe TemplatePower,
* il faut se ref�rer � http://templatepower.codocad.com/
*/
//******** Pour chaque page $chemin repr�sente le path(chemin) de script dans le site (� la racine)
//******** ---------------- $chemin_commun repr�sente le path des utilitaires dont on aura besoin
//******** ---------------- $chemin_images repr�sente le path des images
$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres
require_login("P"); //PP


// rev 981 simplification des appels via idnationale ( ou -1)
if ($id=optional_param('id','',PARAM_CLEAN)) {  //attention pas CLE_C2I
    if ($id !=-1) {
        $ligne=get_examen_byidnat ($id);
        $idq=$ligne->id_examen;
        $ide=$ligne->id_etab;
    } else  {
       $idq=-1; $ide= $USER->id_etab_perso;
    }
} else {
    $idq=optional_param("idq",-1,PARAM_INT);   // -1 en cr�ation
    $ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT); //�tab de l'examen, d�faut = ici '
}

if ($dup_id=optional_param('dup_id','',PARAM_CLE_C2I)) {
    $ligne=get_examen_byidnat ($dup_id);
    $copie_id=$ligne->id_examen;
    $copie_ide=$ligne->id_etab;
} else {
    $copie_id=optional_param("copie_id",0,PARAM_INT); //duplication ?
    $copie_ide=optional_param("copie_ide",0,PARAM_INT);
}


//$idq=optional_param("idq",-1,PARAM_INT);   // -1 en cr�ation
//$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT); //�tab de l'examen, d�faut = ici '
//$copie_id=optional_param("copie_id",0,PARAM_INT); //duplication ?
//$copie_ide=optional_param("copie_ide",0,PARAM_INT);

$url_retour=optional_param("url_retour","",PARAM_CLEAN);

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates

$fiche=<<<EOF


<form name="monform" id="monform" action="action.php" method="post">

<table class="fiche">
<tbody>
  <tr>
    <th>{form_libelle}</th>
    <td><textarea name="nom_examen" cols="60" rows="5" class="required"
					title="{js_libelle_manquant}">{nom_examen}</textarea></td>
 </tr>
<!-- START BLOCK : examen_anonyme -->
 <tr>
    <th>{form_examen_anonyme}</th>
    <td>
       <input name="anonyme" type="checkbox" value="1" {checked_anonyme} />       <br/>
       <span class="commentaire1">{msg_examen_anonyme} </span>
   </td>
 </tr>

<!-- END BLOCK : examen_anonyme -->


 <tr>
   <th>{form_tirage}</th>
   <td>
     <!-- START BLOCK : type_tirage -->
      	<input name="type_tirage" type="radio" value="manuel" {checked_manuel} /> {form_manuel}
       	&nbsp;&nbsp;
       	<input name="type_tirage" type="radio" value="aleatoire" {checked_aleatoire} /> {form_aleatoire}
       <!-- END BLOCK : type_tirage -->
<!-- START BLOCK : tirage_pool -->
		&nbsp;&nbsp;
		<input name="type_tirage_xx" type="radio"  value="pool"  checked disabled />{form_tirage_pool}
		<input name="type_tirage" type="hidden"  value="pool" >
<!-- END BLOCK : tirage_pool -->
<!-- START BLOCK : tirage3 -->
		&nbsp;&nbsp;
		<input name="type_tirage" type="radio" value="passage" {checked_passage} /> {form_passage_aleatoire}
<!-- END BLOCK : tirage3 -->
        </td>
 </tr>



<!-- START BLOCK : pool -->
  <tr>
     <th>{form_pool}<br/>
     <span  class="commentaire1">{commentaire_pool}</span></th>
     <td>
      <div class="normale">
      <p class="double">
      <label for="est_pool"> {form_est_un_pool} : </label>
       <input name="est_pool" id="est_pool" type="radio" value="1" {checked_pool_oui}/> {oui}
       <input name="est_pool" type="radio" value="0" {checked_pool_non}/> {non}
      </p>
      <p class="double">
       <label for="nb_q_pool">{form_nb_questions_pool} : </label>
       <input type="text" name="nb_q_pool" id="nb_q_pool" class="validate-digits"
		      title="{js_valeur_numerique_attendue}" value="{nb_q_pool}" size="4"/>
      </p>
      <p class="double">
         <label for="pool_nb_groupes"> {form_nb_groupes_pool} </label>
         <input type="text" name="pool_nb_groupes" id="pool_nb_groupes" class="validate-digits"
           title="{js_valeur_numerique_attendue}"  value="{pool_nb_groupes}" size="4"/>
       </p>
       <p class="simple"> </p>
       </div>
     </td>
  </tr>
<!-- END BLOCK : pool -->
<!-- START BLOCK : pool_valide -->
   <tr>
       <th>{form_pool}<br/>
         <span class="commentaire1">{commentaire_pool}</span>

       </th>
       <td>
         {form_est_un_pool} : <img src="{chemin_images}/case1.gif" alt=""/>
           <input name="est_pool" type="hidden" value="1"/> <br/>
         {form_nb_questions_pool} : {nb_q_pool} <br/>
         <input type="hidden" name="nb_q_pool" value="{nb_q_pool}"/>
         {form_nb_groupes_pool} : {pool_nb_groupes} <br/>
         <input type="hidden" name="pool_nb_groupes" value="{pool_nb_groupes}"/>
       </td>
  </tr>
<!-- END BLOCK : pool_valide -->

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
              {form_auteurs} {form_nom_coll}</th>
            <td>{auteur}</td>
          </tr>

          <tr>
            <th> {universite} </th>
            <td>{etablissement} <ul style="display:inline;"> {consulter_fiche} </ul></td>
          </tr>


<!-- START BLOCK : debut_examen -->
<tr>
<th> {form_debut_examen}</th>
 <td><input type="text" name="date_debut" id="f_date_debut" size="30" readonly="readonly" value="{date_debut}" class="required" title="{js_date_debut_manquante}"/>
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
</td>
</tr>
<!-- END BLOCK : debut_examen -->

<!-- START BLOCK : fin_examen -->
<tr>
<th> {form_fin_examen}</th>
 <td><input type="text" name="date_fin" id="f_date_fin" size="30" readonly="readonly" value="{date_fin}" class="required" title="{js_date_fin_manquante}"/>
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
</td>
</tr>
<!-- END BLOCK : fin_examen -->

 <tr>
   <th>{form_mot_passe_examen}</th>
   <td  ><input size="20" type="text" value="{mot_de_passe}" name="mot_de_passe" class="saisie" /></td>
 </tr>

<!-- START BLOCK : restrictions_ip -->   
  <tr>
   <th> {form_restriction_ip}<br/>
   <div class="commentaire1">{info_restriction_ip}</div></th>

 <td>{multi_select_ips}</td>     
  </tr>
<!-- END BLOCK : restrictions_ip -->   
 
  <tr>
    <th>{form_ordre_q}</th>
    <td>
    	<input name="ordre_q" type="radio" value="fixe" 		{ch_oqf} /> {form_fixe}
    	&nbsp;&nbsp;
        <input name="ordre_q" type="radio" value="aleatoire" 	{ch_oqa} /> {form_aleatoire}
   </td>
 </tr>
  <tr>
    <th>{form_ordre_r}</th>
    <td>
    	<input name="ordre_r" type="radio" value="fixe" 		{ch_orf} /> {form_fixe}
    	&nbsp;&nbsp;
        <input name="ordre_r" type="radio" value="aleatoire" 	{ch_ora} /> {form_aleatoire}
   </td>
 </tr>

<!-- START BLOCK : correction -->
  <tr>
    <th>{form_correction}</th>
    <td>
    	<input name="correction" type="radio" value="1" {ch_coro} /> {oui}
    	&nbsp;&nbsp;
    	<input name="correction" type="radio" value="0" {ch_corn} /> {non}
   </td>
 </tr>
<!-- END BLOCK : correction -->

<!-- START BLOCK : envoi_resultat -->
  <tr>
    <th>{form_envoi_resultat}</th>
    <td>
    	<input name="envoi_resultat" type="radio" value="1" {ch_envoio} /> {oui}
    	&nbsp;&nbsp;
    	<input name="envoi_resultat" type="radio" value="0" {ch_envoin} /> {non}
   </td>
 </tr>
<!-- END BLOCK : envoi_resultat -->

<!-- START BLOCK : limitedureepassage -->
<tr>
<th>{form_duree_limite}<br/>
</th>
<td>
 <input type="text" name="ts_dureelimitepassage" class="validate-digits"
              title="{js_valeur_numerique_attendue}" value="{ts_dureelimitepassage}" size="4"/>
<div class="commentaire1">{info_duree_limite}</div>
</td>
</tr>
<!-- END BLOCK : limitedureepassage -->

<!-- START BLOCK : affiche_chrono -->
  <tr>
    <th>{form_affiche_chrono}</th>
    <td>
        <input name="affiche_chrono" type="radio" value="1" {ch_chronoo} /> {oui}
        &nbsp;&nbsp;
        <input name="affiche_chrono" type="radio" value="0" {ch_chronon} /> {non}
   </td>
 </tr>
<!-- END BLOCK : affiche_chrono -->

<!-- START BLOCK : seuil -->

<tr>
    <th>{note_mini}</th>
    <td>
       <select size="1" name="resultat_mini" class="required validate-digits" title="{js_valeur_numerique_attendue}">
          <option value="">{selectionner}</option>
           <!-- START BLOCK : option_mini -->
              <option value="{val}" {selected}>{aff}</option>
            <!-- END BLOCK : option_mini -->
      </select>
    </td>
 </tr>
 <!-- END BLOCK : seuil -->


<!-- START BLOCK : nbquestions -->
<tr>
<th>{form_nombre_questions}<br/>
</th>
<td>
 <input type="text" name="nbquestions" class="validate-digits"
              title="{js_valeur_numerique_attendue}" value="{nbquestions}" size="4"/>
<div class="commentaire1">{info_nombre_questions}</div>
</td>
</tr>
<!-- END BLOCK : nbquestions -->

<!-- START BLOCK : domaines -->
<tr>
<th>{form_referentiels_traites}<br/>
<div class="commentaire1">{info_referentiels_traites}</div></th>
<td>
{multi_select_refs}
</td>
</tr>
<!-- END BLOCK : domaines -->



<!-- START BLOCK : tags -->
<tr>
<th>{form_tags}<br/>
<div class="commentaire1">{info_tags}</div></th>

 <td><textarea name="tags" cols="60" rows="5"
                   >{tags}</textarea></td>
</tr>
<!-- END BLOCK : tags -->


</tbody>
</table>
 <br/>

<div class="centre">
 {bouton_annuler} &nbsp;{bouton_reset} &nbsp; {bouton:enregistrer}

<input name="id" type="hidden" value="{id}" />
<input name="ide" type="hidden" value="{ide}" />
<input name="dupliquer" type="hidden" value="{dupliquer}" />
<input type="hidden" name="url_retour" value="{url_retour}" />
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->

<!-- START BLOCK : consulter -->
<input name="consulter" type="hidden" value="1" />
<!-- END BLOCK : consulter -->
</div>
<!-- START BLOCK : seuil_fixe -->
<input type="hidden" name="resultat_mini" value="{resultat_mini}" />
<!-- END BLOCK : seuil_fixe -->

</form>
EOF;



$tpl->assignInclude("corps", $fiche,T_BYVAR); // le template g�rant l'examen
$tpl->prepare($chemin);


$CFG->utiliser_validation_js=1;
$CFG->utiliser_js_calendar=1;   //forc� (est � 0 dans config pour accelerer les pages)

	$tpl->newBlock("debut_examen");
	$tpl->newBlock("fin_examen");


$tpl->gotoBlock("_ROOT");
// tres important pour retour a la liste
$tpl->assign("url_retour", $url_retour);


//////////////////////////////
// gestion de la duplication
$dupliquer = 0;

if ($copie_id  && $copie_ide) {
	v_d_o_d("ed"); //PP
	$idq= copie_examen($copie_id,$copie_ide,true); // duplique et copie les questions (sauf si membre d'un pool)
	$dupliquer = 1;
	$tpl->gotoBlock("_ROOT");
} else v_d_o_d("em");

//////////////////////////////

$tpl->assign("_ROOT.id", $idq);
$tpl->assign("_ROOT.ide", $ide);
$tpl->assign("dupliquer",$dupliquer);

if ($idq !=-1) { // modification
	$ligne=get_examen($idq,$ide); // lecture ou relecture
	if ($dupliquer == 0) { // pas de duplication
		$tpl->assign("_ROOT.titre_popup", traduction("modifier_examen") . " " .$ide. "." . $idq);
	} else { // duplication
		$tpl->assign("_ROOT.titre_popup", traduction( "dupliquer_examen") . " " .$copie_ide. "." . $copie_id);
	}

	//// gestion du pool de questions
	//
	if ($CFG->autoriser_pool && ($USER->type_plateforme == 'certification' || $CFG->pool_en_positionnement)) {
		if ($ligne->pool_pere == 0) {
			$a_des_groupes = 0; // passe � 1 si le pool a des d�j� des groupes d�finis
			if ($ligne->est_pool == 1) {
				$groupes = liste_groupe_pool($idq, $ide);
				if (sizeof($groupes) > 0)
					$a_des_groupes = 1;
				if ($a_des_groupes == 1)
					$tpl->newBlock('pool_valide');
				else
					$tpl->newBlock('pool');
				$tpl->setChecked($ligne->est_pool, "checked_pool_oui","checked_pool_non");
				$tpl->assign("nb_q_pool", $ligne->nb_q_pool);
				$tpl->assign("pool_nb_groupes",  $ligne->pool_nb_groupes);
			}
		}else {
			//membre d'un pool attention ...'

		}
	}

} else { // nouvel examen
	v_d_o_d("ea"); //PP

	$tpl->traduit("_ROOT.titre_popup","nouvel_examen");
	$tpl->assign("_ROOT.ch_anonyme_n", " checked");
	$ligne=new stdClass();
	$ligne->nom_examen=$ligne->mot_de_passe="";
	$ligne->resultat_mini = $CFG->examen_seuil_validation;
	$ligne->type_tirage=$CFG->examen_type_tirage_defaut;
	$ligne->ordre_q=$CFG->examen_ordre_questions_defaut;
	$ligne->ordre_r=$CFG->examen_ordre_reponses_defaut;
	$ligne->id_etab=$USER->id_etab_perso;
	$ligne->anonyme=0;
	$ligne->correction=$ligne->envoi_resultat=0;
	$ligne->ts_datecreation=$ligne->ts_datemodification=time();
    $ligne->ts_dureelimitepassage=$CFG->limite_temps_passage;
	$ligne->pool_pere=$ligne->est_pool=0;
    $ligne->referentielc2i=-1; //rev 944
    $ligne->nbquestions=0;     // rev 944
    $ligne->affiche_chrono=0;     // rev 957  notice php
    $ligne->template_resultat='';     // rev 957  notice php
    $ligne->tags='';
    $ligne->subnet=''; 



    $ligne->ts_datedebut=mktime( $CFG->examen_heure_debut_defaut, $CFG->examen_minute_debut_defaut,0, (int) date('m'), (int) date('d') + $CFG->examen_date_defaut, (int) date('Y'));

    $ligne->ts_datefin=$ligne->ts_datedebut+3600*$CFG->examen_duree_defaut;  // 1 heure par d�faut

    $ligne->auteur=get_fullname($USER->id_user);
	$ligne->auteur_mail=get_mail($USER->id_user);

    if (($USER->type_plateforme == 'certification' ||  $CFG->pool_en_positionnement )&& $CFG->autoriser_pool) {
        $tpl->newBlock("pool");
        $tpl->setChecked(false, "checked_pool_oui","checked_pool_non");
        $tpl->assign("nb_q_pool", $CFG-> pool_nb_questions_defaut);
            $tpl->assign("pool_nb_groupes",$CFG->pool_nb_groupes_defaut);

    }

}



$tpl->gotoBlock("_ROOT"); // important si il y a eu un block pool cr��
$tpl->assignObjet($ligne);	// un bon coup ... (donne des warnings inconnus en mode debug_templates ;-)))
$et=get_etablissement($ligne->id_etab,false);
if ($et) {
     $tpl->assign("etablissement",$et->nom_etab);
     print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("{$CFG->chemin}/codes/acces/etablissement/fiche.php?idq=".$et->id_etab));
}


//dtes dans les input fields et dans le calendrier
$dd=strftime(traduction("jscalendar_if"),$ligne->ts_datedebut);
$df=strftime(traduction("jscalendar_if"),$ligne->ts_datefin);

$tpl->assign("debut_examen.date_debut",strftime(traduction("jscalendar_if"),$ligne->ts_datedebut));
$tpl->assign("fin_examen.date_fin",strftime(traduction("jscalendar_if"),$ligne->ts_datefin));


$tpl->setChecked($ligne->ordre_q=="fixe","ch_oqf","ch_oqa");
$tpl->setChecked($ligne->ordre_r=="fixe","ch_orf","ch_ora");

$tpl->assign("date_creation",userdate($ligne->ts_datecreation,'strftimedatetime'));
$tpl->assign("date_modification",userdate($ligne->ts_datecreation,'strftimedatetime'));
$tpl->assign("auteur",cree_lien_mailto($ligne->auteur_mail,$ligne->auteur));

// rev 986  avant les newBlocks ci-dessous
if ($CFG->restrictions_ip) {
    $tpl->newBlock('restrictions_ip');
    $plages=get_plages_ip_declarees($USER->id_etab_perso);
    if (count($plages))
        print_multiselect_plages_ips ($tpl,'multi_select_ips','subnet',$ligne->subnet);
    else
        $tpl->assign('multi_select_ips',traduction('info_pas_de_plages_ip_declarees'));
}

if ($ligne->pool_pere==0) { //attention
	$tpl->newBlock("type_tirage");
	$tpl->setChecked($ligne->type_tirage==EXAMEN_TIRAGE_MANUEL,"checked_manuel");
    $tpl->setChecked($ligne->type_tirage==EXAMEN_TIRAGE_ALEATOIRE,"checked_aleatoire"); //rev 867 attention au 3eme cas !
} else {
	$tpl->newBlock("tirage_pool");
}



//rev 945

if (!$CFG->pas_de_timer) {
    $tpl->newBlock("affiche_chrono");
    if ($ligne->affiche_chrono == 1) {
            $tpl->assign("ch_chronoo", " checked=\"checked\"");
            $tpl->assign("ch_chronon", "");
        } else {
            $tpl->assign("ch_chronon", " checked=\"checked\"");
            $tpl->assign("ch_chronoo", "");
        }

}




if ($USER->type_plateforme == "certification") {
    //$tpl->newBlock("n_correction");
    //$tpl->newBlock("n_envoi_resultat");
    $tpl->newBlock("seuil_fixe");
    $tpl->assign ("resultat_mini",0);  // en certification 

      // rev 985 circulaire autorise des certification par domaine
        if ($CFG->autoriser_qcm_par_domaine_en_certification) {
            //rev 980 un membre d'un pool ne peut �tre modifi� ainsi
            if (!$ligne->pool_pere) {
                $tpl->newBlock("nbquestions");
                $tpl->assign("nbquestions",$ligne->nbquestions?$ligne->nbquestions:config_nb_aleatoire($ide));


                $tpl->newBlock("domaines");
                print_multiselect_referentiels ($tpl,'multi_select_refs','referentielc2i',$ligne->referentielc2i);
            }
        }



} else
    if ($USER->type_plateforme == "positionnement") {

        //rev 980 un membre d'un pool ne peut �tre anonyme !!!
        if (!$ligne->pool_pere) {

            $tpl->newBlock("tirage3");
            $tpl->setChecked($ligne->type_tirage==EXAMEN_TIRAGE_PASSAGE,"checked_passage");


            if ($CFG->examen_anonyme && $ligne->pool_pere ==0){
                if (is_admin(false,$CFG->universite_serveur)) { // rev 809 seul un admin peut
                    $tpl->newBlock("examen_anonyme");
                    $tpl->setChecked($ligne->anonyme,"checked_anonyme");
                }
            }
        }
        $tpl->newBlock("correction");


        // correction visible ou non apr�s passage de l'examen
        if ($ligne->correction == 1) {
            $tpl->assign("ch_coro", " checked=\"checked\"");
            $tpl->assign("ch_corn", "");

        } else {
            $tpl->assign("ch_corn", " checked=\"checked\"");
            $tpl->assign("ch_coro", "");
        }

        $tpl->newBlock("envoi_resultat");


        // correction visible ou non apr�s passage de l'examen
        if ($ligne->envoi_resultat == 1) {
            $tpl->assign("ch_envoio", " checked=\"checked\"");
            $tpl->assign("ch_envoin", "");
        } else {
            $tpl->assign("ch_envoin", " checked=\"checked\"");
            $tpl->assign("ch_envoio", "");
        }



        //rev 980 un membre d'un pool doit avoir le meme resultat_mini que son p�re
        // et la valeur_de resultat_mini est obligatoire
        if (!$ligne->pool_pere) {
            $tpl->newBlock("seuil");
            // revoir avec un print_select_from_table
            for ($m = 10; $m <= 100; $m += 10) {
                $tpl->newBlock("option_mini");
                $tpl->assign("val", $m);
                $tpl->assign("aff", $m . " %");
                $tpl->setSelected ($ligne->resultat_mini == $m);
            }
        } else {
            $tpl->newBlock("seuil_fixe"); // revision du 14/11/2012 cf Univ. Reims 
            $tpl->assign ("resultat_mini",$ligne->resultat_mini);  // en certification
            
        }

        // rev 944
        if ($CFG->autoriser_qcm_par_domaine_en_positionnement) {
            //rev 980 un membre d'un pool ne peut �tre modifi� ainsi
            if (!$ligne->pool_pere) {
                $tpl->newBlock("nbquestions");
                $tpl->assign("nbquestions",$ligne->nbquestions?$ligne->nbquestions:config_nb_aleatoire($ide));


                $tpl->newBlock("domaines");
                print_multiselect_referentiels ($tpl,'multi_select_refs','referentielc2i',$ligne->referentielc2i);
            }
        }


    }

$tpl->newBlock('limitedureepassage');
$tpl->assign("ts_dureelimitepassage",$ligne->ts_dureelimitepassage);

if ($CFG->activer_tags_examen) {
    $tpl->newBlock('tags');
    $tpl->assign('tags',$ligne->tags);
}

$tpl->gotoBlock("_ROOT");

if ($dupliquer)
    print_bouton_annuler_duplication($tpl);
else
    print_bouton_annuler($tpl);
if ($idq=="-1")
    print_bouton_reset($tpl,"validator.reset();");
else
    $tpl->assign("bouton_reset","");

$tpl->printToScreen(); //affichage
?>
