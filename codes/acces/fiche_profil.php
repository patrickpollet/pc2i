<?php

/**
 * @author Patrick Pollet
 * @version $Id: fiche_profil.php 1231 2011-03-25 15:24:21Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	Fiche d'item
//
////////////////////////////////



$fiche=<<<EOF

<!-- START BLOCK : rafraichi_liste -->
<script type="text/javascript">
        if (window.opener)
            window.opener.location.href=window.opener.location.href;
</script>

<!-- END BLOCK : rafraichi_liste -->

<table class="fiche">
        <tbody>
          <tr>
            <th>{form_libelle}</th>
            <td >{titre}</td>
          </tr>
<!--INCLUDE BLOCK : table_profil -->

      <tr>
         <th>{membres}</th>
           <td>
              <table width='100%' class='sansbordure'>
<!-- START BLOCK : membre -->

        <tr>
        <td>{nom_mail} &nbsp;</td>
        <td> <ul style="display:inline;">{consulter_fiche}  {supprimer_membre}</ul><br/></td>
        </tr>
<!-- END BLOCK : membre -->
        </table>
           </td>
      </tr>
   </tbody>
</table>

EOF;


$chemin = '../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

$idq = required_param("idq", PARAM_INT);
$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT);
$url_retour=optional_param("url_retour","",PARAM_CLEAN);
$supp=optional_param("supp","",PARAM_CLEAN);

require_login('P');
v_d_o_d("ul");  // rev 821 pas grave si il les voit

$ligne = get_profil($idq); //fatale si inconnu





require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates
$tpl->assignInclude("corps",$fiche,T_BYVAR); // le template g�rant la fiche du profil
$tpl->assignInclude("table_profil",profil_en_table(),T_BYVAR);  //tableau des profils avec cases a cocher non modifiables
$tpl->prepare($chemin);


if ($supp && is_admin()) {
    supprime_utilisateur_profil ($idq,$supp);
    $tpl->newBlock("rafraichi_liste");
    $tpl->gotoBlock ("_ROOT");

}


$tpl->assign("_ROOT.titre_popup", traduction("fiche_profil") . " " . $idq." : ".$ligne->intitule);

$tpl->assign("titre", $ligne->intitule);

garni_table_profil($tpl,$ligne);

if ($membres=get_utilisateurs_avec_profil($idq)) {
    foreach ($membres as $membre) {
        $tpl->newBlock("membre");
        $str=cree_lien_mailto($membre->email,_regle_nom_prenom($membre->nom,$membre->prenom)); //rev 843
        $tpl->assign ("nom_mail",$str);
        print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("personnel/fiche.php?id=".$membre->login));
        // rev 962 icone de radiation (plus rapide que d'éditer ce compte)
        if (is_admin(false,$CFG->universite_serveur)) {
	        print_menu_item($tpl,"supprimer_membre",
             get_menu_item_supprimer("fiche_profil.php?idq=".$idq."&amp;supp=".$membre->login,
              traduction("js_radier_0",false,addslashes($membre->login))
             ));
        } else
	        $tpl->assign("supprimer_membre","");
    }
}

$tpl->print_boutons_fermeture($url_retour);


$tpl->printToScreen(); //affichage
?>

