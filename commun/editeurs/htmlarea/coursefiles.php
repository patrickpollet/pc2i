<?php // $Id: coursefiles.php,v 1.13.8.6 2009/06/09 04:58:38 jonathanharker Exp $

//  Manage all uploaded files in a course file area

//  This file is a hack to files/index.php that removes
//  the headers and adds some controls so that images
//  can be selected within the Richtext editor.

//  All the Moodle-specific stuff is in this top section
//  Configuration and access control occurs here.
//  Must define:  USER, basedir, baseweb, html_header and html_footer
//  USER is a persistent variable using sessions


     $chemin="../../..";
    $chemin_commun = $chemin."/commun";
    include($chemin_commun."/c2i_params.php");
    $id = required_param('id', PARAM_INT);


    require_login("P"); //PP   pose un pb de session expirée ?
    $ide=optional_param('ide',$USER->id_etab_perso,PARAM_INT);

//    $id      = required_param('id', PARAM_INT);
    $file    = optional_param('file', '', PARAM_PATH);
    $wdir    = optional_param('wdir', '', PARAM_PATH);
    $action  = optional_param('action', '', PARAM_ACTION);
    $name    = optional_param('name', '', PARAM_FILE);
    $oldname = optional_param('oldname', '', PARAM_FILE);
    //$usecheckboxes  = optional_param('usecheckboxes', 1, PARAM_INT);
    $usercheckboxes=0;
    $save    = optional_param('save', 0, PARAM_BOOL);
    $text    = optional_param('text', '', PARAM_RAW);
    $confirm = optional_param('confirm', 0, PARAM_BOOL);

/*
    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    require_login($course);
    require_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $id));
*/
    function html_footer() {
        echo "\n\n</body>\n</html>";
    }

    function html_header($id, $wdir, $formfield=""){

        global $CFG;
        /**
        if (!empty($_SERVER['HTTPS']) and $_SERVER['HTTPS'] != 'off') {
            $url = preg_replace('|https?://[^/]+|', '', $CFG->wwwroot).'/lib/editor/htmlarea/';
        } else {
            $url = $CFG->wwwroot.'/lib/editor/htmlarea/';
        }
        */
          $url= $CFG->wwwroot.'/commun/editeurs/htmlarea/'  ;

        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
            "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html>
        <head>

        <meta http-equiv="content-type" content="text/html; charset={$CFG->encodage}" />
        <title>coursefiles</title>
        <script type="text/javascript">
//<![CDATA[


        function set_value(params) {
            /// function's argument is an object containing necessary values
            /// to export parent window (url,isize,itype,iwidth,iheight, imodified)
            /// set values when user click's an image name.
            var upper = window.parent;
            var insimg = upper.document.getElementById('f_url');

            try {
                if(insimg != null) {
                    if(params.itype.indexOf("image/gif") == -1 && params.itype.indexOf("image/jpeg") == -1 && params.itype.indexOf("image/png") == -1) {
                        alert("<?php print_string("notimage","editor");?>");
                        return false;
                    }
                    for(field in params) {
                        var value = params[field];
                        switch(field) {
                            //PP l'ajout de l'appel a escape est obligatoire sinon tronque les parametres (bizarre sauf le 1er donc Moodle est content) !!!
                            case "url"   :   upper.document.getElementById('f_url').value = value;
                                     upper.ipreview.location.replace('popups/preview.php?imageurl='+ escape(value));
                                    // alert('popups/preview.php?imageurl='+ value);
                                break;
                            case "isize" :   upper.document.getElementById('isize').value = value; break;
                            case "itype" :   upper.document.getElementById('itype').value = value; break;
                            case "iwidth":    upper.document.getElementById('f_width').value = value; break;
                            case "iheight":   upper.document.getElementById('f_height').value = value; break;
                        }
                    }
                } else {
                    for(field in params) {
                        var value = params[field];
                        switch(field) {
                            case "url" :
                                //upper.document.getElementById('f_href').value = value;
                                upper.opener.document.getElementById('f_href').value = value;
                                upper.close();
                                break;
                            //case "imodified" : upper.document.getElementById('imodified').value = value; break;
                            //case "isize" : upper.document.getElementById('isize').value = value; break;
                            //case "itype" : upper.document.getElementById('itype').value = value; break;
                        }
                    }
                }
            } catch(e) {
                if ( window.tinyMCE != "undefined" || window.TinyMCE != "undefined" ) {
                    upper.opener.Dialog._return(params.url);
                    upper.close();
                } else {
                    alert("Something odd just occurred!!!");
                }
            }
            return false;
        }

        function set_dir(strdir) {
            // sets wdir values
            var upper = window.parent.document;
            if(upper) {
                for(var i = 0; i < upper.forms.length; i++) {
                    var f = upper.forms[i];
                    try {
                        f.wdir.value = strdir;
                    } catch (e) {

                    }
                }
            }
        }

        function set_rename(strfile) {
            var upper = window.parent.document;
            upper.getElementById('irename').value = strfile;
            return true;
        }

        function reset_value() {
            var upper = window.parent.document;
            for(var i = 0; i < upper.forms.length; i++) {
                var f = upper.forms[i];
                for(var j = 0; j < f.elements.length; j++) {
                    var e = f.elements[j];
                    if(e.type != "submit" && e.type != "button" && e.type != "hidden") {
                        try {
                            e.value = "";
                        } catch (e) {
                        }
                    }
                }
            }
           // upper.getElementById('irename').value = 'xx';

            var prev = window.parent.ipreview;
            if(prev != null) {
                prev.location.replace('<?php echo $url ?>blank.html');
            }
            var uploader = window.parent.document.forms['uploader'];
            if(uploader != null) {
                uploader.reset();
            }
            set_dir('<?php print($wdir);?>');
            return true;
        }
//]]>
        </script>
        <style type="text/css">
        body {
            background-color: white;
            margin-top: 2px;
            margin-left: 4px;
            margin-right: 4px;
        }
        body,p,table,td,input,select,a {
            font-family: Tahoma, sans-serif;
            font-size: 11px;
        }
        select {
            position: absolute;
            top: -20px;
            left: 0px;
        }
        img.icon {
          vertical-align:middle;
          margin-right:4px;
          width:16px;
          height:16px;
          border:0px;
        }
        </style>
        </head>
        <body onload="reset_value();">

        <?php
    }
/*
    if (! $basedir = make_upload_directory("$course->id")) {
        error("The site administrator needs to fix the file permissions");
    }
*/

/**
$basedir=$CFG->chemin_ressources."/questions/".$id."_"."0";
cree_dossier_si_absent($basedir);
$basedir=$basedir."/documents";
cree_dossier_si_absent($basedir);

**/
$basedir=get_document_location($id,$ide);
$baseweb = $CFG->wwwroot;

//  End of configuration and access control


    if ($wdir == '') {
        $wdir='/';
    }

    switch ($action) {

        case "upload":
            html_header($id, $wdir);


            /*
            require_once($CFG->dirroot.'/lib/uploadlib.php');

            if ($save and confirm_sesskey()) {
                $um = new upload_manager('userfile',false,false,$course,false,0);
                $dir = "$basedir$wdir";
                if ($um->process_file_uploads($dir)) {
                    notify(get_string('uploadedfile'));
                }
                // um will take care of error reporting.
                displaydir($wdir);
            } else {
                $upload_max_filesize = get_max_upload_file_size($CFG->maxbytes);
                $filesize = display_size($upload_max_filesize);

                $struploadafile = get_string("uploadafile");
                $struploadthisfile = get_string("uploadthisfile");
                $strmaxsize = get_string("maxsize", "", $filesize);
                $strcancel = get_string("cancel");

                echo "<p>$struploadafile ($strmaxsize) --> <strong>$wdir</strong>";
                echo "<table border=\"0\"><tr><td colspan=\"2\">\n";
                echo "<form enctype=\"multipart/form-data\" method=\"post\" action=\"coursefiles.php\">\n";
                upload_print_form_fragment(1,array('userfile'),null,false,null,$course->maxbytes,0,false);
                echo " <input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
                echo " <input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />\n";
                echo " <input type=\"hidden\" name=\"action\" value=\"upload\" />\n";
                echo " <input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />\n";
                echo " </td><tr><td align=\"right\">";
                echo " <input type=\"submit\" name=\"save\" value=\"$struploadthisfile\" />\n";
                echo "</form>\n";
                echo "</td>\n<td>\n";
                echo "<form action=\"coursefiles.php\" method=\"get\">\n";
                echo " <input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
                echo " <input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />\n";
                echo " <input type=\"hidden\" name=\"action\" value=\"cancel\" />\n";
                echo " <input type=\"submit\" value=\"$strcancel\" />\n";
                echo "</form>\n";
                echo "</td>\n</tr>\n</table>\n";
            }
            */
            $fich = isset($_FILES["userfile"]) ? $_FILES["userfile"]['name']:""; //nom du fichier
            if ($fich) {
                copy ($_FILES["userfile"]['tmp_name'],$basedir."/".$_FILES["userfile"]['name']);
            }
            displaydir($wdir);

            html_footer();
            break;

        case "cancel":
            clearfilelist();

        default:
            html_header($id, $wdir);
            displaydir($wdir);
            html_footer();
            break;
}


/// FILE FUNCTIONS ///////////////////////////////////////////////////////////


function setfilelist($VARS) {
    global $USER;

    $USER->filelist = array ();
    $USER->fileop = "";

    $count = 0;
    foreach ($VARS as $key => $val) {
        if (substr($key,0,4) == "file") {
            $count++;
            $val = rawurldecode($val);
            if (!detect_munged_arguments($val, 0)) {
                $USER->filelist[] = $val;
            }
        }
    }
    return $count;
}

function clearfilelist() {
    global $USER;

    $USER->filelist = array ();
    $USER->fileop = "";
}


function printfilelist($filelist) {
    global $basedir, $CFG;

    foreach ($filelist as $file) {
        if (is_dir($basedir.$file)) {
            echo "<img src=\"$CFG->pixpath/f/folder.gif\" class=\"icon\" alt=\"".get_string('folder')."\" /> $file<br />";
            $subfilelist = array();
            $currdir = opendir($basedir.$file);
            while (false !== ($subfile = readdir($currdir))) {
                if ($subfile <> ".." && $subfile <> ".") {
                    $subfilelist[] = $file."/".$subfile;
                }
            }
            printfilelist($subfilelist);

        } else {
            $icon = mimeinfo("icon", $file);
            echo "<img src=\"$CFG->pixpath/f/$icon\"  class=\"icon\" alt=\"".get_string('file')."\" /> $file<br />";
        }
    }
}


function print_cell($alignment="center", $text="&nbsp;") {
    echo "<td align=\"$alignment\" nowrap=\"nowrap\">\n";
    echo "$text";
    echo "</td>\n";
}

function get_image_size($filepath) {
/// This function get's the image size

    /// Check if file exists
    if(!file_exists($filepath)) {
        return false;
    } else {
        /// Get the mime type so it really an image.
        if(mimeinfo("icon", basename($filepath)) != "image.gif") {
            return false;
        } else {
            $array_size = getimagesize($filepath);
            return $array_size;
        }
    }
    unset($filepath,$array_size);
}

function displaydir ($wdir) {
//  $wdir == / or /a or /a/b/c/d  etc

    global $basedir;
    global $usecheckboxes;
    global $id,$ide;
    global $USER, $CFG;

    $fullpath = $basedir.$wdir;
/*
    $directory = opendir($fullpath);             // Find all files
    while (false !== ($file = readdir($directory))) {
        if ($file == "." || $file == "..") {
            continue;
        }

        if (is_dir($fullpath."/".$file)) {
            $dirlist[] = $file;
        } else {
            $filelist[] = $file;
        }
    }
    closedir($directory);
*/
    $filelist=get_list_of_files('',$fullpath); //pas de navigation
    $strfile = get_string("file");
    $strname = get_string("name");
    $strsize = get_string("size");
    $strmodified = get_string("modified");
    $straction = get_string("action");
    $strmakeafolder = get_string("makeafolder");
    $struploadafile = get_string("uploadafile");
    $strwithchosenfiles = get_string("withchosenfiles");
    $strmovetoanotherfolder = get_string("movetoanotherfolder");
    $strmovefilestohere = get_string("movefilestohere");
    $strdeletecompletely = get_string("deletecompletely");
    $strcreateziparchive = get_string("createziparchive");
    $strrename = get_string("rename");
    $stredit   = get_string("edit");
    $strunzip  = get_string("unzip");
    $strlist   = get_string("list");
    $strchoose   = get_string("choose");


    echo "<form action=\"coursefiles.php\" method=\"post\" name=\"dirform\">\n";
    echo "<table border=\"0\" cellspacing=\"2\" cellpadding=\"2\" width=\"100%\">\n";

    if ($wdir == "/") {
        $wdir = "";
    } else {
        $bdir = str_replace("/".basename($wdir),"",$wdir);
        if($bdir == "/") {
            $bdir = "";
        }
        print "<tr>\n<td colspan=\"5\">";
        print "<a href=\"coursefiles.php?id=$id&amp;wdir=$bdir&amp;usecheckboxes=$usecheckboxes\" onclick=\"return reset_value();\">";
        print "<img src=\"$CFG->wwwroot/commun/editeurs/htmlarea/images/folderup.gif\" height=\"14\" width=\"24\" border=\"0\" alt=\"".get_string('parentfolder')."\" />";
        print "</a></td>\n</tr>\n";
    }

    $count = 0;

    if (!empty($dirlist)) {
        asort($dirlist);
        foreach ($dirlist as $dir) {

            $count++;

            $filename = $fullpath."/".$dir;
            $fileurl  = $wdir."/".$dir;
            $filedate = userdate(filemtime($filename), "%d %b %Y, %I:%M %p");

            echo "<tr>";

            if ($usecheckboxes) {
                if ($fileurl === '/moddata') {
                    print_cell();
                } else {
                    print_cell("center", "<input type=\"checkbox\" name=\"file$count\" value=\"$fileurl\" onclick=\"return set_rename('$dir');\" />");
                }
            }
            print_cell("left", "<a href=\"coursefiles.php?ide=$ide&amp;id=$id&amp;wdir=$fileurl\" onclick=\"return reset_value();\"><img src=\"$CFG->pixpath/f/folder.gif\" class=\"icon\" alt=\"".get_string('folder')."\" /></a> <a href=\"coursefiles.php?id=$id&amp;wdir=$fileurl&amp;usecheckboxes=$usecheckboxes\" onclick=\"return reset_value();\">".htmlspecialchars($dir)."</a>");
            print_cell("right", "&nbsp;");
            print_cell("right", $filedate);

            echo "</tr>";
        }
    }


    if (!empty($filelist)) {
        asort($filelist);
        foreach ($filelist as $file) {

            $icon = mimeinfo("icon", $file);
            $imgtype = mimeinfo("type",$file);

            $count++;
            $filename    = $fullpath."/".$file;
          //  $fileurl     = "$wdir/$file";
          $fileurl=$file; // pas de navigation possible
            $filedate    = userdate(filemtime($filename), "%d %b %Y, %I:%M %p");

            $dimensions = get_image_size($filename);
            if($dimensions) {
                $imgwidth = $dimensions[0];
                $imgheight = $dimensions[1];
            } else {
                $imgwidth = "Unknown";
                $imgheight = "Unknown";
            }
            unset($dimensions);
            echo "<tr>\n";

            if ($usecheckboxes) {
                print_cell("center", "<input type=\"checkbox\" name=\"file$count\" value=\"$fileurl\" onclick=\";return set_rename('$file');\" />");
            }
            echo "<td align=\"left\" nowrap=\"nowrap\">";
           // $ffurl = get_file_url($id.$fileurl);
           // $ffurl=$fileurl;
            
            //$ffurl=$CFG->wwwroot."/commun/send_document.php?idq=" . $id. "&amp;ide=" . $ide. "&amp;type=image&amp;idf=".$fileurl;

            // ne pas stocker adreese serveur dans l'image !
            //$ffurl=str_replace($CFG->serverroot,'',$ffurl);
            //relatif important pour portabilté toute PF
              $ffurl="../../commun/send_document.php?idq=" . $id. "&amp;ide=" . $ide. "&amp;type=image&amp;idf=".$fileurl;
            
            
            
            /*
            link_to_popup_window ($ffurl, "display",
                                  "<img src=\"$CFG->pixpath/f/$icon\" class=\"icon\" alt=\"$strfile\" />",
                                  480, 640);
            */
            $file_size = filesize($filename);

            echo "<a onclick=\"return set_value(info = {url: '".$ffurl."',";
            echo " isize: '".$file_size."', itype: '".$imgtype."', iwidth: '".$imgwidth."',";
            echo " iheight: '".$imgheight."', imodified: '".$filedate."' })\" href=\"#\">$file</a>";
            echo "</td>\n";

            if ($icon == "zip.gif") {
                $edittext = "<a href=\"coursefiles.php?ide=$ide&amp;id=$id&amp;wdir=$wdir&amp;file=$fileurl&amp;action=unzip&amp;sesskey=$USER->sesskey\">$strunzip</a>&nbsp;";
                $edittext .= "<a href=\"coursefiles.php?ide=$ide&amp;id=$id&amp;wdir=$wdir&amp;file=$fileurl&amp;action=listzip&amp;sesskey=$USER->sesskey\">$strlist</a> ";
            } else {
                $edittext = "&nbsp;";
            }
            print_cell("right", "$edittext ");
            print_cell("right", $filedate);

            echo "</tr>\n";
        }
    }
    echo "</table>\n";

    if (empty($wdir)) {
        $wdir = "/";
    }
/***
    echo "<table border=\"0\" cellspacing=\"2\" cellpadding=\"2\">\n";
    echo "<tr>\n<td>";
    echo "<input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
    echo "<input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />\n";
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />\n";
    $options = array (
                   "move" => "$strmovetoanotherfolder",
                   "delete" => "$strdeletecompletely",
                   "zip" => "$strcreateziparchive"
               );
    if (!empty($count)) {
        choose_from_menu ($options, "action", "", "$strwithchosenfiles...", "javascript:getElementById('dirform').submit()");
    }
    if (!empty($USER->fileop) and ($USER->fileop == "move") and ($USER->filesource <> $wdir)) {
        echo "<form action=\"coursefiles.php\" method=\"get\">\n";
        echo " <input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
        echo " <input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />\n";
        echo " <input type=\"hidden\" name=\"action\" value=\"paste\" />\n";
        echo " <input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />\n";
        echo " <input type=\"submit\" value=\"$strmovefilestohere\" />\n";
        echo "</form>";
    }
    echo "</td></tr>\n";
    echo "</table>\n";
    */
    echo "</form>\n";
}
?>
