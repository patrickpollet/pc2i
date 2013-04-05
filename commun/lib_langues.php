<?php
/**
 * @author Patrick Pollet
 * @version $Id: lib_langues.php 1267 2011-09-26 16:53:23Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/*
 * bibliotheque de manipulation de la traduction
 * pour l'instant les langues ne SONT pas g�r�es par �tablissement au niveau national
 * ou par composantes au niveau local
 * pour traduire la plateforme il faut :
 * -
 * renommer et traduire le fichier langues/fr.php
 * renseignr  son nom dans le fichier commun/constantes.php  ( Version <1.41)
 * ou dans config.php (V >=1.5)



// charger les langues en premier
// rev 1027 ne pas appeler erreur_fatale car rien n'est encore initialis� donc die() !!!!

*********************************************************************************/
//fichier commun a toutes les plate-formes
if (!isset($fichier_langue_defaut))
	$fichier_langue_defaut="fr.php";

 // rev 978 fichier sp�cifique � la pf c2in1, c2i2mead ...
 if (!isset($fichier_langue_plateforme))
    $fichier_langue_plateforme="plateforme.php";

if (!isset($fichier_langue))
	 $fichier_langue = $fichier_langue_defaut;

if (!is_readable($chemin."/langues/".$fichier_langue))
	 $fichier_langue = $fichier_langue_defaut;

if (! is_readable($chemin."/langues/".$fichier_langue))
	die ("err_fichier_langues_inconnu ".$chemin."/langues/".$fichier_langue);

include_once ($chemin."/langues/".$fichier_langue);

// rev 1026 svn 970) fichier sp�cifique � chaque plateforme C2I
if (! is_readable($chemin.'/langues/'.$fichier_langue_plateforme))
    die  ("err_fichier_langues_inconnu ".$chemin.'/langues/'.$fichier_langue_plateforme);

include_once ($chemin.'/langues/'.$fichier_langue_plateforme);

/* gestion des fichiers de langues supplementaires dans le dossier codes/xxxx/langues courant*/
//pas de warning si non trouve
if (is_file("./langues/".$fichier_langue)) {
    @include_once ("./langues/".$fichier_langue);

}

/* gestion du fichier de langue supplementaire dans le dossier locale en dernier*/
//pas de warning si non trouve

// on ne peut pas encore tester si la pf est locale ou non (config non lue)
// d'ou ce test...
if (is_file($chemin."/codes/nationale/".$fichier_langue)) {
    @include_once ($chemin."/codes/nationale/".$fichier_langue);
}
elseif (is_file($chemin."/codes/locale/".$fichier_langue)) {
    @include_once ($chemin."/codes/locale/".$fichier_langue);
}

function ucFirstConditionnel ($chaine, $ouiNon) {
    return ($ouiNon ? ucfirst($chaine):$chaine);
}


 /**
* cette fonction accepte un nombre variable d'arguments
* ce qui permet d'inserer des %s dans les chaines a traduire
* ex :  "test1"=>" %s ca va ?",
*  	"test2"=>" %s %s ",
*  	"test3"=>" %s aie ouille %s %s ",
*	  "multiplication"=>"%s x %s = %s"
* et
* print traduction ("validation")."\n";
* print traduction ("validation",0)."\n";

* print traduction ("test1",1, "hello")."\n";
* print traduction ("test2",1, "hello",0)."\n";
* print traduction ("test3",1, "hello",0," ca roule")."\n";
* et qui donne

* Validation
* validation
* hello ca va ?
* hello 0
* hello aie ouille 0  ca roule

* for ($j=1; $j<10; $j++)
*	for ($i=1; $i<10; $i++) {
*   		print traduction ("multiplication",0, $j, $i, $i*$j)."\n";
*	}
* ...
* 8 x 8 = 64
* 8 x 9 = 72
* 9 x 1 = 9
* 9 x 2 = 18
* 9 x 3 = 27
* 9 x 4 = 36
* 9 x 5 = 45
* 9 x 6 = 54
* 9 x 7 = 63
* 9 x 8 = 72
* 9 x 9 = 81


*/


function traduction_cond ($cle){
	if (est_traduite($cle))
		return traduction( $cle) ;
	else return $cle;

}

function est_traduite($cle) {
	global $textes_langues,$CFG,$USER;
	return isset($textes_langues[$cle]);
}

/**
 * rev 973  pour des clients REST qui causent utf8
 *  si la Bd est en utf8 ET que les fichiers de langues ne le sont pas encore
 * regl� en revision 978 si la BD est en utf8, les fichiers de langue le seront aussi
 *
 */
function retouche_utf8($string) {
    global $CFG;
   // if (!empty($CFG->unicodedb)) return utf8_encode($string);
   // else return $string;
   return $string;
}

function traduction($cle,$ucFirst=1) {

	global $textes_langues,$CFG,$USER;

	if (isset($textes_langues[$cle])) {
		if (func_num_args()<=2)
			return retouche_utf8(ucFirstConditionnel($textes_langues[$cle],$ucFirst));
		else {
			$res=$textes_langues[$cle];
			if (strstr($res,"%s")) {  //il y a bien des %s dans la cl� de traduction (sinon renvoie 1)
				$args="";
				for ($i=2; $i <func_num_args(); $i++) {
					$args.= ",\"".func_get_arg($i)."\"";
				}
				//print "$args\n";

				$cmd="\$res=sprintf(\"$res\" ".$args.");";
				//print "$cmd\n";
				eval($cmd);
				//print "$res\n";
				return retouche_utf8(ucFirstConditionnel($res,$ucFirst));

			} else {   //pas de %s tout mettre bout � bout
				$ret=retouche_utf8(ucFirstConditionnel($textes_langues[$cle],$ucFirst));
				for ($i=2; $i <func_num_args(); $i++) {
					$ret.= " ".func_get_arg($i)." ";
					return $ret;
				}
			}
		}
	}

	//attention lib_langues charg� vant lib_config
	// donc notice possibles au 1er �cran
	if (!empty($CFG->debug_traduction)) {

            return "[[$cle]]";
    }         // signale pb au d�veloppeur
	else {

        return null; // signale pb au d�veloppeur en mode DEBUG_TEMPLATES g�r� par templatepower
    }
}

// compat Moodle
//utilis� pour les fichiers de traduction de l'�diteur HTML
function get_string ($str,$ou='',$a='') {
	global $CFG,$string; //tres important de mettre $string en global pour le 2eme chargement !!!
	//print ("$str $ou\n");
	if (!empty($ou)&& file_exists($CFG->chemin.'/langues/'.$ou.'.php')) {
		require_once ($CFG->chemin.'/langues/'.$ou.'.php');
		if (!empty($string[$str]))
            if ($CFG->unicodedb) return $string[$str];
            else return utf8_decode($string[$str]);
        else return "rate";
	}
    return traduction($str,true,$a);
    //return $CFG->chemin;
    }

    function print_string($str,$ou='',$a='') {
    	echo get_string($str,$ou,$a);
    }

