<?php


/**
 * @author Patrick Pollet
 * @version $Id: export_autoMC.php 1108 2010-07-20 14:55:50Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
 *	export un zip contenant deux fichiers
 *un au format Latex
 * pour traitemnt par la logiciel libre automultiple choice AMC
 * http://home.gna.org/auto-qcm/
 *
 \begin{questionmult}{pref}
  Parmi les villes suivantes, lesquelles sont des pr�fectures~?
  \begin{reponses}
    \bonne{Poitiers}
    \mauvaise{Sainte-Menehould}
    \bonne{Avignon}
  \end{reponses}
\end{questionmult}

et un au format csv contenant la liste des inscrits

ATTENTION dans les textes EN LIGNE
certains antislashes doivent �tre doubl�s car interpr�t�s par PHP
voir http://php.net/manual/fr/regexp.reference.backslash.php
a ce jour  \n \v \f  \r  comme dans \noindent    \fill \vspace  \restitueGroupe

rev 975 :
   le nombre de questions dans options.xml ne doit pas etre 0 
   bar�me PLUS bareme{e=0,v=0,p=-1,b=1,m=-0.25}
          mais \bareme{b=0,m=0,d=(NBC+NMC==N || NBC+NMC==0 ? 0 : NBC/NB-NMC/NM)}  
          
   emission en UTF8 ( Dimanche 20/006/2010)  
rev v2 
   plus de conversion en utf8 puisque nous y sommes         

*/

//******** Pour chaque page $chemin repr�sente le path(chemin) de script dans le site (� la racine)
//******** ---------------- $chemin_commun repr�sente le path des utilitaires dont on aura besoin
//******** ---------------- $chemin_images repr�sente le path des images
$chemin = '../..';

require_once ($chemin. "/commun/c2i_params.php"); //fichier de param�tres

require_once($CFG->chemin_commun.'/lib_csv.php');

require_login("P"); //PP

$idq = required_param("idq", PARAM_INT, "");
$ide = required_param("ide", PARAM_INT, "");
$export =optional_param('export', 'AMC', PARAM_ALPHANUM); // entete du nom de fichier emis
$type =optional_param('type', 1, PARAM_ALPHANUM); // 1= normal  2 avec grille de r�ponses s�par�e

v_d_o_d("el"); //PP

$dir = $CFG->chemin_ressources.'/tmp/amc/';
cree_dossier_si_absent($dir);
$dir .= time();
cree_dossier_si_absent($dir);
cree_dossier_si_absent($dir.'/images');


$ligne = get_examen($idq, $ide);

// c'est AMC qui les r�ordonnera pas nous !'
$questions=get_questions($idq,$ide,false,'id_etab,id');

$inscrits=get_inscrits($idq,$ide,'nom,prenom');


// forcer conversion en UTF8  rev 975 bis
$CFG->AMC_conversion_utf8= !$CFG->unicodedb;

$filename=$export."_".$ide."_".$idq;  // nom des fichiers sans extension

/**
 * rev 973 passage par la classe CsvExporter
 */

// ligne suivantes les noms des attributs dans $ligne dans cet ordre
$ligne_csv=array("nom","prenom","numetudiant","email");
//conversion num�rique du score
$ligne_cvt=array(false,   false, false, false); //conversion point virgule pour OO

// rev 975 compter les inscrits ayant un num�ro d'�tudiant codable en AMC (num�ric)'
$nbinscrits=0;
$mycsv=new CsvExporter($filename.'.csv',array(),$ligne_csv,$ligne_cvt,$CFG->AMC_conversion_utf8);
$mycsv->add_comment("# ".traduction("liste_etudiants_inscrits") . nom_complet_examen($ligne) );
//rev 1020 nom de la colonne contenant le N) �tudiant parametrable et identique � celui emis par la commande AMCcode du source Latex
$mycsv->add_comment("nom;prenom;{$CFG->AMC_nom_colonne_numetudiant};email");
foreach($inscrits as $inscrit) {
    //bug AMC 0.224 qui refuse que la derniere colonne soit vide ;-(
    //if (empty($inscrit->email)) $inscrit->email="pas de mail";
    if (! is_numeric($inscrit->numetudiant))
    	continue; 
    // pour la reconnaissance automatique marche avec des N� etudiants 
    $inscrit->numetudiant=str_pad($inscrit->numetudiant,$CFG->AMC_taille_numetudiant,'0',STR_PAD_LEFT);
    $mycsv->add_ligne($inscrit);
	$nbinscrits++;
}

$filelocation=$mycsv->close();
copy($filelocation,$dir."/".$filename.'.csv');
unlink ($filelocation);



$fp = fopen($dir."/".$filename.".tex", "w");

$groupe="{".$CFG->c2i."}";

fputs ($fp,get_entete(count($questions),$type));

foreach ($questions as $ligne_q) {
	fputs($fp,get_latex_question($ligne,$ligne_q,$groupe,$dir));
}

// NOMBRE D'EXEMPLAIRES= nb inscrits  plus un petit pourcentage en plus
$nbcopies=ceil($nbinscrits*(100+$CFG->AMC_extra_copies)/100);
fputs($fp,get_entete_exemplaire($nbcopies));

if ($type==1)
   fputs($fp,get_entete_copie(1));

//il suffira donc de le d�commenter dans le fichier Latex si on change d'avis
if ($ligne->ordre_q !='fixe')
    fputs($fp,"\melangegroupe".$groupe."\n");
else
    fputs($fp,"%%\melangegroupe".$groupe."\n");

fputs($fp,"\\restituegroupe".$groupe." \n\n");

if ($type==2) {   //sujet et r�ponses separ�s
    fputs ($fp,"\clearpage \n \AMCdebutFormulaire\n\n");
    fputs($fp,get_entete_copie(2));
}

fputs($fp,get_epilogue($type));

fclose($fp);





//options
$fp = fopen($dir."/options.xml", "w");
fputs($fp,get_options($ligne,count($questions),$nbcopies,$type));
fclose($fp);


zip_dossier($dir,'.',"../".$filename.".zip");
supprimer_dossier($dir); // cleanup the temp directory


//TODO pas la peine de faire un fichier. Bricoler les entetes et imprimer ...
// envoi du fichier avec une entete mime adapt�e et donc t�l�chargement
header("Location:".$CFG->chemin_commun."/send_csv.php?idf=".$filename.".zip&dir=tmp/amc");



/**
 * construit une entr�e Latex pour la question
 * @param ligne_e le record BD de l'examen
 * @param $ligne_q  le record BD de la question
 * @param $groupe nom du groupe auquel appartient cette question
 * @param $dir    le dossier tempo ou on construit le zip
 */

function get_latex_question ($ligne_e,$ligne_q,$groupe,$dir) {

	global $CFG;

	//bar�me de base pour chaque question
	$baremeBase="e=0,v=0";  // en cas d'erreur ou aucune case coch�e' 
	// ce sont les valeurs par d�faut
	// en plus amc 0.296 a un probl�me avec un bar�me trop long ...  
	$baremeBase=''; 
	if ($CFG->pas_de_scores_negatifs) // note plancher
		$baremeBase.="p=0";
	else
		$baremeBase.="p=-1";

	$multiColBegin=$CFG->AMC_multicol?"\begin{multicols}{".$CFG->AMC_multicol."}\AMCBoxedAnswers\n":"";
	$multiColEnd=$CFG->AMC_multicol?"\end{multicols}\n":"";

    //option a ajouter a chaque r�ponse si on ne veut pas les m�langer
    $ordreReponses= $ligne_e->ordre_r !='fixe'?"":"[o]";


	$images=array('jpg','jpeg' ,'png');  //gif non reconnu par latex

	$qid="{".$ligne_q->id_etab."_".$ligne_q->id."}";

	$docText="";  //vides si pas d'images'
	if ($CFG->AMC_inclure_images) {
		$docs=get_documents($ligne_q->id,$ligne_q->id_etab, false);

		if (!empty($docs)){
			foreach($docs as $doc) {
				//print_r($doc); print('<br/>');
				if (in_array($doc->extension,$images)) {
					$fic=get_document_location($ligne_q->id,$ligne_q->id_etab).'/'.$doc->id_doc.'.'.$doc->extension;
					if (file_exists($fic)) {
						$nomFic=$ligne_q->id_etab.'_'.$ligne_q->id.'_'.$doc->id_doc.'.'.$doc->extension;
						copier_element($fic,$dir.'/images/'.$nomFic);
						//print ($nomFic.'<br/>');
						$docText.=get_latex_figure('images/'.$nomFic);

					}
				}
			}

		}
	}

	$qtext="{".fix_latex_reserves(trim($ligne_q->titre))."}";
	//non m�lang�es c'est AMC qui m�lange '
	$reps=get_reponses($ligne_q->id,$ligne_q->id_etab,false,false );


    //calcul du bar�me de cette question
	/**
    FAUX ceci donne pour une question � 5 rep donc une bonne bareme{e=0,v=0,p=-1,b=1,m=-0.25}
    et est compris par AMC comme 
        bonnes r�ponses= bonne coch�e OU mauvaise non coch�e 
        mauvaises r�ponses = bonne pas coch�e OU mauvaise coch�e 
        donc si il r�ponds bien il a 5 points et pas 1 

	$B=0;  //nombre de bonnes r�ponse
	$M=0;  // nombre de mauvaises r�ponse;
	foreach ($reps as $reponse) {
		if ($reponse->bonne=='OUI') $B++;
		else $M++;
	}

	
	if ($B)
		$BV=1/$B; //fraction de 1 pt par Bonne
	else
		$BV=0;
	if ($M)
		$MV=-1/$M; //p�nalit� par mauvaise
	else
		$MV=-$BV;

	$bareme="{".$baremeBase.",b=$BV,m=$MV"."}";
	**/
	
	// rev 975 bar�me minist�riel 
	$bareme='{'.$baremeBase.',b=0,m=0,d=(NBC+NMC==N || NBC+NMC==0 ? 0 : NBC/NB-NMC/NM)'.'}'; 
	
	$reponses="";
	foreach ($reps as $rep) {
		if (  $rep->bonne=="OUI")
			$reponses.="    \bonne{".fix_latex_reserves(trim($rep->reponse))."}\n";
		else
			$reponses.="    \mauvaise{".fix_latex_reserves(trim($rep->reponse))."}\n";
	}



	$modele=<<<EOM
\element$groupe{
    \begin{questionmult}$qid\bareme$bareme
	$qtext
	$docText
	$multiColBegin
	\begin{reponses}$ordreReponses
	   $reponses
	\end{reponses}
	$multiColEnd
	\end{questionmult}
}

EOM;

	return $modele;

}



/**
 * contruit entete du document Latex
 */

function get_entete ($nbquestions,$type) {
	global $CFG;

	$c2i="{".$CFG->c2i."}";
	$nbquestions="{".$nbquestions."}";

    $extras="";
    if ($type==2) $extras=',ensemble'; // option ensemble pour grille de r�ponses s�par�es


    // num�rotation des r�ponses en cas de grille s�par�e (d�faut AMC=lettres)
    if ($CFG->numerotation_reponses==1) // on veut des chiffres
        $extras.=',chiffres';

   // $enc=$CFG->unicodedb?'utf8':'latin1';
	$enc='utf8';
	

$ret=<<<EOT

%%% TODO generer ici la date de generation et la version de la PF

\documentclass{article}
\usepackage{etex}
\\reserveinserts{28}

%% revision 0.222 AMC surtout ne pas charger ce paquet
%% pour ne pas perturber le document de calage !
%% AMC le chargera automatiquement sur une directive includeGraphics uniquement dans le sujet et le corrige
%%\usepackage{graphicx}
%%\DeclareGraphicsExtensions{.png,.gif,.jpg,.jpeg}


\usepackage[$enc]{inputenc}
\usepackage[T1]{fontenc}



\usepackage[bloc$extras]{automultiplechoice}
%grille de reponses avec deux colonnes
\usepackage{multicol}

%vire le "trefle"
\def\multiSymbole{ }


\begin{document}

\AMCrandomseed{1237893}



%%% preparation du groupe unique des questions
%%% qui pourront enuite etre melanges ou pas

\\nouveaugroupe$c2i $nbquestions

EOT;
	return $ret;

}


/**
 * construit l'entete d'un exemplaire
 */
function get_entete_exemplaire ($nbcopies) {

	global $CFG;
	$nbcopies="{".$nbcopies."}";

$ret=<<<EOT
%%% fabrication des copies

\exemplaire$nbcopies{

EOT;
    return $ret;
}

/**
 * construit l'entete de chaque copie
 *
 */
function get_entete_copie ($type) {
    global $CFG;
	$texte1=traduction('AMC_entete_grille_reponses');
	$texte2=traduction('AMC_info_numetudiant');
	$texte3=traduction('AMC_info_nom_prenom');
	
    $titre=$type==2? '{\large\bf'.$texte1.' }':''; //titre de la feuille de r�ponses s�par�e
    //rev 1023 important de coder ici les accolades et par dans le texte en ligne (php les vire si elles encadrent une variable!)
    $nbCols='{'.$CFG->AMC_taille_numetudiant.'}';
    $idColNE='{'.$CFG->AMC_nom_colonne_numetudiant.'}';  // nom de la colonne a rechercher dans le CSV en retour

    $ret=<<<EOT
%%% debut de l entete des copies :

%%% TODO titre de l examen (posit ou certif) et date

%%% a vous de personnaliser cette zone
%%% (logo, mise en pages, marges, polices ...)
%%% numerotation des pages
%%% fin de l en-tete

$titre

%%%  debut de la zone d identification du candidat  AMC rev 0.202 18 janvier 2010

\\noindent\AMCcode{$idColNE}{$nbCols}\hspace*{\\fill}
\begin{minipage}{.5\linewidth}
$\longleftarrow{}$  $texte2

\\vspace{3ex}

\champnom{\\fbox{
    \begin{minipage}{.9\linewidth}
      $texte3

      \\vspace*{.5cm}\dotfill
      \\vspace*{1mm}
    \end{minipage}
  }}
\end{minipage}


%%%  fin de la zone d identification du candidat

EOT;

	return $ret;
}

/**
 * emet ou non la commande d'impression de la grille de r�ponses
 * puis toujours un saut de page
 */

function get_epilogue($type) {
    global $CFG;

    $cp=$CFG->AMC_recto_verso?'\cleardoublepage':'\clearpage';
    $formulaire=<<<EOT
   \begin{multicols}{2}
       \\formulaire
    \end{multicols}

EOT;

    $emetFormulaire=$type==2?$formulaire:"";

	$ret=<<<EOT
    $emetFormulaire
	$cp

}

\end{document}

EOT;
	return $ret;

}


/**
 * conversion de tous les caractères qui pourraient perturber Latex
 */
function fix_latex_reserves($str) {
	global $CFG;
	// remplacer les caract�res windows cp-1252 qui apparaissent de temps a autre ...
	$str=fix_special_chars($str);

	$res1=array('#','%','&','~','^','{','}','_');
	// ne marche pas pour $

	foreach ($res1 as $c) {
		$str=str_replace($c,'\\'.$c,$str);
	}

	$res2=array('$'=>'\\$',
				'>'=>'$>$',
                '<'=>'$<$',
            );
	foreach ($res2  as $c=>$rep)
		$str=str_replace($c,$rep,$str);
	return $str;

}

/**
 * emet la directive necessaire � l'insertion d'une figure
 */
function get_latex_figure($chemin){
	$chemin='{'.$chemin.'}';
	$ret=<<<EOT
	\includegraphics$chemin
EOT;
	return $ret;
}



/**
 * genere les options pour AMC
 */
function get_options ($examen,$nbquestions,$nbcopies,$type) {
    global $CFG;
    /*
     * Cela semble effectivement une bonne id�e (mettre �ventuellement tout de m�me une valeur 0.5 
     * dans le cas o� on demande une feuille s�par�e pour les r�sultats, car alors des lettres sont 
     * �crites dans les cases pour les rep�rer, de sorte que la bonne consigne est de remplir totalement la case).
     */
    
    $seuil=$type==1?0.15:0.5;

    $cle=$examen->id_etab.'_'.$examen->id_examen;
    $ret=<<<EOT
<?xml version="1.0" encoding="{$CFG->encodage}" standalone="yes"?>
<projetAMC>
  <assoc_code>{$CFG->AMC_nom_colonne_numetudiant}</assoc_code>
  <code_examen>$cle</code_examen>
  <docs>sujet_{$cle}.pdf</docs>
  <docs>corrige_{$cle}.pdf</docs>
  <docs>calage_{$cle}.pdf</docs>
  <export_csv_separateur>TAB</export_csv_separateur>
  <format_export>CSV</format_export>
  <liste_key>{$CFG->AMC_nom_colonne_numetudiant}</liste_key>
  <listeetudiants>%PROJET/AMC_{$cle}.csv</listeetudiants>
  <nom_examen>{$examen->nom_examen}</nom_examen>
  <nombre_copies>{$nbcopies}</nombre_copies>
  <note_max>{$nbquestions}</note_max>
  <seuil>$seuil</seuil>
  <texsrc>%PROJET/AMC_{$cle}.tex</texsrc>
  <!-- ajout PP extrait de ~/.AMC.xml (pour l avoir par projet) -->
  <encodage_csv>$CFG->encodage</encodage_csv>
  <encodage_liste>UTF-8</encodage_liste>
  <!-- aucun arrondi à la note inferieure defaut est 0.5, inferieur -->
  <note_arrondi>normal</note_arrondi>
  <note_grain>0.000001</note_grain>
  <export_csv_cochees>1</export_csv_cochees>
  <after_export>dir</after_export>
</projetAMC>


EOT;
    return $ret;

}


?>
