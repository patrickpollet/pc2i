<?php // $Id: preview.php,v 1.4 2007/01/27 23:23:44 skodak Exp $ preview for insert image dialog
 $chemin="../../../..";
	$chemin_commun = $chemin."/commun";
    include($chemin_commun."/c2i_params.php");
   // $id = optional_param('id',0, PARAM_INT);

    require_login("P"); //PP
    $imageurl = required_param('imageurl', PARAM_RAW);
    
    // l'url est relatif au dossier codes/questions
    $imageurl= '../../'.$imageurl;


   // @header('Content-Type: text/html; charset=utf-8');
    @header("Content-Type: text/html; charset={$CFG->encodage}");


    $imagetag = clean_text('<img src="'.htmlSpecialChars(stripslashes_safe($imageurl)).'" alt="" />');
    //$imagetag = clean_text('<img src="'.htmlSpecialChars($imageurl).'" alt="" />');
    //$imagetag = clean_text('<img src="'.$imageurl.'" alt="" />');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?php echo get_string('preview') ?></title>
<style type="text/css">
 body { margin: 2px; }
</style>
</head>
<body bgcolor="#ffffff">

<?php //echo $imageurl; ?>
<?php echo $imagetag ?>

</body>
</html>
