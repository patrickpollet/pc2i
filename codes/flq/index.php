<?php
$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de paramètres
require_once ($chemin_commun . "/lib_flq.php");
require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

//$tpl = new C2IPrincipale(); //créer une instance
$tpl = new C2IPrincipale();

//inclure d'autre block de templates

$liste=<<<EOL

<!-- INCLUDE BLOCK : multip_haut -->


<!-- START BLOCK : flq -->
<form name="monform" id="monform" action="index.php" method="post">
<input type="hidden" name="show_input_question" id="show_input_question" value="0">

<table class="fiche" style="width:100%;" >
<tbody>
  <tr>
    <th>{form_flq_discipline}</th>
    <td>
    {select_flq_disc}
      </td>
 </tr>

<!-- START BLOCK : flq_cours -->
  <tr>
    <th>{form_flq_cours}</th>
    <td>
    {select_flq_cours}
      </td>
 </tr>

<!-- END BLOCK : flq_cours -->
 
 </tbody>
 </table>
 
 <!-- START BLOCK : flq_questions -->
<input type="hidden" name="id_question" id="id_question" value="0">
<input type="hidden" name="idq" id="idq" value="{idq}">
<table class="fiche" style="margin-top:2px;width:100%;table-layout:fixed;">

<tr>
<th style="width:60%;">{form_flq_question}</th>
<th style="width:10%;">{form_flq_etu_int}</th>
<th style="width:30%;">{form_flq_je_int}</th>

</tr>
 
 <!-- START BLOCK : flq_questions_ligne -->
 
 <tr><td style="width:60%;" ><p>
 {flq_question}
 </p>
 </td>
  <td style="width:10%;text-align:center;" >
 {flq_question_int}
 </td>
  <td style="width:30%;">
  
  <a href="#" onClick="validFlqForm('{flq_id_question}')">
 {form_flq_je_int}</a>
 </td>
 </tr>
 <!-- END BLOCK : flq_questions_ligne -->
 </table> 
 <table class="fiche" style="margin-top:2px;width:100%;">
  <!-- START BLOCK : flq_question_add -->

 <tr>
  <td><a href="#" onClick="addFlqQuestionInput({var_idq})">{form_flq_add_question}</a></td>
 </tr>
  <!-- END BLOCK : flq_question_add -->
  
    <!-- START BLOCK : flq_question_add_input -->

 <tr>
  <td style="align:center;">{question} </td>
  <td><textarea  name="input_question" cols="40" rows="6"></textarea></td>
  <td><input type="submit"  name="valider_flq_add_question" value="valider"></td>
 </tr>
  <!-- END BLOCK : flq_question_add_input -->

 </table> 

 <!-- END BLOCK : flq_questions -->
</form>
<!-- END BLOCK : flq -->
<!-- INCLUDE BLOCK : multip -->



EOL;
if(!empty($_GET['idq']))
	$idq=$_GET['idq'];
if(!empty($_POST['idq']))
	$idq=$_POST['idq'];
	
	
$options=array (
	"liste"=>1,
	"corps_byvar"=>$liste
);
	


$tpl->prepare($chemin,$options);
$tpl->gotoBlock("_ROOT");

$a_discipline=get_flq_discipline();
$tpl->newBlock("flq");


if (!empty($_POST['valider_flq_add_question']) && !empty($_POST['flq_cours']) && !empty($_POST['input_question'])){
	
	add_flq_question($_POST['flq_cours'],$_POST['input_question'],$_POST['idq'],$_POST['flq_disc'],$USER->id_user);
}
if(!empty($_POST['id_question'])){
	
	increment_flq_question($_POST['id_question'],$USER->id_user);
}



print_select_from_table($tpl,"select_flq_disc",$a_discipline,
                "flq_disc",
                "saisie",
                "onChange=valid_select_flq('$idq');style='width:200'",
                "id_notion","libelle","choisissez",$_POST['flq_disc']);

	$tpl->newBlock("flq_cours");
	
	if(!empty($_POST['flq_disc'])){
	$a_cours=get_flq_cours_from_disc($_POST['flq_disc']);
	}else{
		$a_cours=array();
	}
	print_select_from_table($tpl,"select_flq_cours",$a_cours,
                "flq_cours",
                "saisie",
                "onChange=valid_select_flq('$idq');style='width:200'",
                "id_lien","origine","choisissez",$_POST['flq_cours']);




	$tpl->newBlock("flq_questions"); 
	if(!empty($_GET['idq'])){
		$idq=$_GET['idq'];
		$tpl->assign("idq",$idq);
		
	}else{
		$idq=$_POST['idq'];
		$tpl->assign("idq",$idq);
		
	}
	if(!empty($_POST['flq_cours'])){
		$a_questions=get_flq_questions_from_cours($idq,$_POST['flq_cours'],$_POST['flq_disc']);
	}elseif(empty($_POST['flq_cours']) && empty($_POST['flq_disc'])){
		$a_questions=get_flq_questions_from_cours($idq,'','');
		
	}else{
		$a_questions=get_flq_questions_from_cours($idq,$_POST['flq_cours'],'');
	} 
		
	foreach($a_questions as $question){
		$tpl->newBlock("flq_questions_ligne");
		$tpl->assign("flq_question_int",$question->nb_int);
		$tpl->assign("flq_question",$question->libelle);
		
		$tpl->assign("flq_id_question",$question->id);
		
		
	}   
if(!empty($_POST['flq_cours'])){
	$tpl->newBlock("flq_question_add");
}
	$tpl->assign("var_idq",$idq);
if (!empty($_POST['show_input_question'])){
	$tpl->newBlock("flq_question_add_input");
}

$tpl->gotoBlock("_ROOT");



print_menu_haut($tpl,"qcm");


$tpl->printToScreen(); //affichage
?>
