<?php


/**
 * @author Patrick Pollet Vincent Bellenger
 * @version $Id: exportQCMDirect.php 1120 2010-09-09 07:24:40Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
 *	export qcm direct avec redirection vers send_csv
 * version 1.5 sans template car template poser faut sauter les sauts de lignes ...
 * format emis avec le r�ferentiel entre parentheses si demand�
 * la/les bonne(s)  r�ponses sont marqu�es par un "tab"V
 *
1 -  (B1.1) aaaQuelle est la fonction g�n�rale du menu Fichier dans une fen�tre informatique ?
A - Acc�s � l'aide de l'application
B - Acc�s et gestion de documents informatiques	V
C - Modification de contenu avec des fonctions, par exemple, de copier/coller
D - Organisation des fen�tres cr��es par une application
E - Acc�s aux informations relatives � l'application et � son utilisation
saut de ligne entre les questions
voir pour l'import des questions a ce format le script codes/questions/import_questions.php '
*/
////////////////////////////////

/*
* Pour la description des diff�rentes m�thodes de la classe TemplatePower,
* il faut se ref�rer � http://templatepower.codocad.com/
*/
//******** Pour chaque page $chemin repr�sente le path(chemin) de script dans le site (� la racine)
//******** ---------------- $chemin_commun repr�sente le path des utilitaires dont on aura besoin
//******** ---------------- $chemin_images repr�sente le path des images
$chemin = '../..';

require_once ($chemin. "/commun/c2i_params.php"); //fichier de param�tres

require_login("P"); //PP

$idq = required_param("idq", PARAM_INT, "");
$ide = required_param("ide", PARAM_INT, "");
$export =optional_param('export', 'rien_a_faire_la', PARAM_ALPHANUM); // pas d'erreur fatale !'
$montre_ref=optional_param("montre_ref",0,PARAM_INT);
$melange_questions=optional_param("melange_questions",0,PARAM_INT); //force ou non le melange
$melange_reponses=optional_param("melange_reponses",0,PARAM_INT);   //normalment non. tout le monde a la meme sujet !

v_d_o_d("el"); //PP

$ligne = get_examen($idq, $ide);

/* pas besoin de template pour ca ,surtout que template power enleve les sauts de lignes !!
 * et casse la mise en forme
 */
$melange_questions= $melange_questions && $ligne->ordre_q !='fixe';

$questions=get_questions($idq,$ide,$melange_questions,false);

$num_q = 0;

$filename=$export."_".$ide."_".$idq.".txt";
$fp = fopen($CFG->chemin_ressources."/csv/".$filename, "w");

foreach ($questions as $ligne_q) {
	$num_q++;
	if ($montre_ref)
		  $referentiel = "(".trim($ligne_q->referentielc2i) . "." . trim($ligne_q->alinea).")";
    else
		$referentiel="";
    // rev 986 avril 2013 enleve saut de lignes si present dans le texte
    $ligne="$num_q - $referentiel " .trim(clean($ligne_q->titre,strlen($ligne_q->titre)));
	fputs($fp,$ligne."\n");
	// r�ponses
	$melange_reponses=$melange_reponses && $ligne->ordre_r !='fixe';
	$reps=get_reponses($ligne_q->id,$ligne_q->id_etab,$melange_reponses,false );
	// rev 963 d'apr�s le CDC les r�ponses doivent �tre num�rot�s A,B,C ... et pas 1,2,3 ...'
	//$num_r = 0;

	$num_r=ord("A")-1;
	foreach($reps as $ligne_r) {
		$num_r++;
		//$ligne ="$num_r - " .trim($ligne_r->reponse);
		//$ligne= chr($num_r)." - ".trim($ligne_r->reponse);
		// rev 986 avril 2013 enleve saut de lignes si present dans le texte
		$ligne= chr($num_r)." - ".trim(clean($ligne_r->reponse,strlen($ligne_r->reponse)));
		
		if ($ligne_r->bonne == "OUI")
			$ligne.= chr(9) . "V";
		fputs($fp,$ligne."\n");
	}
	fputs($fp,"\n");  //ligne vide
}


fclose($fp);
//TODO pas la peine de faire un fichier. Bricoler les entetes et imprimer ...
// envoi du fichier avec une entete mime adapt�e et donc t�l�chargement
header("Location:".$CFG->chemin_commun."/send_csv.php?idf=".$filename);

?>
