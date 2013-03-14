<?php
/**
 * @author Patrick Pollet
 * @version $Id: barcode2D.php 1183 2010-12-15 11:51:32Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */



$chemin = '..';

require_once($chemin."/commun/c2i_params.php");
require_once( $chemin."/templates/class.TemplatePower.inc.php");

$quoi=optional_param ('quoi',$locale_url_univ,PARAM_URL);

$doc='http://fr.wikipedia.org/wiki/Code_QR';

$google = 'http://chart.apis.google.com/chart?cht=qr&chs=120x120&chl=';

$url=$google.addslashes(htmlspecialchars($quoi));

$fiche=<<<EOF
<img src='{image}' />
<br/>
{msg_scanner_cette_image}<br/>
<a href='{doc}'>{msg_doc_code_qr} </a>

EOF;

$tpl = new C2IPopup();	//créer une instance

$tpl->assignInclude("corps",$fiche,T_BYVAR);
$tpl->prepare($chemin);
$tpl->assign("_ROOT.titre_popup",traduction('scanner_cette_image'));

$tpl->assign('image',$url);
$tpl->assign('doc',$doc);
//$tpl->print_boutons_fermeture();
$tpl->printToScreen();

?>
