<?php


/**
 * @author Patrick Pollet
 * @version $Id: entrer.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	page de connexion aux plateformes
//  de certification et de positionnement.
//  on verifie de o� on vient,
//  selon le cas la connexion se fait � la plateforme de certification
//  ou � la plateforme de positionnement
//  le cas des ent est pris en compte
//
////////////////////////////////



//PP : on peut arriver ici depuis certification ou positionnement
//via include()  apr�s un include � cas/caslogin.php
// et $chemin  est alors '.' ; dans ce cas on n'a lu QUE les constantes
if (!isset($chemin)) {
	$chemin = '..';
}
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";

require_once($chemin_commun."/c2i_params.php");

//rev 978  soit dans le formulaire (qui est en post) soit d�ja defini si inclusion par caslogin.php
$id_examen=isset($id_examen)?$id_examen:optional_param('id_examen',0,PARAM_INT);
$id_etab=isset($id_etab)?$id_etab:optional_param('id_etab',0,PARAM_INT);

if (! isset($page_origine_explode)){
	$page_origine_explode = explode("?",@basename($_SERVER['HTTP_REFERER']));
}
$page_origine=$page_origine_explode[0];

if (!isset($verif)) $verif="";

if ($verif !="ent") {
	if (empty($USER->id_user)){ // connexion ?
		////////////////////////////////////////
		//
		//	test de la page de r�f�rence pour savoir quelle plateforme charger
		//
		////////////////////////////////////////

		// si appel par un formulaire , on a vu l'�cran login/mot de passe
		// sinon on a �t� inclus par un plugin (cas/caslogin.php)



		switch($page_origine){ // page d'arriv�e sur la plateforme
			case "positionnement.php" :
				$identifiant=required_param("identifiant",PARAM_RAW);
				$passe=required_param("passe",PARAM_RAW);
				$verif = !empty($USER->verif)?$USER->verif:"bdd";
				// recherche en base de donn�es si pas "ent"
				$type_p = "positionnement";
				break;
			case "certification.php" :
				$identifiant=required_param("identifiant",PARAM_RAW);
				$passe=required_param("passe",PARAM_RAW);
				$verif = !empty($USER->verif)?$USER->verif:"bdd";
				// recherche en base de donn�es si pas "ent"
				$type_p = "certification";
				break;
			case "anonyme.php" :
                // rev 978
                if ($err=valide_acces_anonyme(optional_param("email","",PARAM_RAW))) {
                    @detruire_session();
                    header("location:$chemin/anonyme.php?err_c2i=$err");
                    exit;
                }


				$verif = "anonyme"; // on squize le test du mot de passe   NON (PP)!!!
				// recherche en base de donn�es si pas "ent"
				$type_p = "positionnement";
				// On verifie qu'un examen anonyme existe, pas la peine de l'inscrire s'il n'y en a pas
				list($id_examen, $id_etab) =get_examen_anonyme();
				// rev 974 ne pas cr�� de compte si l'examen est de type tirage lors du passage
				// c'est inutile (mais alors pas de stat)
				if ($id_examen != ""){
                		$compte=cree_compte_anonyme($id_etab, optional_param("email","",PARAM_RAW));
        				$identifiant=$passe=$compte->login;
                        $tags='passage anonyme '.$id_examen.'.'.$id_etab.' '.getremoteaddr().' '.time();
        				inscrit_candidat($id_examen,$id_etab,$compte->login,$tags);

				}
				else{
					erreur_fatale("err_pas_examen_anonyme","",true);
					exit;
				}
				break;
			default :
				// on est revenu ici depuis un menu
				// on garde $verif et $type_p comme ils sont .
				break;
		}

		$premiereFois=1;
		if ((@trim($identifiant)=="") || (@trim($passe)=="")) {
			//print("location:$chemin/$type_p.php?err_c2i=pa");
           erreur_fatale("err_login_mdp_vides","",true);

		}

	}  else {  // on revient ici depuis un "menu"
		// pour que les SQL fonctionne ...
		$identifiant=$USER->id_user ;
		$verif=$USER->verif;
		$type_p=$USER->type_plateforme;
		$page_origine=$USER->page_origine;
		// pas deux fois appel � espion pour cette connexion ...
		$premiereFois=0;
		if ($verif !="ent") {
			if (isset($identifiant) && $identifiant !=$USER->id_user){  //SB
				erreur_fatale("err_deja_connecte_autre_login", $USER->id_user,true);
			}
			switch($page_origine_explode){ // page d'arriv�e sur la plateforme
				case "positionnement.php" :
					if ($USER->type_plateforme != "positionnement") erreur_fatale("err_deja_logue_en","certification","",true);
					break;
				case "certification.php" :
					if ($USER->type_plateforme != "certification") erreur_fatale("err_deja_logue_en","positionnement","",true);
					break;
				default :;
			}

        }
	}
}

//connexion au serveur et base de donn�es et v�rification acc�s possible
// todo plus simple selon compte->auth et $compte->type_user!!!
//print "$identifiant $verif";

if ( $verif == "bdd" || $verif=="ent" || $verif=="anonyme"){

	if ( $compte=get_compte($identifiant,false)) { //cherche les 2 tables
		if (($verif=="bdd") && $premiereFois )
			$ok=authentifie_compte($compte,$passe);  //manuel ou ldap ou anonyme (pas cas)
		else $ok=true; //via un ENT ou anonyme


		if ($ok){ // utilisateur connect�
			if ($premiereFois) {
				//print("maj_conn");

                //TODO si 'prof' et 1er acc�s ($compte->connexion==0) afficher la charte d'utilisation des questions
                // si refus�e pas de connexion

				maj_info_connexion ($compte,$verif,$type_p);  //maj date de connexion...
				if($compte->type_user=="P") lecture_droits();	//pour l'instant '
			}
			
			// V2 voir si la PF est en mode maintenance 
			if (!empty($CFG->mode_maintenance)) {
			    if (!is_admin()) {
			        detruire_session();
			        header ("location:{$CFG->chemin}/codes/maintenance.php");
			    }    
			}
			
			
			
			//garder en session ce qui doit l'�tre
			register_user_data($compte,$verif,$type_p,$page_origine);

			switch ($compte->type_user) {
				case "P":
					if ($type_p=="certification"){
						if ($USER->droits['row_admin']->limite_positionnement == 1 && ! is_admin()){
							detruire_session();
							erreur_fatale("err_acces_certification",$USER->id_user);
						}
					}

					//envoyer la page d'accueil'
					require_once( $chemin."/templates/class.TemplatePower.inc.php");
                   
                     // la page d'accueil est une page com�me une autre
                        $tpl = new C2IPrincipale();
                        $tpl->assignInclude('corps', $chemin."/templates2/accueil.tpl");
                        $tpl->prepare($chemin);
                         print_menu_haut($tpl,'');
                         $tpl->gotoBlock("_ROOT");

                        $tpl->traduit ("texte_bienvenue","texte_bienvenue_".$USER->type_plateforme);

                        if ($CFG->utiliser_notions_parcours &&  a_capacite("config")){
                            $tpl->newBlock("notions_parcours"); // ajout � la liste
                        }
                        if ($CFG->prof_peut_passer_qcm) {
                            $tpl->newBlock("qcm_profs");
                        }
                        if ($CFG->prof_peut_avoir_parcours) {
                            $tpl->newBlock("parc_profs");
                        }

					if ($CFG->universite_serveur == 1) {
						$message_a = traduction("pn");
					}
					else  {
						$message_a = traduction ("pl");
						// rev 1.5
						$CFG->utiliser_prototype_js=1;
                        $tpl->newBlock ('cherche_version'); // rev 1014
					}
					$message_a = get_fullname($USER->id_user)." : ".$message_a;
					$tpl->assign("_ROOT.message_a",$message_a);
					$tpl->printToScreen();
					exit;

				case "E":
                    // rev 978 tentative d'acc�s direct a un examen si ide et idq connu (ex depuis un lien Moodle)

                    if ($id_examen && $id_etab) {
                        if ($ex=get_examen($id_examen,$id_etab,false)) { // si connu
                        if ($ex->{$USER->type_plateforme}=='OUI')          // si sur la bonne PF
                            header("location:$CFG->chemin/codes/qcm/passage.php?idq=$id_examen&ide=$id_etab&" . $session_nom . "=" . session_id());
                        exit;
                        }
                    }

				     header("location:$CFG->chemin/codes/qcm/liste.php?" . $session_nom . "=" . session_id());
				     exit;
				case "A":
				   list ($id_examen, $id_etab) = get_examen_anonyme();
			   	   header("location:$CFG->chemin/codes/qcm/passage.php?idq=$id_examen&ide=$id_etab&" . $session_nom . "=" . session_id());
			   	   exit;
			   	default:
			   	   	break; // ????
			} // type utilisateur
		} // pas authentifi�
	} //compte inconnu
	// rev 931 �viter la boucle infinie CAS <-->PF
	else if ( $verif=="ent")erreur_fatale("err_pas_de_compte_pf","",false);

} //methode de verif inconnue
// n'a pas acc�s � la plate forme compte inexistant ou �chec ldap
//var_register_session("identifiant",$identifiant);
//var_register_session("page_origine",$page_origine_explode[0]);
// IMPORTANT pour retour a l'�cran de login ...'
var_register_session("type_p",$type_p);
//var_register_session("err_c2i","err_pa");

// rev 978 remettre le lien vers l'examen pour un autre essai de connexion
if ($id_examen && $id_etab)
        $extra='&amp;id_examen='.$id_examen.'&amp;id_etab='.$id_etab;
    else
        $extra='';

header("location:$chemin/codes/quitter.php?err_c2i=err_pa&page_origine=".$page_origine."&amp;".$session_nom."=".session_id().$extra);

