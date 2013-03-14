<?php
/**
 * @author Patrick Pollet
 * @version $Id: lib_ajax.php 726 2009-04-23 06:21:03Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */




function get_fckeditor($name,$valeur,$width,$height)  {
	global $CFG;

	$editeur_path=$CFG->chemin_commun.'/editeurs/fckeditor/';

	require_once($editeur_path.'fckeditor.php');
	$Config['Enabled'] = true ;
	$oFCKeditor = new FCKeditor($name) ;
	$oFCKeditor->BasePath = $editeur_path;
	$oFCKeditor->Value = stripslashes($valeur);
	$oFCKeditor->Width  = $width; //"100%" ;
	$oFCKeditor->Height = $height; //"1000px" ;
	$oFCKeditor->Config['CustomConfigurationsPath'] = '../fckconfig.js'  ;
	return  $oFCKeditor->CreateHtml() ;

}

$CFG->editorbackgroundcolor=#FFFFFF;

$CFG->editordictionary='fr';
$CFG->editorfontfamily='Trebuchet MS,Verdana,Arial,Helvetica,sans-serif';
$CFG->editorfontlist='Trebuchet:Trebuchet MS,Verdana,Arial,Helvetica,sans-serif;Arial:arial,helvetica,sans-serif;Courier New:courier new,courier,monospace;Georgia:georgia,times new roman,times,serif;Tahoma:tahoma,arial,helvetica,sans-serif;Times New Roman:times new roman,times,serif;Verdana:verdana,arial,helvetica,sans-serif;Impact:impact;Wingdings:wingdings';
$CFG->editorhidebuttons='';
$CFG->editorkillword=1;
$CFG-> editorspelling=1;

/**
 * Prints a basic textarea field.
 *
 * @uses $CFG
 * @param boolean $usehtmleditor ?
 * @param int $rows ?
 * @param int $cols ?
 * @param null $width <b>Legacy field no longer used!</b>  Set to zero to get control over mincols
 * @param null $height <b>Legacy field no longer used!</b>  Set to zero to get control over minrows
 * @param string $name ?
 * @param string $value ?
 * @param int $id    id de la balise HTML
 * @todo Finish documenting this function
 */
function print_textarea($usehtmleditor, $rows, $cols, $width, $height, $name, $value='',
 $class='',$title='',
 $return=true, $question_id='',$question_ide='',$id='') {
/// $width and height are legacy fields and no longer used as pixels like they used to be.
/// However, you can set them to zero to override the mincols and minrows values below.

    global $CFG;
    static $scriptcount = 0; // For loading the htmlarea script only once.

    $mincols = 65;
    $minrows = 10;
    $str = '';

    if ($id === '') {
        $id = 'edit-'.$name;
    }



        if ($usehtmleditor) {
            $rows +=5;

            if (!empty($question_id) ) {

                // needed for course file area browsing in image insert plugin
                $str .= ($scriptcount < 1) ? '<script type="text/javascript" src="'.
                        p_session($CFG->wwwroot .'/commun/editeurs/htmlarea/htmlarea.php?ide='.$question_ide.'&amp;id='.$question_id).'"></script>'."\n" : '';
            } else {

                $str .= ($scriptcount < 1) ? '<script type="text/javascript" src="'.
                        p_session($CFG->wwwroot .'/commun/editeurs/htmlarea/htmlarea.php').'" ></script>'."\n" : '';

            }
            $str .= ($scriptcount < 1) ? '<script type="text/javascript" src="'.
                    $CFG->wwwroot .'/commun/editeurs/htmlarea/lang/en.php"></script>'."\n" : '';
            $scriptcount++;

            if ($height) {    // Usually with legacy calls
                if ($rows < $minrows) {
                    $rows = $minrows;
                }
            }
            if ($width) {    // Usually with legacy calls
                if ($cols < $mincols) {
                    $cols = $mincols;
                }
            }
        }

    $str .= '<textarea class="'.$class. '" title="'.$title.'" id="'. $id .'" name="'. $name .'" rows="'. $rows .'" cols="'. $cols .'">';
    if ($usehtmleditor) {
        $str .= htmlspecialchars($value); // needed for editing of cleaned text!
    } else {
        $str .= $value;
    }
    $str .= '</textarea>'."\n";

    if ($usehtmleditor) {
        // Show shortcuts button if HTML editor is in use, but only if JavaScript is enabled (MDL-9556)
        /******
        $str .= '<script type="text/javascript">
//<![CDATA[
document.write(\''.addslashes(editorshortcutshelpbutton()).'\');
//]]>
</script>';
*****/
    }

    if ($return) {
        return $str;
    }
    echo $str;
}

/**
 * Sets up the HTML editor on textareas in the current page.
 * If a field name is provided, then it will only be
 * applied to that field - otherwise it will be used
 * on every textarea in the page.
 *
 * In most cases no arguments need to be supplied
 *
 * @param string $name Form element to replace with HTMl editor by name
 */
function use_html_editor($name='', $editorhidebuttons='', $id='',$return=false) {
    global $THEME;

    $editor = 'editor_'.md5($name); //name might contain illegal characters
    if ($id === '') {
        $id = 'edit-'.$name;
    }
    $str= "\n".'<script type="text/javascript" defer="defer">'."\n";
    $str.= '//<![CDATA['."\n\n"; // Extra \n is to fix odd wiki problem, MDL-8185
    $str .= "$editor = new HTMLArea('$id');\n";
    $str .= "var config = $editor.config;\n";

    $str .= print_editor_config($editorhidebuttons,true);

    if (empty($THEME->htmleditorpostprocess)) {
        if (empty($name)) {
            $str .= "\nHTMLArea.replaceAll($editor.config);\n";
        } else {
            $str .= "\n$editor.generate();\n";
        }
    } else {
        if (empty($name)) {
            $str .= "\nvar HTML_name = '';";
        } else {
            $str .= "\nvar HTML_name = \"$name;\"";
        }
        $str .= "\nvar HTML_editor = $editor;";
    }
    $str .= '//]]>'."\n";
    $str .= '</script>'."\n";

    if ($return)
    	return $str;
    else echo $str;
}

function print_editor_config($editorhidebuttons='', $return=false) {
    global $CFG;

    $str = "config.pageStyle = \"body {";

    if (!(empty($CFG->editorbackgroundcolor))) {
        $str .= " background-color: $CFG->editorbackgroundcolor;";
    }

    if (!(empty($CFG->editorfontfamily))) {
        $str .= " font-family: $CFG->editorfontfamily;";
    }

    if (!(empty($CFG->editorfontsize))) {
        $str .= " font-size: $CFG->editorfontsize;";
    }

    $str .= " }\";\n";
    $str .= "config.killWordOnPaste = ";
    $str .= (empty($CFG->editorkillword)) ? "false":"true";
    $str .= ';'."\n";
    $str .= 'config.fontname = {'."\n";

    $fontlist = isset($CFG->editorfontlist) ? explode(';', $CFG->editorfontlist) : array();
    $i = 1;                     // Counter is used to get rid of the last comma.

    foreach ($fontlist as $fontline) {
        if (!empty($fontline)) {
            if ($i > 1) {
                $str .= ','."\n";
            }
            list($fontkey, $fontvalue) = split(':', $fontline);
            $str .= '"'. $fontkey ."\":\t'". $fontvalue ."'";

            $i++;
        }
    }
    $str .= '};';

    if (!empty($editorhidebuttons)) {
        $str .= "\nconfig.hideSomeButtons(\" ". $editorhidebuttons ." \");\n";
    } else if (!empty($CFG->editorhidebuttons)) {
        $str .= "\nconfig.hideSomeButtons(\" ". $CFG->editorhidebuttons ." \");\n";
    }
/*
    if (!empty($CFG->editorspelling) && !empty($CFG->aspellpath)) {
        $str .= print_speller_code($CFG->htmleditor, true);
    }
*/
    if ($return) {
        return $str;
    }
    echo $str;
}



/**
 * Prints out code needed for spellchecking.
 * Original idea by Ludo (Marc Alier).
 *
 * Opening CDATA and <script> are output by weblib::use_html_editor()
 * @uses $CFG
 * @param boolean $usehtmleditor Normally set by $CFG->htmleditor, can be overriden here
 * @param boolean $return If false, echos the code instead of returning it
 * @todo Find out if lib/editor/htmlarea/htmlarea.class.php::print_speller_code() is still used, and delete if not
 */
function print_speller_code ($usehtmleditor=false, $return=false) {
    global $CFG;
    $str = '';

    if(!$usehtmleditor) {
        $str .= 'function openSpellChecker() {'."\n";
        $str .= "\tvar speller = new spellChecker();\n";
        $str .= "\tspeller.popUpUrl = \"" . $CFG->wwwroot ."/commun/speller/spellchecker.html\";\n";
        $str .= "\tspeller.spellCheckScript = \"". $CFG->wwwroot ."/commun/speller/server-scripts/spellchecker.php\";\n";
        $str .= "\tspeller.spellCheckAll();\n";
        $str .= '}'."\n";
    } else {
        $str .= "function spellClickHandler(editor, buttonId) {\n";
        $str .= "\teditor._textArea.value = editor.getHTML();\n";
        $str .= "\tvar speller = new spellChecker( editor._textArea );\n";
        $str .= "\tspeller.popUpUrl = \"" . $CFG->wwwroot ."/commun/speller/spellchecker.html\";\n";
        $str .= "\tspeller.spellCheckScript = \"". $CFG->wwwroot ."/commun/speller/server-scripts/spellchecker.php\";\n";
        $str .= "\tspeller._moogle_edit=1;\n";
        $str .= "\tspeller._editor=editor;\n";
        $str .= "\tspeller.openChecker();\n";
        $str .= '}'."\n";
    }

    if ($return) {
        return $str;
    }
    echo $str;
}

/**
 * Print button for spellchecking when editor is disabled
 */
function print_speller_button () {
    echo '<input type="button" value="Check spelling" onclick="openSpellChecker();" />'."\n";
}


/**
 * Print a help button.
 *
 * Prints a special help button for html editors (htmlarea in this case)
 * @uses $CFG
 */
function editorshortcutshelpbutton() {

    global $CFG;
    return "";
    $imagetext = '<img src="' . $CFG->wwwroot . '/commun/editeurs/htmlarea/images/kbhelp.gif" alt="'.
        get_string('editorshortcutkeys').'" class="iconkbhelp" />';

    return helpbutton('editorshortcuts', get_string('editorshortcutkeys'), 'moodle', true, false, '', true, $imagetext);
}

?>
