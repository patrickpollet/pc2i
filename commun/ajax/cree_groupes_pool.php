<?php

/**
 * @author Patrick Pollet
 * @version $Id: cree_groupes_pool.php 638 2009-04-06 14:46:32Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
////////////////////////////////
//
//via ajax
//
//	variables :
//	$ide = identifiant examen
///	$idq = identifiant �tablissement examen
////////////////////////////////

$chemin = '../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");
require_once($chemin_commun."/lib_ajax.php");

require_once( $chemin."/templates/class.TemplatePower.inc.php");    //inclusion de moteur de templates

require_once($chemin_commun."/lib_pool.php");


if (! require_login ("P",false))
        die_erreur("erreur 0");

//ajax pas d'erreur fatale ...
$idq=optional_param("idq",0,PARAM_INT);
$ide=optional_param("ide",0,PARAM_INT);
$url_retour=optional_param("url_retour","",PARAM_RAW); //criteres rafraichissement liste examens


if (!$idq || ! $ide )
      die_erreur("erreur 1");

if (! teste_droit("em")) die_erreur ("erreur 2");


$ligne=get_examen ($idq,$ide,false);
if (!$ligne) die_erreur(traduction ("err_examen_inconnu"));

if (!$ligne->est_pool)
    die_erreur (traduction("err_pas_un_pool"));



$fils= generer_groupes($idq,$ide);  // g�n�rer et r�cuperation de la liste
//envoi comme une liste de liens HTML
echo traduction ("form_liste_groupes");
echo "<ul>";
foreach ($fils as $fil) {
    $btn1=print_menu_item(false,false,get_menu_item_modifier("ajout.php?idq={$fil->id_examen}&ide={$fil->id_etab}"));
    $btn2=print_menu_item(false,false,get_menu_item_consulter("fiche.php?idq={$fil->id_examen}&ide={$fil->id_etab}"));

    $texte=<<<EOT
       <li class="menu_niveau2_item">
          $fil->nom_examen  $btn1  $btn2
       </li><br/>
EOT;
    echo $texte;
}

echo "</ul>";

if ($url_retour) { //rafraichir la liste des examens il y en a en plus
    $texte=<<<EOT
<script type="text/javascript">
        if (window.opener)
            window.opener.location.href='liste.php?$url_retour';
</script>
EOT;
   echo $texte;
}

die_ok(true);
/************************
 le div present sur la fiche du pool pere qu'il faut remplacer par la liste des fils
 <div id="liste_fils">
<!-- START BLOCK : liste_groupes -->
                    {form_liste_groupes} :
                    <ul>
                    <!-- START BLOCK : groupe -->
                        <li>{nom}  {modifier_fiche} {consulter_fiche} </li>
                    <!-- END BLOCK : groupe -->
                    </ul>
            <!-- END BLOCK : liste_groupes -->
            <!-- START BLOCK : creer_groupes -->
                    <a href="{url}">{generer_groupes}</a>
            <!-- END BLOCK : creer_groupes -->
            <span class="rouge1">{pool_manque_question}</span>
</div>
*************/
?>
