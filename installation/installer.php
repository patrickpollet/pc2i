<?php

/**
 * @author Patrick Pollet
 * @version $Id: installer.php 1262 2011-09-09 11:27:26Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */


header("Pragma:no-cache");

$chemin = '..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";

//print_r($_POST); die();

/**
 * soit ce fichier a été créé par index.php soit il a été créé à la main
 * sans les deux cas il doit être utilisé
 */
require_once ($chemin_commun."/constantes.php");

require_once($chemin_commun."/weblib.php");
require_once ($chemin_commun."/fonctions_session.php");
require_once ($chemin_commun."/fonctions_divers.php");
require_once ($chemin_commun."/lib_langues.php");
require_once("mini_config.php");
require_once("lib_install.php");

//donc on peut ...
require_once ($chemin_commun."/lib_bd.php");
require_once ($chemin_commun."/lib_acces.php");

$wwwroot=required_param("wwwroot",PARAM_RAW);
$dataroot=required_param("dataroot",PARAM_RAW);
$c2i=required_param("c2i",PARAM_RAW);
$prefix=required_param("prefix",PARAM_RAW);

//important pour affichage correct des logos 
$CFG->c2i=$c2i;

//ajax
if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
	// un petit peu d'ajax
	
    
	require_once($CFG->chemin_commun."/lib_ajax.php");
	
	$test = new InstallSqlLoader ($connexion);
	$test->parse_file('database.sql',false);
	if ($test->getErrors()) {
	    print_r($test->getErrors());
	    echo "0";
	    die ();
	}
	//todo maj de chemin_ressources
	//celui recu de la nationale est faux !
	//maintenant on peut ...
	require_once($chemin_commun."/lib_config.php");
	set_config("pfc2i","chemin_ressources",$dataroot,true);
	set_config("pfc2i","wwwroot",$wwwroot,true);
	set_config("pfc2i","verifier_install",0);
	$now=time();
    set_config("pfc2i","date_mise_a_jour",$now,0);
    set_config("pfc2i","date_installation",$now,0);
    set_config("pfc2i","date_creation_config",$now,0);
    set_config("pfc2i","date_derniere_maj",$now,0);
    set_config("pfc2i","prefix",$prefix,0);
   
    set_config("pfc2i","c2i",$c2i,0);
    set_config("pfc2i","adresse_pl_nationale","https://c2i.education.fr/{$c2i}/",0);
    set_config("pfc2i","adresse_feedback_questions","qcm-{$c2i}@education.gouv.fr/",0);
    
    if ($c2i=='c2i1')
        set_config("pfc2i","algo_tirage",3,true);  //par famille
    else 
        set_config("pfc2i","algo_tirage",1,true);      // par domaine
    
    //créér un compte admin
    $pwd= mot_de_passe_a($CFG->longueur_mot_de_passe_aleatoire);
    $user=new StdClass();
    $user->login=$user->nom=$user->prenom='admin';
    $user->email='admin@univ';
    $user->password=md5($pwd);
    $user->etablissement=0;
    $user->est_admin_univ='O';
    $user->origine='install';
    $user->ts_connexion = $user->ts_derniere_connexion =time();
    cree_utilisateur($user,0,false); //pas d'espion encore
    
    // avec ses droits
    $data=new StdClass();
    $data->login='admin';
    $data->id_profil=1; // admin
    insert_record('droits',$data,false,false,false);
    
    
    if ($c2i !='xx') { 
    	set_config('pfc2i','universite_serveur',0);  // force synchro de la BD vide avec la nationale
    	print  traduction ("epilogue_install",true,$pwd,$c2i,$c2i);
    	die();   	 
    } else {
    	//plateforme nationale sans referentiel 
    	//mettre le compte super-admin et le laisser faire
    	$user=new StdClass();
    	$user->login='admin';
    	$user->etablissement=1;
    	$user->est_superdamin=$user->est_admin_univ='O';
    	update_utilisateur($user,false);  //pas d'espion encore
    	
    	set_config('pfc2i','universite_serveur',0);  // pas de synchro de la BD vide avec la nationale
    	print  traduction ("epilogue_install2",true,$user->password);
    	die();
    }



}

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
//mise en page v1.6 sans tables
$tpl = new C2IPrincipale($CFG->chemin."/templates2/popup.tpl"); //cr�er une instance

$fiche=<<<EOF
<form id="monform"  action="#">
<input type="hidden" name="wwwroot" value="{wwwroot}"/>
<input type="hidden" name="dataroot" value="{dataroot}"/>
<input type="hidden" name="c2i" value="{c2i}"/>
<input type="hidden" name="prefix" value="{prefix}"/>

</form>
<!--
 <div id="titre">{titre_popup} </div>
 -->
{contenu_direct}
<span id="spinner" style="display: none"><img src="{chemin_images}/spinner.gif" alt="Requête en cours "/></span>
<div id="maj">
</div>

<script type="text/javascript">

  majDiv("maj","installer.php","spinner","monform");

</script>
EOF;


$options=array (
	"corps_byvar"=>$fiche
);


$tpl->prepare($chemin,$options);
$CFG->utiliser_prototype_js=1;

$tpl->traduit ("titre_popup","installation2");

//rev 978 renvoyer � Ajax capital sinon les required_params vont échouer !!!
$tpl->assign('wwwroot',$wwwroot);
$tpl->assign('dataroot',$dataroot);
$tpl->assign('c2i',$c2i);
$tpl->assign('prefix',$prefix);

ob_start();

echo "connexion avec la bd établie<br/>";
echo "<hr/><hr/>";
enteteTests("création / mise à jour de la base de données");
intituleTests("un peu de patience ...");
$content = ob_get_contents();
ob_end_clean();
$tpl->assign("_ROOT.contenu_direct",$content);





$tpl->printToScreen();										//affichage
?>