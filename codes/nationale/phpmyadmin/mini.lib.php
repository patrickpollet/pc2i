<?php

/**
 * juste le minimum pour que l'export phpmyadmin fonctionne ...'
 */

$mysql_version =get_record_sql('SELECT VERSION() as V');
$mysql_version=$mysql_version->V;
if ( $mysql_version ) {
	$match = explode('.', $mysql_version);
	define('PMA_MYSQL_INT_VERSION',
		(int) sprintf('%d%02d%02d', $match[0], $match[1],
			intval($match[2])));
	define('PMA_MYSQL_STR_VERSION', $mysql_version);
	unset($mysql_version, $match);
} else {
	define('PMA_MYSQL_INT_VERSION', 32332);
	define('PMA_MYSQL_STR_VERSION', '3.23.32');
}

checkPhpVersion();
/**
 * detects PHP version
 */
function checkPhpVersion()	{
	$match = array();
	if (! preg_match('@([0-9]{1,2}).([0-9]{1,2}).([0-9]{1,2})@',
		phpversion(), $match)) {
		$result = preg_match('@([0-9]{1,2}).([0-9]{1,2})@',
			phpversion(), $match);
	}
	if (isset($match) && ! empty($match[1])) {
		if (! isset($match[2])) {
			$match[2] = 0;
		}
		if (! isset($match[3])) {
			$match[3] = 0;
		}
		define('PMA_PHP_INT_VERSION',
			(int) sprintf('%d%02d%02d', $match[1], $match[2], $match[3]));
	} else {
		define('PMA_PHP_INT_VERSION', 0);
	}
	define('PMA_PHP_STR_VERSION', phpversion());
}

/**
 * Get supported SQL compatibility modes
 *
 * @return  array   supported SQL compatibility modes
 */
function PMA_DBI_getCompatibilities(){
    if (PMA_MYSQL_INT_VERSION < 40100) {
        return array();
    }
    $compats = array('NONE');
    if (PMA_MYSQL_INT_VERSION >= 40101) {
        $compats[] = 'ANSI';
        $compats[] = 'DB2';
        $compats[] = 'MAXDB';
        $compats[] = 'MYSQL323';
        $compats[] = 'MYSQL40';
        $compats[] = 'MSSQL';
        $compats[] = 'ORACLE';
        // removed; in MySQL 5.0.33, this produces exports that
        // can't be read by POSTGRESQL (see our bug #1596328)
        //$compats[] = 'POSTGRESQL';
        if (PMA_MYSQL_INT_VERSION >= 50002) {
            $compats[] = 'TRADITIONAL';
        }
    }
    return $compats;
}


/**
 * et hop c'est par ici que ca se passe ...
 */
function PMA_DBI_Query($sql,$tmp1='',$tmp2='') {
    return ExecRequete($sql);
}


function PMA_DBI_num_rows ($resultat) {
    return NombreLignes($resultat);
}

/**
 * inutile 
 */
function PMA_convert_display_charset($texte) {
    return $texte;
}

// The following function is meant for internal use only.
// Do not call it from outside this library!
function PMA_mysql_fetch_array($result, $type = FALSE) {
    global $cfg, $allow_recoding, $charset, $convcharset;

    if ($type != FALSE) {
        $data = mysql_fetch_array($result, $type);
    } else {
        $data = mysql_fetch_array($result);
    }

    /* No data returned => do not touch it */
    if (! $data) {
        return $data;
    }

    if (!defined('PMA_MYSQL_INT_VERSION') || PMA_MYSQL_INT_VERSION >= 40100
        || !(isset($cfg['AllowAnywhereRecoding']) && $cfg['AllowAnywhereRecoding'] && $allow_recoding)) {
        /* No recoding -> return data as we got them */
        return $data;
    } else {
        $ret = array();
        $num = mysql_num_fields($result);
        $i = 0;
        for ($i = 0; $i < $num; $i++) {
            $name = mysql_field_name($result, $i);
            $flags = mysql_field_flags($result, $i);
            /* Field is BINARY (either marked manually, or it is BLOB) => do not convert it */
            if (stristr($flags, 'BINARY')) {
                if (isset($data[$i])) {
                    $ret[$i] = $data[$i];
                }
                if (isset($data[$name])) {
                    $ret[PMA_convert_display_charset($name)] = $data[$name];
                }
            } else {
                if (isset($data[$i])) {
                    $ret[$i] = PMA_convert_display_charset($data[$i]);
                }
                if (isset($data[$name])) {
                    $ret[PMA_convert_display_charset($name)] = PMA_convert_display_charset($data[$name]);
                }
            }
        }
        return $ret;
    }
}

function PMA_DBI_fetch_array($result) {
    return PMA_mysql_fetch_array($result);
}

function PMA_DBI_fetch_assoc($result) {
    return PMA_mysql_fetch_array($result, MYSQL_ASSOC);
}

function PMA_DBI_fetch_row($result) {
    return PMA_mysql_fetch_array($result, MYSQL_NUM);
}

function PMA_DBI_num_fields($result) {
    return mysql_num_fields($result);
}


function PMA_DBI_field_len($result, $i) {
    return mysql_field_len($result, $i);
}

function PMA_DBI_field_name($result, $i) {
    return mysql_field_name($result, $i);
}

function PMA_DBI_field_flags($result, $i) {
    return PMA_convert_display_charset(mysql_field_flags($result, $i));

}

/**
 * @todo add missing keys like in from mysqli_query (orgname, orgtable, flags, decimals)
 */
function PMA_DBI_get_fields_meta($result) {
    $fields       = array();
    $num_fields   = mysql_num_fields($result);
    for ($i = 0; $i < $num_fields; $i++) {
        $fields[] = PMA_convert_display_charset(mysql_fetch_field($result, $i));
    }
    return $fields;
}

/**
 * Frees the memory associated with the results
 *
 * @param result    $result,...     one or more mysql result resources
 */
function PMA_DBI_free_result() {
    foreach ( func_get_args() as $result ) {
        if ( is_resource($result)
          && get_resource_type($result) === 'mysql result' ) {
            mysql_free_result($result);
        }
    }
}


/**
 * Adds backquotes on both sides of a database, table or field name.
 * and escapes backquotes inside the name with another backquote
 *
 * <code>
 * echo PMA_backquote('owner`s db'); // `owner``s db`
 * </code>
 *
 * @param   mixed    $a_name    the database, table or field name to "backquote"
 *                              or array of it
 * @param   boolean  $do_it     a flag to bypass this function (used by dump
 *                              functions)
 * @return  mixed    the "backquoted" database, table or field name if the
 *                   current MySQL release is >= 3.23.6, the original one
 *                   else
 * @access  public
 */
function PMA_backquote($a_name, $do_it = true) 	{
	if (! $do_it) {
		return $a_name;
	}

	if (is_array($a_name)) {
		$result = array();
		foreach ($a_name as $key => $val) {
			$result[$key] = PMA_backquote($val);
		}
		return $result;
	}

	// '0' is also empty for php :-(
	if (strlen($a_name) && $a_name !== '*') {
		return '`' . str_replace('`', '``', $a_name) . '`';
	} else {
		return $a_name;
	}
	} // end of the 'PMA_backquote()' function

/**
 * Add slashes before "'" and "\" characters so a value containing them can
 * be used in a sql comparison.
 *
 * @param   string   the string to slash
 * @param   boolean  whether the string will be used in a 'LIKE' clause
 *                   (it then requires two more escaped sequences) or not
 * @param   boolean  whether to treat cr/lfs as escape-worthy entities
 *                   (converts \n to \\n, \r to \\r)
 *
 * @param   boolean  whether this function is used as part of the
 *                   "Create PHP code" dialog
 *
 * @return  string   the slashed string
 *
 * @access  public
 */

//modif PP 03/07/2009 pb avec les slashes dans les nouvelles questions stockées comme \' et restituées comme \\'' 
// ce qui plante les SQL insert ... on vire les \ qui sont inscrits en base 

function PMA_sqlAddslashes($a_string = '', $is_like = false, $crlf = false, $php_code = false) 	{
	if ($is_like) {
		$a_string = str_replace('\\', '\\\\\\\\', $a_string);
	} else {
		//$a_string = str_replace('\\', '\\\\', $a_string);   NON !!! 
                $a_string = str_replace('\\', '', $a_string);
	}

	if ($crlf) {
		$a_string = str_replace("\n", '\n', $a_string);
		$a_string = str_replace("\r", '\r', $a_string);
		$a_string = str_replace("\t", '\t', $a_string);
	}

	if ($php_code) {
		$a_string = str_replace('\'', '\\\'', $a_string);
	} else {
		$a_string = str_replace('\'', '\'\'', $a_string);
	}

	return $a_string;
} // end of the 'PMA_sqlAddslashes()' function

/**
 * removes quotes (',",`) from a quoted string
 *
 * checks if the sting is quoted and removes this quotes
 *
 * @param   string  $quoted_string  string to remove quotes from
 * @param   string  $quote          type of quote to remove
 * @return  string  unqoted string
 */
function PMA_unQuote($quoted_string, $quote = null){
	$quotes = array();

	if (null === $quote) {
		$quotes[] = '`';
		$quotes[] = '"';
		$quotes[] = "'";
	} else {
		$quotes[] = $quote;
	}

	foreach ($quotes as $quote) {
		if (substr($quoted_string, 0, 1) === $quote
			&& substr($quoted_string, -1, 1) === $quote ) {
			$unquoted_string = substr($quoted_string, 1, -1);
			// replace escaped quotes
			$unquoted_string = str_replace($quote . $quote, $quote, $unquoted_string);
			return $unquoted_string;
		}
	}

	return $quoted_string;
}

?>
