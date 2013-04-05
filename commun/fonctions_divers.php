<?php
//$Id
/*----------------REVISIONS----------------------
v 1.1 : PP 16/10/2006 ajout de
      isint,isnum,isfloat,isalpha
      v_d_o_d

		SB : ajout d'une fonction non_sql qui v�rifie la pr�sence d'un ; ou d'un espace (permet de corriger le probl�me des acc�s)
        inutile en V1.5 quand on generalisera l'utilisation de required_param et optional_param

V 1.5 ne contiendra plus que les vrais divers

------------------------------------------------*/

/**
 * mises ici car appel�e par tous les lib_xxxx.php
 *mais ici on trvaille sur $USER->droits et $USER->capacite en V 1.6
 */
function is_admin ($login=false,$etab_vise=false) {
	global $USER;
	//return false;
	if (empty($USER->id_user)) return false;  //qui demande ?
	if (empty($login)) $login=$USER->id_user; //parametre optionnel non utilis� encore
	if (empty($USER->type_user) || $USER->type_user!="P") return false; //meme pas en reve

	if (empty($etab_vise)) $etab_vise=$USER->id_etab_perso;
	if (empty($etab_vise)) return false; //peut pas savoir
	if (empty($USER->droits)) return false;      //impossible ?
	//surtout pas de notice php ...pour un �tudiant
	$ok= @(($USER->droits['row_admin']-> est_superadmin=='O' && $USER->id_etab_perso==1)
			|| ($USER->droits['row_admin']->est_admin_univ=='O' &&  $USER->id_etab_perso==$etab_vise));
	//print "alors comme ca t'es admin ?':$ok " ;
	if ($ok) return true;
	else {
		//gerer les h�ritages dans les composantes...
		$et=get_etablissement($etab_vise);
		if ($et->pere<=1)return false;   //nationale ou locale niveau 1
		else return is_admin($login,$et->pere);
	}

}

 function is_super_admin ($login=false) {
    global $USER;
    if (!is_admin($login)) return false;
    return  @($USER->droits['row_admin']-> est_superadmin=='O' && $USER->id_etab_perso==1);

 }

 // pour d�cider du bandeau et d'autre droits
function is_utilisateur_anonyme ($login=false) {
    global $USER;
    if (!$login) $login=$USER->id_user;
    //notez le triple egal !
    return strpos($login,"ANONYME")===0;
}


/**
 * retourne le num�ro de '�tablissement de la personne ayant pour login $login
 * compatible V1.3
 * conn devient optionnel
 * ne doit pas �chouer
 * $conn est rest� pour compat script 1.4 mais n'est pas utilis�'
 */

function etab($login, $conn=false,$die=0){
    $login=addslashes($login); // rev 984
    $objet_u=get_record("utilisateurs","login = '".$login."'",false);
    if (!$objet_u)
        $objet_u=get_record("inscrits","login = '".$login."'",$die);
    if ($objet_u)
        return $objet_u->etablissement;
    else return 0;
}

/**
 * fonction de d�bug
 * @param string titre texte a mettre avant la valeur
 * @param object obj
 * ex print_object ("reponse",$ligne); )
 */
function print_object($titre,$obj) {
	print ("<pre class='commentaire1'>".$titre.' = '.htmlspecialchars(print_r($obj,true)).'</pre>');

}

/**
 * Escape all dangerous characters in a data record
 *
 * $dataobject is an object containing needed data
 * Run over each field exectuting addslashes() function
 * to escape SQL unfriendly characters (e.g. quotes)
 * Handy when writing back data read from the database
 *
 * @param $dataobject Object containing the database record
 * @return object Same object with neccessary characters escaped
 */
function addslashes_object( $dataobject ) {
    $a = get_object_vars( $dataobject);
    foreach ($a as $key=>$value) {
      $a[$key] = addslashes( $value );
    }
    return (object)$a;
}


/**
 * Moodle replacement for php stripslashes() function,
 * works also for objects and arrays.
 *
 * The standard php stripslashes() removes ALL backslashes
 * even from strings - so  C:\temp becomes C:temp - this isn't good.
 * This function should work as a fairly safe replacement
 * to be called on quoted AND unquoted strings (to be sure)
 *
 * @param mixed something to remove unsafe slashes from
 * @return mixed
 */
function stripslashes_safe($mixed) {
    // there is no need to remove slashes from int, float and bool types
    if (empty($mixed)) {
        //nothing to do...
    } else if (is_string($mixed)) {
        if (ini_get_bool('magic_quotes_sybase')) { //only unescape single quotes
            $mixed = str_replace("''", "'", $mixed);
        } else { //the rest, simple and double quotes and backslashes
            $mixed = str_replace("\\'", "'", $mixed);
            $mixed = str_replace('\\"', '"', $mixed);
            $mixed = str_replace('\\\\', '\\', $mixed);
        }
    } else if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = stripslashes_safe($value);
        }
    } else if (is_object($mixed)) {
        $vars = get_object_vars($mixed);
        foreach ($vars as $key => $value) {
            $mixed->$key = stripslashes_safe($value);
        }
    }

    return $mixed;
}

/**
 * Escape all dangerous characters in a data record
 *
 * $dataobject is an object containing needed data
 * Run over each field exectuting addslashes() function
 * to escape SQL unfriendly characters (e.g. quotes)
 * Handy when writing back data read from the database
 *
 * @param $dataobject Object containing the database record
 * @return object Same object with neccessary characters escaped
 */
function stripslashes_object( $dataobject ) {
    $a = get_object_vars( $dataobject);
    foreach ($a as $key=>$value) {
      $a[$key] = stripslashes( $value );
    }
    return (object)$a;
}

/**
 * Generate and return a random string of the specified length.
 *
 * @param int $length The length of the string to be created.
 * @return string
 */
function random_string ($length=15) {
    $pool  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pool .= 'abcdefghijklmnopqrstuvwxyz';
    $pool .= '0123456789';
    $poollen = strlen($pool);
    mt_srand ((double) microtime() * 1000000);
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= substr($pool, (mt_rand()%($poollen)), 1);
    }
    return $string;
}

function mot_de_passe_a($longueur){
	// g�n�re un mot de passe al�atoire de la taille $longueur
	$ar = array ("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","2","3","4","6","7","8","9");
	srand((double) microtime() * 10000000);
	$i=0;
	$mot = "";
	while ($i < $longueur){
		$mot .= $ar[array_rand($ar,1)];
		$i++;
	}
	return $mot;
}

function chaine_xml($chaine){
	// retourne $cha�ne format�e pour le fichier xml
	return str_replace("<","&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", str_replace("&nbsp;"," ", $chaine))));
}

function chaine_non_xml($chaine){
	// retourne $cha�ne venant d'un fichier xml reformat�e "html" pour �viter les erreurs d'interpr�tation
	return str_replace("&lt;","<", str_replace("&gt;", ">", str_replace("&amp;", "&", $chaine)) );
}


/**
* met bout a bout 2 chaines avec un s�parateur entre les deux
* SI n�cessaire en v�rifiant au pr�alable qu'aucune des
* deux chaines n'est vide ... pour ne pas se retrouver
* avec un s�parateur seul au d�but ou � la fin
* @param $valeur1, $valeur2
* @param $separateur
* @return
*/

function concatAvecSeparateur ($valeur1,$valeur2,$separateur) {
    if ($valeur2) {
        if ($valeur1) $valeur1.=$separateur;
        $valeur1.=$valeur2;
    }
    return $valeur1;
}


/**
 * convertit une valeur a emettre en virant d'evnetuels blancs, saut de lignes, tab ...
 * et le s�parateur CSV'
 */
function to_csv ($chaine) {
    global $CFG;
    return str_replace($CFG->csv_separateur,"",clean($chaine,strlen($chaine)));

}

/**
 * utile pour les import CSV depuis excel !
 * apparu en V1.5
 * vire les guillemets aui auraient �t� ajout�s par Excel et au passage les blancs d�but fin
 */
function vire_guillemets($chaine) {
  return trim(str_replace("\"","",$chaine));
}

/**
* prepare une ligne CSV
* @param colonnes tableau des noms de colonnes dans la BD ou autre dans l'ordre voulu
* @param ligne un objet contenant les infos � emettre
*                si false, on emet la ligne d'ent�te en traduisant les noms de colonnes
* @param $cvt (rev 820) un tableua de booleens pour la conversion point en virgule d�cimale
*/
function ligne_to_csv ($colonnes, $ligne,$cvt=array()) {
    global $CFG;
    $ret="";
    $cpt=0;
    if ($ligne) {
        foreach ($colonnes as $nom) {
            if (!empty($cvt[$cpt])) $ligne->$nom=note_xls($ligne->$nom);
            if ($cpt)
                $ret .= "$CFG->csv_separateur".to_csv($ligne->$nom);
            else
                $ret .= to_csv($ligne->$nom);
            $cpt++;
        }
    } else {
        foreach($colonnes as $nom) {
            if ($cpt)
                $ret.="$CFG->csv_separateur".to_csv (traduction($nom,false));
            else
                $ret.=to_csv (traduction($nom,false));
            $cpt++;
        }
    }
    return $ret;  //on ne met pas le \n pour pouvoir la completer plus tard
}

//PP  lutte contre l'injection SQL
// fonctions v�rifiant qu'un argument est num�rique ou litt�ral strict
//p�rim� quand les reuqired/optional_aparm seront g�n�ralis�s

function isint($num){
        return ereg("(^[0-9]+$)",(string)$num);
}

function isfloat($num){
        return ereg("(^[0-9]*\.[0-9]*$)",(string)$num);
}

function isnum($num){
        return (isint($num) || isfloat($num));
}

function isalpha ($num) {
        return ereg("(^[a-z]+$)",(string)$num);
}

function non_sql($chaine){
	//SB
	// v�rifie la pr�sence d'un point virgule ou d'un espace pour une variable utilis�e dans une requ�te ne devant pas en contenir (cas du passage de login en url en admin)
	// stoppe le script si c'est le cas
	if (strpos($chaine,";")) erreur_fatale("err_param_suspect",$chaine);
	if (strpos($chaine," ")) erreur_fatale("err_param_suspect",$chaine);
	return true;
}


/******** TESTS
$n = 43; echo "isint($n): ".isint($n)."\n";
$n = "34"; echo "isint($n): ".isint($n)."\n";
$n = "0123"; echo "isint($n): ".isint($n)."\n";
$n = "notnum"; echo "isint($n): ".isint($n)."\n";

$n = "123"; echo "isfloat($n): ".isfloat($n)."\n";
$n = ".1"; echo "isfloat($n): ".isfloat($n)."\n";
$n = "0.1"; echo "isfloat($n): ".isfloat($n)."\n";
$n = "123.123"; echo "isfloat($n): ".isfloat($n)."\n";
$n = "6."; echo "isfloat($n): ".isfloat($n)."\n";
$n = "notnum"; echo "isfloat($n): ".isfloat($n)."\n";

$n = "123"; echo "isnum($n): ".isnum($n)."\n";
$n = "0.1"; echo "isnum($n): ".isnum($n)."\n";
$n = "notnum"; echo "isnum($n): ".isnum($n)."\n";

$n = "1;drop table c2iinscrits"; echo "isnum($n): ".isnum($n)."\n";
$n = "1.65;drop table c2iinscrits"; echo "isnum($n): ".isnum($n)."\n";
*************/


//end PP

/** la fontion tester_version_pf retourne la version de plateforme  install�e
*/
function return_version_pf($force=false){
	global $CFG;
	global $chemin;

	//d�but compat V2
	if (!$force & isset($CFG->version)) return $CFG->version;

	if (!isset($chemin)) $chemin=".";

	$fichier_version = $chemin."/commun/version.txt";
    //print $fichier_version;
	if (is_file($fichier_version)){
		if ($fp = fopen ($fichier_version, "r")) $ret= fgets($fp, 4096);
		else $ret= traduction( "non d�finie");
	}
	else $ret = traduction( "inconnu");
	$ret=clean(trim($ret));
	$CFG->version=$ret; // vire sat de lignes eventuels
	return $ret;
}

/**
 * rev 908 n�cessaire sur la nationale pour determiner le dossier de l'image de t�l�chargement
 */
function return_version_majeure_pf() {
    $ver=return_version_pf();
    $tab=explode(" ",$ver);
    //print_r($tab);
    if (count($tab)>=1) {
        return $tab[0];
    }
    else return '1.5';
}

/** la fontion verifier_version_pf retourne la version de plateforme  install�e
 * appel�e en ajax (voir fonctions_diverses
*/

function get_version_svn() {
	 global $CFG;
	if (defined("ADRESSE_VERSION")){
		$adrversion = ADRESSE_VERSION;
		if (strstr($adrversion,"version.txt")=="") {
			if (substr($adrversion,strlen($adrversion)-1,1) != '/') $fichier_national = ADRESSE_VERSION."/version.txt";
			else $fichier_national = ADRESSE_VERSION."version.txt";
		}
		else $fichier_national = ADRESSE_VERSION;
        $vn="";
        $via="fopen";
        //rev 905 voir http://c2i.education.fr/forum-c2i-1/viewtopic.php?t=80

        if (!empty($CFG->utiliser_curl)) {
	        $session = curl_init();
	        curl_setopt($session, CURLOPT_URL, $fichier_national);
	        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	        $vn = curl_exec($session);
	        curl_close($session);
            $via ="curl";
        }
        else {
	        if ($fp = fopen($fichier_national,"r")){
		        $vn = fgets($fp, 4096);
		        fclose($fp);
	        }
        }
        $CFG->version_svn=$vn;
        return clean(trim($vn));
	}
	return "aie";

}

function verifier_version_pf(){

		$vl = return_version_pf();
		$vn=get_version_svn();

		// attention 2 espaces dans le fichier de refrence !
       // print "|".$vn. "|".$vl."|";
       // print strlen($vn)." ".strlen($vl);
       // print hexdump($vn,false,false, true)."|".hexdump($vl,false,false,true);
       $via=empty($CFG->utiliser_curl)?'fopen':'curl';
		if ("V".$vl != "V".$vn) return ('Votre version est la '.$vl.', attention il existe une version plus r&eacute;cente '.$vn);
         else return 'version '.$vl. ' &agrave; jour  via '.$via;
}

/**
 * nettoyage complet non ascii et troncature
 * requis pour les arbres HTML_TreeMenu et export cvs sit saut de lignes !!!
 */
 function clean($chaine,$longueur=100){
    /*
    $chaine=str_replace (',','',$chaine);
    $chaine=str_replace ('\r','',$chaine);
    $chaine=str_replace ('\n','',$chaine);
    $chaine=str_replace ('\t','',$chaine);
    return $chaine;
    **/
     $chaine=preg_replace('/[^(\x20-\xFF)]*/','', $chaine);
     if ($longueur  && strlen($chaine)>$longueur)
            return substr($chaine,0,$longueur-4 )." ...";
      else return $chaine;
 }

/**
 * rev 1.4
 * fonction a utiliser pour emettre des notes dans des CSV
 * pour �viter les gags avec openOffice et quelques fois Excel
 */

function note_xls($note) {
// 2 chiffres apr�s la virugle, VIRGULE et pas point, pas de s�parateur des milliers

   if (!is_numeric($note)) return $note;
   return number_format($note,2,",","");
}




/** ajoute un slash au bout d'une url s'il n'y en a pas  et q'url n'est pas vide*/
function add_slash_url($url){
	$url = trim($url);
	if ($url=="") return $url;
	return substr($url, -1, 1)=='/'?$url:$url.'/';
}

/**
 * ne doit pas �chouer :
 */
function whoisconnected(){
	// retourne les noms et pr�noms de la personne connect�e
	  global $USER;
      if (function_exists("get_fullname"))  //attention est dans lib_acces qui peut ne pas etre encore charg�e
      	return get_fullname($USER->id_user);  //respect regeles nom+pr�nom V 1.5
      else return "";


}

/**
* For outputting debugging info
* AJOUT�E REV 1.5 986 POUR IMPLEMENTATION D'UN cron 
* @uses STDOUT
* @param string $string ?
* @param string $eol ?
* @todo Finish documenting this function
*/
function mtrace($string, $eol="\n", $sleep=0) {

    if (defined('STDOUT')) {
        fwrite(STDOUT, $string.$eol);
    } else {
        echo $string . $eol;
    }

    flush();

    //delay to keep message on user's screen in case of subsequent redirect
    if ($sleep) {
        sleep($sleep);
    }
}


/**
* Checks to see if is a browser matches the specified
* brand and is equal or better version.
*
* @uses $_SERVER
* @param string $brand The browser identifier being tested
* @param int $version The version of the browser
* @return bool true if the given version is below that of the detected browser
*/
function check_browser_version($brand='MSIE', $version=5.5) {
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }

    $agent = $_SERVER['HTTP_USER_AGENT'];

    switch ($brand) {

        case 'Camino':   /// Mozilla Firefox browsers

            if (preg_match("/Camino\/([0-9\.]+)/i", $agent, $match)) {
                if (version_compare($match[1], $version) >= 0) {
                    return true;
                }
            }
            break;


        case 'Firefox':   /// Mozilla Firefox browsers

            if (preg_match("/Firefox\/([0-9\.]+)/i", $agent, $match)) {
                if (version_compare($match[1], $version) >= 0) {
                    return true;
                }
            }
            break;


        case 'Gecko':   /// Gecko based browsers

            if (substr_count($agent, 'Camino')) {
                // MacOS X Camino support
                $version = 20041110;
            }

            // the proper string - Gecko/CCYYMMDD Vendor/Version
            // Faster version and work-a-round No IDN problem.
            if (preg_match("/Gecko\/([0-9]+)/i", $agent, $match)) {
                if ($match[1] > $version) {
                    return true;
                }
            }
            break;


        case 'MSIE':   /// Internet Explorer

            if (strpos($agent, 'Opera')) {
                // Reject Opera
                return false;
            }
            $string = explode(';', $agent);
            if (!isset($string[1])) {
                return false;
            }
            $string = explode(' ', trim($string[1]));
            if (!isset($string[0]) and !isset($string[1])) {
                return false;
            }
            if ($string[0] == $brand and (float)$string[1] >= $version ) {
                return true;
            }
            break;

        case 'Opera':  /// Opera

            if (preg_match("/Opera\/([0-9\.]+)/i", $agent, $match)) {
                if (version_compare($match[1], $version) >= 0) {
                    return true;
                }
            }
            break;

        case 'Chrome':
            if (preg_match("/Chrome\/(.*)[ ]+/i", $agent, $match)) {
                if (version_compare($match[1], $version) >= 0) {
                    return true;
                }
            }
            break;

        case 'Safari':  /// Safari
            // Look for AppleWebKit, excluding strings with OmniWeb, Shiira and SymbianOS
            if (strpos($agent, 'OmniWeb')) {
                // Reject OmniWeb
                return false;
            } elseif (strpos($agent, 'Shiira')) {
                // Reject Shiira
                return false;
            } elseif (strpos($agent, 'SymbianOS')) {
                // Reject SymbianOS
                return false;
            }
            if (strpos($agent, 'iPhone') or strpos($agent, 'iPad') or strpos($agent, 'iPod')) {
                // No Apple mobile devices here - editor does not work, course ajax is not touch compatible, etc.
                return false;
            }

            if (preg_match("/AppleWebKit\/([0-9]+)/i", $agent, $match)) {
                if (version_compare($match[1], $version) >= 0) {
                    return true;
                }
            }

            break;

    }

    return false;
}



/**
 * ajout�e rev 1.5 975 pour les exports AMC, Moodle
 * ceci permet d'�liminer les caract�res sp�ciaux issus de Windows CP 1252
 * qui trainent toujours dans la BD '
 */
function fix_special_chars ($string) {
	global $CFG;

	if (!empty($CFG->unicodedb)) return $string;

    $table =array( chr(0x92)=>"'",   //apostrophe simple
                   chr(0x96)=>"'",
                   chr(0x9c)=>"oe",
                   chr(0x82)=>" "); // symbole Registered
      foreach ($table  as $c=>$rep)
		$string=str_replace($c,$rep,$string);
	return $string;
}

function is_utf8($string){

	// From http://w3.org/International/questions/qa-forms-utf-8.html
	return preg_match('%^(?:
	[\x09\x0A\x0D\x20-\x7E] # ASCII
	| [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
	| \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
	| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
	| \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
	| \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
	| [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
	| \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
	)*$%xs', $string);
}


/**
 * rev 973 conversion conditionnelle en utF8 si la DB ne l'est pas encore
 */
function to_utf8 ($string) {
    global $CFG;
  //   $bom = "\xef\xbb\xbf";
    if (!empty($CFG->unicodedb)) return $string;
    return utf8_encode($string);
}

/**
 * regarde si une chaine corresponds � un URL (pour les liens des notions)
 */
function is_url ($chaine) {
return
    substr($chaine, 0, 7) == "http://" || substr($chaine, 0, 8) == "https://";
}


/**
 * essaie de normaliser un nom ou un pr�nom
 * avec 1ere lettres en majuscule
 * attention au tirets
 */
function mkjoli($string) {

    global $CFG;
    if (!$CFG->utiliser_mkjoli) return $string;
	$mots=explode (" ",$string);
	for ($i=0; $i<count($mots);$i++){
		$mots2=explode ("-",$mots[$i]);
		for ($j=0; $j<count($mots2);$j++){
			$mots2[$j]=ucfirst(strtolower($mots2[$j]));
		}
		$mots[$i]=join("-",$mots2);
	}
	return join(" ",$mots);
}

/**
*   rev 978
*  PHP validate email
*  http://www.webtoolkit.info/
*
**/

function is_valid_email($email){
    return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
}

/**
 * Returns the maximum size for uploading files.
 *
 * There are seven possible upload limits:
 * 1. in Apache using LimitRequestBody (no way of checking or changing this)
 * 2. in php.ini for 'upload_max_filesize' (can not be changed inside PHP)
 * 3. in .htaccess for 'upload_max_filesize' (can not be changed inside PHP)
 * 4. in php.ini for 'post_max_size' (can not be changed inside PHP)
 * 5. by the Moodle admin in $CFG->maxbytes
 * 6. by the teacher in the current course $course->maxbytes
 * 7. by the teacher for the current module, eg $assignment->maxbytes
 *
 * These last two are passed to this function as arguments (in bytes).
 * Anything defined as 0 is ignored.
 * The smallest of all the non-zero numbers is returned.
 *
 * @param int $sizebytes ?
 * @param int $coursebytes Current course $course->maxbytes (in bytes)
 * @param int $modulebytes Current module ->maxbytes (in bytes)
 * @return int The maximum size for uploading files.
 * @todo Finish documenting this function
 */
function get_max_upload_file_size($sitebytes=0, $coursebytes=0, $modulebytes=0) {

    if (! $filesize = ini_get('upload_max_filesize')) {
        $filesize = '5M';
    }
    $minimumsize = get_real_size($filesize);

    if ($postsize = ini_get('post_max_size')) {
        $postsize = get_real_size($postsize);
        if ($postsize < $minimumsize) {
            $minimumsize = $postsize;
        }
    }

    if ($sitebytes and $sitebytes < $minimumsize) {
        $minimumsize = $sitebytes;
    }

    if ($coursebytes and $coursebytes < $minimumsize) {
        $minimumsize = $coursebytes;
    }

    if ($modulebytes and $modulebytes < $minimumsize) {
        $minimumsize = $modulebytes;
    }

    return $minimumsize;
}

/**
 * Converts numbers like 10M into bytes.
 *
 * @param mixed $size The size to be converted
 * @return mixed
 */
function get_real_size($size=0) {
    if (!$size) {
        return 0;
    }
    $scan['MB'] = 1048576;
    $scan['Mb'] = 1048576;
    $scan['M'] = 1048576;
    $scan['m'] = 1048576;
    $scan['KB'] = 1024;
    $scan['Kb'] = 1024;
    $scan['K'] = 1024;
    $scan['k'] = 1024;

    while (list($key) = each($scan)) {
        if ((strlen($size)>strlen($key))&&(substr($size, strlen($size) - strlen($key))==$key)) {
            $size = substr($size, 0, strlen($size) - strlen($key)) * $scan[$key];
            break;
        }
    }
    return $size;
}

/**
*  alternative a la fonction standard json_encode de php 5.2
*  pour des versions php
*  d'apr�s http://fr.php.net/manual/fr/function.json-encode.php
*
* NE PAS UTILISER CELLE de php qui ne fonctionne qu'avec des donn�es UTF8 !!!!
* cf http://www.php.net/manual/fr/function.json-encode.php
*
* Sept 2010 cette fonction ne marche pas avec des donn�es UTF8 (pb avec strreplace ?)
*/

//if (!function_exists('json_encode'))
//{
  function mon_json_encode($a=false)
  {


    if (is_null($a)) return 'null';
    if ($a === false) return 'false';
    if ($a === true) return 'true';
    if (is_scalar($a))
    {
      if (is_float($a))
      {
        // Always use "." for floats.
        return floatval(str_replace(",", ".", strval($a)));
      }

      if (is_string($a))
      {
       // return $a;
        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
//       static  $bom = "\xef\xbb\xbf";
//        if (strpos($a, $bom) === 0) {
//            $a= substr($a, strlen($bom));
//        }


        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';


      }
      else
        return $a;
    }
    $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a))
    {
      if (key($a) !== $i)
      {
        $isList = false;
        break;
      }
    }
    $result = array();
    if ($isList)
    {
      foreach ($a as $v) $result[] = mon_json_encode($v);
      return '[' . join(',', $result) . ']';
    }
    else
    {
      foreach ($a as $k => $v) $result[] = mon_json_encode($k).':'.mon_json_encode($v);
      return '{' . join(',', $result) . '}';
    }
  }
//}


function get_flag ($drapeau, $valeur) {
    return $drapeau & $valeur;
}

function set_flag (&$drapeau,$valeur,$masque=0) {
        $drapeau = ($drapeau & ~$masque) | $valeur;
}

function unset_flag (&$drapeau, $valeur,$masque=0) {
        $drapeau = ($drapeau & $masque ) & ~$valeur;
}

 /*
	 * @version     1.3.2
	 * @author      Aidan Lister < aidan@php.net>
	 * @author      Peter Waller < iridum@php.net>
	 * @link        http://aidanlister.com/repos/v/function.hexdump.php
	 * @param       string  $data        The string to be dumped
	 * @param       bool    $htmloutput  Set to false for non-HTML output
	 * @param       bool    $uppercase   Set to true for uppercase hex
	 * @param       bool    $return      Set to true to return the dump
	 */
	function hexdump($data, $htmloutput = true, $uppercase = false, $return = false)
	{
	    // Init
	    $hexi   = '';
	    $ascii  = '';
	    $dump   = ($htmloutput === true) ? '<pre>' : '';
	    $offset = 0;
	    $len    = strlen($data);

	    // Upper or lower case hexidecimal
	    $x = ($uppercase === false) ? 'x' : 'X';

	    // Iterate string
	    for ($i = $j = 0; $i < $len; $i++)
	    {
	        // Convert to hexidecimal
	        $hexi .= sprintf("%02$x ", ord($data[$i]));

	        // Replace non-viewable bytes with '.'
	        if (ord($data[$i]) >= 32) {
	            $ascii .= ($htmloutput === true) ?
	                            htmlentities($data[$i]) :
	                            $data[$i];
	        } else {
	            $ascii .= '.';
	        }

	        // Add extra column spacing
	        if ($j === 7) {
	            $hexi  .= ' ';
	            $ascii .= ' ';
	        }

	        // Add row
	        if (++$j === 16 || $i === $len - 1) {
	            // Join the hexi / ascii output
	            $dump .= sprintf("%04$x  %-49s  %s", $offset, $hexi, $ascii);

	            // Reset vars
	            $hexi   = $ascii = '';
	            $offset += 16;
	            $j      = 0;

	            // Add newline
	            if ($i !== $len - 1) {
	                $dump .= "\n";
	            }
	        }
	    }

	    // Finish dump
	    $dump .= $htmloutput === true ? '</pre>' : '';
	    $dump .= "\n";

	    // Output method
	if ($return === false)
		{
	      	echo $dump;
	    	}
	else	{
	      	return $dump;
	    	}
	}
