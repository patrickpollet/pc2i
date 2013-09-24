<?php
/**************************************************************************************
This file is part of "Plate-forme de certification et positionnement pour le C2i niveau 1" Copyright (C) 2004-2007 : MEN- MESR-SG-SDTICE Rachid EL BOUSSARGHINI - St�phane BAUDOUX This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public licence as published by the Free Software Foundation; either version 2 of the License, or any later version. This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
**************************************************************************************/

/**
 * @author Patrick Pollet
 * @version $Id: index.php 1229 2011-03-21 17:24:44Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '.';

$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");


$page=<<<EOP

<!-- INCLUDE BLOCK : C2Iheader -->

<body  onload="{global_onload}" >
<!-- INCLUDE BLOCK : C2Ilogo -->

<div id="contenu_principale" class="centre">
    <div style="width:800px;margin: 0 auto;" class="commentaire2">{texte_bienvenue}</div>
    <br/><br/>
    <span class="taille2">{texte_choix_pf}</span>
<!-- START BLOCK : CERT -->
	<p><a href="certification.php"><img src="{chemin_images}/test02c.gif"   title="{plc}" alt="{plc}" /></a></p>

<!-- END BLOCK : CERT -->
<!-- START BLOCK : POS -->
	<p><a href="positionnement.php"><img src="{chemin_images}/test02.gif"   title="{plp}" alt="{plp}"/></a></p>

<!-- END BLOCK : POS -->
<!-- START BLOCK : ANONYME -->
     <p> <a href="anonyme.php"><img src="{chemin_images}/test02a.gif"  title="{pla}" alt="{pla}"/></a></p>
<!-- END BLOCK : ANONYME -->

</div>
<!-- INCLUDE BLOCK : C2Ifooter -->

</body>
</html>

EOP;

/*
 * pb avec la nationale qui a les 2 � 0 !!!
 *
 */


if ($etab=get_etablissement($CFG->universite_serveur,0)) {
    if ($etab->positionnement ==0)
        header("location:$chemin/certification.php");
} else {
    // pas d'établissements donc base de données non mise à jour depuis une nationale
    header("location:$chemin/installation/majbase.php");
}


$ano=get_examen_anonyme();
if ($etab->certification ==0 && !$ano ) // //ca suffit  comme test
        header("location:$chemin/positionnement.php");

require_once( $chemin."/templates/class.TemplatePower.inc.php");    //inclusion de moteur de templates
$tpl = new C2IPrincipale($page,T_BYVAR );    //cr�er une instance
$tpl->prepare($chemin);

$tpl->assign ("url_accueil","");
if ($etab->certification ==1)
        $tpl->newBlock ("CERT");
$tpl->newBlock ("POS");


// nb pas de php dans un teplate , ni de javascript dans un bloc !!!!
if ($ano)
     $tpl->newBlock("ANONYME");

$tpl->printToScreen();