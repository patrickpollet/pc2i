<?php


/**
 * @author Patrick Pollet
 * @version $Id: lib_fichiers.php 1301 2012-09-11 14:47:04Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
 * bibliotheque de manipulations de fichiers
 * @uses global $CFG configuration g�n�rale
 * @uses $CFG->chemin_ressources   (par d�faut en 1.4 $chemin/ressources en relatif)
 */

 if (is_admin()) {   //utilisateur courant uniquement
    maj_bd_fichiers();
 }

 function maj_bd_fichiers () {
 }


$currdir = $CFG->chemin_ressources;

if (($res = verifie_droits_fichiers($currdir)) != '')
	erreur_fatale($res,$currdir);

// cr�ation des dossiers minimaux
cree_dossier_si_absent($currdir . "/csv");
cree_dossier_si_absent($currdir . "/questions");
cree_dossier_si_absent($currdir . "/telechargement");
cree_dossier_si_absent($currdir . "/universites");
cree_dossier_si_absent($currdir . "/notions");
cree_dossier_si_absent($currdir . "/tmp");
cree_dossier_si_absent($currdir . "/apogee");
cree_dossier_si_absent($currdir . "/fckeditor");
cree_dossier_si_absent($currdir . "/archives");
cree_dossier_si_absent($currdir . "/filter");
cree_dossier_si_absent($currdir . "/ressources_locales");

// cr�ation d'un fichier .htaccess au cas ou ce dossier est dans la zone "web"
if (!file_exists($currdir . '/.htaccess')) {
	if ($handle = fopen($currdir . '/.htaccess', 'w')) { // For safety
		@ fwrite($handle, "deny from all\r\nAllowOverride None\r\nNote: this file is broken intentionally, we do not want anybody to undo it in subdirectory!\r\n");
		@ fclose($handle);
	}
}



if (!empty($CFG->verifier_install)) {
    if ((@is_file($chemin."/installation/installer.php")) ||
    (@is_file($chemin."/installation/maj.php")) ||
    (@is_file($chemin."/installation/initbase.php")) ||
    (@is_file($chemin."/installation/index.php")))
       erreur_fatale("err_rep_install_present");
}

   if (get_config('activer_filtre_latex',false)) {
    cree_dossier_si_absent($currdir . "/filter");
    cree_dossier_si_absent($currdir . "/filter/tex");
    cree_dossier_si_absent($currdir . "/tmp/latex");

   }



/**
* Renvoie la liste des sous-dossiers visibles d'un dossier
* utilis� pour charger dynamiquement des classes
*/
function get_list_of_plugins($plugin = 'codes', $exclude = '', $basedir = '') {
    global $CFG;
    $plugins = array ();
    if (empty ($basedir)) {
        # This switch allows us to use the appropiate theme directory - and potentialy alternatives for other plugins
        switch ($plugin) {
            case "theme" :
                $basedir = $CFG->themedir;
                break;
            default :
                $basedir = $CFG->libdir . '/' . $plugin;
        }
    } else {
        $basedir = $basedir . '/' . $plugin;
    }

    if (file_exists($basedir) && filetype($basedir) == 'dir') {
        $dirhandle = opendir($basedir);
        while (false !== ($dir = readdir($dirhandle))) {
            $firstchar = substr($dir, 0, 1);
            if ($firstchar == '.' or $dir == 'CVS' or $dir == '_vti_cnf' or $dir == $exclude) {
                continue;
            }
            if (filetype($basedir . '/' . $dir) != 'dir') {
                continue;
            }
            $plugins[] = $dir;
        }
        closedir($dirhandle);
    }
    if ($plugins) {
        asort($plugins);
    }
    return $plugins;
}



/**
 * renvoie la liste des fichier d'un dossier
 * ne cmmencant eventuellement pas par $exclude (comparaison en majuscule/minuscule)
 */
function get_list_of_files($exclude='',$basedir) {

	$liste = array ();

	if (file_exists($basedir) && filetype($basedir) == 'dir') {
		$dirhandle = opendir($basedir);
		while (false !== ($file = readdir($dirhandle))) {
			$firstchar = substr($file, 0, 1);
			if ($firstchar == '.' or $file == 'CVS' or $file == '_vti_cnf') {
				continue;
			}
            if ($exclude && stripos($file,$exclude)===0)
                continue;
			if (filetype($basedir . '/' . $file) == 'file') {
				$liste[] = $file;
			}
        }
		closedir($dirhandle);
		if ($liste) {
			asort($liste);
		}
		return $liste;
	}
}

/**
 * verifie que l'utilisateur " apache " a les droits sur un dossier
 * peut �tre appel�e depuis un �cran de changement de configuration
 * @param string  $dir le dossier a tester (peut �tre relatif)
 * @return string vide sans erreur sinon une cl� d'erreur dans le fichier de langue'
 */
function verifie_droits_fichiers($dir) {
	// v�rification de l'existence
	if (! @is_dir($dir))
		return "err_dossier_ressources_inconnu";
	// v�rification des droits
	if (!is_writable($dir))
		return "err_dossier_ressources_droits";
	return "";
}

function cree_dossier_si_absent($dir) {
	@ mkdir($dir);
	if (($res = verifie_droits_fichiers( $dir)) != "")
			erreur_fatale("err_dossier_ressources_sature",$dir);
}


/**
 * retourne le type MIME � partir de l'extension de fichier contenu dans $nomFichier
 * Exemple : $nomFichier = " fichier . pdf " => type renvoy� : " application / pdf "
 */

function typeMime($nomFichier) {
  //code perim�
  /***********************************
  global $chemin;
   // on d�tecte d'abord le navigateur, <E7>a nous servira plus tard
   if(preg_match("@Opera(/| )([0-9].[0-9]{1,2})@", $_SERVER['HTTP_USER_AGENT'], $resultats))
      $navigateur="Opera";
   elseif(preg_match("@MSIE ([0-9].[0-9]{1,2})@", $_SERVER['HTTP_USER_AGENT'], $resultats))
      $navigateur="Internet Explorer";
   else $navigateur="Mozilla";

   // on r�>cup<E8>re la liste des extensions de fichiers et leurs types Mime associ�>s
   $mime=parse_ini_file($chemin."/commun/mime.ini");
   $extension=strtolower(substr($nomFichier, strrpos($nomFichier, ".")+1));

   //
   ///on affecte le type Mime si on a trouv�> l'extension sinon le type par d�>faut (un flux d'octets).
   // Attention : Internet Explorer et Opera ne supporte pas le type MIME standard
   if(@array_key_exists($extension, $mime)) $type=$mime[$extension];
   else $type=($navigateur!="Mozilla") ? 'application/octetstream' : 'application/octet-stream';

   return $type;
   ********************************/
   //rev 981
   return mimeinfo('type',$nomFichier);

}


function isgoodfile ($idf) {
// v�rifie le nom du fichier � afficher
// = lettres|chiffres|_ ou -  suivi de .csv ou .tsv ou .xls, rien d'autre
// un point interne est permis mais pas de "/"
        return eregi("(^[a-z_0-9A-Z.\-]+\.(csv|tsv|xls|txt|pdf|ods|odt|doc|gif|jpg|jpeg|png|htm|html|zip|tex)$)",$idf);
}

/* TESTS
print isgoodfile("toto.csv")?" OK":" KO"; print"\n";
print isgoodfile("toto.tsv")?" OK":" KO"; print"\n";
print isgoodfile("toto.exe")?" OK":" KO"; print"\n";
print isgoodfile("../../enter.php")?" OK":" KO";print"\n";
print isgoodfile("resultats_65_12.csv") ?" OK":" KO";print"\n";

*/

/* copier_element
 * @param       string  nom d'origine de l'�>lement
 * @param       string  nom de destination de l'�>lement
 *
 * @return      bool    resultat de la copie
 *
 * copie un fichier ou un dossier :
 *  - copy pour un fichier
 *  - copier_dossier pour un dossier
 */
function copier_element($origine, $dest)
	{
	if ( @is_file("$origine") )
	{
		$perms = @fileperms("$origine");
		//print "copie de $origine -> $dest<br/>";
		return @copy("$origine", "$dest") && @chmod("$dest", $perms);
	}
	else if ( @is_dir("$origine") )
	{
		copier_dossier("$origine", "$dest");
	}
	else
	{

		erreur_fatale("DEV: erreur copier_element". $origine);
	}
	}

/* copier_dossier
 * @param       string  nom d'origine du dossier
 * @param       string  nom de destination du dossier
 *
 * @return      bool    resultat de la copie
 *
 * copie r�>cursive d'un dossier
 */
function copier_dossier($origine, $dest){
	if ( !is_dir("$dest") )	{
		@mkdir("$dest", 0777);
	}
   // print "copie de $origine -> $dest<br/>";
	$dir = @opendir("$origine");
	while ( $fich = @readdir($dir) )	{
		if ( ($fich != ".") && ($fich != "..") )		{
			copier_element("$origine/$fich", "$dest/$fich");
		}
	}

	@closedir($dir);
	}

/* supprimer_dossier
 * @param       string  nom du dossier <E0> supprimer
 *
 * @return      bool    resultat de la copie
 *
 * suppression d'un dossier
 */
function supprimer_dossier($dossier){
	if ( $dossier == "" ) {

		// Pb de s�curite (pourrait effectuer un unlink � la racine...)
		return false;
	}

	$dir = @opendir("$dossier");
	while ( $element = @readdir($dir) ){
		if ( ($element != ".") && ($element != "..") )        {
			if ( is_dir("$dossier/$element") )     {
				supprimer_dossier("$dossier/$element");
			}
			elseif ( is_file("$dossier/$element") )
			{
				unlink("$dossier/$element");
			}
			else
			{
				erreur_fatale("DEV: Impossible de supprimer". $dossier);
			}
		}
	}
	@closedir($dir);
	@rmdir($dossier);
}


# Written by Sean Nall <all.marx {at} gmail>
# and placed into the public domain.
# d'aprs http://fr.php.net/manual/fr/function.move-uploaded-file.php
# @function upload_file
# NE GERE PAS un renommage de fichier; i.e. dirPath doit �tre un DOSSIER
#
# @param $field        string        the name of the file upload form field
# @param $dirPath    string        the relative path to which to store the file (no trailing slash)
# @param $maxSize    int            the maximum size of the file
# @param $allowed    array        an array containing all the "allowed" file mime-types
#
# @return mixed        the files' stored path on success, false on failure.

function upload_file($field = '', $dirPath = '', $maxSize = 100000, $allowed = array()){

//print_r($_FILES);

    foreach ($_FILES[$field] as $key => $val)
        $$key = $val; //renseigne $tmp_name $name $size $error et $type ! (ignorer les warnings eclipse )

    if ((!is_uploaded_file($tmp_name)) || ($error != 0) || ($size == 0) || ($size > $maxSize))
        return false;    // file failed basic validation checks

    if ((is_array($allowed)) && (!empty($allowed)))
        if (!in_array($type, $allowed))
            return false;    // file is not an allowed type

    $path=$dirPath . DIRECTORY_SEPARATOR . strtolower(basename($name));
    $i=1;
    while(file_exists($path)) {
       $path = $dirPath . DIRECTORY_SEPARATOR . strtolower(basename($name))."_$i";
        $i++;
    }
    if (move_uploaded_file($tmp_name, $path) && @chmod($path,0644))
        return $path;

    return false;
}
/********************************************
DEMO:

if (array_key_exists('submit', $_POST))  // form has been submitted
{
    if ($filepath = upload_file('music_upload', 'music_files', 700000, array('audio/mpeg','audio/wav')))
        echo 'File uploaded to ' . $filepath;
    else
        echo 'An error occurred uploading the file... please try again.';
}
echo '
        <form method="post" action="' .$_SERVER['PHP_SELF']. '" enctype="multipart/form-data">
            <input type="file" name="music_upload" id="music_upload" />
            <input type="submit" name="submit" value="submit" />
        </form>
    ';

print_r($_FILES);     // for debug purposes
*************************************************/



/**
 * @param string $dossier_parent   chemin complet du dossier parent du dossier � zipper
 * @param string $nom_dossier nom (relatif) du dossier � zipper
 * @param string $nom_archive nom complet de l'archive zip � cr�er '
 */
function zip_dossier ($dossier_parent,$nom_dossier,$nom_archive) {
	global $CFG;
	//$CFG->cmd_zip='';
	if (!empty($CFG->cmd_zip) && file_exists($CFG->cmd_zip)){  // rev 1011

		$separator = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? ' &' : ' ;';
		$commandezip = 'cd '.escapeshellarg($dossier_parent).$separator.
		               $CFG->cmd_zip.' -r '.escapeshellarg($nom_archive). ' '.$nom_dossier;
		//All converted to backslashes in WIN
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$commandzip = str_replace('/','\\',$commandzip);
		}

		//echo($commandezip);
		exec($commandezip);


	}
	else { //pclzip 2.7 boggue aussi avec une archive de 3.4 Mo !!!!

    	//rev 973 il peut y a avoir plusieurs appels � zip_dossier
    	if (!defined('PCLZIP_TEMPORARY_DIR')) {

        	//define( 'PCLZIP_TEMPORARY_DIR', $CFG->chemin_ressources."/tmp/"  ); //important
        	define( 'PCLZIP_TEMPORARY_DIR', "/tmp/"  ); //important
    	}

		ini_set("memory_limit","128M");

		//$CFG->pclzip_trace=1;  //tests avec trace dans $CFG->chemin_ressources."/tmp/pclzip-trace_".time().".txt";

		require_once($CFG->chemin_commun."/pclzip-2-7/pcltrace.lib.php");
		require_once($CFG->chemin_commun."/pclzip-2-7/pclzip-trace.lib.php");

		if (!empty($CFG->pclzip_trace)) {

			$fn=$CFG->chemin_ressources."/tmp/pclzip-trace_".time().".txt";
			//print ("pcltrace on : $fn");
			PclTraceOn($p_level=5, $p_mode="log", $p_filename=$fn);
		}
		$archive = new PclZip($nom_archive);

		$archive->add($dossier_parent.'/'.$nom_dossier,PCLZIP_OPT_REMOVE_PATH, $dossier_parent."/");

		//test gros zip (48Mo de donn�es)
		//l'argument PCLZIP_OPT_ADD_TEMP_FILE_THRESHOLD est tr�s important !! (si >1Mo utiliser temporaires !)
		// pclzip > 1.7 !
		// $archive = new PclZip($CFG->chemin_ressources."/tmp/test.zip");
		// $archive->add($CFG->chemin_ressources,PCLZIP_OPT_REMOVE_PATH, $CFG->chemin_ressources."/",PCLZIP_OPT_ADD_TEMP_FILE_ON);



	}

}

/**
 * rev 980 si �diteur HTML activ� stocker les images dans un dossier tempo pour les
 * nouvelles questions d'ou le nouveau parametre autocreate
 */
function get_document_location($idq,$ide,$autocreate=false) {
    global $CFG;

    $chemin=$CFG->chemin_ressources . "/questions/".$ide."_".$idq;
    if ($autocreate) {
            if ($autocreate) cree_dossier_si_absent($chemin);
            cree_dossier_si_absent($chemin.'/documents');
        }
   return $chemin.'/documents';

}

/**
 * encode en base64 un document attach� � une question
 * @param string $idf nom du document (sans chemin) ex 1.jpg
 * @param int $idq, $ide : identifiants questions
 */
function encode_document( $idf, $idq,$ide ) {
	global $CFG;
	if (empty($idf)) {
		return '';
	}
	//$idf=$CFG->chemin_ressources."/questions/".$ide."_".$idq."/documents/".$idf;
	$idf=get_document_location($idq,$ide)."/".$idf;

  //  print $idf;
  //rev 976 vire un warning dans file_get_contents
    if (!file_exists($idf))
        return '';

	if (!$binary = file_get_contents($idf)) {
		return '';
	}

	$content = addslashes(base64_encode( $binary ));
	return $content;
}



/**
 * decode (base64) et sauve un document attach� � une question
 * @param string $idf nom du document (sans chemin) ex 1.jpg
 * @param string $base64 le contenu
 * @param int $idq, $ide : identifiants questions
 */
function decode_document( $idf, $base64,$idq,$ide ) {
	global $CFG;

	//$idf=$CFG->chemin_ressources."/questions/".$ide."_".$idq."/documents/".$idf;
    // avec autocration si n�cessaire
	$idf=get_document_location($idq,$ide,true)."/".$idf;


	// convert and save file contents
	if (!$content = base64_decode(stripslashes( $base64 ))) {
		return '';
	}
	if (!$fh = fopen( $idf, 'w' )) {
		return '';
	}
	if (!fwrite( $fh, $content )) {
		return '';
	}
	fclose( $fh );

	// return the (possibly) new filename

	return $idf;
}


/**
 * @return List of information about file types based on extensions.
 *   Associative array of extension (lower-case) to associative array
 *   from 'element name' to data. Current element names are 'type' and 'icon'.
 *   Unknown types should use the 'xxx' entry which includes defaults.
 */
function get_mimetypes_array() {
    return array (
        'xxx'  => array ('type'=>'document/unknown', 'icon'=>'unknown.gif'),
        '3gp'  => array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
        'ai'   => array ('type'=>'application/postscript', 'icon'=>'image.gif'),
        'aif'  => array ('type'=>'audio/x-aiff', 'icon'=>'audio.gif'),
        'aiff' => array ('type'=>'audio/x-aiff', 'icon'=>'audio.gif'),
        'aifc' => array ('type'=>'audio/x-aiff', 'icon'=>'audio.gif'),
        'applescript'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'asc'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'asm'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'au'   => array ('type'=>'audio/au', 'icon'=>'audio.gif'),
        'avi'  => array ('type'=>'video/x-ms-wm', 'icon'=>'avi.gif'),
        'bmp'  => array ('type'=>'image/bmp', 'icon'=>'image.gif'),
        'c'    => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'cct'  => array ('type'=>'shockwave/director', 'icon'=>'flash.gif'),
        'cpp'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'cs'   => array ('type'=>'application/x-csh', 'icon'=>'text.gif'),
        'css'  => array ('type'=>'text/css', 'icon'=>'text.gif'),
        'csv'  => array ('type'=>'text/csv', 'icon'=>'excel.gif'),
        'dv'   => array ('type'=>'video/x-dv', 'icon'=>'video.gif'),
        'dmg'  => array ('type'=>'application/octet-stream', 'icon'=>'dmg.gif'),

        'doc'  => array ('type'=>'application/msword', 'icon'=>'word.gif'),
        'docx' => array ('type'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'icon'=>'docx.gif'),
        'docm' => array ('type'=>'application/vnd.ms-word.document.macroEnabled.12', 'icon'=>'docm.gif'),
        'dotx' => array ('type'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.template', 'icon'=>'dotx.gif'),
        'dotm' => array ('type'=>'application/vnd.ms-word.template.macroEnabled.12', 'icon'=>'dotm.gif'),

        'dcr'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'dif'  => array ('type'=>'video/x-dv', 'icon'=>'video.gif'),
        'dir'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'dxr'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'eps'  => array ('type'=>'application/postscript', 'icon'=>'pdf.gif'),
        'fdf'  => array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),
        'flv'  => array ('type'=>'video/x-flv', 'icon'=>'video.gif'),
        'gif'  => array ('type'=>'image/gif', 'icon'=>'image.gif'),
        'gtar' => array ('type'=>'application/x-gtar', 'icon'=>'zip.gif'),
        'tgz'  => array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
        'gz'   => array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
        'gzip' => array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
        'h'    => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'hpp'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'hqx'  => array ('type'=>'application/mac-binhex40', 'icon'=>'zip.gif'),
        'htc'  => array ('type'=>'text/x-component', 'icon'=>'text.gif'),
        'html' => array ('type'=>'text/html', 'icon'=>'html.gif'),
        'xhtml'=> array ('type'=>'application/xhtml+xml', 'icon'=>'html.gif'),
        'htm'  => array ('type'=>'text/html', 'icon'=>'html.gif'),
        'ico'  => array ('type'=>'image/vnd.microsoft.icon', 'icon'=>'image.gif'),
        'ics'  => array ('type'=>'text/calendar', 'icon'=>'text.gif'),
        'isf'  => array ('type'=>'application/inspiration', 'icon'=>'isf.gif'),
        'ist'  => array ('type'=>'application/inspiration.template', 'icon'=>'isf.gif'),
        'java' => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'jcb'  => array ('type'=>'text/xml', 'icon'=>'jcb.gif'),
        'jcl'  => array ('type'=>'text/xml', 'icon'=>'jcl.gif'),
        'jcw'  => array ('type'=>'text/xml', 'icon'=>'jcw.gif'),
        'jmt'  => array ('type'=>'text/xml', 'icon'=>'jmt.gif'),
        'jmx'  => array ('type'=>'text/xml', 'icon'=>'jmx.gif'),
        'jpe'  => array ('type'=>'image/jpeg', 'icon'=>'image.gif'),
        'jpeg' => array ('type'=>'image/jpeg', 'icon'=>'image.gif'),
        'jpg'  => array ('type'=>'image/jpeg', 'icon'=>'image.gif'),
        'jqz'  => array ('type'=>'text/xml', 'icon'=>'jqz.gif'),
        'js'   => array ('type'=>'application/x-javascript', 'icon'=>'text.gif'),
        'latex'=> array ('type'=>'application/x-latex', 'icon'=>'text.gif'),
        'm'    => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'mov'  => array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
        'movie'=> array ('type'=>'video/x-sgi-movie', 'icon'=>'video.gif'),
        'm3u'  => array ('type'=>'audio/x-mpegurl', 'icon'=>'audio.gif'),
        'mp3'  => array ('type'=>'audio/mp3', 'icon'=>'audio.gif'),
        'mp4'  => array ('type'=>'video/mp4', 'icon'=>'video.gif'),
        'm4v'  => array ('type'=>'video/mp4', 'icon'=>'video.gif'),
        'm4a'  => array ('type'=>'audio/mp4', 'icon'=>'audio.gif'),
        'mpeg' => array ('type'=>'video/mpeg', 'icon'=>'video.gif'),
        'mpe'  => array ('type'=>'video/mpeg', 'icon'=>'video.gif'),
        'mpg'  => array ('type'=>'video/mpeg', 'icon'=>'video.gif'),

        'odt'  => array ('type'=>'application/vnd.oasis.opendocument.text', 'icon'=>'odt.gif'),
        'ott'  => array ('type'=>'application/vnd.oasis.opendocument.text-template', 'icon'=>'odt.gif'),
        'oth'  => array ('type'=>'application/vnd.oasis.opendocument.text-web', 'icon'=>'odt.gif'),
        'odm'  => array ('type'=>'application/vnd.oasis.opendocument.text-master', 'icon'=>'odm.gif'),
        'odg'  => array ('type'=>'application/vnd.oasis.opendocument.graphics', 'icon'=>'odg.gif'),
        'otg'  => array ('type'=>'application/vnd.oasis.opendocument.graphics-template', 'icon'=>'odg.gif'),
        'odp'  => array ('type'=>'application/vnd.oasis.opendocument.presentation', 'icon'=>'odp.gif'),
        'otp'  => array ('type'=>'application/vnd.oasis.opendocument.presentation-template', 'icon'=>'odp.gif'),
        'ods'  => array ('type'=>'application/vnd.oasis.opendocument.spreadsheet', 'icon'=>'ods.gif'),
        'ots'  => array ('type'=>'application/vnd.oasis.opendocument.spreadsheet-template', 'icon'=>'ods.gif'),
        'odc'  => array ('type'=>'application/vnd.oasis.opendocument.chart', 'icon'=>'odc.gif'),
        'odf'  => array ('type'=>'application/vnd.oasis.opendocument.formula', 'icon'=>'odf.gif'),
        'odb'  => array ('type'=>'application/vnd.oasis.opendocument.database', 'icon'=>'odb.gif'),
        'odi'  => array ('type'=>'application/vnd.oasis.opendocument.image', 'icon'=>'odi.gif'),
        'ogg'  => array ('type'=>'audio/ogg', 'icon'=>'audio.gif'),
        'ogv'  => array ('type'=>'video/ogg', 'icon'=>'video.gif'),

        'pct'  => array ('type'=>'image/pict', 'icon'=>'image.gif'),
        'pdf'  => array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),
        'php'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'pic'  => array ('type'=>'image/pict', 'icon'=>'image.gif'),
        'pict' => array ('type'=>'image/pict', 'icon'=>'image.gif'),
        'png'  => array ('type'=>'image/png', 'icon'=>'image.gif'),

        'pps'  => array ('type'=>'application/vnd.ms-powerpoint', 'icon'=>'powerpoint.gif'),
        'ppt'  => array ('type'=>'application/vnd.ms-powerpoint', 'icon'=>'powerpoint.gif'),
        'pptx' => array ('type'=>'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'icon'=>'pptx.gif'),
        'pptm' => array ('type'=>'application/vnd.ms-powerpoint.presentation.macroEnabled.12', 'icon'=>'pptm.gif'),
        'potx' => array ('type'=>'application/vnd.openxmlformats-officedocument.presentationml.template', 'icon'=>'potx.gif'),
        'potm' => array ('type'=>'application/vnd.ms-powerpoint.template.macroEnabled.12', 'icon'=>'potm.gif'),
        'ppam' => array ('type'=>'application/vnd.ms-powerpoint.addin.macroEnabled.12', 'icon'=>'ppam.gif'),
        'ppsx' => array ('type'=>'application/vnd.openxmlformats-officedocument.presentationml.slideshow', 'icon'=>'ppsx.gif'),
        'ppsm' => array ('type'=>'application/vnd.ms-powerpoint.slideshow.macroEnabled.12', 'icon'=>'ppsm.gif'),

        'ps'   => array ('type'=>'application/postscript', 'icon'=>'pdf.gif'),
        'qt'   => array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
        'ra'   => array ('type'=>'audio/x-realaudio-plugin', 'icon'=>'audio.gif'),
        'ram'  => array ('type'=>'audio/x-pn-realaudio-plugin', 'icon'=>'audio.gif'),
        'rhb'  => array ('type'=>'text/xml', 'icon'=>'xml.gif'),
        'rm'   => array ('type'=>'audio/x-pn-realaudio-plugin', 'icon'=>'audio.gif'),
        'rtf'  => array ('type'=>'text/rtf', 'icon'=>'text.gif'),
        'rtx'  => array ('type'=>'text/richtext', 'icon'=>'text.gif'),
        'sh'   => array ('type'=>'application/x-sh', 'icon'=>'text.gif'),
        'sit'  => array ('type'=>'application/x-stuffit', 'icon'=>'zip.gif'),
        'smi'  => array ('type'=>'application/smil', 'icon'=>'text.gif'),
        'smil' => array ('type'=>'application/smil', 'icon'=>'text.gif'),
        'sqt'  => array ('type'=>'text/xml', 'icon'=>'xml.gif'),
        'svg'  => array ('type'=>'image/svg+xml', 'icon'=>'image.gif'),
        'svgz' => array ('type'=>'image/svg+xml', 'icon'=>'image.gif'),
        'swa'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'swf'  => array ('type'=>'application/x-shockwave-flash', 'icon'=>'flash.gif'),
        'swfl' => array ('type'=>'application/x-shockwave-flash', 'icon'=>'flash.gif'),

        'sxw'  => array ('type'=>'application/vnd.sun.xml.writer', 'icon'=>'odt.gif'),
        'stw'  => array ('type'=>'application/vnd.sun.xml.writer.template', 'icon'=>'odt.gif'),
        'sxc'  => array ('type'=>'application/vnd.sun.xml.calc', 'icon'=>'odt.gif'),
        'stc'  => array ('type'=>'application/vnd.sun.xml.calc.template', 'icon'=>'odt.gif'),
        'sxd'  => array ('type'=>'application/vnd.sun.xml.draw', 'icon'=>'odt.gif'),
        'std'  => array ('type'=>'application/vnd.sun.xml.draw.template', 'icon'=>'odt.gif'),
        'sxi'  => array ('type'=>'application/vnd.sun.xml.impress', 'icon'=>'odt.gif'),
        'sti'  => array ('type'=>'application/vnd.sun.xml.impress.template', 'icon'=>'odt.gif'),
        'sxg'  => array ('type'=>'application/vnd.sun.xml.writer.global', 'icon'=>'odt.gif'),
        'sxm'  => array ('type'=>'application/vnd.sun.xml.math', 'icon'=>'odt.gif'),

        'tar'  => array ('type'=>'application/x-tar', 'icon'=>'zip.gif'),
        'tif'  => array ('type'=>'image/tiff', 'icon'=>'image.gif'),
        'tiff' => array ('type'=>'image/tiff', 'icon'=>'image.gif'),
        'tex'  => array ('type'=>'application/x-tex', 'icon'=>'text.gif'),
        'texi' => array ('type'=>'application/x-texinfo', 'icon'=>'text.gif'),
        'texinfo'  => array ('type'=>'application/x-texinfo', 'icon'=>'text.gif'),
        'tsv'  => array ('type'=>'text/tab-separated-values', 'icon'=>'text.gif'),
        'txt'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'wav'  => array ('type'=>'audio/wav', 'icon'=>'audio.gif'),
        'wmv'  => array ('type'=>'video/x-ms-wmv', 'icon'=>'avi.gif'),
        'asf'  => array ('type'=>'video/x-ms-asf', 'icon'=>'avi.gif'),
        'xdp'  => array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),
        'xfd'  => array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),
        'xfdf' => array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),

        'xls'  => array ('type'=>'application/vnd.ms-excel', 'icon'=>'excel.gif'),
        'xlsx' => array ('type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'icon'=>'xlsx.gif'),
        'xlsm' => array ('type'=>'application/vnd.ms-excel.sheet.macroEnabled.12', 'icon'=>'xlsm.gif'),
        'xltx' => array ('type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.template', 'icon'=>'xltx.gif'),
        'xltm' => array ('type'=>'application/vnd.ms-excel.template.macroEnabled.12', 'icon'=>'xltm.gif'),
        'xlsb' => array ('type'=>'application/vnd.ms-excel.sheet.binary.macroEnabled.12', 'icon'=>'xlsb.gif'),
        'xlam' => array ('type'=>'application/vnd.ms-excel.addin.macroEnabled.12', 'icon'=>'xlam.gif'),

        'xml'  => array ('type'=>'application/xml', 'icon'=>'xml.gif'),
        'xsl'  => array ('type'=>'text/xml', 'icon'=>'xml.gif'),
        'zip'  => array ('type'=>'application/zip', 'icon'=>'zip.gif')
    );
}

/**
 * Obtains information about a filetype based on its extension. Will
 * use a default if no information is present about that particular
 * extension.
 * @param string $element Desired information (usually 'icon'
 *   for icon filename or 'type' for MIME type)
 * @param string $filename Filename we're looking up
 * @return string Requested piece of information from array
 */
function mimeinfo($element, $filename) {
    static $mimeinfo = null;
    if (is_null($mimeinfo)) {
        $mimeinfo = get_mimetypes_array();
    }

    if (preg_match('/\.([a-zA-Z0-9]+)$/', $filename, $match)) {
        if (isset($mimeinfo[strtolower($match[1])][$element])) {
            return $mimeinfo[strtolower($match[1])][$element];
        } else {
            return $mimeinfo['xxx'][$element];   // By default
        }
    } else {
        return $mimeinfo['xxx'][$element];   // By default
    }
}

//print ("zzz".encode_document('5.jpg',2359,1));
