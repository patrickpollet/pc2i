<?php


/**
 * @author Patrick Pollet
 * @version $Id: export_referentiel_xml_moodle.php 1272 2011-10-17 14:24:58Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
 * export du r�ferentiel au format XML attendu par le module Moodle referentiel
 */
ob_start();

$chemin = '../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

//require_login('P');

$CFG->separateur_codage_competence='';  // vide sur la PF . sur mod/referentiel !

$CFG->separateur_codage_item='.';  //toujours


$name=$CFG->c2i.'_referentiel_moodle_'.time().'.xml';

header("content-type: text/xml");
header('Content-Disposition: attachment; filename="'.$name.'"');



/**
 * classes emettant du XML
 */

class competence {
	var $domaine,$code, $description, $numero;
	var $items = array ();

	function __construct($domaine,$code, $description, $numero) {
        $this->domaine=$domaine;
		$this->code = $code;
		$this->description = fix_special_chars( $description) ; // rev 975
		$this->numero = $numero;
		$this->items = $this->get_alineas_associes();
		$this->nb_item_competences = count($this->items);
	}

	/**
	 * liste des items (alineas) d'une comp�tence (domaine)
	 */
	function get_alineas_associes() {
		$alineas = get_alineas($this->domaine.$this->code); //A1, B3 ...
		$ret = array ();
		$i = 1;
		foreach ($alineas as $alinea) {
			$ret[] = new item($this->domaine,$this->code,$alinea->alinea, $alinea->aptitude, $i++);
		}
		return $ret;
	}


	function toXML() {
        global $CFG;
        $code=$this->domaine.$CFG->separateur_codage_competence.$this->code;
		$ret =<<<EOR
         <code_competence>{$code}</code_competence>
   <description_competence>
<text>{$this->description}</text>
</description_competence>
   <num_competence>{$this->numero}</num_competence>
   <nb_item_competences>{$this->nb_item_competences}</nb_item_competences>

EOR;
		foreach ($this->items as $item) {
			$ret .= "<item>\n" . $item->toXML() . "</item>\n";
		}

		return $ret;
	}
}

class item {
	var $domaine,$competence,$code, $description, $numero, $poids = 1, $empreinte = 1;

	function __construct($domaine,$competence,$code, $description, $numero) {
        $this->domaine=$domaine;
        $this->competence= $competence;
		$this->code =$code;
		$this->description =fix_special_chars( $description);  // rev 975
		$this->numero = $numero;
        $this->requis=traduction('obligatoire');
	}

	function toXML() {
        global $CFG;
		$ret =<<<EOR
    <code>{$this->domaine}{$CFG->separateur_codage_competence}{$this->competence}{$CFG->separateur_codage_item}{$this->code}</code>
    <description_item>
<text>{$this->description}</text>
</description_item>
    <type_item>$this->requis</type_item>
    <poids_item>{$this->poids}</poids_item>
    <empreinte_item>{$this->empreinte}</empreinte_item>
    <num_item>{$this->numero}</num_item>
EOR;

		return $ret;
	}

}

class domaine {
	var $code, $description, $numero;
	var $competences = array ();

	function __construct($code, $description, $numero) {
		$this->code = $code;
		$this->description = fix_special_chars( $description);
		$this->numero = $numero;
		$this->competences = $this->get_referentiels_associes();
		$this->nb_competences = count($this->competences);

	}


	/**
	 * liste des referentiels appartenant � un domaine (A,B...)
	 */
	function get_referentiels_associes() {
		global $CFG;
		$ret = array ();
		$sql =<<<EOS
			select * from {$CFG->prefix}referentiel
			where referentielc2i like '{$this->code}%'
			order by referentielc2i
EOS;
			$refs = get_records_sql($sql);
			$i = 1;
			foreach ($refs as $ref) {
				// code interne = 1,2,3 ...
				//plus facile pour emettre a volont� A1 ou A.1 selon ....
				$ref->referentielc2i=str_replace($this->code,'',$ref->referentielc2i);
				$ret[] = new competence($this->code,$ref->referentielc2i, fix_special_chars( $ref->domaine), $i++);
			}
			return $ret;
	}

	function toXML() {
		$ret =<<<EOR
			<code_domaine>{$this->code}</code_domaine>
			<description_domaine>
			<text>{$this->description}</text>
			</description_domaine>
			<num_domaine>{$this->numero}</num_domaine>
			<nb_competences>{$this->nb_competences}</nb_competences>
EOR;

			foreach ($this->competences as $competence) {
				$ret .= "<competence>\n" . $competence->toXML() . "</competence>\n";
			}
			return $ret;

	}

	function get_codes_competences() {
		global $CFG;
		$ret="";
		foreach ($this->competences as $competence)
		foreach($competence->items as $item)
		$ret.=$competence->domaine.$CFG->separateur_codage_competence.$competence->code.'.'.$item->code.'/';
		return $ret;
	}

	function get_empreintes() {
		$ret="";
		foreach ($this->competences as $competence)
		foreach($competence->items as $item)
		$ret.=$item->empreinte.'/';
		return $ret;
	}
}

/**
 * y en aura-t-il jamais d'autres ?
 * BUG ne fonctionne qu'avec le C2i niveau1 referentiel V1
 */

$domaines=array(
    new domaine("D", traduction('domaine_D'), 1),
);

/**
 * parcourir le tableau des domaines et renvoyer la liste
 */
function get_liste_codes_comp($domaines) {
	$ret="";
    foreach($domaines as $domaine)
     $ret.=$domaine->get_codes_competences();
    return $ret;
}

/**
 * parcourir le tableau des domaines et renvoyer la liste
 */
function get_liste_empreintes($domaines) {
    $ret="";
    foreach($domaines as $domaine)
     $ret.=$domaine->get_empreintes();
    return $ret;
}

$description = traduction('description_referentiel');
$code = $CFG->c2i;
$nom = traduction('nom_referentiel');
$url = traduction('url_referentiel', false);
$time = time();
$nbdomaines = count($domaines);
$liste_codes_comp = get_liste_codes_comp($domaines);
$liste_empreintes = get_liste_empreintes($domaines);

$xml =<<<EOX
<?xml version="1.0" encoding="{$CFG->encodage}"?>
<referentiel>
 <name>$nom</name>
 <code_referentiel>$code</code_referentiel>
 <description_referentiel>
   <text>$description</text>
 </description_referentiel>
 <url_referentiel>$url</url_referentiel>
 <seuil_certificat>80</seuil_certificat>
 <time_modified>$time</time_modified>
 <nb_domaines>$nbdomaines</nb_domaines>
 <liste_codes_competence>$liste_codes_comp</liste_codes_competence>
 <liste_empreintes_competence>$liste_empreintes</liste_empreintes_competence>
EOX;

foreach ($domaines as $domaine) {
	$xml .= "<domaine>\n" . $domaine->toXML() . "\n</domaine>\n";
}

$xml .= "</referentiel>\n";
while (@ ob_end_clean());
echo $xml;
?>
