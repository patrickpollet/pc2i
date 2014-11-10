<?php
/**************************************************************************************
This file is part of "Plate-forme de certification et positionnement pour le C2i niveau 1"
Copyright (C) 2004-2009 :
Copyright (C) 2004-2009 :
MEN- MESR-SG-SDTICE Rachid EL BOUSSARGHINI - St�phane BAUDOUX -Patrick POLLET
This program is free software; you can redistribute it and/or modify it under the terms
 of the GNU General Public licence as published by the Free Software Foundation;
 either version 2 of the License, or any later version.
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
**************************************************************************************/

/**
 * @version $Id: positionnement.php 1300 2012-09-11 14:01:01Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */


//PP test imm�diat si CAS requis
require_once("commun/constantes.php");

//rev 978 acc�s direct � un examen
// a faire avant une eventuelle redirection CAS
$id_examen=!empty($_GET['id_examen'])?$_GET['id_examen']:0;
$id_etab=!empty($_GET['id_etab'])?$_GET['id_etab']:0;


if (!empty($cas_force))
    if (isset($cas_url) && isset($cas_port) && isset($cas_service)) {
        $chemin='.';
       $type_p="positionnement";
       include("cas/caslogin.php");
       exit;
}

$login=<<<EOL
<div id="Layer1" style="position:absolute; left:51%; top:167px; z-index:1">
<form action="codes/entrer.php" method="post" name="fc" id="monform">
  <table>
    <tr>
      <td class="texte_login" colspan="2">Identifiant</td>
    </tr>
    <tr>
      <td><input name="identifiant" type="text" id="identifiant" size="25" maxlength="100" class="saisie required" title="{js_login_manquant}" tabindex="1"/></td>
      <td>
       {bouton_connexion}
       <input name="id_examen" type="hidden" value="$id_examen"/>
       <input name="id_etab" type="hidden" value="$id_etab"/>

      </td>
    </tr>
    <tr>
      <td class="texte_login" colspan="2">Mot de passe </td>
    </tr>
    <tr>
      <td colspan="2"><input name="passe" type="password" id="passe" size="25"  class="saisie required" title="{js_mdp_manquant}" tabindex="2" /></td>
    </tr>
 <!-- START BLOCK : nouv_mdp -->
     <tr><td>&nbsp;</td></tr>
    <tr>
    <td class="taille1"><a href="#" onclick="openPopup('codes/acces/nouv_mdp.php','','{lpm}','{hpm}')">{mdp_oublie}</a></td>
    </tr>
<!-- END BLOCK : nouv_mdp -->
  </table>
</form>
</div>


<!--START BLOCK : cas -->

<div id="Layer2" style="position:absolute; left:22%; top:177px; width: 320px; z-index:1">
<form action="cas/caslogin.php" method="post" name="fc2">
  <table>
    <tr>
      <td><input name="cas" type="submit" class="saisie_bouton" value="{acces_cas}"/>
          <input name="type_p" type="hidden" value="{type_p}"/>
          <input name="id_examen" type="hidden" value="$id_examen"/>
       <input name="id_etab" type="hidden" value="$id_etab"/>
      </td>
    </tr>
    <tr>
      <td class="taille1">{info_acces_cas}</td>
    </tr>
  </table>
</form>
</div>

<!--END BLOCK : cas -->
EOL;

$chemin='.';
$chemin_commun = $chemin."/commun";

require_once($chemin_commun."/c2i_params.php");

$err_c2i=optional_param("err_c2i",0,PARAM_ALPHANUM);

require_once( $chemin."/templates/class.TemplatePower.inc.php");


if ($CFG->theme == 'v14')

    $tpl = new C2IPrincipale( $chemin."/templates/login.html" );
else
        $tpl = new C2IPrincipale( $chemin."/templates2/login.tpl" );
$tpl->assignInclude("login",$login,T_BYVAR);

$tpl->prepare($chemin);

// rev 981 validation du mail
$CFG->utiliser_validation_js=1;

print_bouton_connexion($tpl);

$tpl->traduit("alt_type_p","texte_bienvenue_positionnement" );


$tpl->assign ("type_p","");
if (!empty($err_c2i)) {
    $tpl->newBlock("ALERT");
    $tpl->assign('err_msg',addslashes(traduction($err_c2i)));
}
else
    $tpl->newBlock ("FOCUS");


if (isset($cas_url) && isset($cas_port) && isset($cas_service)) {
    $tpl->newBlock('cas');
    $tpl->assign("type_p","positionnement");
}

if (empty($CFG->pas_de_motdepasse_oublie)) // rev 897
    $tpl->newBlock("nouv_mdp");

$tpl->printToScreen();

?>