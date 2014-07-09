<?php
		
/*
// PP 21/01/2013
// renseigne la variable globale js chemin_images selon le thème courant
// cette varaible est ensuite utilisée partout
// version bien plus simple qu'avaec la V1.5
*/		
		
		
$js=<<<EOJ

var chemin_images=\"{$CFG->chemin_images}\";
var chemin_serveur=\"{$CFG->wwwroot}\";

EOJ;

echo $js;