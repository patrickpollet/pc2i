<?php

/**
 * @version $Id: anonyme.php 1225 2011-03-16 18:37:54Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '.';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");
list($id_examen, $id_etab) = get_examen_anonyme();
if (! isset($id_examen)){

    erreur_fatale ("err_pas_examen_anonyme");
}


$login=<<<EOL
<div id="Layer1" style="position:absolute; left:51%; top:167px;width: 320px; z-index:1;">
<form action="codes/entrer.php" method="post" name="fc" id="monform">
  <table>
    <tr>
      <td class="texte_login" colspan="2">Adresse electronique </td>
    </tr>
    <tr>
      <td><input name="email" type="text" id="email" size="25" maxlength="100" class="saisie {required} validate-email" title="{js_mail_incorrect}"/></td>
      <td>{bouton_connexion}
      </td>
    </tr>
    <tr>
      <td colspan="2" class="taille1" > {msg_anonyme_info_mail}</td>
    </tr>
  </table>
</form>
</div>
EOL;


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

$tpl->traduit("alt_type_p","texte_bienvenue_anonyme" );


$tpl->assign ("type_p","");  // c'est du positionnement ...'

//rev 981 controle saisie au niveau du client
$tpl->assign('required',$CFG->anonyme_controle_adresse_mail?'required':'');

switch ($CFG->anonyme_controle_adresse_mail) {
    case 1: $tpl->traduit("msg_anonyme_info_mail","msg_anonyme_info_mail_requis");break;
    case 2: $tpl->traduit("msg_anonyme_info_mail","msg_anonyme_info_mail_connue_pf");break;
    case 3: $tpl->traduit("msg_anonyme_info_mail","msg_anonyme_info_mail_connue_ldap");break;
}
$err_c2i=optional_param("err_c2i",0,PARAM_ALPHANUM);

if (!empty($err_c2i)){
    $tpl->newBlock("ALERT");
       $tpl->assign('err_msg',addslashes(traduction($err_c2i)));
}



$tpl->printToScreen();

?>