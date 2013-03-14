<?php
/**
 * @author Patrick Pollet
 * @version $Id: entete.php 288 2008-06-18 23:21:13Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 * 
 * convertit en png les documents de type gif qui ne sont pas 
 * reconnus par laTex et met a jour la bd nationale
 * prerequis ! installer imagemagic pour le programme convert 
 * 
 */
 
 
 set_time_limit(0);
$chemin = '../../../';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");                 //fichier de paramètres
require_login('P'); //PP
if (!is_admin(false,$CFG->universite_serveur)) die("pas admin");



$sql=<<<EOS
select * from c2iquestionsdocuments
where extension='gif';
EOS;


$docs=get_records_sql($sql);
print_r($docs);

foreach($docs as $doc) {
	$path=get_document_location ($doc->id,$doc->id_etab);
	print $path."<br/>";
	if (file_exists($path.'/'.$doc->id_doc.'.gif')) {
		$cmd="cd $path ; convert {$doc->id_doc}.gif {$doc->id_doc}.png";
		print $cmd."<br/>";
		exec ($cmd);
		$sql2=<<<EOS
		update c2iquestionsdocuments
		set extension ='png'
		where id={$doc->id} and id_etab={$doc->id_etab} and id_doc={$doc->id_doc}
EOS;
		print $sql2."<br/>";
		ExecRequete($sql2);
	}else 
		print "document $path".'/'.$doc->id_doc.'.gif introuvable<br/>'; 	
	
	
}

/****
 * resultats le 21/12/2009
 
 avant
 ppollet@ppollet-laptop:/work/c2iv15/questions$ find -name *.gif
./1_2304/documents/3.gif
./1_2304/documents/2.gif
./1_419/documents/1.gif
./1_1660/documents/1.gif
./1_157/documents/1.gif
./1_1389/documents/1.gif
ppollet@ppollet-laptop:/work/c2iv15/questions$ find -name *.png
./28_2382/documents/1.png
./_1981/documents/1.png
./28_2385/documents/2.png

après 
ppollet@ppollet-laptop:/work/c2iv15/questions$ find -name *.png
./1_419/documents/1.png
./28_2382/documents/1.png
./1_1660/documents/1.png
./_1981/documents/1.png
./1_157/documents/1.png
./28_2385/documents/2.png
./1_1389/documents/1.png

et bd mise à jour
 
 */


?>

