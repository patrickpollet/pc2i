<?php  //$Id: lib.php,v 1.1.2.7 2010/04/10 00:17:45 iarenaza Exp $

function tex_filter_maj_config() {

    $preambule = "\\usepackage[latin1]{inputenc}\n\\usepackage{amsmath}\n\\usepackage{amsfonts}\n\\RequirePackage{amsmath,amssymb,latexsym}\n";

    add_config('tex', 'filter_tex_latexpreamble', $preambule, $preambule, "Entete fichier Latex", 0);
    add_config('tex', 'filter_tex_latexbackground', '#FFFFFF', '#FFFFFF', 'Couleur de fond transparent des images g�n�r�es', 1);
    add_config('tex', 'filter_tex_density', '120', '120', '', 1);

    if (PHP_OS == 'Linux') {
        $default_filter_tex_pathlatex = "/usr/bin/latex";
        $default_filter_tex_pathdvips = "/usr/bin/dvips";
        $default_filter_tex_pathconvert = "/usr/bin/convert";

    } else
        if (PHP_OS == 'Darwin') {
            // most likely needs a fink install (fink.sf.net)
            $default_filter_tex_pathlatex = "/sw/bin/latex";
            $default_filter_tex_pathdvips = "/sw/bin/dvips";
            $default_filter_tex_pathconvert = "/sw/bin/convert";

        } else
            if (PHP_OS == 'WINNT' or PHP_OS == 'WIN32' or PHP_OS == 'Windows') {
                // note: you need Ghostscript installed (standard), miktex (standard)
                // and ImageMagick (install at c:\ImageMagick)
                $default_filter_tex_pathlatex = "\"c:\\texmf\\miktex\\bin\\latex.exe\" ";
                $default_filter_tex_pathdvips = "\"c:\\texmf\\miktex\\bin\\dvips.exe\" ";
                $default_filter_tex_pathconvert = "\"c:\\imagemagick\\convert.exe\" ";

            } else {
                $default_filter_tex_pathlatex = '';
                $default_filter_tex_pathdvips = '';
                $default_filter_tex_pathconvert = '';
            }

    add_config('tex', 'filter_tex_pathlatex', $default_filter_tex_pathlatex, $default_filter_tex_pathlatex, '', 1);
    add_config('tex', 'filter_tex_pathdvips', $default_filter_tex_pathdvips, $default_filter_tex_pathdvips, '', 1);
    add_config('tex', 'filter_tex_pathconvert', $default_filter_tex_pathconvert, $default_filter_tex_pathconvert, '', 1);

    // Even if we offer GIF and PNG formats here, in the update callback we check whether
    // all the paths actually point to executables. If they don't, we force the setting
    // to GIF, as that's the only format mimeTeX can produce.
    //$formats = array('gif' => 'GIF', 'png' => 'PNG');
    add_config('tex', 'filter_tex_convertformat', 'png', 'png', '', 1);
    add_config('tex',  'filter_tex_utiliser_flatten_pour_gif',0,0,'',1);


}

function tex_filter_maj_bd() {
    global $CFG;

}



function tex_filter_get_executable($debug=false) {
    global $CFG;

    $error_message1 = "Your system is not configured to run mimeTeX. You need to download the appropriate<br />"
                     ."executable for you ".PHP_OS." platform from <a href=\"http://moodle.org/download/mimetex/\">"
                     ."http://moodle.org/download/mimetex/</a>, or obtain the C source<br /> "
                     ."from <a href=\"http://www.forkosh.com/mimetex.zip\">"
                     ."http://www.forkosh.com/mimetex.zip</a>, compile it and "
                     ."put the executable into your<br /> moodle/filter/tex/ directory.";

    $error_message2 = "Custom mimetex is not executable!<br /><br />";

    if ((PHP_OS == "WINNT") || (PHP_OS == "WIN32") || (PHP_OS == "Windows")) {
        return "$CFG->dirroot/commun/filtres/tex/mimetex.exe";
    }

    $custom_commandpath = "$CFG->dirroot/commun/filtres/tex/mimetex";
    if (file_exists($custom_commandpath)) {
        if (is_executable($custom_commandpath)) {
            return $custom_commandpath;
        } else {
            error($error_message2.$error_message1);
        }
    }

    switch (PHP_OS) {
        case "Linux":   return "$CFG->dirroot/commun/filtres/tex/mimetex.linux";
        case "Darwin":  return "$CFG->dirroot/commun/filtres/tex/mimetex.darwin";
        case "FreeBSD": return "$CFG->dirroot/commun/filtres/tex/mimetex.freebsd";
    }

    error($error_message1);
}

function tex_sanitize_formula($texexp) {
    /// Check $texexp against blacklist (whitelisting could be more complete but also harder to maintain)
    $tex_blacklist = array(
        'include','command','loop','repeat','open','toks','output',
        'input','catcode','name','^^',
        '\def','\edef','\gdef','\xdef',
        '\every','\errhelp','\errorstopmode','\scrollmode','\nonstopmode',
        '\batchmode','\read','\write','csname','\newhelp','\uppercase',
        '\lowercase','\relax','\aftergroup',
        '\afterassignment','\expandafter','\noexpand','\special',
        '\let', '\futurelet','\else','\fi','\chardef','\makeatletter','\afterground',
        '\noexpand','\line','\mathcode','\item','\section','\mbox','\declarerobustcommand'
    );

    return  str_ireplace($tex_blacklist, 'forbiddenkeyword', $texexp);
}

function tex_filter_get_cmd($pathname, $texexp) {
    $texexp = tex_sanitize_formula($texexp);
    $texexp = escapeshellarg($texexp);
    $executable = tex_filter_get_executable(false);

    if ((PHP_OS == "WINNT") || (PHP_OS == "WIN32") || (PHP_OS == "Windows")) {
        $executable = str_replace(' ', '^ ', $executable);
        return "$executable ++ -e  \"$pathname\" -- $texexp";

    } else {
        return "\"$executable\" -e \"$pathname\" -- $texexp";
    }
}

/**
 * Purge all caches when settings changed.
 */
function filter_tex_updatedcallback($name) {
    global $CFG;
    //reset_text_filters_cache();

    if (file_exists("$CFG->chemin_ressources/filter/tex")) {
        remove_dir("$CFG->chemin_ressources/filter/tex");
    }
    /*
    if (file_exists("$CFG->dataroot/filter/algebra")) {
        remove_dir("$CFG->dataroot/filter/algebra");
    }
    */
    if (file_exists("$CFG->chemin_ressources/tmp/latex")) {
        remove_dir("$CFG->chemin_ressources/tmp/latex");
    }

    delete_records('cache_filters', 'filter', 'tex');
    //delete_records('cache_filters', 'filter', 'algebra');

    if (!(is_file($CFG->filter_tex_pathlatex) && is_executable($CFG->filter_tex_pathlatex) &&
          is_file($CFG->filter_tex_pathdvips) && is_executable($CFG->filter_tex_pathdvips) &&
          is_file($CFG->filter_tex_pathconvert) && is_executable($CFG->filter_tex_pathconvert))) {
        // LaTeX, dvips or convert are not available, and mimetex can only produce GIFs so...
        set_config('filter_tex_convertformat', 'gif');
    }
}

?>
