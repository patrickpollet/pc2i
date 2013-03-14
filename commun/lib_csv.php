<?php


/**
 * @author Patrick Pollet
 * @version $Id: lib_csv.php 1099 2010-06-20 19:35:55Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/*
 * taches de maintenance
 * n'est pas chargées dans les pages courantes
 * utiliser require_once quand nécessaire
 */
if (is_admin()) { //utilisateur courant uniquement
    maj_bd_csv();
}

function maj_bd_csv() {
    global $CFG, $USER;
}

/**
 * toujours dans fonctions_divers tant que tout n'utilise pas CsvExporter
 */
if (0) {

/**
 * convertit une valeur a emettre en virant d'evnetuels blancs, saut de lignes, tab ...
 * et le séparateur CSV'
 */
function to_csv ($chaine) {
    global $CFG;
    return str_replace($CFG->csv_separateur,"",clean($chaine,strlen($chaine)));

}

/**
* prepare une ligne CSV
* @param colonnes tableau des noms de colonnes dans la BD ou autre dans l'ordre voulu
* @param ligne un objet contenant les infos à emettre
*                si false, on emet la ligne d'entéte en traduisant les noms de colonnes
* @param $cvt (rev 820) un tableua de booleens pour la conversion point en virgule décimale
*/
function ligne_to_csv ($colonnes, $ligne,$cvt=array(),$cvt_utf8=false) {
    global $CFG;
    $ret="";
    $cpt=0;
    if ($ligne) {
        foreach ($colonnes as $nom) {
        	$info=$ligne->$nom;
            if (!empty($cvt[$cpt])) $info=note_xls($info);
            else 
            	if ($cvt_utf8)
            		$info=to_utf8($info);
            if ($cpt)
                $ret .= "$CFG->csv_separateur".to_csv($info);
            else
                $ret .= to_csv($info);
            $cpt++;
        }
    } else {
        foreach($colonnes as $nom) {
        	$info=traduction ($nom, false);
        	if ($cvt_utf8)
            		$info=to_utf8($info);
            if ($cpt)
                $ret.="$CFG->csv_separateur".to_csv ($info);
            else
                $ret.=to_csv ($info);
            $cpt++;
        }
    }
    return $ret;  //on ne met pas le \n pour pouvoir la completer plus tard
}

}

class CsvExporter {
    // 1ere ligne csv a traduire
    var  $entete_csv = array ();
    // ligne suivantes les noms des attributs dans $ligne dans cet ordre
    var  $ligne_csv = array ();
    // rev 948 conversion numérique du score
    var  $ligne_cvt = array ();
    var $filename_csv='';
    var $row=0;
    var $fp;
    var $cvt_utf8;



    function CsvExporter ($filename_csv,$entete_csv,$ligne_csv,$ligne_cvt,$cvt_utf8=false) {

        global $CFG;
        $this->filename_csv=$filename_csv;
        $this->entete_csv=$entete_csv;
        $this->ligne_csv=$ligne_csv;
        $this->ligne_cvt=$ligne_cvt;
        // forcer conversion utf8 ( Moodle, AMC ...)
        $this->cvt_utf8=$cvt_utf8 && (!$CFG->unicodedb);

        $this->fp = fopen("{$CFG->chemin_ressources}/csv/$filename_csv", "w");
        if (!empty($this->entete_csv)) {
            fputs($this->fp, ligne_to_csv( $this->entete_csv,false)."\n");
            $this->row = 1;
        }
    }

    /**
     * ajoute un objet que l'on met en forme
     */
    function add_ligne($ligne) {
    	global $CFG;
    	// rev 975 pour les exports Moodle ou AMC
        fputs($this->fp, ligne_to_csv($this->ligne_csv,$ligne,$this->ligne_cvt,$this->cvt_utf8)."\n");
        $this->row++;
    }

    /**
     * ajoute une ligne déja formatée
     */
    function add_comment($ligne) {
    	global $CFG;
    	// rev 975 pour les exports Moodle ou AMC
    	if ($this->cvt_utf8)
    		$ligne=to_utf8($ligne);
        fputs($this->fp, $ligne."\n");
        $this->row++;
    }

    function close () {
        global $CFG;
       fclose($this->fp);
       return $CFG->chemin_ressources.'/csv/'.$this->filename_csv;
    }
}

?>
