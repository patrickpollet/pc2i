<?PHP // $Id: pix.php,v 1.28.2.3 2010/04/10 00:17:45 iarenaza Exp $
      // This function fetches math. images from the data directory
      // If not, it obtains the corresponding TeX expression from the cache_tex db table
      // and uses mimeTeX to create the image file

   // $nomoodlecookie = true;     // Because it interferes with caching

    $chemin="../../../";



define('NO_HEADERS',1); // pas d'entete par c2i_params !

/*
 * tres important
 * dans le cas ou des lignes blanches
 * ou des print trainent dans le code de la PF !!!!!
 */
ob_start();
define('V2', 1);
require_once ("$chemin/commun/c2i_params.php");


/*

    if (empty($CFG->textfilters)) {
        error ('Filter not enabled!');
    } else {
        $filters = explode(',', $CFG->textfilters);
        if (array_search('filter/tex', $filters) === FALSE) {
            error ('Filter not enabled!');
        }
    }

    // disable moodle specific debug messages
    disable_debugging();
*/

 //   require_once($CFG->libdir.'/filelib.php');
 //   require_once($CFG->dirroot.'/filter/tex/lib.php');
   require_once($CFG->dirroot.'/commun/filtres/tex/latex.php');

    $cmd    = '';               // Initialise these variables
    $status = '';

    $image=required_param("file",PARAM_FILE);
    $pathname = $CFG->chemin_ressources.'/filter/tex/'.$image;


    if (!file_exists($pathname)) {
        $md5 = str_replace(".{$CFG->filter_tex_convertformat}",'',$image);
        if ($texcache = get_record("cache_filters","filter='tex' and md5key='{$md5}'",false)) {

            // try and render with latex first
            $latex = new latex();
            $density = $CFG->filter_tex_density;
            $background = $CFG->filter_tex_latexbackground;
            $texexp = html_entity_decode($texcache->rawtext);
            $latex_path = $latex->render($texexp, $md5, 12, $density, $background);
            if ($latex_path) {
                copy($latex_path, $pathname);
                $latex->clean_up($md5);

            } else {
                // failing that, use mimetex
                $texexp = $texcache->rawtext;
                $texexp = str_replace('&lt;', '<', $texexp);
                $texexp = str_replace('&gt;', '>', $texexp);
                $texexp = preg_replace('!\r\n?!', ' ', $texexp);
                $texexp = '\Large '.$texexp;
                $cmd = tex_filter_get_cmd($pathname, $texexp);
                system($cmd, $status);
            }
        }
    }

    if (file_exists($pathname)) {
       // send_file($pathname, $image);
       $handle = @fopen($pathname, "r");
        if ($handle) {
            while (!feof($handle)) {
                    $buffer = fgets($handle, 4096);
                    echo $buffer;
            }
            fclose($handle);
        }
    } else {
       // if (debugging()) {
        if (1) {
            echo "The shell command<br />$cmd<br />returned status = $status<br />\n";
            echo "Image not found!<br />";
            echo "Please try the <a href=\"$CFG->wwwroot/filter/tex/texdebug.php\">debugging script</a>";
        } else {
            echo "Image not found!<br />";
            echo "Please try the <a href=\"$CFG->wwwroot/filter/tex/texdebug.php\">debugging script</a><br />";
            echo "Please turn on debug mode in site configuration to see more info here.";
        }
    }
?>
