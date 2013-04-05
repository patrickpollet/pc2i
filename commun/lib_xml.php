<?php


/**
 * @author Patrick Pollet
 * @version $Id: lib_xml.php 1303 2012-09-14 14:10:49Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
 * bibliotheque de manipulations des imports export via XML
 * ne pas inclure syst�matiquement mais juste quand besoin
 * @uses global $CFG configuration g�n�rale
 * @uses $CFG->chemin_ressources   (par d�faut en 1.4 $chemin/ressources en relatif)
 */

 if (is_admin()) {   //utilisateur courant uniquement
    maj_bd_xml();
 }

 function maj_bd_xml () {
 }


$currdir = $CFG->chemin_ressources;
if (($res = verifie_droits_fichiers($currdir)) != '')
	erreur_fatale($res,realpath($currdir));




/**
 * reconstruit toutes les XML en cas de d�placement dossier ressources, pertes ...
 * ne g�rait pas les documents en 1.4, ni en 1.5
 */
function repare_xmls() {
    $questions=get_records("questions");
    foreach ($questions as $ligne) {
        to_xml_qti($ligne->id,$ligne->id_etab);
    }
}

/**
 * inutile de tester le droits. apple�e quand ca va bien ...
 * devrait disparaitre en 1.6 au profit d'un format XML plus l�ger ...
 */
function to_xml_qti($idq,$ide=false) {

	global $USER,$CFG;
	if (!$ide) $ide=$USER->id_etab_perso;

	set_time_limit(0);

	$ligne = get_question($idq,$ide);


	if (!is_dir($CFG->chemin_ressources."/questions/".$ide."_".$idq)){
		mkdir($CFG->chemin_ressources."/questions/".$ide."_".$idq);
	}

	// gestion des familles
	$famille_proposee=chaine_xml($ligne->famille_proposee);
	$id_famille_proposee = $ligne->id_famille_proposee;
	$id_famille_validee = $ligne->id_famille_validee;

	$titre=chaine_xml($ligne->titre);
	$ref = $ligne->referentielc2i;
	$alinea=$ligne->alinea;


	$auteur=chaine_xml($ligne->auteur);
	$auteur_mel=chaine_xml($ligne->auteur_mail);
	$univ=$ligne->id_etab;
	$lang=$ligne->langue;
	$mots_cles=explode(";",chaine_xml($ligne->mots_cles));
	$difficulte=chaine_xml($ligne->difficulte);
	switch($difficulte){
		case "Tr�s facile" : $difficulte = "very easy" ; break;
		case "facile" : $difficulte = "easy" ; break;
		case "moyen" : $difficulte = "medium" ; break;
		case "difficile" : $difficulte = "difficult" ; break;
		case "tr�s difficile" : $difficulte = "very difficult" ; break;
		default : $difficulte = "medium" ; break;
	}
	$pre_requis=chaine_xml($ligne->pre_requis);
	$os=chaine_xml($ligne->os);
	$suite_b=chaine_xml($ligne->suite_bureau);
	$autre_l=chaine_xml($ligne->autre_logiciel);
	$duree_vie=explode("-",chaine_xml($ligne->duree_de_vie));
	$contexte=chaine_xml($ligne->contexte);
	$caracteristiques=chaine_xml($ligne->caracteristiques);
	$date_de_creation=chaine_xml($ligne->date_de_creation);

	$c2i1 = "<?xml version=\"1.0\" encoding=\"".$CFG->encodage."\"?>";
	$c2i1 .= "\n<".$CFG->c2i.">";
	$c2i1 .= "\n<univ>".$ide."</univ>";
	$c2i1 .= "\n<question>".$idq."</question>";
	$c2i1 .= "\n<ref>".$ref."</ref>";
	$c2i1 .= "\n<alinea>".$alinea."</alinea>";

	$c2i1 .= "\n<famille_proposee>".$famille_proposee."</famille_proposee>";
	$c2i1 .= "\n<id_famille_proposee>".$id_famille_proposee."</id_famille_proposee>";
	$c2i1 .= "\n<id_famille_validee>".$id_famille_validee."</id_famille_validee>";

	$c2i1 .= "\n<os>".$os."</os>";
	$c2i1 .= "\n<suite_bureau>".$suite_b."</suite_bureau>";
	$c2i1 .= "\n<autre_logiciel>".$autre_l."</autre_logiciel>";
	$c2i1 .= "\n<auteur>";
	$c2i1 .= "\n<nom>".$auteur."</nom>";
	$c2i1 .= "\n<etab>".$ide."</etab>";
	$c2i1 .= "\n<courriel>".$auteur_mel."</courriel>";
	$c2i1 .= "\n</auteur>";
	$c2i1 .= "\n</".$CFG->c2i.">";

	// �criture du fichier xml contenant les champs sp�cifiques c2i nuveau 1
	$fp_c2i1 = fopen($CFG->chemin_ressources."/questions/".$ide."_".$idq."/c2i1.xml","w");
	fputs($fp_c2i1, $c2i1);
	fclose($fp_c2i1);

	$lom = "<?xml version=\"1.0\" encoding=\"".$CFG->encodage."\"?>";

	$lom .= "\n<lom xmlns=\"http://ltsc.ieee.org/xsd/LOM\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchemainstance\" xsi:schemaLocation=\"http://ltsc.ieee.org/xsd/LOM http://ltsc.ieee.org/xsd/lomv1.0/lom.xsd\">";
	$lom .= "\n<general>";
	$lom .= "\n<title>";
	$lom .= "\n<string>".$titre."</string>";
	$lom .= "\n</title>";
	$lom .= "\n<language>";
	$lom .= $lang;
	$lom .= "</language>";
	$lom .= "\n<description>";
	$lom .= "\n<string>".$caracteristiques."</string>";
	$lom .= "\n</description>";
	if ($ligne->mots_cles !=""){
		foreach($mots_cles as $mot){
			$lom .= "\n<keyword>";
			$lom .= "\n<string>".trim($mot)."</string>";
			$lom .= "\n</keyword>";
		}
	}
	$lom .="\n</general>";
	$lom .= "\n<lifeCycle>";
	$lom .= "\n<status>";
	$lom .= "\n<source>LOMv1.0</source>";
	$lom .= "\n<value>final</value>";
	$lom .= "\n</status>";
	$lom .= "\n<contribute>";
	$lom .= "\n<role>";
	$lom .= "\n<source>LOMv1.0</source>";
	$lom .= "\n<value>author</value>";
	$lom .= "\n</role>";
	$lom .= "\n<entity>";
	$lom .= "\nBEGIN:VCARD";
	$lom .= "\nFN:".$auteur;
	$lom .= "\nEND:VCARD";
	$lom .= "\n</entity>";
	$lom .= "\n<date>";
	$lom .= "\n<dateTime>".$date_de_creation."</dateTime>";
	$lom .= "\n</date>";
	$lom .= "\n</contribute>";
	$lom .= "\n</lifeCycle>";
	$lom .= "\n<technical>";
	$lom .= "\n<duration>";
	if (sizeof($duree_vie)==3) $lom .= "P".$duree_vie[0]."Y".$duree_vie[1]."M".$duree_vie[2]."D";
	$lom .= "</duration>";
	$lom .= "\n</technical>";
	$lom .= "\n<educational>";
	$lom .= "\n<difficulty>";
	$lom .= "\n<source>LOMv1.0</source>";
	$lom .= "\n<value>".$difficulte."</value>";
	$lom .= "\n</difficulty>";
	$lom .= "\n</educational>";
	$lom .= "\n<classification>";
	$lom .= "\n<purpose>prerequisite</purpose>";
	$lom .= "\n<description>".$pre_requis."</description>";
	$lom .= "\n</classification>";
	$lom .= "\n<classification>";
	$lom .= "\n<purpose>competency</purpose>";
	$lom .= "\n<description>".$contexte."</description>";
	$lom .= "\n</classification>";
	$lom .= "\n</lom>";

	// �criture du fichier xml contenant les champs lom
	$fp_lom = fopen($CFG->chemin_ressources."/questions/".$ide."_".$idq."/lom.xml","w");
	fputs($fp_lom, $lom);
	fclose($fp_lom);

	$qti = "<?xml version=\"1.0\" encoding=\"".$CFG->encodage."\" standalone=\"no\"?>";
	$qti .= "\n<questestinterop>";
	$qti .= "\n<item title=\"Multiple Choice\" ident=\"XXX\">";
	$qti .= "\n<presentation>";
	$qti .= "\n<flow>";
	$qti .= "\n<response_lid ident=\"".$ide.".".$idq."\" rcardinality=\"multiple\">";
	$qti .= "\n<render_choice maxnumber=\"10\">";
	$qti .= "\n<material>";
	$qti .= "\n<mattext texttype=\"text/plain\"><![CDATA[".$titre."]]></mattext>";
	$qti .= "\n</material>";

    $docs=get_documents($idq,$ide,false);
    foreach ($docs as $rowd) {
		$typemime = typeMime($rowd->id_doc.".".$rowd->extension);
		$qti .= "\n<material>";
		$qti .= "\n<matimage imagtype=\"".$typemime."\" uri=\"documents/".$rowd->id_doc.".".$rowd->extension."\"/>";
		$qti .= "\n</material>";
	}


	// gestion des r�ponses
	$reponses=get_reponses($idq,$ide,false);
    foreach($reponses as $ligne_r) {
  			$qti .= "\n<flow_label>";
			$qti .= "\n<response_label ident=\"".$ligne_r->num."\">";
			$qti .= "\n<material>";
			$qti .= "\n<mattext texttype=\"text/plain\"><![CDATA[".chaine_xml($ligne_r->reponse)."]]></mattext>";
			$qti .= "\n</material>";
			$qti .= "\n</response_label>";
			$qti .= "\n</flow_label>";
	}

	$qti .= "\n</render_choice>";
	$qti .= "\n</response_lid>";
	$qti .= "\n</flow>";
	$qti .= "\n</presentation>";
	$qti .= "\n<resprocessing>";
	$qti .= "\n<outcomes>";
	$qti .= "\n<decvar varname=\"SCORE\" vartype=\"Integer\" defaultval=\"0\" maxvalue=\"1\" minvalue=\"0\"/>";
	$qti .= "\n</outcomes>";
	$qti .= "\n<respcondition title=\"Correct\" continue=\"Yes\">";
	$qti .= "\n<conditionvar>";
	$qti .= "\n<and>";


    foreach ($reponses as $ligne_r)
        if ($ligne_r->bonne=='OUI') {
   			$qti .= "\n<varequal respident=\"".$ide.".".$idq."\">";
			$qti .= "\n<![CDATA[".$ligne_r->num."]]></varequal>";
		}


	$qti .= "\n<not>";
	$qti .= "\n<or>";

    foreach ($reponses as $ligne_r)
        if ($ligne_r->bonne=='NON') {
			$qti .= "\n<varequal respident=\"".$ide.".".$idq."\">";
			$qti .= "\n<![CDATA[".$ligne_r->num."]]></varequal>";
		}


	$qti .= "\n</or>";
	$qti .= "\n</not>";
	$qti .= "\n</and>";
	$qti .= "\n</conditionvar>";
	$qti .= "\n</respcondition>";
	$qti .= "\n</resprocessing>";
	$qti .= "\n</item>";
	$qti .= "\n</questestinterop>";

	// �criture du fichier xml contenant les champs lom
	$fp_qti = fopen($CFG->chemin_ressources."/questions/".$ide."_".$idq."/qti.xml","w");
	fputs($fp_qti, $qti);
	fclose($fp_qti);
}


/**
 * TODo tant que le format reduit non d�ploy� ( v 1.6)
 */
function from_xml_qti () {
}



class category_xml_moodle {
    var $text;

    function __construct ($text) {
        $this->text=$text;
    }

    function toxml() {
        $ret=<<<EOT

    <question type="category">
        <category>
            <text>\$course\$/{$this->text}</text>
        </category>
    </question>

EOT;
    return $ret;
    }
}

/**
 * classes d'export des questions au format xml_moodle 1.9
 */

class question_xml_moodle {

    var $type;
    var $name;
    var $questiontext;
    var $question;
    var $documents=null;
    var $reponses=null;
    var $image='';
    var $generalfeedback='';
    var $defaultgrade=1;
    var $penalty=0;
    var $hidden=0;
    var $shuffleanswers;
    var $single;
    var $correctfeedback=''; //prevision 1.6 et Moodle
    var $incorrectfeedback=''; //prevision 1.6 et Moodle
    var $partiallycorrectfeedback=''; //prevision 1.6 et Moodle

    function __construct ($type,$question,$reponses=null,$documents=null) {
	    $this->question=$question;
	    $this->type=$type;
	    $this->name=$question->id_etab.".".$question->id;
	    $this->questiontext=$question->titre;
	    $this->shuffleanswers=!empty($reponses);
	    $this->single=0;  //jamais
	    $this->defaultgrade=empty($reponses) ?0:1;
	    $this->reponses=$reponses;
	    $this->documents=$documents;

	    if (! est_validee($question)) $this->hidden=1;
	    $this->generalfeedback=isset($question->generalfeedback)?$question->generalfeedback:'';
	    $this->correctfeedback=isset($question->correctfeedback)?$question->correctfeedback:'';
	    $this->incorrectfeedback=isset($question->incorrectfeedback)?$question->incorrectfeedback:'';
	    $this->partiallycorrectfeedback=isset($question->partiallycorrectfeedback)?$question->partiallycorrectfeedback:'';


    }


    //partie commune a toutes (description, multichoice ...)
    function beginxml() {
        $ret=<<<EOT
            <question type="{$this->type}">
                <name><text>{$this->name}</text>
                </name>
                <questiontext format="html">
                    <text><![CDATA[{$this->questiontext}]]></text>
                    </questiontext>
                    <generalfeedback><text><![CDATA[{$this->generalfeedback}]]></text></generalfeedback>
                    <defaultgrade>{$this->defaultgrade}</defaultgrade>
                    <penalty>0</penalty>
                    <hidden>{$this->hidden}</hidden>
                    <shuffleanswers>{$this->shuffleanswers}</shuffleanswers>

EOT;
                    return $ret;
    }

                    function endxml() {
                        return "\n</question>\n\n";

                    }

                    function reponsesxml() {

                        if (empty( $this->reponses)) return "";

                        $B=0;  //nombre de bonnes r�ponse
                        $M=0;  // nombre de mauvaises r�ponse;
                        foreach ($this->reponses as $reponse) {
                            if ($reponse->bonne=='OUI') $B++;
                            else $M++;
                        }

                        if ($B)
                          $BV=100/$B; //fraction de 1 pt par Bonne
                        else
                            $BV=0;
                        if ($M)
                          $MV=-100/$M; //p�nalit� par mauvaise
                        else
                          $MV=-$BV;
                        /*
                        $BV=100/$B;
                        $MV=100/$M;
                        */
                        //$this->single=$B==1; // drapeau r�ponse unique pour Moodle
                        $this->single=0; // toujours en choix multiple meme si une bonne r�ponse
                        $ret=<<<EOT
                            <single>{$this->single}</single>
                            <correctfeedback><text><![CDATA[{$this->correctfeedback}]]></text></correctfeedback>
                            <partiallycorrectfeedback><text><![CDATA[{$this->partiallycorrectfeedback}]]></text></partiallycorrectfeedback>
                            <incorrectfeedback><text></text><![CDATA[{$this->incorrectfeedback}]]></incorrectfeedback>
                            <answernumbering>123</answernumbering>
EOT;
                        foreach ($this->reponses as $reponse) {
                            if ($reponse->bonne=='OUI')$valeur=$BV;
                            else $valeur=$MV;
                            //CDATA tr�s important pour les symboles < > pouvant �tre dans les textes...

                        $item=<<<EOT
                             <answer fraction="{$valeur}">
                                <text><![CDATA[{$reponse->reponse}]]></text>
                                <feedback><text><![CDATA[{$reponse->feedback}]]></text></feedback>
                            </answer>

EOT;

                        $ret .=$item;
                        }

                        return $ret;

                    }
                    // rev 971 envoie � Moodle une des images int�gr�es si pr�sente
                    function docsxml() {

                          if (empty($this->documents)) return "<image></image>\n";
                          //Moodle n'en prend qu'un seul donc le 1er valide part
                          foreach($this->documents as $doc) {
                                if ($b64=  encode_document($doc->id_doc.'.'.$doc->extension, $this->question->id,$this->question->id_etab )) {
                                      return "<image>c2iquestion/".$this->name."/".$doc->id_doc.'.'.$doc->extension."</image>\n".
                                      "<image_base64>".$b64."</image_base64>\n";

                                }
                          }
                          //rat�
                          return "<image></image>\n";
                    }

                    function toxml() {
                        return $this->beginxml().$this->docsxml().$this->reponsesxml().$this->endxml();
                    }
    }

/**
 * export XML Moodle 2.0 difference dans le nom de l'image qui ne doit plus contenir le chemin c2iquestion/xxx/
 * ajout� revision 1.5 983 20/07/2011
 */
class question_xml_moodle_20 extends question_xml_moodle {

    function docsxml() {

                          if (empty($this->documents)) return "<image></image>\n";
                          //Moodle n'en prend qu'un seul donc le 1er valide part
                          foreach($this->documents as $doc) {
                                if ($b64=  encode_document($doc->id_doc.'.'.$doc->extension, $this->question->id,$this->question->id_etab )) {
                                     // return "<image>c2iquestion/".$this->name."/".$doc->id_doc.'.'.$doc->extension."</image>\n".
                                     return "<image>".$doc->id_doc.'.'.$doc->extension."</image>\n".
                                      "<image_base64>".$b64."</image_base64>\n";

                                }
                          }
                          //rat�
                          return "<image></image>\n";
                    }
}


/**
 * export XML Moodle 2.1 difference dans le nom de l'image qui ne doit plus contenir le chemin c2iquestion/xxx/
 * TODO
 */
class question_xml_moodle_21 extends question_xml_moodle_20 {
}

class description_xml_moodle extends question_xml_moodle {

    function __construct ($name,$titre) {
        $this->questiontext=$titre;
        $this->type="description";
        $this->name=$name;
    }

}


class question_xml_c2i {

    var $question;
    var $reponses;
    var $documents;

    function __construct ($question,$reponses=null,$documents=null) {
        global $CFG;
        $this->question=$question;
        $this->reponses=$reponses;
        $this->documents=$documents;



    }


    //partie commune a toutes (description, multichoice ...)
    function beginxml() {
        $ret=<<<EOT
            <question>
                <auteur>{$this->question->auteur}</auteur>
                <auteur_mail>{$this->question->auteur_mail}</auteur_mail>
                <etablissement>{$this->question->id_etab}</etablissement>
                <id>{$this->question->id}</id>
                <date_creation>{$this->question->ts_datecreation}</date_creation>
                <date_modification>{$this->question->ts_datemodification}</date_modification>
                <famille>{$this->question->id_famille_validee}</famille>
                <domaine>{$this->question->referentielc2i}</domaine>
                <alinea>{$this->question->alinea}</alinea>
                <titre>{$this->question->titre}</titre>

EOT;
                    return $ret;
    }

                    function endxml() {
                        return "\n</question>\n\n";

                    }

                    function reponsesxml() {

                        if (empty( $this->reponses)) return "";
                        $ret = "<reponses>";
                        foreach ($this->reponses as $reponse) {
                            $item=<<<EOT
                                    <reponse>{$reponse->reponse}</reponse>
                                <bonne>{$reponse->bonne}</bonne>

EOT;
                            $ret .=$item;
                        }
                        $ret .= "</reponses>";

                        return $ret;

                    }


                    function docsxml() {
                         if (empty( $this->documents)) return "";
                        $ret = "<documents>\n";
                          foreach($this->documents as $doc) {
                                if ($b64=  encode_document($doc->id_doc.'.'.$doc->extension, $this->question->id,$this->question->id_etab )) {
                                      $ret.="<document>".$doc->id_doc.'.'.$doc->extension."</document>\n".
                                      "<image_base64>".$b64."</image_base64>\n";
                                }
                          }

                        $ret .= "</documents>\n";

                        return $ret;
                    }

                    function toxml() {
                        return $this->beginxml().$this->reponsesxml().$this->docsxml().$this->endxml();
                    }


    }


/* Usage
 Grab some XML data, either from a file, URL, etc. however you want. Assume storage in $strYourXML;

 $objXML = new xml2Array();
 $arrOutput = $objXML->parse($strYourXML);
 print_r($arrOutput); //print it out, or do whatever!

 */
class xml2Array {

    var $arrOutput = array();
    var $resParser;
    var $strXmlData;

    /**
     * Convert a utf-8 string to html entities
     *
     * @param string $str The UTF-8 string
     * @return string
     */
    function utf8_to_entities($str) {
        global $CFG;

        $entities = '';
        $values = array();
        $lookingfor = 1;

        return $str;
    }

    /**
     * Parse an XML text string and create an array tree that rapresent the XML structure
     *
     * @param string $strInputXML The XML string
     * @return array
     */
    function parse($strInputXML, $encodage) {
        //$this->resParser = xml_parser_create ('UTF-8');

        $this->resParser = xml_parser_create ($encodage);
        xml_set_object($this->resParser,$this);
        xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");

        xml_set_character_data_handler($this->resParser, "tagData");

        $this->strXmlData = xml_parse($this->resParser,$strInputXML );
        if(!$this->strXmlData) {
            die(sprintf("XML error: %s at line %d",
                xml_error_string(xml_get_error_code($this->resParser)),
                xml_get_current_line_number($this->resParser)));
        }

        xml_parser_free($this->resParser);

        return $this->arrOutput;
    }

    function tagOpen($parser, $name, $attrs) {
        $tag=array("name"=>$name,"attrs"=>$attrs);
        array_push($this->arrOutput,$tag);
    }

    function tagData($parser, $tagData) {
        if(trim($tagData)) {

            if(isset($this->arrOutput[count($this->arrOutput)-1]['tagData'])) {
                //$this->arrOutput[count($this->arrOutput)-1]['tagData'] .= $this->utf8_to_entities($tagData);
                $this->arrOutput[count($this->arrOutput)-1]['tagData'] .= $tagData;
            } else {
                $this->arrOutput[count($this->arrOutput)-1]['tagData'] = $tagData;
                //$this->arrOutput[count($this->arrOutput)-1]['tagData'] = $this->utf8_to_entities($tagData);
            }
        }
    }

    function tagClosed($parser, $name) {
        $this->arrOutput[count($this->arrOutput)-2]['children'][] = $this->arrOutput[count($this->arrOutput)-1];
        array_pop($this->arrOutput);
    }

}





/**
 *
 * Array 2 XML class
 * Convert an array or multi-dimentional array to XML
 *
 * @author Kevin Waterson
 * @copyright 2009 PHPRO.ORG
 *
 */
class array2xml extends DomDocument
{

    public $nodeName;

    private $xpath;

    private $root;

    private $node_name;


    /**
    * Constructor, duh
    *
    * Set up the DOM environment
    *
    * @param    string    $root        The name of the root node
    * @param    string    $nod_name    The name numeric keys are called
    *
    */
    public function __construct($root='root', $node_name='node')
    {
        parent::__construct();

        /*** set the encoding ***/
        $this->encoding = "UTF-8";

        /*** format the output ***/
        $this->formatOutput = true;

        /*** set the node names ***/
        $this->node_name = $node_name;

        /*** create the root element ***/
        $this->root = $this->appendChild($this->createElement( $root ));

        $this->xpath = new DomXPath($this);
    }

    /*
    * creates the XML representation of the array
    *
    * @access    public
    * @param    array    $arr    The array to convert
    * @aparam    string    $node    The name given to child nodes when recursing
    *
    */
    public function createNode( $arr, $node = null)
    {
        if (is_null($node))
        {
            $node = $this->root;
        }
        foreach($arr as $element => $value)
        {
            $element = is_numeric( $element ) ? $this->node_name : $element;

            $child = $this->createElement($element, (is_array($value) ? null : $value));
            $node->appendChild($child);

            if (is_array($value))
            {
                self::createNode($value, $child);
            }
        }
    }
    /*
    * Return the generated XML as a string
    *
    * @access    public
    * @return    string
    *
    */
    public function __toString()
    {
        return $this->saveXML();
    }

    /*
    * array2xml::query() - perform an XPath query on the XML representation of the array
    * @param str $query - query to perform
    * @return mixed
    */
    public function query($query)
    {
        return $this->xpath->evaluate($query);
    }

} // end of class



/**
 * rev 974 exportation d'examen au format json ou xml (pour clients mobiles)
 * IL FAUT UTILISER htmlentities et pas htmlspecialchars pour les accents avec php <5.2 !
 */

function array_for_examen($examen) {
    global $USER;
    $res=array();

    $res["idnat"]=$examen->id_etab.'.'.$examen->id_examen;
    $res["nom"]=htmlentities($examen->nom_examen);
    $res["auteur"]=htmlentities($examen->auteur);
    $res["ts_datedebut"]=$examen->ts_datedebut;
    $res["ts_datefin"]=$examen->ts_datefin;
    $res["typep"]=$examen->certification=="OUI"?'certification':'positionnement';
    $res["anonyme"]=$examen->anonyme==1;;
    $res["correction"]=$examen->correction==1;;
    $res["chrono"]=$examen->affiche_chrono==1; // doit �tre un BOOLEAN pour java
    $res["temps"]=$examen->ts_dureelimitepassage;


// rev 986  BUG ici en appel webservice type_p ='webservice' et tire_questions ne renvoyait rien ...
    if ($examen->type_tirage==EXAMEN_TIRAGE_PASSAGE) { //cas particulier
       $old_tp=$USER->type_plateforme;
       $USER->type_plateforme='positionnement';
       $questions=tire_questions($examen->id_examen,$examen->id_etab);  //choisies au hasard maintenant
       $USER->type_plateforme=$old_tp;           
    } else

        // respect du choix de l'enseignant sur l'ordre
     $questions = get_questions($examen->id_examen, $examen->id_etab, $examen->ordre_q !='fixe');
    //tableau des ids des questions associ�es
    $questionsids = array ();
    foreach ($questions as $question) {
        $qid = $question->id_etab . '.' . $question->id;
        $questionsids[] = $qid;
        $res["questions"][] = array_for_question($qid, $question);
    }
    //liste des questions a renvoyer pour correction format une chaine  1.1_1.2_1.3_....
    $res["questionsids"] = implode('_',$questionsids);
    return $res;

}

function array_for_question($qid, $question) {
    global $CFG;
    $res = array ();
    $res["idnat"] = $qid;
    //important d'encoder les symboles comme > < &   pour Android'
    $res["texte"] = htmlentities( $question->titre,ENT_COMPAT, 'UTF-8');
    $res["domaine"] = $question->referentielc2i;
    $res["alinea"] = $question->alinea;
    $res["reponses"] = array ();
    $reponses = get_reponses($question->id, $question->id_etab, true, false);
    foreach ($reponses as $reponse) {
        $res["reponses"][] = array_for_reponse($qid, $reponse);
    }
    $res["documents"] = array ();
    $documents = get_documents($question->id, $question->id_etab);
    foreach ($documents as $document) {
        $res["documents"][] = array_for_document($qid, $document);
    }

    return $res;
}

function array_for_reponse($qid, $reponse) {
    $res = array ();
    $res["q_idnat"] = $qid;
    // l'id unique de la r�ponse a renvoyer si coch�e
    $res["id"] = $reponse->id_etab.'_'.$reponse->id.'_'.$reponse->num;
     //important d'encoder les symboles comme > < &   pour Android'
    $res["texte"] = htmlentities($reponse->reponse,ENT_COMPAT, 'UTF-8');
    //mode triche
   // $res["checked"]=$reponse->bonne=='OUI';
    return $res;
}

function array_for_document($qid, $document) {
    global $CFG;
    $res = array ();
    $res["q_idnat"] = $qid;
    $res["id"] = $document->id_doc;
    $res["ext"] = $document->extension;
    //url de base du document
    $url=$CFG->wwwroot."/commun/send_document.php?ide=" . $document->id_etab . "&amp;idq=" . $document->id . "&amp;idf=" . $document->id_doc . "." . $document->extension;
    $nom_doc=$document->description? $document->description : traduction("document");
        switch ($document->extension) { // image ou autre
            case "jpg" :;
            case "jpeg" :;
            case "gif" :;
            case "png" :;
                $res["url"]=$url."&amp;type=image";
                break;
            default :    
                $res["url"]= $url."&amp;type=doc";
                break;
        }

    return $res;
}

