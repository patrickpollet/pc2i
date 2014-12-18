<?php
/**
 * @version $Id: weblib.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
* cette bibliotheque contient les fonctions
* elle est fortement inspir�e de la biblioth�que weblib de Moodle
* elle est automatiquement incluse dans les pages via c2iparams.php
*
* V2 les fonctions "speciales" a� un module devraient �tre placees
* dans un script lib.php dans le dossier du module et non dans commun
*
*
*/

/**
 * PARAM_RAW specifies a parameter that is not cleaned/processed in any way;
 * originally was 0, but changed because we need to detect unknown
 * parameter types and swiched order in clean_param().
 */
define('PARAM_RAW', 666);

/**
 * PARAM_CLEAN - obsoleted, please try to use more specific type of parameter.
 * It was one of the first types, that is why it is abused so much ;-)
 */
define('PARAM_CLEAN',    0x0001);

/**
 * PARAM_INT - integers only, use when expecting only numbers.
 */
define('PARAM_INT',      0x0002);

/**
 * PARAM_INTEGER - an alias for PARAM_INT
 */
define('PARAM_INTEGER',  0x0002);

/**
 * PARAM_ALPHA - contains only english letters.
 */
define('PARAM_ALPHA',    0x0004);

/**
 * PARAM_ACTION - an alias for PARAM_ALPHA, use for various actions in formas and urls
 * @TODO: should we alias it to PARAM_ALPHANUM ?
 */
define('PARAM_ACTION',   0x0004);

/**
 * PARAM_FORMAT - an alias for PARAM_ALPHA, use for names of plugins, formats, etc.
 * @TODO: should we alias it to PARAM_ALPHANUM ?
 */
define('PARAM_FORMAT',   0x0004);

/**
 * PARAM_NOTAGS - all html tags are stripped from the text. Do not abuse this type.
 */
define('PARAM_NOTAGS',   0x0008);

 /**
 * PARAM_MULTILANG - alias of PARAM_TEXT.
 */
define('PARAM_MULTILANG',  0x0009);

 /**
 * PARAM_TEXT - general plain text compatible with multilang filter, no other html tags.
 */
define('PARAM_TEXT',  0x0009);

/**
 * PARAM_FILE - safe file name, all dangerous chars are stripped, protects against XSS, SQL injections and directory traversals
 */
define('PARAM_FILE',     0x0010);

/**
 * PARAM_CLE_C2I - un nombre(id_etab), un point et un autre nombre
 */
define('PARAM_CLE_C2I',     0x0011);


/**
 * PARAM_PATH - safe relative path name, all dangerous chars are stripped, protects against XSS, SQL injections and directory traversals
 * note: the leading slash is not removed, window drive letter is not allowed
 */
define('PARAM_PATH',     0x0020);

/**
 * PARAM_HOST - expected fully qualified domain name (FQDN) or an IPv4 dotted quad (IP address)
 */
define('PARAM_HOST',     0x0040);

/**
 * PARAM_URL - expected properly formatted URL.
 */

define('PARAM_URL',      0x0080);

/**
 * PARAM_LOCALURL - expected properly formatted URL as well as one that refers to the local server itself. (NOT orthogonal to the others! Implies PARAM_URL!)
 */
define('PARAM_LOCALURL', 0x0180);

/**
 * PARAM_CLEANFILE - safe file name, all dangerous and regional chars are removed,
 * use when you want to store a new file submitted by students
 */
//define('PARAM_CLEANFILE',0x0200);

/**
 * PARAM_ALPHANUM - expected numbers and letters only.
 */
define('PARAM_ALPHANUM', 0x0400);

/**
 * PARAM_BOOL - converts input into 0 or 1, use for switches in forms and urls.
 */
define('PARAM_BOOL',     0x0800);

/**
 * PARAM_CLEANHTML - cleans submitted HTML code and removes slashes
 * note: do not forget to addslashes() before storing into database!
 */
define('PARAM_CLEANHTML',0x1000);

/**
 * PARAM_ALPHAEXT the same contents as PARAM_ALPHA plus the chars in quotes: "/-_" allowed,
 * suitable for include() and require()
 * @TODO: should we rename this function to PARAM_SAFEDIRS??
 */
define('PARAM_ALPHAEXT', 0x2000);

/**
 * PARAM_SAFEDIR - safe directory name, suitable for include() and require()
 */
define('PARAM_SAFEDIR',  0x4000);

/**
 * PARAM_SEQUENCE - expects a sequence of numbers like 8 to 1,5,6,4,6,8,9.  Numbers and comma only.
 */
define('PARAM_SEQUENCE',  0x8000);



/** no warnings at all */
define ('DEBUG_NONE', 0);
/** E_ERROR | E_PARSE */
define ('DEBUG_MINIMAL', 5);
/** E_ERROR | E_PARSE | E_WARNING | E_NOTICE */
define ('DEBUG_NORMAL', 15);
/** E_ALL without E_STRICT and E_RECOVERABLE_ERROR for now */
define ('DEBUG_ALL', 2047);
/** DEBUG_ALL with extra Moodle debug messages - (DEBUG_ALL |�32768) */
define ('DEBUG_DEVELOPER', 34815);



function getremoteaddr() {
/* MODIF PP 18/12/2014 
 * dans certains cas avec un proxy (comme les nationales actuelles
 * ceci retourne une valeur du type (null), xxx.yyy.zzz.uuu  
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return ($_SERVER['HTTP_CLIENT_IP']);
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return ($_SERVER['HTTP_X_FORWARDED_FOR']);
    }
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        return ($_SERVER['REMOTE_ADDR']);
    }
    return '';
 */
    return determineIP();   
}


/* By Grant Burton @ BURTONTECH.COM (11-30-2008): IP-Proxy-Cluster Fix */
function checkIP($ip) {
    if (!empty($ip) && ip2long($ip)!=-1 && ip2long($ip)!=false) {
        $private_ips = array (
        array('0.0.0.0','2.255.255.255'),
        array('10.0.0.0','10.255.255.255'),
        array('127.0.0.0','127.255.255.255'),
        array('169.254.0.0','169.254.255.255'),
        array('172.16.0.0','172.31.255.255'),
        array('192.0.2.0','192.0.2.255'),
        array('192.168.0.0','192.168.255.255'),
        array('255.255.255.0','255.255.255.255')
        );

        foreach ($private_ips as $r) {
            $min = ip2long($r[0]);
            $max = ip2long($r[1]);
            if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
        }
        return true;
    } else {
        return false;
    }
}

function determineIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && checkIP($_SERVER["HTTP_CLIENT_IP"])) {
        return $_SERVER["HTTP_CLIENT_IP"];
    }
    
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip) {
            if (checkIP(trim($ip))) {
                return $ip;
            }
        }
    }    
    if (!empty($_SERVER['HTTP_X_FORWARDED'])&& checkIP($_SERVER["HTTP_X_FORWARDED"])) {
        return $_SERVER["HTTP_X_FORWARDED"];
    } elseif (!empty($_SERVER["HTTP_X_CLUSTER_CLIENT_IP"]) && checkIP($_SERVER["HTTP_X_CLUSTER_CLIENT_IP"])) {
        return $_SERVER["HTTP_X_CLUSTER_CLIENT_IP"];
    } elseif (!empty($_SERVER["HTTP_FORWARDED_FOR"]) && checkIP($_SERVER["HTTP_FORWARDED_FOR"])) {
        return $_SERVER["HTTP_FORWARDED_FOR"];
    } elseif (!empty($_SERVER["HTTP_FORWARDED"]) && checkIP($_SERVER["HTTP_FORWARDED"])) {
        return $_SERVER["HTTP_FORWARDED"];
    } elseif(!empty($_SERVER["REMOTE_ADDR"]) && checkIP($_SERVER["REMOTE_ADDR"]))  {
        return $_SERVER["REMOTE_ADDR"];
    } else {
        return '';
    }
}







/**
* Function to check the passed address is within the passed subnet
* recup�r�e de Moodle 1.9.xx
* The parameter is a comma separated string of subnet definitions.
* Subnet strings can be in one of three formats:
*   1: xxx.xxx.xxx.xxx/xx
*   2: xxx.xxx
*   3: xxx.xxx.xxx.xxx-xxx   //a range of IP addresses in the last group.
* Code for type 1 modified from user posted comments by mediator at
* {@link http://au.php.net/manual/en/function.ip2long.php}
*
* TODO one day we will have to make this work with IP6.
*
* @param string $addr    The address you are checking
* @param string $subnetstr    The string of subnet addresses
* @return bool
*/
function address_in_subnet($addr, $subnetstr) {
 //   echo $addr. ' '.$subnetstr;

    $subnets = explode(',', $subnetstr);
    $found = false;
    $addr = trim($addr);

    foreach ($subnets as $subnet) {
        $subnet = trim($subnet);
        if (strpos($subnet, '/') !== false) {
            /// type 1
            list($ip, $mask) = explode('/', $subnet);
            if (!is_numeric($mask) || $mask < 0 || $mask > 32) {
                continue;
            }
            if ($mask == 0) {
                return true;
            }
            if ($mask == 32) {
                if ($ip === $addr) {
                    return true;
                }
                continue;
            }
            $mask = 0xffffffff << (32 - $mask);
            $found = ((ip2long($addr) & $mask) == (ip2long($ip) & $mask));
        } else if (strpos($subnet, '-') !== false)  {
            /// type 3
            $subnetparts = explode('.', $subnet);
            $addrparts = explode('.', $addr);
            $subnetrange = explode('-', array_pop($subnetparts));
            if (count($subnetrange) == 2) {
                $lastaddrpart = array_pop($addrparts);
                $found = ($subnetparts == $addrparts &&
                $subnetrange[0] <= $lastaddrpart && $lastaddrpart <= $subnetrange[1]);
            }
        } else { /// type 2
            if ($subnet[strlen($subnet) - 1] != '.') {
                $subnet .= '.';
            }
            $found = (strpos($addr . '.', $subnet) === 0);
        }

        if ($found) {
            break;
        }
    }
    return $found;
}




/**
 * Returns the name of the current script, WITH the querystring portion.
 * this function is necessary because PHP_SELF and REQUEST_URI and SCRIPT_NAME
 * return different things depending on a lot of things like your OS, Web
 * server, and the way PHP is compiled (ie. as a CGI, module, ISAPI, etc.)
 * <b>NOTE:</b> This function returns false if the global variables needed are not set.
 *
 * @return string
 */
 function me() {

    if (!empty($_SERVER['REQUEST_URI'])) {
        return $_SERVER['REQUEST_URI'];

    } else if (!empty($_SERVER['PHP_SELF'])) {
        if (!empty($_SERVER['QUERY_STRING'])) {
            return $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
        }
        return $_SERVER['PHP_SELF'];

    } else if (!empty($_SERVER['SCRIPT_NAME'])) {
        if (!empty($_SERVER['QUERY_STRING'])) {
            return $_SERVER['SCRIPT_NAME'] .'?'. $_SERVER['QUERY_STRING'];
        }
        return $_SERVER['SCRIPT_NAME'];

    } else if (!empty($_SERVER['URL'])) {     // May help IIS (not well tested)
        if (!empty($_SERVER['QUERY_STRING'])) {
            return $_SERVER['URL'] .'?'. $_SERVER['QUERY_STRING'];
        }
        return $_SERVER['URL'];

    } else {
       // notify('Warning: Could not find any of these web server variables: $REQUEST_URI, $PHP_SELF, $SCRIPT_NAME or $URL');
        return false;
    }
}

/**
 * Like {@link me()} but returns a full URL
 * @see me()
 * @return string
 */
function qualified_me() {

    global $CFG;

    if (!empty($CFG->wwwroot)) {
        $url = parse_url($CFG->wwwroot);
    }

    if (!empty($url['host'])) {
        $hostname = $url['host'];
    } else if (!empty($_SERVER['SERVER_NAME'])) {
        $hostname = $_SERVER['SERVER_NAME'];
    } else if (!empty($_ENV['SERVER_NAME'])) {
        $hostname = $_ENV['SERVER_NAME'];
    } else if (!empty($_SERVER['HTTP_HOST'])) {
        $hostname = $_SERVER['HTTP_HOST'];
    } else if (!empty($_ENV['HTTP_HOST'])) {
        $hostname = $_ENV['HTTP_HOST'];
    } else {
      //  notify('Warning: could not find the name of this server!');
        return false;
    }

    if (!empty($url['port'])) {
        $hostname .= ':'.$url['port'];
    } else if (!empty($_SERVER['SERVER_PORT'])) {
        if ($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) {
            $hostname .= ':'.$_SERVER['SERVER_PORT'];
        }
    }

    // TODO, this does not work in the situation described in MDL-11061, but
    // I don't know how to fix it. Possibly believe $CFG->wwwroot ahead of what
    // the server reports.
    if (isset($_SERVER['HTTPS'])) {
        $protocol = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
    } else if (isset($_SERVER['SERVER_PORT'])) { # Apache2 does not export $_SERVER['HTTPS']
        $protocol = ($_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
    } else {
        $protocol = 'http://';
    }

    $url_prefix = $protocol.$hostname;
    // avec le PF les url_retour sont encod�s pour �tre pass�s entier entre pages
    return $url_prefix .me();
}


 function get_js_closewindow() {
 	global $CFG;
//rev 970  annulé en V1.6
//       if ($CFG->utiliser_lightwindow_js)
//            return "myLightWindow.deactivate()";
//        else if ($CFG->utiliser_thickbox_js)
//        	return "self.parent.tb_remove()";
//        else 
           return "window.close()";

 }

/**
 * rev 1.5 (remplace les document.write ... a la fin de chaque script action.php
 * qui ne se redirige pas sur la fiche de l'item ajout� '
 * ferme une fenetre popup et rafraichi ou non l'ouvreur (la liste qui l'a apppel�)
 * s'occupe de lui passer la session'
 * @see codes/config/config_m.php
 * @param string $opener url de la fenetre qui a ouvert le popup
 * @param boolean $rafraichi rafraichir ou non l'ouvreur
 */
function ferme_popup ($opener, $rafraichi=true) {
    global $CFG;

    $fermer=get_js_closewindow();

    if ($opener && $rafraichi) {
         $action="window.opener.location.href='".p_session($opener,1)."';" ;
       }
    else {
	    $action="";

    }

    $texte=<<<EOT
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset={$CFG->encodage}"/>
                <title></title>
                <meta name="description" content="">
            </head>
            <body>
                <script type="text/javascript">
                    $action
                    $fermer;
                </script>

            </body>
        </html>
EOT;

    print($texte);
    die();
}

/**
 * typiquement un script action qui ensuite va sur la fiche
 * s'occupe de lui passer la session'
 * @param string $toUrl url de la page de destination avec ses param�tres eventuels (ex fiche.php?id=xxx)
 * @param string $opener url de la fenetre qui a ouvert ce popup
 * @param string $urlRetour parametre de rafraichissement de l'ouvreur (typiquement des crit�res de recherche ou de tri)'
 *                  qui on �t� recu avec un urlencode
 */
function redirect ($toUrl,$opener=false,$urlRetour=false,$psession=true) {
	global $CFG;

	$cible="window.opener";

	if ($opener) {  //impossible avec thickbox !!!!
		if (!empty($urlRetour)) {
            $sep=(strpos($opener,"?")>0) ? "&":"?";
			$action=$cible.".location.href='" .p_session($opener.$sep.urldecode($urlRetour),1)."';" ;
			
			//pp_debug_output("urlretour=".$urlRetour);
			//pp_debug_output ("action=".$action);
        }
		else
			$action=$cible.".location.href='" .p_session($opener,1)."';" ;
	}else $action="";

	if ($psession)  // rev 948 retour direct � un onglet (doit �tre faux)
		$toUrl=p_session($toUrl,1);

      //rev 1074 bug introduit par validation W3C psession a mis &amp; et pas & pour la session ...
      $toUrl = str_replace('&amp;', '&', $toUrl);
      $action = str_replace('&amp;', '&', $action);
	 $texte=<<<EOT
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset={$CFG->encodage}">
                <title></title>
                <meta name="description" content="">
            </head>
            <body>
                <script language="javascript">
                    $action
                     document.location.href="{$toUrl}"
                </script>
            </body>
        </html>
EOT;

	 print($texte);
	 die();
}

/**
 * rafraichi la liste sans fermer le popup
 * balance un javascript execut� de suite et non affich� dans la page ...
 *
 */
function rafraichi_liste ($opener,$urlRetour) {
	global $CFG;

	if ($opener)
		if ($urlRetour)
			$action="window.opener.location.href='" .p_session($opener."?".urldecode($urlRetour),1)."';" ;
		else
			$action="window.opener.location.href='" .p_session($opener,1)."';" ;
	else return;

	 $texte=<<<EOT
                <script language="javascript">
                    $action
                </script>
EOT;
	 print($texte);
}


// rev 1.41
// doit remplacer tous les die() dans les popups
//on bloque les notices php pour �tre a peu pres sur d'y arriver !'
// TODO attention avec le webservice !

/**
 * @param string $message message d'erreur
 * @param string $extra texte supplementaire a afficher
 * @param boolean $retourAccueil : imprime un bouton continuer qui renvoie � la page initiale
 */
function erreur_fatale ($message,$extra="",$retourAccueil=false) {
    global $chemin,$session_nom,$CFG;

// au cas ou l'erreur est en tout d�but de page (droits)
require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

// notez si la fenetre COURANTE ( pas cette qui va �tre cr��e !) est un Popup
$isPopup =isset($CFG->isPopup) ? $CFG->isPopup: false;

@$tpl = new C2IPopup(  );    //cr�er une instance

$modele=<<<EOM
<table width="100%">
  <tr>
    <td class="centre erreur">{resultat}
<!-- START BLOCK : retour_accueil-->
        <br/><br/>
    {bouton_continuer}
<!-- END BLOCK : retour_accueil -->
  </td>
</tr>
</table>
EOM;

$tpl->assignInclude("corps",$modele,T_BYVAR);
$tpl->prepare($chemin);


// essaie de traduire
@$trad=traduction($message,1,$extra);  // un seul %s permis

// si pas trouv� envoie le en clair (erreur grave en francais a signaler aux devs)
$trad=$trad? $trad :$message." ".$extra;

//print $trad."<br/>".$extra;

$tpl->assign("resultat", $trad);
$tpl->assign("_ROOT.titre_popup", traduction ("erreur_fatale"));
if ($retourAccueil && ! $isPopup) {
    $tpl->newBlock("retour_accueil");
    $url_retour=$chemin."/codes/quitter.php?".$session_nom."=".session_id();
    $tpl->assign("bouton_continuer",
        get_bouton_action("continuer",
        "document.location.href=\"".$url_retour."\""));
}else
   $tpl->print_boutons_fermeture();
$tpl->printToScreen();
@espion2("erreur_fatale",$trad,$extra);

//detruire_session();

die();

}

// rev 1.41
// choisit le logo en fonction de la pf
// devrait remplacer le code de la V 1.4 partout
// introduite car le mode DEBUG_TEMPLATES cassait le positionnement

//point de passage de tous y compris erreur_fatale

// en V2 sera fait par une classe C2ITemplate et une globale $CFG


function logo ($tpl,$avecQui=true) {
    global $USER,$CFG;
    if (isset($USER->type_plateforme) && ! empty($USER->type_plateforme)) {
    	    $t_pf=$USER->type_plateforme;
	    if ($t_pf=="certification") $tpl->assignGlobal("image_c","c");
	    else if  ($t_pf=="positionnement")
		    if (is_utilisateur_anonyme($USER->id_user)) $tpl->assignGlobal("image_c","a");
		    else $tpl->assignGlobal("image_c","");
	    else $tpl->assignGlobal("image_c","v"); // non ger� ...
    }  else $tpl->assignGlobal("image_c","v"); // perdu ou autre ...

}


/**
 * v 1.5 imprime le menu principal horizontal pour tout type d'utilisateurs'
 * remplace les deux anciens include commun/menu_perso et commun/menu_etud de la V 1.4
 * @param $tpl			le template porteur
 * @param $type_item  	l'element selectionn� dans cette barre de navigation
 * pour l'instant le type d'utilisateur est cherch� dans la varaible globale $USER
 * TODO mieux g�rer cette barre de navigation
 */
function print_menu_haut ($tpl,$type_item) {
	global $USER,$CFG;
	$tpl->assignURL("_ROOT.url_accueil",$CFG->chemin."/codes/entrer.php");
	if ($USER->verif !="ent") {
		$tpl->newBlock("quitter");
		$tpl->assignURL("url_quit",$CFG->chemin."/codes/quitter.php");
		$tpl->assign("quitter",traduction("menu_deconnexion"));
	}

	if ($USER->type_user=="P")
         __print_menu_personnel($tpl,$type_item);
	if ($USER->type_user=="E")
         __print_menu_etudiant($tpl,$type_item);
	//sinon rien ...
}




/**
 * rev 981 version simplifiée sans swap_image ...
 */
function __print_menu_personnel ($tpl,$type_item) {
    global $USER,$CFG;
    require_login("P");
    $isAdmin=is_admin($USER->id_user); // pas la peine de le tester � chaque fois
    
    // V2 en cas de pf générique, ne pas afficher certains items si le referentiel n'a pas été défini
	$refs= get_referentiels('',false);
	if (count($refs) >0 ) {
        if ($isAdmin ||a_capacite("ql")){
                $tpl->newBlock("menu_item");
                $tpl->assign ('item','questions');
                $tpl->assign ('_b',$type_item=='q'?'_b':'');  //image soulign�e ou non
                $tpl->traduit('alt','alt_gerer_questions');
                $tpl->assignURL("url",$CFG->chemin."/codes/questions/liste.php");
         }

         if ($isAdmin ||a_capacite("el")){
                $tpl->newBlock("menu_item");
                $tpl->assign ('item','exams');
                $tpl->assign ('_b',$type_item=='e'?'_b':'');
                 $tpl->traduit('alt','alt_gerer_examens');
                $tpl->assignURL("url",$CFG->chemin."/codes/examens/liste.php");
         }
	}     

            if ($isAdmin || a_capacite("etl") || a_capacite("ul")){
               $tpl->newBlock("menu_item");
                $tpl->assign ('item','inscrits');
                $tpl->assign ('_b',$type_item=='a'?'_b':'');
                 $tpl->traduit('alt','alt_gerer_acces');
                $tpl->assignURL("url",$CFG->chemin."/codes/acces/acces2.php");
            }

            if ($isAdmin || a_capacite("config")){
                $tpl->newBlock("menu_item");
                $tpl->assign ('item','config');
                $tpl->assign ('_b',$type_item=='c'?'_b':'');
                 $tpl->traduit('alt','alt_gerer_config');
                $tpl->assignURL("url",$CFG->chemin."/codes/config/configuration2.php");

            }
            /**
            if ($CFG->utiliser_notions_parcours) {
                if ($isAdmin || a_capacite("config")){
                $tpl->newBlock("menu_item");
                $tpl->assign ('item','notions');
                $tpl->assign ('_b',$type_item=='n'?'_b':'');
                 $tpl->traduit('alt','alt_gerer_notions');
                $tpl->assignURL("url",$CFG->chemin."/codes/notions/liste.php");
            
                }
            }
            */
            if ($CFG->utiliser_notions_parcours) {
                if ($isAdmin || a_capacite("config")){
                    $tpl->newBlock("menu_item");
                    $tpl->assign ('item','ressources');
                    $tpl->assign ('_b',$type_item=='n'?'_b':'');
                    $tpl->traduit('alt','alt_gerer_ressources');
                    $tpl->assignURL("url",$CFG->chemin."/codes/ressources/liste.php");
            
                }
            }

            if ($CFG->prof_peut_passer_qcm)
                __print_menu_etudiant($tpl,$type_item);


     // V 1.41 l'item retour est dans un block dans principale.html ', il �tait syst�matique dans le template avant
     if ($type_item !='') { //pas sur la page accueil !!!
        $tpl->newBlock("retour");
        $tpl->assignURL("url_retour",$CFG->chemin."/codes/entrer.php");
     }


}








/**
 * rev 981 version tr�s simplifi�e sans swp_image ...
 */
function __print_menu_etudiant ($tpl,$type_item) {
    global $USER,$CFG;
    require_login("E");
    $tpl->newBlock("menu_item");
    $tpl->assign ('item','qcm');
    $tpl->assign ('_b',$type_item=='qcm'?'_b':'');  //image soulign�e ou non
    $tpl->traduit('alt','alt_gerer_qcms');
    $tpl->assignURL("url",$CFG->chemin."/codes/qcm/liste.php");
    if ($CFG->utiliser_notions_parcours) {
        if (! is_utilisateur_anonyme($USER->id_user)) {
            $tpl->newBlock("menu_item");
            $tpl->assign ('item','parcours');
            $tpl->assign ('_b',$type_item=='parc'?'_b':'');  //image soulign�e ou non
            $tpl->traduit('alt','alt_gerer_parcours');
            $tpl->assignURL("url",$CFG->chemin."/codes/parcours/liste.php");

        }
    }

}




/**
 * ajoute un javascript suppl�mentaire � la page dans l'entete
 * @param template $tpl le template porteur
 * @param string $path le chemin vers le js en relatif par rapport � $chemin
 * @see
 * il faut que le template ait le marqueur "EXTRA_JS" dans son HEAD
 */
function add_javascript ($tpl,$path) {
    global $CFG;
    // ne pas ajouter plusieurs fois ceux qui sont d�ja dans commun !
    // voir tpl->printtoscreen
    switch($path) {  //notation raccourcie pour les "standard" add_javascript($tpl,"fabtabulous")
        case "prototype": $CFG->utiliser_prototype_js=1; break;
        case "scriptacoulous": $CFG->utiliser_prototype_js=1;$CFG->utiliser_scriptacoulous_js=1; break;
        case "validation":  $CFG->utiliser_prototype_js=1;$CFG->utiliser_validation_js=1; break;
        case "tables_sortables":   $CFG->utiliser_tables_sortables_js=1;break;
        case "fabtabulous":   $CFG->utiliser_fabtabulous_js=1;break;
        default :
            $tpl->extra_js[]=$path;
            }


}

/**
 * ajoute un css suppl�mentaire � la page dans l'entete
 * @param template $tpl le template porteur
 * @param string $path le chemin vers le js en relatif par rapport � $chemin
 * @see
 * il faut que le template ait le marqueur "EXTRA_CSS" dans son HEAD
 */
function add_css ($tpl,$path) {
            $tpl->extra_css[]=$path;
}

/**
 * renvoie l'icone associ�e � un examen selon son �tat '
 * l'image doit �tre dans themes/xxx/images  (nom.gif)
 * et le texte alt dans langues/fr.php
 */
function image_etat_examen ($ligne) {
	switch (etat_examen($ligne)) {
		case  -1 :return "termine"; break;
		case  0 :return "en_cours"; break;
		case 1 : return "a_venir" ; break;
	}
}

/**
 * si la condition est vraie cr�� le lien, sinon juste le texte
 * voir codes/examens/fiche.php
 */
function cree_lien_conditionnel ($url, $texte,$condition) {
    $texte=traduction ($texte);
    if ($condition)
        return "<a href='$url'>$texte</a>";
    else
        return $texte;
}

function cree_lien_mailto($mail,$texte ){
	if (empty($texte)) return "";

	if (!empty($mail))
		return "<span class=\"mailto\"> <a href=\"mailto:$mail\" title=\"$mail\">$texte</a></span>";
	else return $texte;
}





/**
 * rev 831 le meme template pour haut et bas  en passant par un sousTemplate
 * essaie de r�duire le nombre de pages affich�es si trop
 */

/**
 * fonction appel�e par la m�thode prepare des classes  templatepower si les options de construction de la page
 * demande une multipagination
 * on le fait ainsi car les assignInclude doivent �tre fait AVANT le prepare, donc on le fait
 * au d�but de cette m�thode
 *
 */

function assignIncludeMultipagination ($tpl) {

    $haut=<<<EOH
    <div id="multi_pagination_haut">{multi_pagination_haut} </div>
EOH;
    $bas=<<<EOH
    <div id="multi_pagination_bas">{multi_pagination_bas} </div>
EOH;


    global $CFG;
    if ($CFG->multip_haut)
        $tpl->assignInclude("multip_haut",$haut,T_BYVAR);
    if ($CFG->multip_bas)
        $tpl->assignInclude("multip",$bas,T_BYVAR);
 }

function print_multipagination ($tpl) {
    global $CFG,$USER;
    global $url_multipagination,$indice_deb,$indice_fin,$indice_max,$indice_ecart,$num_page; //tempo
    if ($CFG->multip_haut)
        __print_multipagination($tpl,"_ROOT.multi_pagination_haut",$url_multipagination,$indice_deb,$indice_fin,$indice_max,$indice_ecart,$num_page);
    if ($CFG->multip_bas)
        __print_multipagination($tpl,"_ROOT.multi_pagination_bas",$url_multipagination,$indice_deb,$indice_fin,$indice_max,$indice_ecart,$num_page);

    //debug
    $USER->url_multipagination=$url_multipagination;
    $USER->indice_deb= $indice_deb;
    $USER->indice_fin=$indice_fin;
    $USER->indice_fin=$indice_max;
    $USER->indice_ecart=$indice_ecart;
    $USER->indice_max=$indice_max;
    $USER->num_page=$num_page;
}



function __print_multipagination($tpl,$balise,$url_multipagination,$indice_deb,$indice_fin,$indice_max,$indice_ecart,$num_page){

	global $USER,$CFG;

// compat W3C strict il FAUT un fieldset ou un div ...
	$modele=<<<EOM
		<!-- START BLOCK : multi_pagination -->

			<form action="{nom_page}" method="post">
                <div>
				<!-- START BLOCK : multi_g -->
				<a href="{page_m}" class="commentaire1">{precedent}</a>
					<!-- END BLOCK : multi_g -->

				<!-- START BLOCK : multi_n -->
				{url}
				<!-- END BLOCK : multi_n -->

				<!-- START BLOCK : multi_d -->
				<a href="{page_m}" class="commentaire1">{suivant}</a>
					<!-- END BLOCK : multi_d -->
					&nbsp;
				| <span class="commentaire1">{afficher_la_page}</span>
					<input name="num_page" type="text" class="saisie"  value="{num_page}" size="3" maxlength="3" />

						<!-- START BLOCK : PAR_PAGE -->
						<span class="commentaire1">{afficher_par_page}</span>
							<input name="indice_ecart" type="text" class="saisie" value="{indice_ecart}" size="3" maxlength="3" />
								<!-- END BLOCK : PAR_PAGE -->

								{bouton_ok}
						<!-- START BLOCK : multi_id_session -->
						<input name="{session_nom}" type="hidden" value="{session_id}" />
							<!-- END BLOCK : multi_id_session -->
                  </div>
           </form>

	 <!-- END BLOCK : multi_pagination -->
EOM;



	if (!isset ($indice_ecart))
		$indice_ecart = 10;
	if (!isset ($url_multipagination))
		$url_multipagination = $_SERVER['REQUEST_URI'];

	$url_multi = p_session(concatAvecSeparateur($url_multipagination,"indice_ecart=".$indice_ecart,"&amp;"));
	$nb_blocks = ceil(($indice_max ) / $indice_ecart);

	if ($num_page) {
	   if ($num_page<=0) $num_page=1;
	   if ($num_page>$nb_blocks) $num_page=$nb_blocks;
	   $indice_deb = $indice_ecart * ($num_page -1);
	} else
	   $num_page = 1 + round($indice_deb / $indice_ecart);

	if (($indice_max > $indice_fin) || ($indice_deb > 0)) {
		$tmptpl= new SubTemplatePower($modele,T_BYVAR);    //cr�er une instance
		// a le meme chemin que le template porteur  si existe
		if (!$tpl) $tmptpl->prepare($CFG->chemin);
		else $tmptpl->prepare($tpl->chemin);

		$tmptpl->newBlock("multi_pagination");
		// nom de la page action

		$tmptpl->assign("nom_page", $url_multipagination);
		$tmptpl->assign("num_page", $num_page);
		// gestion de l'affichage des liens pr�c�dent et suivant
		if ($indice_deb > 0) {
			$tmptpl->newBlock("multi_g");
			$indice_g = ($indice_deb - $indice_ecart);
			$tmptpl->assign("page_m", $url_multi . "&amp;indice_deb=" . $indice_g);
		}
		if ($indice_max > $indice_fin) {
			$tmptpl->newBlock("multi_d");
			$indice_d = ($indice_fin +1);
			$tmptpl->assign("page_m", $url_multi . "&amp;indice_deb=" . $indice_d);
	   }


	   // gestion des pages interm�diaires

	   if ($nb_blocks>$CFG->multi_max_pages){
			$start_page=$num_page-$CFG->multi_max_pages+1;
			if ($start_page<=0) $start_page=1;
			if ($start_page >1) {
				$tmptpl->newBlock("multi_n");
				$tmptpl->assign("url","<a href='".$url_multi."&amp;indice_deb=1'>1</a>");

				$tmptpl->newBlock("multi_n");
				$tmptpl->assign("url", "&nbsp;..&nbsp;");
			}

		} else
			$start_page=1;

		$display_count=0;
		while ($display_count < $CFG->multi_max_pages && $start_page <=$nb_blocks) {

			$indice_suite = ($start_page -1) * $indice_ecart;
			$tmptpl->newBlock("multi_n");

		    if ($indice_suite == $indice_deb)
				$tmptpl->assign("url","<span class='rouge'> $start_page</span>");
			else
				$tmptpl->assign("url","<a href='".$url_multi."&amp;indice_deb=".$indice_suite."'>$start_page</a>");

			$display_count++;
			$start_page++;
		}

		if ($start_page <=$nb_blocks) {
			if ($start_page <$nb_blocks) {
				$tmptpl->newBlock("multi_n");
				$tmptpl->assign("url", "&nbsp;..&nbsp;");
			}
			$tmptpl->newBlock("multi_n");
			$indice_suite = ($nb_blocks -1) * $indice_ecart;
			$tmptpl->assign("url","<a href='".$url_multi."&amp;indice_deb=".$indice_suite."'>$nb_blocks</a>");

		}
		if ($CFG->multi_parpage) {
			$tmptpl->newBlock("PAR_PAGE");
			$tmptpl->assign("indice_ecart", $indice_ecart);
		}
		print_bouton_ok($tmptpl,"multi_pagination.bouton_ok");
		form_session($tmptpl, "multi_id_session"); // v 1.41 ici pour eviter notice php
    	$tpl->assign ($balise,$tmptpl->getOutputContent());

	} else $tpl->assign($balise,"");  //    EFFACER MARQUEUR

}







/**
* emet un template complet de type select
* @param $tpl le template porteur
* @param $varname le nom du marqueur qui va recevoir la liste (select_xxxx recommand�)
* @param $table le tableau des trucs a mettre en ligne (tableau d'objets !)
* @param $nom_select : nom du select -> attribut name
* @param $class_select : classe du select : attribut class
* @param $extra_attr   : attributs supplementaires (onClick, style ...)
* @param $value_option : champ des objet � mettre comme value des options
* @param $texte_option : champ des objets � mettre comme texte des options
* @param $option_neutre : faut-il un premier item ? (choisissez : ...)
* @param $value_selected : valeur qui va declencher la selection d'une option
* ex :

$table=array(new test(1,"un"),new test(2,"deux"),new test(3,"trois"));

print_select_from_table ($tpl,"_ROOT.select_test",$table,"test","saisie","","id","texte","choisissez","2");
*/


/**
 * appel normal ou ajax
 * si ajax $tpl=false
 */

function get_select_from_table ($tpl,$table,
				$nom_select,
				$classe_select,
				$extra_attrs,
				$value_option,$texte_option,
				$option_neutre,
				$value_selected,$size=1) {
	global $CFG;

	$modele=<<<EOT
<!-- START BLOCK : select -->
<select
	name="{nom_select}" id="{nom_select}"
	class="{classe_select}"
	{extra_attrs}>
        <!-- START BLOCK : option_neutre -->
		<option value="">{option_neutre} : </option>
	<!-- END BLOCK : option_neutre -->
	<!-- START BLOCK : option -->
			<option value="{value_option}" {selected}>{texte_option}</option>
	<!-- END BLOCK : option -->
</select>
<!-- END BLOCK : select -->

EOT;
	$tmptpl= new SubTemplatePower($modele,T_BYVAR);    //cr�er une instance
	// a le meme chemin que le template porteur  si existe
    if (!$tpl) $tmptpl->prepare($CFG->chemin);
	else $tmptpl->prepare($tpl->chemin);
	$tmptpl->newBlock("select");
	$tmptpl->assign("nom_select",$nom_select);
	$tmptpl->assign("classe_select",$classe_select?$classe_select:'saisie');
	$tmptpl->assign("extra_attrs",$extra_attrs?$extra_attrs:'');
	if ($option_neutre) {  // item sans valeur au d�but ?
		$tmptpl->newBlock("option_neutre");
		$tmptpl->assign("option_neutre",$option_neutre);
	}
	if ($table)
		foreach($table as $option) {
			$tmptpl->newBlock("option");
			$tmptpl->assign("value_option",$option->$value_option);
			$tmptpl->assign("texte_option",$option->$texte_option);
			$tmptpl->setSelected ($option->$value_option==$value_selected);
		}
	return $tmptpl->getOutputContent();
}


/**
 * appel normal
 */
function print_select_from_table ($tpl,$varname,$table,
                $nom_select,
                $classe_select,
                $extra_attrs,
                $value_option,$texte_option,
                $option_neutre,
                $value_selected,$size=10) {

  $tpl->assign($varname,get_select_from_table ($tpl,$table,
                $nom_select,
                $classe_select,
                $extra_attrs,
                $value_option,$texte_option,
                $option_neutre,
                $value_selected,
                $size) );
}


/**
 * introduit revison 944 pour les examens par domaine
 */
function get_multiselect_from_table ($tpl,$table,
                $nom_select,
                $classe_select,
                $extra_attrs,
                $value_option,$texte_option,
                $option_tous,$texte_tous,
                $values_selected,
                $size=10) {
    global $CFG;

    $modele=<<<EOT
<!-- START BLOCK : select -->
<select
    name="{nom_select}[]" id="{nom_select}"  multiple="multiple"  size="$size"
    class="{classe_select}"
    {extra_attrs}>
        <!-- START BLOCK : option_neutre -->
        <option value="{value_neutre}">{texte_neutre}</option>
    <!-- END BLOCK : option_neutre -->
    <!-- START BLOCK : option -->
            <option value="{value_option}" {selected}>{texte_option}</option>
    <!-- END BLOCK : option -->
</select>
<!-- END BLOCK : select -->

EOT;
    $tmptpl= new SubTemplatePower($modele,T_BYVAR);    //cr�er une instance
    // a le meme chemin que le template porteur  si existe
    if (!$tpl) $tmptpl->prepare($CFG->chemin);
    else $tmptpl->prepare($tpl->chemin);
    $tmptpl->newBlock("select");
    $tmptpl->assign("nom_select",$nom_select);
    $tmptpl->assign("classe_select",$classe_select?$classe_select:'saisie');
    $tmptpl->assign("extra_attrs",$extra_attrs?$extra_attrs:'');
    if ($option_tous) {  // item sans valeur au d�but ?
        $tmptpl->newBlock("option_neutre");
        $tmptpl->assign("value_neutre",$option_tous);
        $tmptpl->assign("texte_neutre",$texte_tous);
    }
    if ($table)
        foreach($table as $option) {
            $tmptpl->newBlock("option");
            $tmptpl->assign("value_option",$option->$value_option);
            $tmptpl->assign("texte_option",$option->$texte_option);
           // print ($values_selected[$option->$value_option]);
            $tmptpl->setSelected (!empty($values_selected[$option->$value_option]));
        }
    return $tmptpl->getOutputContent();
}


/**
 * appel normal
 */
function print_multiselect_from_table ($tpl,$varname,$table,
                $nom_select,
                $classe_select,
                $extra_attrs,
                $value_option,$texte_option,
                $option_tous,$texte_tous,
                $values_selected,$size=10) {

  $tpl->assign($varname,get_multiselect_from_table ($tpl,$table,
                $nom_select,
                $classe_select,
                $extra_attrs,
                $value_option,$texte_option,
                $option_tous,$texte_tous,
                $values_selected,$size) );
}

/**
 * imprime un multiselecteur de referentiels
 * @param string $selected  ceux qui ont �t� choisis auparavant liste separ�es par des virgules ou vide ou -1
 */

function print_multiselect_referentiels ($tpl,$varname,$nom_select,$selected) {


    $table=get_referentiels();
    foreach($table as $ref)
         $ref->domaine=$ref->referentielc2i." - ".$ref->domaine;

    $choisis=array();
    $sels=get_referentiels_liste($selected);
    foreach($sels as $ref) 
        $choisis[$ref->referentielc2i]=1;

    return print_multiselect_from_table($tpl,$varname,$table,$nom_select,'','',
                                        'referentielc2i','domaine',-1,traduction('touts_referentiels',false),$choisis,
                                        1+count($table));

}


/**
* imprime un multiselecteur de referentiels
* @param string $selected  ceux qui ont �t� choisis auparavant liste separ�es par des virgules ou vide ou -1
*/

function print_multiselect_plages_ips ($tpl,$varname,$nom_select,$selected) {
    $table=get_plages_ip_declarees();
    foreach ($table as $ip)
        $ip->info = $ip->nom.' ('.$ip->adresses.' )';
    $choisis=array();
    $sels=get_ips_liste($selected);
    foreach($sels as $ref)
        $choisis[$ref->id]=1;
    
    return print_multiselect_from_table($tpl,$varname,$table,$nom_select,'','',
                                            'id','info',-1,traduction('touts_ips',false),$choisis, 1+count($table));
    
    
}




/**
 * le select �tant d�ja pos�, n'ajouter que ses options a partir de la table
 */

function get_options_from_table ($tpl,$table,
                $value_option,$texte_option,
                $option_neutre,
                $value_selected) {
    global $CFG;

    $modele=<<<EOT
        <!-- START BLOCK : option_neutre -->
        <option value="">{option_neutre} : </option>
    <!-- END BLOCK : option_neutre -->
    <!-- START BLOCK : option -->
            <option value="{value_option}" {selected}>{texte_option}</option>
    <!-- END BLOCK : option -->

EOT;
    $tmptpl= new SubTemplatePower($modele,T_BYVAR);    //cr�er une instance
    // a le meme chemin que le template porteur
     if (!$tpl) $tmptpl->prepare($CFG->chemin);
    else $tmptpl->prepare($tpl->chemin);

    if ($option_neutre) {  // item sans valeur au d�but ?
        $tmptpl->newBlock("option_neutre");
        $tmptpl->assign("option_neutre",$option_neutre);
    }
    if ($table)
        foreach($table as $option) {
            $tmptpl->newBlock("option");
            $tmptpl->assign("value_option",$option->$value_option);
            $tmptpl->assign("texte_option",$option->$texte_option);
            $tmptpl->setSelected ($option->$value_option==$value_selected);
        }
    return $tmptpl->getOutputContent();
}

function print_options_from_table ($tpl,$varname,$table,
                $value_option,$texte_option,
                $option_neutre,
                $value_selected) {

  $tpl->assign($varname,get_options_from_table ($tpl,$table,
                $value_option,$texte_option,
                $option_neutre,
                $value_selected));
}

/**
 * affiche une triple liste de selection ref+alinea+famille li�e
 * voir codes/notions/liste.php pourt un exemple sans famille
 * et codes:questions/liste.php pour un complet
 */

function print_selecteur_ref_alinea_famille($tpl,
                    $nom_forme,                                                       //nom de la forme
                    $nom_select_ref, $class_ref,$extra_attrs_ref="style='width:200px'",            //select referentiel
                    $nom_select_alinea,$class_alinea,$extra_attrs_alinea= "style='width:100px'",      //select alinea
                    $nom_select_famille,$class_famille,$extra_attrs_famille="style='width=200px'",     //select famille
                    $nom_input_famille,$class_if,$extra_attrs_if,                                //input famille
                    $referentielc2i,$alinea,$famille,$texte_famille,
                    $ajoutVide=false) {         //rev 977 C2I V2 ajouter un item 'vide' pour recherche questions/notions orphelines

     global $CFG;

     $CFG->utiliser_prototype_js=1;  //forcé


    $name_select_ref="referentielc2i";
    $name_select_alinea="alinea";


//partie commune

  $supp=<<<EOS
  <input type="hidden" name="alinea_prec" id="alinea_prec" value="$alinea"/>
  <input type="hidden" name="famille_prec" id="famille_prec" value="$famille"/>
  <script type="text/javascript" >
  //<![CDATA[
     function majCentral(theDiv,theScript,theSpinner,theInput) {
        var ar=new Ajax.Updater(theDiv,theScript,{parameters : Form.serialize(\$("$nom_forme")) ,
                            evalScripts:true,
                            onComplete : function () {
                               // theSelect=$(theDiv);
                               // $(theInput).value=theSelect.options[theSelect.selectedIndex].value;
                              },
                            onFailure: function(transport){
                                  alert("une erreur s'est produite, le serveur est peut-etre temporairement inaccessible");}
                            });
       }

     function miseAJourAlinea() {majCentral("{$name_select_alinea}","{$CFG->chemin_commun}/ajax/get_alineas.php",null,"alinea_prec"); }
     setTimeout('miseAJourAlinea()',1);
     //]]>
    </script>

EOS;

   $supp_fam=<<<EOS

     <script type="text/javascript" >
     //<![CDATA[
     function miseAJourMotsClesFamille() {majCentral("mots_clesf","{$CFG->chemin_commun}/ajax/get_mots_cles_familles.php",null,"famille_prec"); }

     function miseAJourFamille() {majCentral("famille","{$CFG->chemin_commun}/ajax/get_familles.php",null,"famille_prec"); }
     setTimeout('miseAJourFamille()',2);
     setTimeout('miseAJourMotsClesFamille()',3);
     //]]>
    </script>

EOS;

//zones javascript supplementaires

 if ($nom_select_famille) $supp.=$supp_fam;


    $table=get_referentiels();
    if ($ajoutVide) {
        $vide=new StdClass();
        $vide->referentielc2i=traduction ('vide');
        $vide->domaine=traduction ('pas_encore_de_valeur');
        array_unshift($table,$vide);
    }
    foreach($table as $ref) $ref->domaine=$ref->referentielc2i." - ".$ref->domaine;

    $onchange="onchange='miseAJourAlinea();'";
    $extra_attrs=<<<EOF
        size="1" $extra_attrs_ref $onchange
EOF;
    print_select_from_table($tpl,$nom_select_ref,$table,$name_select_ref,$class_ref,
                            $extra_attrs,"referentielc2i","domaine",traduction ("ref_c2i"),$referentielc2i);


    //vide au d�part
    $table=array();
    $onchange=$nom_select_famille? "onchange='miseAJourFamille();'":"";
    $extra_attrs=<<<EOF
    size="1" $extra_attrs_alinea $onchange
EOF;
    $sa=get_select_from_table($tpl,$table,$name_select_alinea,$class_alinea,
                        $extra_attrs,"","",traduction ("alinea"),false);
    $tpl->assign ($nom_select_alinea,$sa.$supp);


 if ($nom_select_famille) {
   //vide au d�part
    $table=array();
    $onchange="onchange='miseAJourMotsClesFamille();'";   // rev 836
    $extra_attrs=<<<EOF
    size="1" $extra_attrs_famille $onchange
EOF;
    $sa=get_select_from_table($tpl,$table,"famille",$class_famille,
                        $extra_attrs,"","",traduction ("famille"),false);
    $tpl->assign ($nom_select_famille,$sa);


 }

}


/** qq boutons courant
 * *
 *  usage $tpl->assign("place du bouton", get_bouton_xxx(...)
 * seul l'attribut value (ce qui est affich�) est "traductible"
 * d'ou l'appel a traduction (pas les accolades car on va faire un assign, donc c'est fini !')'
 */


/**
 * renvoie un bouton pret � �tre ins�r�
 * @param string $action nom de l'action
 * @param string $onClick quoi faire si on clique dessus
 * @param string $classe classe du bouton (d�faut=saisie_bouton)
 * @param string $type type du bouton (button, submit, reset)
 */
function get_bouton_action($action,$onClick="",$class="",$type="") {
    $class=$class?$class:"saisie_bouton";
	$type=$type?$type:"button";

    $ret=	"<input name='bouton_".$action."'".
					" value=\"".traduction ('bouton_'.$action)."\"".
					" type='".$type."'".
					" class='".$class."'".
					" id='bouton_".$action."'";
	if ($onClick)
		$ret .= " onclick='".$onClick."'";
    $ret.="/>";
   // print $ret;
    return $ret;
}

/**
 * cas particulier on peut revenir � une fiche ou � une liste
 * et url_retour peut �tre vide auquel cas, rien
 * ceci signale au developpeur qu'il a oubli� de sp�cifier $url_retour dans sa page ...
 */

function get_bouton_retour ($url_retour,$ou="retour_fiche") {
	if (isset($url_retour) && !empty($url_retour))
		return get_bouton_action($ou,"document.location.href=\"".$url_retour."\"");
	else return "";
}


/**
 * encore plus simple pour les boutons classiques mettre {bouton:nom} dans le template :-)
 */
function get_bouton_standard ($balise){
	$tab=explode(":",$balise);
	if (count($tab) <2) return $balise;

	//TODO
	switch ($tab[1]) {
		case 'annuler':return get_bouton_action("annuler","javascript:".get_js_closewindow().";"); break;
		case 'fermer':return get_bouton_action("fermer","javascript:".get_js_closewindow().";"); break;
		case 'ok':return get_bouton_action("ok","","","submit" ); break;
		case 'enregistrer': return get_bouton_action("enregistrer","","","submit" ); break;
		case 'reset':return  get_bouton_action("reset","","","reset" ); break;
        case 'imprimer':return  get_bouton_action("imprimer","javascript:window.print();" ); break;
        case 'confirmer':return  get_bouton_action("confirmer","","","submit" ); break;
        case 'tout_cocher':return  get_bouton_action("tout_cocher",""); break;


		default: return $balise; break;
	}
}

/**
 * si tpl et balise sont vides, renvoie l'HTML sans l'assigner (utile en ajax)
 */
function print_bouton ($tpl,$balise,$action,$onClick="",$class="",$type=""){

    $html=get_bouton_action($action,$onClick,$class,$type);
    if ($tpl && $balise)
	    $tpl->assignGlobal($balise,$html);

   return $html;
}

/**
 * ou alors mettre 'bouton_xxxx' et appeler ces methodes a la fin du script
 */
function print_bouton_retour($tpl,$url_retour="",$ou="retour_fiche",$balise="bouton_retour"){
	if (empty($url_retour)) $url_retour="javascript:history.back();";
	$tpl->assignGlobal($balise, get_bouton_retour($url_retour,$ou));
}

function print_bouton_fermer($tpl,$balise="bouton_fermer"){

  $ferme=get_js_closewindow();
	print_bouton($tpl,$balise,"fermer","javascript:".$ferme.";");
    //print_bouton($tpl,$balise,"fermer","{global_windowclose}");
}

function print_bouton_annuler($tpl,$balise="bouton_annuler"){
        $ferme=get_js_closewindow();
	print_bouton($tpl,$balise,"annuler","javascript:".$ferme.";");
    // print_bouton($tpl,$balise,"annuler","{global_windowclose}");
}

/**
 * rev 836 pour gerer annulation en cas de duplication
 * c'est un bouton submit comme une autre
 * et on g�re dans ajout.php
 */
function print_bouton_annuler_duplication($tpl,$balise="bouton_annuler"){
    print_bouton($tpl,$balise,"annuler","","","submit");
}

function print_bouton_reset($tpl,$onClick="",$balise="bouton_reset"){
	print_bouton($tpl,$balise,"reset",$onClick,"","reset");
}

function print_bouton_enregistrer($tpl,$balise="bouton_enregistrer"){
	print_bouton($tpl,$balise,"enregistrer","","","submit");
}

function print_bouton_confirmer($tpl, $balise="bouton_confirmer"){
	print_bouton($tpl,$balise,"confirmer","","","submit");
}


function print_bouton_tout_cocher($tpl, $form,$balise="bouton_tout_cocher"){
    print_bouton($tpl,$balise,"tout_cocher","javascript:checkall($form);");
}

function print_bouton_tout_decocher($tpl, $form,$balise="bouton_tout_decocher"){
    print_bouton($tpl,$balise,"tout_decocher","javascript:uncheckall($form);");
}

/**
 * rev 839 si on clique sur annuler DOIt annuler pas soumettre !!! (return false)
 * rev 986 il n'est PAS nécessaire de masquer le div au click sur OK car on va soumettre
 * le formulaire (sauf si refuser par un validateur, auquel cas il ne DOIT pas 
 * être masqué pour que l'on voit les erreurs
 */

function print_boutons_criteres ($tpl, $balise="boutons_criteres",$id_div='criteres') {
    global $CFG;

    $ligne=<<<EOL
        <input  type="image"
                src="{$CFG->chemin_images}/i_ok_blanc.gif" />
        &nbsp;
        <input  type="image"
                onclick="showHide('$id_div','','hide');return false;"
                src="{$CFG->chemin_images}/i_annuler_blanc.gif" />
EOL;
    


	$ok=traduction('ok');
	$annuler=traduction('annuler');
	

    $ligne2=<<<EOL
    	<input  type="submit" class="saisie_bouton"
                value="$ok" />
        &nbsp;
        <input  type="button" class="saisie_bouton"
                onclick="showHide('$id_div','','hide');return false;"
                value="$annuler"  />
EOL;
    if (!empty($CFG->boutons_graphiques))
    	$tpl->assign($balise, $ligne);
	else
	    $tpl->assign($balise, $ligne2);
}

function print_bouton_ok($tpl, $balise="bouton_ok") {
    global $CFG;
	$ligne=<<<EOL
    <input type="image"  src="{$CFG->chemin_images}/i_ok.gif" />
EOL;
	$ok=traduction('ok');
	$ligne2=<<<EOL
 <input type='submit' value='$ok'/>
EOL;
	if (!empty($CFG->boutons_graphiques))
    	$tpl->assign($balise, $ligne);
	else
	    $tpl->assign($balise, $ligne2);

}

/**
 * rev 1078 options pour des boutons graphiques ou non
 */
function print_bouton_connexion($tpl, $balise="bouton_connexion") {
    global $CFG;
	$ligne=<<<EOL
 	<input name="imageField" type="image" src="{$CFG->chemin_images}/i_connect.gif" />
EOL;
	$connexion=traduction('connexion');
	$ligne2=<<<EOL
 <input type='submit' class='saisie_bouton' value='$connexion'/>
EOL;
	if (!empty($CFG->boutons_graphiques))
    	$tpl->assign($balise, $ligne);
	else
	    $tpl->assign($balise, $ligne2);
}



/**
 * modeles standard des  items de menu de niveau 2  en  <=V1.5
 * pourront changer plus tard
 */

function get_menu_item_criteres() {
	return new icone_action('criteres',"showHide('criteres','','show')",'menu_criteres');
	
}

function get_menu_item_tout_afficher($url="clear_criteres()") {
    return new icone_action('afficher_tout',$url,'menu_afficher_tout');
}


function get_menu_item_imprimer() {
	return new icone_action('imprimer','window.print()','menu_imprimer');
}

/**
 * envoie un fichier cr�� dans la zone ressource
 * en bricolant les ent�tes en fonctin de son type mime (pdf,csv, txt ...)
 * rev 1013 'url' chang� en 'return' pour �viter emission de openPopup qui perturbe IE7/8
*/
function get_menu_item_csv($fichier) {
	global $CFG;
	return array ( 	'action'=>	'csv','return'	=>$CFG->chemin."/commun/send_csv.php?idf=".$fichier );
}

/**
 * envoie un fichier cr�� dans la zone ressource
 * en bricolant les ent�tes en fonctin de son type mime (pdf,csv, txt ...)
 * rev 1013 'url' chang� en 'return' pour �viter emission de openPopup qui perturbe IE7/8
 */
function get_menu_item_ods($fichier,$dir='') {
	global $CFG;
	return array ( 	'action'=>	'ods',
					'return'	=>$CFG->chemin."/commun/send_csv.php?idf=".$fichier."&amp;dir=".$dir
				 );
}

/**
 * modele standard du menu de niveau 2 l�gende  <=V1.5
 * pour l'instant c'est un popup vers le script legende.php
 */

function get_menu_item_legende($quoi) {
	global $CFG;
	$CFG->utiliser_form_actions=1; //  force chargement des actions si pas d�ja fait 
	return new icone_action ('legende',"doPopup('$CFG->chemin_commun/legende.php?quoi=$quoi') ",'menu_legende');
}


/**
 * une autre facon de mettre des icones a cliquer , par exemple dans une fiche a cot� d'un nom,profil, univ ...
 * @param string $url
 * @param boolean $internal true utiliser doPopup qui ajoute les id de session , false ne pas le fait car lien externe)
 */

function get_menu_item_consulter($url,$internal=true) {
    global $CFG;
    /*
    return array (  'action'=>  'consulter',
                    'url'   =>  $url,
                    'texte'=>'menu_vide'  //pas de texte juste une icone

                );
    */
   
    $CFG->utiliser_form_actions=1; //  force chargement des actions si pas d�ja fait 
    if ($internal)
        return new icone_action('consulter',"doPopup('$url')",'menu_vide');
    else 
        return new icone_action('consulter',"openPopup('$url','',$CFG->largeur_popups,$CFG->hauteur_popups)",'menu_vide');
                
}

function get_menu_item_supprimer($url,$js_conf) {
    global $CFG;
    return array (  'action'=>  'supprimer',
                    'return'   =>  $url,
                    'texte'=>'menu_vide',  //pas de texte juste une icone
                    'image'=>'corbeille',       // l'image se nomme i_corbeille , pas i_supprimer
                    'jsconf'=>$js_conf

                );
}

function get_menu_item_modifier($url) {
    global $CFG;
    /*
    return array (  'action'=>  'modifier',
                    'url'   =>  $url,
                    'texte'=>'menu_vide',  //pas de texte juste une icone
                    'image'=>'crayon'  // les images ne se nomment pas i_modifier mais i_crayon

                );
     */           
    $CFG->utiliser_form_actions=1; //  force chargement des actions si pas d�ja fait 
    return new icone_action('modifier',"doPopup('$url')",'menu_vide');                        
}



/**
 * imprime un �lement de menu niveau 2
 * function perim�e avec le th�me v15
 * @param template $tpl le template porteur
 * @param (string $varname le nom de la balise qui va le porter ou false pour le renvoyer
 * @note : si ces deux parametres sont vide renvoie l'HTML construit par exemple pour �mission Ajax
 * @param item le type de bouton
 */
function print_menu_item ($tpl,$varname,$item) {

    // rev 1047 pour validation W3C (id dupliqu�)
    static $inum=0;
    
     // rev 981  marche aussi avec des icones d'action cr��es comem des classes
     // dans l'attente'
     if (is_object($item)) { 
        return print_menu_item2($tpl,$varname,$item);
     }  

	global $CFG;
	$modele=<<<EOM
		<!-- START BLOCK : menu_generique -->
		   <li class="menu_niveau2_item icone_{image}">
			<a 	title="{title}"
				<!-- START BLOCK : popup -->
				href="{global_jsvoid}"
                onclick="openPopup('{url}','',{lp},{hp})"
				<!-- END BLOCK : popup -->
				<!-- START BLOCK : minipopup -->
                href="{global_jsvoid}"
				onclick="openPopup('{url}','','{lpm}','{hpm}')"
				<!-- END BLOCK : minipopup -->
				<!-- START BLOCK : js -->
                href="{global_jsvoid}"
				onclick="{js}"
				<!-- END BLOCK : js -->
                <!-- START BLOCK : return -->
                href="{url}"
                <!-- END BLOCK : return -->
                <!-- START BLOCK : jsconf -->
                onclick="return confirm('{jsconf}');"
                <!-- END BLOCK : jsconf -->

                >
                <img src="{chemin_images}/i_blank.gif" alt="{alt}" />
				<span class="taille1">{texte}</span>
			</a>
			</li>
		<!-- END BLOCK : menu_generique -->
EOM;

				$tmptpl = new SubTemplatePower($modele,T_BYVAR);    //cr�er une instance

				// a le meme chemin que le template porteur
				if ($tpl)
    				$tmptpl->prepare($tpl->chemin);
				else
    				$tmptpl->prepare($CFG->chemin);
				$tmptpl->newBlock("menu_generique");

               

				if (empty($item['image'])) $item['image']=$item['action'];
				if (empty($item['texte'])) $item['texte']=traduction( "menu_".$item['action']);
				else  $item['texte']=traduction($item['texte']);

                // rev 981 on peut fixer le texte alternatif � la construction de l'�l�ment
                if (empty($item['alt'])) $item['alt']=traduction("alt_".$item['action']);


				$tmptpl->assignGlobal("image",$item['image']);
				$tmptpl->assignGlobal("inum",$inum);

                $tmptpl->assignGlobal("alt",$item['alt']);
				$inum++; // statique

				$tmptpl->assign("title",$item['alt']);

				$tmptpl->assign("texte",$item['texte']);


				if (!empty($item['js'])) {
    				$tmptpl->newBlock("js");
    				$tmptpl->assign("js",$item['js']);
				}
				// nouveau rev 962 une icone a cliquer qui appelle une page SANS popup
				// exemple une icone supprimer
				else if (!empty ($item['return'])) {
    				$tmptpl->newBlock("return");
    				$tmptpl->assign("url",$item['return']);
    				// rev 963 demande de confirmation possible
    				if (!empty($item['jsconf'])) {
        				$tmptpl->newBlock("jsconf");
        				$tmptpl->assign("jsconf",$item['jsconf']);
    				}
				} else {  //popups divers
    				if (!empty($item['url'])) {
        				$tmptpl->newBlock("popup");
        				$tmptpl->assign("url",p_session($item['url'],1));
    				} else
        				if (!empty($item['murl'])) {
            				$tmptpl->newBlock("minipopup");
            				$tmptpl->assign("url",p_session($item['murl']));
        				}
				}


				$res=$tmptpl->getOutputContent();
				if ($tpl)
    				if ($varname)
        				$tpl->assign($varname,$res);
				return $res; // pour chainage des menu_items ou affichage direct Ajax
}





/**
 * imprime un menu de niveau 2 complet avec style CSS
 */
function print_menu($tpl,$varname='menu_niveau2',$items=array(),$id='') {
    if (empty($id)) $id=$varname;

    $res="<div id=\"$id\"><ul class=\"menu_niveau2\">";
    foreach ($items as $item) {
    	//passage progressif aux icones actions V15 avec un li onClick et plus un lien href trop long
       if (is_object($item) && !empty($item->js))	
       		$res.= print_menu_item2($tpl,false,$item);
       else
       		$res.= print_menu_item($tpl,false,$item);
    }
    //print_r($res);
    if ($tpl && $varname)
    	$tpl->assign($varname, $res."</ul></div>");
    else return $res; //chainage

}


/**
 * ajoute le code n�cessaire � l'affichage d'une bulle d'aide
 * ex : mettre dans un template <tr {bulle:astuce:info_tri} >'
 * astuce et info_tri �tant des cl�s dans le fichier de langues
 * et laisser faire la traduction auto
 */
function get_bulle_aide($balise) {
    global $CFG;
    if ($CFG->masquer_infobulles) return ""; //rev 885

	$tab=explode(":",$balise);
	if ($tab[1]) $titre=traduction($tab[1]); else  $titre=traduction ("astuce");
	if ($tab[2]) $texte=traduction ($tab[2]); else $texte=traduction ("err_texte_aide_manquant");
	$texte=addslashes($texte);
	$code=<<<EOC
 onmouseover="AffBulle2('$titre','info.jpg','$texte');window.status='';return true;" onmouseout="HideBulle();"
EOC;
  return $code;
}




/**
* fonction appel�e par la classe trieuse en V2
* non encore implemente� en V 1.5
* @param $tpl le template porteur
* @param $varname le nom du marqueur qui va recevoir le sous-template (l'ic�ne)
* @param $typetri 1 ascendant -1 descendant
*/

function print_icones_tri($tpl,$varname,$typetri=0) {
	global $CFG;


	$modele=<<<EOT
<!-- START BLOCK : icones_tri -->
	<!-- START BLOCK : tri_asc -->
		<img src="{chemin_images}/tri_1.gif" alt=""/>
	<!-- END BLOCK : tri_asc -->
	<!-- START BLOCK : tri_desc -->
		<img src="{chemin_images}/tri_2.gif" alt="" />
	<!-- END BLOCK : tri_desc -->
<!-- END BLOCK : icones_tri -->
EOT;

	$tmptpl= new SubTemplatePower($modele,T_BYVAR);    //cr�er une instance
	// a le meme chemin que le template porteur
	$tmptpl->prepare($tpl->chemin);
	$tmptpl->newBlock("icones_tri");
	if($typetri>=1)
		$tmptpl->newBlock("tri_asc");
	elseif ($typetri <=-1)
		 $tmptpl->newBlock("tri_desc");
	$tpl->assign($varname,$tmptpl->getOutputContent());
}




/**
 * Returns a particular value for the named variable, taken from
 * POST or GET.  If the parameter doesn't exist then an error is
 * thrown because we require this variable.
 *
 * This function should be used to initialise all required values
 * in a script that are based on parameters.  Usually it will be
 * used like this:
 *    $id = required_param('id');
 *
 * @param string $parname the name of the page parameter we want
 * @param int $type expected type of parameter
 * @param string $errMsg : message d'erreur optionnel (PP 02/12/2008)
 * @return mixed
 */
function required_param($parname, $type=PARAM_CLEAN, $errMsg="err_param_requis") {

    // detect_unchecked_vars addition
    global $CFG;
    if (!empty($CFG->detect_unchecked_vars)) {
        global $UNCHECKED_VARS;
        unset ($UNCHECKED_VARS->vars[$parname]);
    }

    if (isset($_POST[$parname])) {       // POST has precedence
        $param = $_POST[$parname];
    } else if (isset($_GET[$parname])) {
        $param = $_GET[$parname];
    } else {
        erreur_fatale($errMsg,$parname);
    }
    // ne peut pas �tre vide !!! (required)
    $tmpStr= clean_param($param, $type);  //PP test final
    // attention "0" est empty !!!

    if (!empty($tmpStr) || $tmpStr==0) return $tmpStr;
    else erreur_fatale("err_param_suspect",$parname.' '.$param. " ".__FILE__ );


}

/**
 * Returns a particular value for the named variable, taken from
 * POST or GET, otherwise returning a given default.
 *
 * This function should be used to initialise all optional values
 * in a script that are based on parameters.  Usually it will be
 * used like this:
 *    $name = optional_param('name', 'Fred');
 *
 * @param string $parname the name of the page parameter we want
 * @param mixed  $default the default value to return if nothing is found
 * @param int $type expected type of parameter
 * @return mixed
 */
function optional_param($parname, $default=NULL, $type=PARAM_CLEAN,$errMsg="err_sql_injection") {

    // detect_unchecked_vars addition
    global $CFG;
    if (!empty($CFG->detect_unchecked_vars)) {
        global $UNCHECKED_VARS;
        unset ($UNCHECKED_VARS->vars[$parname]);
    }

    if (isset($_POST[$parname])) {       // POST has precedence
        $param = $_POST[$parname];
    } else if (isset($_GET[$parname])) {
        $param = $_GET[$parname];
    } else {
        return $default;
    }
     //pose un pb avec les parametres vide au d�part
     $tmpStr= clean_param($param, $type);  //PP test final
     // rev 971 cas des parametres booleens (0 existe !)
      if ($tmpStr || $tmpStr===0)return $tmpStr;
      else return $default;
}

/**
 * @param mixed $param the variable we are cleaning
 * @param int $type expected format of param after cleaning.
 * @return mixed
 */
function clean_param($param, $type) {

    global $CFG;

    if (is_array($param)) {              // Let's loop
        $newparam = array();
        foreach ($param as $key => $value) {
            $newparam[$key] = clean_param($value, $type);
        }
        return $newparam;
    }

    switch ($type) {
        case PARAM_RAW:          // no cleaning at all
            return $param;

        case PARAM_CLEAN:        // General HTML cleaning, try to use more specific type if possible

           // cr�� un bug avec un parametre du type 65.123 -->
            if (is_numeric($param)) {
                return $param;
            }
            // ceci cr�e un BUG si magic_quotes_gpc est d�sactiv� ...
            // ca vire les slashes simples (comme ceux du Latex par exemple)
            $param = stripslashes($param);   // Needed for kses to work fine
            $param = clean_text($param);     // Sweep for scripts, etc
            return addslashes($param);       // Restore original request parameter slashes
           //return $param;

        case PARAM_CLEANHTML:    // prepare html fragment for display, do not store it into db!!
            $param = stripslashes($param);   // Remove any slashes
            $param = clean_text($param);     // Sweep for scripts, etc
            return trim($param);

        case PARAM_INT:
            return (int)$param;  // Convert to integer

        case PARAM_CLE_C2I:
       // print_r($param); print"zzz";
	    if (preg_match('/(\d{1,6})\.(\d{1,6})/',$param, $match))
                 return $param;
	    else return '';

        case PARAM_ALPHA:        // Remove everything not a-z et tiret bas (PP)
            //return eregi_replace('[^a-zA-Z_]', '', $param); deprecated
            return preg_replace('/[^a-zA-Z_]/i', '', $param);

        case PARAM_ALPHANUM:     // Remove everything not a-zA-Z0-9 et tiret bas (PP)
            //return eregi_replace('[^A-Za-z0-9_]', '', $param); depercated
             return preg_replace('/[^A-Za-z0-9_]/i', '', $param);

        case PARAM_ALPHAEXT:     // Remove everything not a-zA-Z/_-
           //return eregi_replace('[^a-zA-Z/_-]', '', $param); deprecated
            return preg_replace('/[^a-zA-Z_]/i', '', $param);
        case PARAM_SEQUENCE:     // Remove everything not 0-9,
            //return eregi_replace('[^0-9,]', '', $param);
            return preg_replace('/[^A-Za-z0-9_-]/i', '', $param);

        case PARAM_BOOL:         // Convert to 1 or 0
            $tempstr = strtolower($param);
            if ($tempstr == 'on' or $tempstr == 'yes' ) {
                $param = 1;
            } else if ($tempstr == 'off' or $tempstr == 'no') {
                $param = 0;
            } else {
                $param = empty($param) ? 0 : 1;
            }
            return $param;

        case PARAM_NOTAGS:       // Strip all tags
            return strip_tags($param);

        case PARAM_TEXT:    // leave only tags needed for multilang
            return clean_param(strip_tags($param, '<lang><span>'), PARAM_CLEAN);

        case PARAM_SAFEDIR:      // Remove everything not a-zA-Z0-9_-
            //return eregi_replace('[^a-zA-Z0-9_-]', '', $param);
            return preg_replace('/[^a-zA-Z0-9_-]/i', '', $param);
/**
        case PARAM_CLEANFILE:    // allow only safe characters
            return clean_filename($param);
**/
            case PARAM_FILE:         // Strip all suspicious characters from filename
                $param = preg_replace('~[[:cntrl:]]|[&<>"`\|\':\\\\/]~u', '', $param);
                $param = preg_replace('~\.\.+~', '', $param);
                if ($param === '.') {
                    $param = '';
                }
                return $param;
            
            case PARAM_PATH:         // Strip all suspicious characters from file path
                $param = str_replace('\\', '/', $param);
                $param = preg_replace('~[[:cntrl:]]|[&<>"`\|\':]~u', '', $param);
                $param = preg_replace('~\.\.+~', '', $param);
                $param = preg_replace('~//+~', '/', $param);
                return preg_replace('~/(\./)+~', '/', $param);

        case PARAM_HOST:         // allow FQDN or IPv4 dotted quad
            preg_replace('/[^\.\d\w-]/','', $param ); // only allowed chars
            // match ipv4 dotted quad
            if (preg_match('/(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})/',$param, $match)){
                // confirm values are ok
                if ( $match[0] > 255
                     || $match[1] > 255
                     || $match[3] > 255
                     || $match[4] > 255 ) {
                    // hmmm, what kind of dotted quad is this?
                    $param = '';
                }
            } elseif ( preg_match('/^[\w\d\.-]+$/', $param) // dots, hyphens, numbers
                       && !preg_match('/^[\.-]/',  $param) // no leading dots/hyphens
                       && !preg_match('/[\.-]$/',  $param) // no trailing dots/hyphens
                       ) {
                // all is ok - $param is respected
            } else {
                // all is not ok...
                $param='';
            }
            return $param;

        case PARAM_URL:          // allow safe ftp, http, mailto urls
            include_once($CFG->chemin_commun . '/validateurlsyntax.php');
            if (!empty($param) && validateUrlSyntax($param, 's?H?S?F?E?u-P-a?I?p?f?q?r?')) {
                // all is ok, param is respected
            } else {
                $param =''; // not really ok
            }
            return $param;

        case PARAM_LOCALURL:     // allow http absolute, root relative and relative URLs within wwwroot
            $param=clean_param($param, PARAM_URL);
            //print($param);
            //print $CFG->wwwroot;
            if (!empty($param)) {
                if (preg_match(':^/:', $param)) {
                    // root-relative, ok!
                    //print ("ok1");
                } elseif (preg_match('/^'.preg_quote($CFG->wwwroot, '/').'/i',$param)) {
                    // absolute, and matches our wwwroot
                    //print "ok2";
                } else {
                    // relative - let's make sure there are no tricks
                    if (validateUrlSyntax($param, 's-u-P-a-p-f+q?r?')) {
                        // looks ok.
                       // print "ok3";
                    } else {
                        //print "KO";
                        $param = '';
                    }
                }
            }
            return $param;

        default:                 // throw error, switched parameters in optional_param or another serious problem
            erreur_fatale("Unknown parameter type: $type");
    }
}

function clean_text($text) {
	//pas fini revoir le code Moodle
	return $text;
}



/**
 * classe pour construire des select a la vol�e
 * mise a la fin car perturbe phpxreg v 0.7
 */
class option_select {
    var $id,$texte;
    function option_select ($id,$texte) {
        $this->id=$id;
        $this->texte=$texte;
    }
}



function print_timer($ligne) {


    global $CFG,$USER;
    if ($CFG->pas_de_timer) return ""; //pas de timer";
    if (! $ligne->affiche_chrono) return ""; //pas de chrono";

       // If the quiz has a time limit, or if we are close to the close time, include a floating timer.
    $showtimer = false;
    $timerstartvalue = 999999999999;
    if ($ligne->ts_datefin) {
        // dur�e restante pour l'examen en secondes
        $timerstartvalue = min($timerstartvalue, $ligne->ts_datefin - time());
        $showtimer = $timerstartvalue < $CFG->debut_timer; // Show the timer if we are less than 60 mins from the deadline.
       // $quizclose=($ligne->ts_datefin - time()) - $timerstartvalue;
    }
    //rev 978 passage limit� � xx minutes apr�s le d�but
    if ($ligne->ts_dureelimitepassage) {
        $showtimer=true;
        $tempsmaxi=$timerstartvalue;  //date de fin
        $timerstartvalue=$ligne->ts_dureelimitepassage*60-(time()-get_date_premier_passage($USER->id_user,$ligne->id_examen,$ligne->id_etab)) ;
        //on ne le corrigera pas si il depasse la date de fin ... rev 978 17/12/2010
        $timerstartvalue=min($timerstartvalue,$tempsmaxi);
    }

    if ($showtimer && $timerstartvalue > 0) {
        $timerstartvalue = max($timerstartvalue, 1); // Make sure it starts just above zero.
    } else return '';//  "$showtimer.' '.$timerstartvalue";

    $texte=traduction ("temps_restant");

    //conversion en millisecondes de la dur�e restante
    $timerstartvaluems=$timerstartvalue*1000;

$script=<<<EOS
<script type="text/javascript">
//<![CDATA[



// @EC PF : client time when page was opened
var ec_page_start = new Date().getTime();
// @EC PF : client time when quiz should end
var ec_quiz_finish = ec_page_start + $timerstartvaluems;

//]]>
</script>
<div id="timer">
<!--EDIT BELOW CODE TO YOUR OWN MENU-->
<table class="liste" border="0" cellpadding="0" cellspacing="0" style="width:120px;">
<tr>
    <td  style="background-color: transparent;" width="100%">
    <table  border="0" width="120" cellspacing="0" cellpadding="0">
    <tr>
        <th  width="100%" scope="col">$texte</th>
    </tr>
    <tr>
        <td id="QuizTimer" align="center" width="100%">
        <form id="clock"><input onfocus="blur()" type="text" id="time"
        style="background-color: transparent; border: none; width: 70%; font-family: sans-serif; font-size: 14pt; font-weight: bold; text-align: center;" />
        </form>
        </td>
    </tr>
    </table>
    </td>
</tr>
</table>
<!--END OF EDIT-->
</div>
<script type="text/javascript">
//<![CDATA[

var timerbox = document.getElementById('timer');
var theTimer = document.getElementById('QuizTimer');
var theTop = 100;
var old = theTop;

movecounter(timerbox);

document.onload = countdown_clock(theTimer);
//]]>
</script>
EOS;

return $script;
}

/**
 * rev 979 pr�paration mise en place de filtres sur les textes, par exemple pour �mettre du Latex
 */
 function affiche_texte($texte,$appliqueFiltres=true) {
    global $CFG;

    //$texte= trim(nl2br(htmlspecialchars($texte)));
    $texte= trim(nl2br($texte));
    if ($appliqueFiltres) {

    if ($CFG->activer_filtre_latex) {
         require_once($CFG->dirroot.'/commun/filtres/tex/filter.php');
         $texte=tex_filter(0,$texte);
    }
    }
    return $texte;
 }


 /*
  * rev 981 utilisation de formes cach�es pour les actions dans les listes et autres
  * @param $tpl
  * @param $varname
  * @param $param_retour  arguments supplementaires a mettre en get (tri,page...)
  * @param $url
  */

  function print_form_actions ($tpl,$varname,$params_retour='',$url='liste.php') {
  	global $CFG;
  	
  	static $dejaEmis=0; // il est aussi forc� dans un popup qui demande une l�gende !!!!



	$params_retour_session=urlencode($params_retour).'&'.get_session_param(); //attention pas de &amp; ici
 
  	   $modele=<<<EOM

<div style="display:none;">

<script type="text/javascript">
//<![CDATA[
function doAction (action,id) {
	var f=document.getElementById("action");
	if (f !=null) {
		f.action.value=action ;
		f.id_action.value=id;
		f.submit();
	}
}

function doPopup(url) {
 url=url+'&url_retour='+'$params_retour_session';
 openPopup(url,'',$CFG->largeur_popups,$CFG->hauteur_popups);
}

function doMiniPopup(url) {
 url=url+'&url_retour='+'$params_retour_session';
 openPopup(url,'',$CFG->largeur_minipopups,$CFG->hauteur_minipopups);
}

//]]>
</script>

<form id="action" action="$url?$params_retour" method="post">
<input type="hidden" name="action" value='' />
<input type="hidden" name="id_action" value='' />
<input type="hidden" name="url_retour" value="{url_retour}" />
<!-- START BLOCK : id_session -->
	<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->
</form>


</div>
EOM;
	
	// l'ajout de ce div cach� est requis avec le th�me v15
	// il est donc fait par le moteur de templates si il n'a pas d�ja �t� fait dans une liste
	// qui a besoin de pr�ciser les param�tres de retour (tri, selection...)
	// il n'est en principe pas activ� dans les Popup 
	// son insertion est aussi forc�e par une simple demande de legende
	if ($dejaEmis) { 
		$tpl->assign($varname,''); return;
	}	 
	$tmptpl = new SubTemplatePower($modele,T_BYVAR);    //cr�er une instance
	// a le meme chemin que le template porteur
    $tmptpl->prepare($tpl->chemin);
    $tmptpl->assign('url_retour',urlencode($params_retour));
	$tpl->assign($varname,$tmptpl->getOutputContent()); 
	$dejaEmis ++;
  }



/**
 * imprime un �lement d'une liste d'icone action
 * tr�s simplifi� depuis le passage au th�me v15 et avec des actions de type js
 * et plus de <a href ...
 * @param template $tpl le template porteur
 * @param (string $varname le nom de la balise qui va le porter ou false pour le renvoyer
 * @note : si ces deux parametres sont vide renvoie l'HTML construit par exemple pour �mission Ajax
 * @param item le type de bouton
 */
function print_menu_item2 ($tpl,$varname,$item) {


    global $CFG;
    $modele=<<<EOM
        <!-- START BLOCK : menu_generique -->
           <li class="menu_niveau2_item icone_{image}"
               <!-- START BLOCK : js -->
                onclick="{js}"
                <!-- END BLOCK : js -->
            >    
            <img src="{chemin_images}/i_blank.gif" alt="{alt}" title="{title}" />  
             <!-- START BLOCK : texte -->
                <span class="taille1">{texte}</span>
             <!-- END BLOCK : texte -->
                 
            </li>
        <!-- END BLOCK : menu_generique -->
EOM;


                $tmptpl = new SubTemplatePower($modele,T_BYVAR);    //cr�er une instance

                // a le meme chemin que le template porteur
                if ($tpl)
                    $tmptpl->prepare($tpl->chemin);
                else
                    $tmptpl->prepare($CFG->chemin);
                $tmptpl->newBlock("menu_generique");

                // rev 981  marche aussi avec des icones d'action cr��es 
                // comme des tableaux (ancienne m�thode)
                if (is_array($item))
                    $item=(object)$item;

                if (empty($item->image)) $item->image=$item->action;
                
                if (empty($item->texte)) $item->texte=traduction( "menu_".$item->action);
                else  $item->texte=traduction($item->texte);
                   
                
                // rev 981 on peut fixer le texte alternatif � la construction de l'�l�ment
                if (empty($item->alt)) $item->alt=traduction("alt_".$item->action);

                $tmptpl->assignGlobal("image",$item->image);

                $tmptpl->assign("title",$item->alt);
                $tmptpl->assign("alt",$item->alt);

                if (!empty($item->js)) {
                    $tmptpl->newBlock("js");
                    $tmptpl->assign("js",$item->js);
                }
                
                if (!empty($item->texte)) { // si pas vide (ex <> menu_texte)
                	$tmptpl->newBlock("texte");
                    $tmptpl->assign("texte",$item->texte);
                }

                $res=$tmptpl->getOutputContent();
                if ($tpl)
                    if ($varname)
                        $tpl->assign($varname,$res);
                return $res; // pour chainage des menu_items ou affichage direct Ajax
}



/**
 * imprime un menu de niveau 2 complet avec style CSS
 */
function print_icones_action($tpl,$varname='icones_action',$items=array(),$id='') {
    if (empty($id)) $id=$varname;

    $res="<div id=\"$id\"><ul class=\"menu_niveau2\" >";
    foreach ($items as $item) {
       $res.= print_menu_item2($tpl,false,$item);
    }
    //print_r($res);
    if ($tpl && $varname)
   		$tpl->assign($varname, $res."</ul></div>");
   	else return $res;	

}


class icone_action{

    var $action='blanc';
    var $js='';
    var $texte='';
    var $alt='';

    function icone_action ($action='blanc',$js='',$texte='menu_vide',$alt='') {
        $this->action=$action;
        $this->js=$js;
        $this->texte=$texte;
        $this->alt=$alt;
    }


}


/**
 * d'apr�s http://www.justindocanto.com/scripts/detect-a-mobile-device-in-php-using-detectmobiledevice 
 */

function detectMobileDevice() {
    if (isset($_SERVER['HTTP_USER_AGENT'])) {  
    if(preg_match('/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i', $_SERVER['HTTP_USER_AGENT'])) {
        return true;
    }
    else {
        return false;
    }
    } else 
        return false; 
}
