<?php
/**
 * @author Patrick Pollet
 * @version $Id: fr.php 708 2009-04-15 16:33:37Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 *
 *  * ATTENTION : ce fichier est declaré en encodage UTF-8 sous Eclipse !
 */


 /*
 * trad
 */
$textes_langues["installation1"]="Installation Etape 1 -Paramètres initiaux";

$textes_langues["installation2"]="Installation Etape 2 - Création de la base de données";

$textes_langues["maj"]="Mise à jour de votre base de données pour la version 1.5";




$textes_langues["epilogue_install"]=<<<EOF
    <hr><hr>
    <div class="information_gauche">Installation Terminée.<br>
   
    <br/>
    Vous pouvez maintenant tester si vos paramètres sont corrects. 
   <br/>
    Pensez à supprimer les fichiers d installation (le dossier installation) lorsque vous aurez fini les tests afin que personne ne puisse les modifier sans votre accord. gardez-en une copie en cas de besoin.<br>
    <span class="rouge">Si vous avez opté pour l installation automatique, n'oubliez pas d'enlever les droits d'écriture sur le fichier commun/constantes.php. </span><br/>


</div>
EOF;

$textes_langues["info_compte_cree"]=<<<EOF
<div>
Un utilisateur ayant les mêmes login et mot de passe que votre correspondant C2I sur  la plateforme nationale a été créé localement.<br/>
Vous pouvez desormais utiliser ce compte pour créer des examens sur votre plate-forme.
</div>
EOF;





$supp=array(
"tests_parametres"=>"test de votre environnement",
"non_critique"=>"non critique",
"parametres_connexion"=>"Paramètres de connexion à la base de données",
"form_serveur_bdd"=>"nom du serveur", "ex_form_serveur_bdd"=>"(ex. localhost)",
"form_nom_bdd"=>"nom de la base MySQL",
"form_user_bdd"=>"utilisateur",
"form_pass_bdd"=>"mot de passe",
"form_prefix"=>"prefixe des tables",
"form_type_c2i"=>'type de la plateforme',

"parametres_installation"=>"Paramètres d'installation de la plateforme'",
"form_repertoire_installation"=>"URL d'accès à votre plateforme",
"ex_form_ri"=>"(ex. http://votre-univ.fr/c2i/)",
"form_repertoire_ressources"=>"Chemin des ressources",
"ex_form_re"=>"(ex. /usr/share/c2i)",

"info_etat_connexion_bd_ok"=>"connexion réussie avec la base de données",
 "info_etat_droits_bd_ok"=>"les droits d'accès sur la base de données semblent suffisants ",
 "info_etat_droits_bd_ko"=>"problème de droits sur la base de données ? ",
"info_etat_droits_ok"=>"les droits sur le dossier des ressources sont corrects" ,
"info_etat_droits_ko"=>"erreur en tentant de créer une table sur la base de données" ,

);

 $textes_langues=array_merge($textes_langues,$supp);

?>
