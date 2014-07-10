<!-- START COMMENT -->
/**
 * @author Patrick Pollet
 * @version $Id: C2Iheader.tpl 1174 2010-12-10 13:16:25Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 *
 * le meta X-UA-Compatible et pour l'�maultion IE de Google Chrome
 * le meta viewport et pour une optimsation de l'affichage sur un mobile
 * voir http://e-sarrion.developpez.com/cours/dev-web-mobile/bases-html/
<!-- END COMMENT -->

<!-- INCLUDE BLOCK : variables_globales -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" dir="{bodydir}" lang="{lang}" xml:lang="{lang}">
<head>
<title>{c2i_niveau}</title>
<link rel="shortcut icon" href="{chemin_theme}/images/logos/favicon_{CFG:c2i}.ico" />
<meta http-equiv="Content-Type" content="text/html; charset={encodage}"/>
<meta http-equiv="X-UA-Compatible" content="chrome=1"/>
<meta name="viewport" content="user-scalable=no,width=device-width" />


<link href="{chemin_theme}/style1.css" rel="stylesheet" media="screen,print" type="text/css" />

<!-- START BLOCK : EXTRA_CSS -->
<!-- START BLOCK : ligne_css -->
<link href="{path}" rel="stylesheet" media="screen" type="text/css"/>
<!-- END BLOCK : ligne_css -->
<!-- END BLOCK : EXTRA_CSS -->

<script src="{chemin_commun}/js/scripts.js" type="text/javascript"></script>

<!-- START BLOCK : EXTRA_JS -->
<!-- START BLOCK : ligne_js -->
<script src="{path}" type="text/javascript"></script>
<!-- END BLOCK : ligne_js -->
<!-- END BLOCK : EXTRA_JS -->


</head>



