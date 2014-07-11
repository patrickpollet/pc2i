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
<form name="monform" id="monform" action="rapport.php" method="post">

<input type="hidden" name="idq" id="idq" value="{idq}">
<input type="hidden" name="delete_question" id="delete_question" value="0">
<input type="hidden" name="envoi_mail" id="envoi_mail" value="0">



 <h1>{nom_examen}</h1>
<table id="liste">
 


 <!-- START BLOCK : flq_questions_ligne -->

  <!-- START BLOCK : flq_questions_notion_libelle -->
 <tr style="background-color:#DFDFDF;"><th >{libelle_notion}</th></tr>
 <!-- END BLOCK : flq_questions_notion_libelle -->
 <!-- START BLOCK : flq_questions_notion -->
 <tr style="background-color:#DFDFDF;"><th >{libelle_lien}</th><th>{libelle_interets}</th><th>{libelle_moderation}</th></tr>
 <!-- END BLOCK : flq_questions_notion -->
 <tr><td>
 {flq_question_libelle}<span class="commentaire1"> {nom} {prenom} {date_creation}</span>
 </td>
  <td>
 {flq_question_int} {flq_abrev_etud}
 </td>
  <td>
  
  <a href="#" onClick="supprQuestionFlq({idq},{flq_id_question},'{flq_avertissement_suppr}')">
 {form_flq_suppr}</a>
 </td>
 </tr>

 <!-- END BLOCK : flq_questions_ligne -->

 </table> 
 <input type="button"  class="saisie_bouton" value="{bouton_envoie_mail_flq}" onClick="envoi_mail_flq('{idq}')">
 </form>
  <h4>{flq_envoye_a} {auteur} {le} {date_flq} {a} {heure_flq}<h4>
<!-- END BLOCK : flq -->
<!-- INCLUDE BLOCK : multip -->



EOL;



if(!empty($_GET['idq']) && empty($idq) ){
		$idq=$_GET['idq'];
	
		
	}else{
			
	
		$idq=$_POST['idq'];
	
		
		
	}	
if(!empty($_POST['delete_question'])){
	delete_flq_question($_POST['delete_question']);
	
}
if(!empty($_GET['nom_examen']) && empty($nom_examen) ){
	$nom_examen=$_GET['nom_examen'];
}
$options=array (
	"liste"=>1,
	"corps_byvar"=>$liste
);
	


$tpl->prepare($chemin,$options);
$tpl->gotoBlock("_ROOT");


$tpl->newBlock("flq");
$tpl->assign("idq",$idq);
$tpl->assign("nom_examen",affiche_texte($nom_examen));
$o_auteur=get_auteur_flq($idq);
$tpl->assign("auteur",$o_auteur[0]->auteur);
$o_date_flq=get_date_flq($idq);

$date = explode('-', $o_date_flq[0]->date);
$date = array_reverse($date);
$date = implode('/', $date);
$tpl->assign("date_flq",$date);
$tpl->assign("heure_flq",$o_date_flq[0]->heure);


 
	$a_rapport=get_flq_questions_from_cours_exam($idq);
	
	$id_notion_prec=-1;
	$libelle_lien_prec=-1;
	foreach($a_rapport as $rapport){
			
		$tpl->newBlock("flq_questions_ligne");
		$tpl->assign("flq_question_int",$rapport->nb_int);
		$tpl->assign("flq_question_libelle",$rapport->libelle);

		$tpl->assign("flq_id_question",$rapport->id);
		$tpl->assign("idq",$idq);
		$a_nom_prenom=get_nom_prenom_from_idquestion($rapport->id);
		
		$nom=$a_nom_prenom[0]->nom;
		$prenom=$a_nom_prenom[0]->prenom;
		$date_creation=$rapport->date_creation;
		$tpl->assign("nom",$nom);
		$tpl->assign("prenom",$prenom);
		$tpl->assign("date_creation",$date_creation);
		
		$a_result=get_lien_libelle_from_question($rapport->id);
		//print_r($a_result);
		$id_lien=$a_result[0]->id_lien;	 
		$lien_libelle=$a_result[0]->origine;
		$id_notion=$rapport->id_notion;
		
		$notion=get_records("notions","referentielc2i !='' and id_notion=$id_notion","","",0,1);
		$notion_libelle=$notion[0]->libelle;
		
		if($id_notion!=$id_notion_prec){
			$tpl->newBlock("flq_questions_notion_libelle");
			$tpl->assign("libelle_notion",$notion_libelle);
			
		}
		
		$id_notion_prec=$id_notion;
		if($id_lien!=$id_lien_prec){
			$tpl->newBlock("flq_questions_notion");
			$tpl->assign("libelle_lien",$lien_libelle);
			
		}
		$id_lien_prec=$id_lien;
		
		
		
	   

	}   



$tpl->gotoBlock("_ROOT");



print_menu_haut($tpl,"e");


$tpl->printToScreen(); //affichage


if(!empty($_POST['envoi_mail'])){
	
	envoi_par_mail_flq($idq);
	
}


?>







