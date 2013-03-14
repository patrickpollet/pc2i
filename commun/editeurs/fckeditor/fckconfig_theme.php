<?php
/*
 * Ajout pour le theme de la plate-forme
 *
 */



$chemin = '../../..';
$chemin_commun = $chemin . "/commun";

require_once ($chemin_commun . "/c2i_params.php");
?>
FCKConfig.EditorAreaCSS = FCKConfig.BasePath + '../../../themes/<?=$CFG->theme?>/style1.css';

