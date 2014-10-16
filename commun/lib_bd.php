<?php

/**
 * @author Patrick Pollet
 * @version $Id: lib_bd.php 1263 2011-09-19 17:04:33Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
/*
 * bibliotheque d'acces à la BD
 * a inclure AVANT les autres (voir c2i_params)
 * trés fortement inspirée de la bibliothèque datalib de Moodle
 * mais simplifiée ( chaque opération n'a qu'un critère & créér par l'appelant
 * NE PAS FAIRE DE MAJ BD ICI !
 * 
 * 
 * OCTOBRE 2014 réécrite en utilisant la bibliothèque mysqli de PHP puisque mysql est desormais décpréciée
 * à partir de PHP 5.5
 */
/**
 * globale utilisée partout
 */

$connexion = Connexion($base_utilisateur, $base_mdp, $ines_base, $adresse_serveur,$mysql_names);
/**
 * fonction "interne"
 */
function __envoi_erreur_fatale($errMsg1, $errMsg2, $sql = "") {
    global $CFG,$connexion;
    //print("sql=".$sql);

    if (empty ($CFG->err_mysql_sans_mysqlerror)) // rev 896
        if ($errMsg1 == "" || $errMsg1 == "err_mysql") {
            $errMsg1 = mysqli_error($connexion);
        }
    if (!empty ($CFG->err_mysql_avec_requete)) //rev 896   empty n'est pas pariel que isset !
        $errMsg1 .= "<br/>" . $sql;
    //print $errMsg2;
    if (isset ($CFG->log_erreur_fatale))
        if (function_exists('espion2')) // erreur fatale durant maj bd ...
            espion2("erreur_fatale", $errMsg1, $errMsg2);
    error_log("SQL " . mysqli_error($connexion) . " STATEMENT: " . $sql);
    if (class_exists('TemplatePower'))
        erreur_fatale($errMsg1, $errMsg2);
    else
        die($errMsg1 . " " . $errMsg2);
}
// Fonction Connexion: connexion � MySQL

/**
 * 
 * @param unknown_type $pNom
 * @param unknown_type $pMotPasse
 * @param unknown_type $pBase
 * @param unknown_type $pServeur
 * @param unknown_type $pNames
 * @param unknown_type $newLink  NON SUPPORTE avec MySQLi
 */
function Connexion($pNom, $pMotPasse, $pBase, $pServeur, $pNames='',$newLink=false) { // Connexion au serveur
    //$connexion = mysql_connect($pServeur, $pNom, $pMotPasse,$newLink);
	$connexion = mysqli_connect($pServeur, $pNom, $pMotPasse);
    if (!$connexion) {
        __envoi_erreur_fatale(mysqli_connect_error(), $pServeur);
    }
    // Connexion � la base
   // if (!mysql_select_db($pBase, $connexion)) {
    if (!mysqli_select_db($connexion, $pBase)) {
        __envoi_erreur_fatale("", "err_mysql_bd", mysqli_error($connexion));
    }
    //ExecRequete("set names latin1",$connexion);
    //ExecRequete("set names utf8",$connexion);
    if (!empty($pNames))
        ExecRequete ("set names $pNames",$connexion);

    // On renvoie la variable de connexion
    return $connexion;
}

/**
 *
 *Ex�cution d'une requ�te avec MySQL style V1.4
 *@param $requete le sql
 *@param $conn    la connexion BD, si manque prends la connexion globale presque toujours sauf maj.php et export_mysql !
 *@param $erreur  erreur_fatale ou non
*/
function ExecRequete($requete, $conn = false, $erreur = true) {
    global $connexion;
    if (!$conn)
        $conn = $connexion;
    //$resultat = mysql_query($requete, $conn);
    $resultat = mysqli_query($conn, $requete);
    if ($resultat) {
        return $resultat;
    } else {
        if ($erreur) {
            __envoi_erreur_fatale("", "", $requete);
        }
    }
}
// Recherche de la ligne suivante
function LigneSuivante($resultat) {
    return mysqli_fetch_object($resultat);
}
function NombreLignes($resultat) {
    return mysqli_num_rows($resultat);
}
function RechercheDonnee($resultat, $ligne) {
    return mysqli_data_seek($resultat, $ligne);
}

/**
 * point de passage oblig� des fonctions get_xxxx des lib_xxxxx
 * @param string $sql	la r�qu�te
 * @param
 */
function get_record_sql($sql, $die = 1, $errMsg1 = "", $errMsg2 = "") {
    //si die est vrai on envoie l'erreur fatale personnalis�e ici '
    if ($resultat = ExecRequete($sql, false, false))
        if ($ligne = LigneSuivante($resultat))
            return stripslashes_object($ligne);
    if ($die) {
        __envoi_erreur_fatale($errMsg1, $errMsg2, $sql);
    } else
        return false;
}
/**
 * point de passage oblig� des fonctions get_xxxx des lib_xxxxx
 * @param string $sql	la r�qu�te
 * @param
 */
function get_records_sql($sql, $die = 0, $errMsg1 = "", $errMsg2 = "") {
    //print $sql;
    if ($resultat = ExecRequete($sql, false, false)) {
        if (NombreLignes($resultat) == 0) {
            if ($die)
                __envoi_erreur_fatale($errMsg1, $errMsg2, $sql);
            else
                return array (); // un tableau vide
            // mieux que return false car derriere on fait souvent
            // un foreach qui sera donc sans cons�quence
            // NI notice/warning PHP du genre
            //Warning: Invalid argument supplied for foreach()
            //in /var/www/c2i/V1.4/plate-forme/commun/noteuse.class.php on line 153
        }
        $ret = array ();
        while ($ligne = LigneSuivante($resultat))
            $ret[] = stripslashes_object($ligne);
        return $ret;
    } else
        __envoi_erreur_fatale("err_mysql", $errMsg2, $sql); // force erreur my_sql
    // ca arrivera

}

function count_records_sql($sql, $die = 0, $errMsg1 = "", $errMsg2 = "") {
    $res = get_records_sql($sql, $die, $errMsg1, $errMsg2);
    return count($res);
}

function get_record($table, $critere = "", $die = 1, $errMsg1 = "", $errMsg2 = "") {
    global $CFG;
    if (empty ($critere))
        $critere = "1"; //surprenant ...
    $sql = "select * from {$CFG->prefix}$table where $critere";
    //print $sql;
    return get_record_sql($sql, $die, $errMsg1, $errMsg2);
}

//similaire a get_record_select de Moodle
//pb ici avec la relecture de la config CFG->prefix n'est pas encore renseign� ...'
function get_records($table, $critere = '', $tri = '', $debut = 0, $nombre = 0, $die = 0, $errMsg1 = "", $errMsg2 = "") {
    global $CFG;
    $sql = "select * from " . $CFG->prefix . "$table ";
    if (!empty ($critere))
        $sql .= " where " . $critere;
    if ($tri)
        $sql .= " order by $tri ";
    if ($debut || $nombre) {
        $sql .= " limit $debut,";
        $sql .= $nombre;
    }
    //print $sql;
    return get_records_sql($sql, $die, $errMsg1, $errMsg2);
}

function count_records($table, $critere = "", $die = 0, $errMsg1 = "", $errMsg2 = "") {
    global $CFG;

    if (empty ($critere))
        $critere = "1";

    $sql = "select count(*) as __nb__ from  {$CFG->prefix}$table where $critere";

    // print $sql;
    if ($res = get_record_sql($sql, 0)) // 1 seul
        return $res->__nb__;
    if ($die) {
        __envoi_erreur_fatale($errMsg1, $errMsg2, $sql);
    } else
        return 0;
}
function delete_records($table, $critere = "", $die = 0, $errMsg1 = "", $errMsg2 = "") {
    global $CFG;
    $sql = "delete from  {$CFG->prefix}$table";
    if (!empty ($critere))
        $sql .= " where " . $critere;
    if ($resultat = ExecRequete($sql, false, false))
        return true;
    else
        if ($die)
            __envoi_erreur_fatale($errMsg1, $errMsg2, $sql);
        else
            return false;
}
/**
* Insert a record into a table and return the "id" field if required
*
* If the return ID isn't required, then this just reports success as true/false.
* $dataobject is an object containing needed data
*
* @uses $connexion
* @uses $CFG
* @param string $table The database table to be checked against.
* @param array $dataobject A data object with values for one or more fields in the record
* @param bool $returnid Should the id of the newly created record entry be returned? If this option is not requested then true/false is returned.
* @param string $primarykey The primary key of the table we are inserting into (almost always "id")
*/
function insert_record($table, $dataobject, $returnid = true, $primarykey = 'id', $die = 1, $errMsg1 = "", $errMsg2 = "") {
    global $CFG, $connexion;
    if (!$dataobject || (!is_object($dataobject) && !is_array($dataobject)))
        __envoi_erreur_fatale("DEV : tentative d'ins�rer un non objet dans ", "$table", print_r($dataobject, true));
    /// In Moodle we always use auto-numbering fields for the primary key
    /// so let's unset it now before it causes any trouble later
    if ($primarykey) //pas encore partout avec la PF c2i
        unset ($dataobject-> {
        $primarykey });
    /// Get the correct SQL from adoDB
    /// PP j'ai reecrit la fonction (simplifie� ici!)
    if (!$insertSQL = get_insert_sql($table, (array) $dataobject)) {
        __envoi_erreur_fatale("DEV : SQL invalide en insertion dans ", $table, print_r($dataobject, true));
    }
    //print($insertSQL);
    //return;
    /// Run the SQL statement
    if (!$rs = ExecRequete($insertSQL, false, false)) {
        if ($die)
            __envoi_erreur_fatale($errMsg1, $errMsg2, $insertSQL);
        else
            return false;
    }
    /// If a return ID is not needed then just return true now (but not in MSSQL DBs, where we may have some pending tasks)
    if (!$returnid) {
        return true;
    }
    /// This only gets triggered with MySQL and MSQL databases
    /// however we have some postgres fallback in case we failed
    /// to find the sequence.
    $id = mysqli_insert_id($connexion);
    return $id;
}

/**
 * Update a record in a table
 *
 * $dataobject is an object containing needed data
 * Relies on $dataobject having a variable "id" to
 * specify the record to update
 *
 * @uses $CFG
 * @uses $connexion
 * @param string $table The database table to be checked against.
 * @param array $dataobject An object with contents equal to fieldname=>fieldvalue.
 *   Must have an entry for 'id' to map to the table specified.
 * @return bool
/// PP en Moodle TOUTE table a une cl� id (autonum en 1er)
/// ce qui n'est pas TOUJOURS le cas de la PF
/// donc j'ai ajout� � update_record DEUX param�tres optionnels key1 et key2
/// pour g�rer le where


 */
function update_record($table, $dataobject, $key1 = 'id', $key2 = '', $die = 1, $errMsg1 = "", $errMsg2 = "") {
    global $CFG;
    // la valeur de la cle primaire DOIT etre dans l'objet
    if (!isset ($dataobject-> $key1)) {
        __envoi_erreur_fatale("DEV: cle manquante update_record", print_r($dataobject, true), $key1);
    }
    $where = " WHERE $key1 = '" . $dataobject-> $key1 . "' ";
    unset ($dataobject-> $key1);
    // idem pour l'autre crit�re si pr�sent ...
    if (!empty ($key2)) {
        if (!isset ($dataobject-> $key2)) {
            return false;
        }
        $where .= " AND $key2='" . $dataobject-> $key2 . "' ";
        unset ($dataobject-> $key2);
    }
    $data = (array) $dataobject;
    //print_r($data);
    // Construct SQL queries
    $numdata = count($data);
    $count = 0;
    $update = '';
    if ($numdata) {
        foreach ($data as $key => $value) {
            $count++;
            $value = addslashes($value);
            $update .= $key . ' = \'' . $value . '\'';
            if ($count < $numdata) {
                $update .= ', ';
            }
        }
        $sql = 'UPDATE ' . $CFG->prefix . $table . ' SET ' . $update . $where;
        //print $sql;
        //return;
        if (!$rs = ExecRequete($sql, false, false)) {
            if ($die)
                __envoi_erreur_fatale($errMsg1, $errMsg2, $sql);
            else
                return false;
        }
    } else {
        __envoi_erreur_fatale("DEV: rien a updater", print_r($dataobject, true));
    }
    return true;
}

/**
 * Get a single value from a table row where all the given fields match the given values.
 * modif PP un seul argument $critere (de la form  "xx='valeur' and yy ='valeur'....)
 * pour �tre homog�ne avec get_record
 * @param string $table the table to query.
 * @param string $field the field to return the value of.
 * @param int $die , $errMsg1, $errMsg2
 * @return mixed the specified value, or false if an error occured.
 */
function get_field($table, $field, $critere, $die = 0, $errMsg1 = "", $errMsg2 = "") {
    global $CFG;
    return get_field_sql($field, 'SELECT ' . $field . ' FROM ' . $CFG->prefix . $table . ' where ' . $critere, $die, $errMsg1, $errMsg2);
}

/**
 * Get a single value from a table.
 *
 * @param string $sql an SQL statement expected to return a single value.
 * @return mixed the specified value, or false if an error occured.
 */
function get_field_sql($field, $sql, $die = 0, $errMsg1 = "", $errMsg2 = "") {
    global $CFG;
    $rs = get_record_sql($sql, $die, $errMsg1, $errMsg2);
    if ($rs)
        return $rs-> $field;
    else
        return false;
}
/**
 * Set a single field in every table row where all the given fields match the given values.
 *
 * @uses $CFG
 * @uses $connexion
 * @param string $table The database table to be checked against.
 * @param string $field the field to set.
 * @param string $newvalue the value to set the field to.
 * @param string $critere.
 * ^param $die ...
 * @return mixed An ADODB RecordSet object with the results from the SQL call or false.
 */
function set_field($table, $field, $newvalue, $critere, $die = 0, $errMsg1 = "", $errMsg2 = "") {
    return set_field_select($table, $field, $newvalue, $critere, $die, $errMsg1, $errMsg2);
}
/**
 * Set a single field in every table row where the select statement evaluates to true.
 *
 * @uses $CFG
 * @uses $connexion
 * @param string $table The database table to be checked against.
 * @param string $field the field to set.
 * @param string $newvalue the value to set the field to.
 * @param string $select a fragment of SQL to be used in a where clause in the SQL call.
 * @param boolean $localcall Leave this set to false. (Should only be set to true by set_field.)
 * @return true ou false ou erreurfatale si $die.
 */
function set_field_select($table, $field, $newvalue, $critere, $die = 0, $errMsg1 = "", $errMsg2 = "") {
    global $CFG;
    $newvalue = addslashes($newvalue);
    $sql =<<<EOS
                update {$CFG->prefix}$table
                set $field='$newvalue'
                where $critere
EOS;
    if (!$rs = ExecRequete($sql, false, false)) {
        if ($die)
            __envoi_erreur_fatale($errMsg1, $errMsg2, $sql);
        else
            return false;
    }
    return true;

}

/**
 * PP
 * inspiree de moodle/lib/adodb/adodb-lib.inc.php
 * gere les caracteres interdit par addslashes !!!!
 * mais il faudra les oter ensuite ???? (voir get_record)
 ***/
function get_insert_sql($table, $dataobject) {
    global $CFG;
    // Strip off the comma and space on the end of both the fields
    // and their values.
    $fields = $values = '';
    foreach ($dataobject as $field => $value) {
        $fields .= "$field ,";
        $value = addslashes($value);
        $values .= "'$value' ,";
    }
    $fields = substr($fields, 0, -2);
    $values = substr($values, 0, -2);
    if ($fields && $values) // Append the fields and their values to the insert query.
        return 'INSERT INTO ' . $CFG->prefix . $table . ' ( ' . $fields . ' ) VALUES ( ' . $values . ' )';
    else
        return false;
}

function mysql_table_exists($table, $autre_conn = null) {
    global $connexion, $CFG;
    $conn = $autre_conn ? $autre_conn : $connexion;
    $sql =<<<EOS
		show tables like '{$CFG->prefix}$table'
EOS;
    if ($res = ExecRequete($sql, $conn, 0))
        return NombreLignes($res) == 1;
    else
        return false;
}

function mysql_column_exists($colonne, $table, $autre_conn = null) {
    global $connexion, $CFG;
    $conn = $autre_conn ? $autre_conn : $connexion;
    $sql =<<<EOS
        show columns from {$CFG->prefix}$table like '$colonne'
EOS;
    if ($res = ExecRequete($sql, $conn, 0))
        return NombreLignes($res) == 1;
    else
        return false;
}

function mysql_add_column($colonne, $attrs = "VARCHAR( 255 ) NULL", $table, $autre_conn = null) {
    global $connexion, $CFG;
    $table = $CFG->prefix . $table;
    $conn = $autre_conn ? $autre_conn : $connexion;
    $sql = "ALTER TABLE `$table` ADD `$colonne`  $attrs";
    ExecRequete($sql, $connexion);
}

function mysql_add_column_if_not_exist($colonne, $attrs = "VARCHAR( 255 ) NULL", $table, $autre_conn = null) {
    if (!mysql_column_exists($colonne, $table, $autre_conn)) {
        mysql_add_column($colonne, $attrs, $table, $autre_conn);
    }
}



/**
 * Liste d'entit�s contenant un ou plusieurs TAGS
 * code inspir� de la recherche multicrit�res des cours de Moodle
 *
 * @param string table (nom de la table SANS le prefixe)
 * @param string $search mots a chercher dans les tags , eventuellement expressions r�guli�res etpr�ced�s de + ou -
 * @param string $sort tri
 * @param int $page ?
 * @param int $recordsperpage ?
 * @return object records
 */
function search_table_bytags($table,$search, $sort='', $page=0, $recordsperpage=-1, &$totalcount=null) {

    global $CFG;
    $LIKE = 'LIKE';
   // $NOTLIKE = 'NOT LIKE';
    $REGEXP = 'REGEXP';
    $NOTREGEXP = 'NOT REGEXP';

    $fullsearch = '';

    $searchterms = explode(" ", $search); // Search for words independently
    foreach ($searchterms as $key => $searchterm) {
        if (strlen($searchterm) < 2) {
            unset ($searchterms[$key]);
        }
    }

    foreach ($searchterms as $searchterm) {
        if ($fullsearch) {
            $fullsearch .= ' AND ';
        }

        if (substr($searchterm,0,1) == '+') {
            $searchterm      = substr($searchterm,1);
            $fullsearch .= " tags $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";

        } else if (substr($searchterm,0,1) == "-") {
            $searchterm      = substr($searchterm,1);
            $fullsearch .= " tags $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
        } else {
            $fullsearch .= ' tags '. $LIKE .' \'%'. $searchterm .'%\' ';

        }
    }

    if (empty($fullsearch)) $fullsearch='1=1';

    $sql = "SELECT *
        FROM {$CFG->prefix}$table
        WHERE ($fullsearch )";
    if ($sort)
        $sql .=" ORDER BY " . $sort;
//print $sql;
    $ret = array();
    $c=0;

    if ($rs = get_records_sql($sql)) {
	    //	print_r($rs);
	    if ($recordsperpage !=-1) {
	    	//print "rpp-1".$recordsperpage;
		    // Tiki pagination
		    $limitfrom = $page * $recordsperpage;
		    $limitto   = $limitfrom + $recordsperpage;
		    foreach ($rs as $row) {
			    // Don't exit this loop till the end
			    // we need to count all the visible courses
			    // to update $totalcount
			    if ($c >= $limitfrom && $c < $limitto) {
				    $ret[] = $row;
			    }
			    $c++;
		    }
	    } else {
	    	//print "rpp est -1".$recordsperpage;
		    $ret=$rs;
		    $c=count($ret);
		    // print "c=".$c;
	    }
    }//else print "tilt";

    // our caller expects 2 bits of data - our return
    // array, and an updated $totalcount
   if (isset($totalcount))  $totalcount = $c;
  // print_r($ret); print $c;
    return $ret;
}
