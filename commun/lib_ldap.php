<?php
/**
 * @author Patrick Pollet
 * @version $Id: lib_ldap.php 1258 2011-06-03 14:16:50Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/*
 * bibliotheque d'acc�s LDAP
 * mettre ce drapeau � 1 pour un debug fort des acc�s ldap dans un fichier codes/locale/config.php
 */


/**
 * partie retouche BD
 * rev 1.4--> 1.41
 */

  if (is_admin()) {   //utilisateur courant uniquement
    maj_bd_ldap();
 }

  function maj_bd_ldap () {
      global $CFG,$USER;



  }

function ldap_maj_config() {

   add_config('ldap','filtre_groupes_ldap', '*', '*', 'Filtre personnalisable de la liste des groupes',1);
   add_config('ldap', 'numetudiant_prefixes_ldap', '', '', "Liste des préfixes dans le numéro d'étudiant (séparé par des virgules)", 1);
}

/**
 * conversion ligne csv selon format en une liste de login
 */
function conversion_ligne_ldap($infos, $ide, $format) {
    global $CFG;

    //tres important
    /**  ce code ne fonctionne pas il ne modifie pas le tableau mais la locale $ligne ...
    foreach ($infos as $ligne)
        $ligne=vire_guillemets($ligne);
    */
    //print_r($infos);
     for ($i=0; $i<count($infos); $i++)
              $infos[$i]=vire_guillemets(stripslashes($infos[$i]));
              //stripslashes important si guillemets tap�s dans la liste
              // et attention � l'ordre !
    if (empty($infos[0])) return false;
    switch ($format) {
        case "format_n":  //num�ro

            if ($cpt=ldap_get_compte_byidnumber($infos[0],$ide))
                return $cpt->login;
             else return false;
            break;
        case "format_l":   //login
               return $infos[0]; // test d'existence fait plus tard dans ce cas
            break;
        case "format_m": //email
              if ($cpt=ldap_get_compte_byemail($infos[0],$ide))
                return $cpt->login;
              else return false;
            break;
        default :
            $cpt=false;
        break;
    }

    return $cpt;
}


function lecture_fichier_ldap ($fichier,$format,$ide,&$resultats) {
	global $CFG;
	$res=array();
	//print($fichier);
	$handle = fopen($fichier, "rb");
	if ($handle) {
		$contents=fread($handle,filesize($fichier));
        // rev 1024 pb avec des fichiers issus de mac
        $contents = preg_replace('/\r\n|\r/', "\n", $contents);
		$res=explode("\n",$contents);
		fclose($handle);
	} else
		erreur_fatale("err_lecture_fichier",$fichier);

	$listelogins=array();
	foreach ($res as $ligne) {
        // REV 971 POUR POUVOIR UTILISER un fichier issu d'apog�e avec les tabulations !
        // convert separators ' or \t to ;
        $ligne = preg_replace('/\t|,/', ';', $ligne);
		$infos=explode(";",$ligne);
		if (count($infos)) { //sauter ligne blanche
			if ($login=conversion_ligne_ldap($infos,$ide,$format))
				$listelogins[]=$login;
			else
				set_erreur (traduction("err_compte_ldap_inconnu",true,$infos[0]),$resultats);
		}
	}
   // print_r($listelogins);
	return $listelogins;
}


/**
 * inscrit massivement une liste de compte ldap (un par ligne)
 * ou un/des groupes separ�s par un ";"
 */
 function inscription_massive_ldap($idq,$ide,$liste=false,$groupes=false,$fichier=false,$format_fic='format_n') {
    global $CFG;
    require_once($CFG->chemin."/commun/lib_rapport.php");
	 $resultats=array(); //�tats des op�rations
	 $comptes=array();   //comptes a cr�er
	 $nb=0;
     $tags='inscription ldap ';
	 if ($liste || $groupes || $fichier) {
	 	  auth_ldap_init($ide); //important on prend le ldap de l'�tablissement de l'examen
	 	                        // qui peut etre <> du courant (ex nationale ou composante)

		 if (! $ldapconn=auth_ldap_connect('','',false))  // pas d'EF '
			 set_erreur(traduction( "err_connexion_ldap"),$resultats);
		 else {
            if ($fichier) {
             $tags .='fichier '.$fichier.' '.time();
             set_ok (traduction ("info_inscription_fichier",false,$fichier,$format_fic),$resultats);
             $comptes=lecture_fichier_ldap($fichier,$format_fic,$ide,$resultats);// tous en m�moire dans un tableau
            }

			 else if ($liste){  // sortie d'un textarea
                  $tags.='liste '.time();
                 // rev 1024 pb avec des fichiers issus de mac
                 $liste = preg_replace('/\r\n|\r/', "\n", $liste);
				 $tmp=explode("\n",$liste); //liste de login
                 if (count($tmp)>0)
                         $comptes=array_merge($comptes,$tmp);
			 }
			 else if ($groupes) {
				 if ($CFG->debug_ldap_groupes) print("inscription des groupes ".$groupes);
				 $groupes=explode(";",$groupes);  //plusieurs separ�s par ;
                 $tags.='groupes '.time();
				 foreach ($groupes as $groupe) {
                     set_ok (traduction ("info_inscription_groupe",false,$groupe),$resultats);
					 $groupe=trim($groupe);
                      if ($CFG->debug_ldap_groupes) print("inscription du groupe ".$groupe);
					 if (empty($groupe)) continue;
                     $tags.= ' '.$groupe;
					 $tmp=get_group_members ($groupe);
					 if (count($tmp)>0)
						 $comptes=array_merge($comptes,$tmp);
					 else
						 set_erreur(traduction("err_groupe_ldap_inconnu_ou_vide",false,$groupe),$resultats);
				 }
			 }
			 //maintenant on y va

			 foreach ($comptes as $uid) {
				 $uid=trim($uid);
                 if (empty($uid)) continue ; // rev 1013 lignes vides possibles
				 if (!auth_user_exists($uid))
					 set_erreur (traduction("err_compte_ldap_inconnu",true,$uid),$resultats);
				 else  {
					 if (!get_compte($uid,false)) {  //attention dans les 2 tables !!!!
						// set_ok(traduction ("info_creation_compte",true,$uid),$resultats);
						 $cpt= auth_get_userinfo_asobj($uid); // va le chercher dans ldap au BON format !
						 $cpt->auth=$cpt->origine="ldap";
						 if (! cree_candidat($cpt,$ide))
							 set_erreur(traduction("err_creation_compte",$uid),$resultats);
						 else {
                             set_ok (traduction("info_candidat_ldap_cree",false,$cpt->login),$resultats);
                             if (!est_inscrit_examen($idq,$ide,$uid)) {
								 inscrit_candidat($idq,$ide,$uid,$tags);
								 set_ok (traduction("info_candidat_inscrit",false,$uid, $ide,$idq),$resultats);
                                 $nb++;
							 } else
                                 // impossible ?
								 set_erreur (traduction("info_candidat_deja_inscrit",false,$uid,$idq),$resultats);
						 }
						//print_r($cpt);
					 } else {  //connu
						 if (!est_inscrit_examen($idq,$ide,$uid)) {
	                       $nb++;
    						 inscrit_candidat($idq,$ide,$uid,$tags);
							 set_ok (traduction("info_candidat_inscrit",false,$uid,$ide,$idq),$resultats);
						 } else
							 set_erreur (traduction("info_candidat_deja_inscrit",false,$uid, $ide,$idq),$resultats);
					 }

				 }
			 }
		 }
		 if ($nb) set_ok (traduction ("info_comptes_traites",false,$nb),$resultats);
	 }
	 return $resultats; // pr�s a �tre affich�s par print_details ...
 }

/**
 * recherche un compte ldap par numero suppan
 * @param numetudiant
 * @return un enregistrement pres a etre ins�r� en BD ou false
 */
function ldap_get_compte_byidnumber($numetudiant,$ide=false) {
    global $CFG,$USER;
    if (!$ide) $ide=$USER->id_etab_perso;
    if (!auth_ldap_init($ide)) return false;
    $elements_recherche=array();
    $elements_recherche[$CFG->field_map_numetudiant]=$numetudiant;
    $tmp=recherche_ldap($elements_recherche,1,$ide);
    if (empty($tmp))
        return false;
    $uid=$tmp[0]->login; // seul et unique
    $cpt= auth_get_userinfo_asobj($uid); // va le chercher dans ldap au BON format !
    $cpt->auth="ldap";
    return $cpt;

}

/**
 * recherche un compte ldap par email (rev 937)
 * @param numetudiant
 * @return un enregistrement pres a etre ins�r� en BD ou false
 */
function ldap_get_compte_byemail($email,$ide=false) {
    global $CFG,$USER;
    if (!$ide) $ide=$USER->id_etab_perso;
    if (!auth_ldap_init($ide)) return false;
    $elements_recherche=array();
    $elements_recherche[$CFG->field_map_email]=$email;
   //print("LGCBM". print_r($elements_recherche,true));

    $tmp=recherche_ldap($elements_recherche,1,$ide);
    //print_r($tmp);
    if (empty($tmp))
        return false;
    $uid=$tmp[0]->login; // seul et unique
    $cpt= auth_get_userinfo_asobj($uid); // va le chercher dans ldap au BON format !
    $cpt->auth="ldap";
    return $cpt;

}

/**
 * recherche un compte par DN
 * ajout� pour gestion des groupes avec memberattribute_isdn
 */
function ldap_get_compte_bydn ($dnid,$dn,$ide=false) {
    global $CFG,$USER;
    if (!$ide) $ide=$USER->id_etab_perso;
    if (!auth_ldap_init($ide)) return false;
    $elements_recherche=array();
    $elements_recherche[$dnid]=$dn;
    $tmp=recherche_ldap($elements_recherche,1,$ide);
    if (empty($tmp))
        return false;
    $uid=$tmp[0]->login; // seul et unique
    $cpt= auth_get_userinfo_asobj($uid); // va le chercher dans ldap au BON format !
    $cpt->auth="ldap";
    return $cpt;
}




/**
 * appel�e par ajax. ne devrait pas envoyer d'erreur fatale ... '
 */

 function recherche_ldap($elements_recherche,$nbre_ligne=false,$ide=false){
    global $CFG,$USER;
    if (!$ide) $ide=$USER->id_etab_perso;
   if (! auth_ldap_init($ide))
   			return array(); //important de prendre le ldap de l'�tablissement de l'examen
    if (!$nbre_ligne) $nbre_ligne=$CFG->nbre_reponses_ldap;

     // Construction de la requete LDAP
    $requete_ldap= "(&(".$CFG->ldap_user_attribute."=*)(".$CFG->ldap_objectclass.")";
    $joker="*";

    foreach ($elements_recherche as $element=>$valeur) {
        if (!$valeur || strlen($valeur) < $CFG->ldap_longueur_mini)
        continue;
        //$requete_ldap .= "(".$element."=".$valeur.$joker.")";

        $chps_rech = explode(",", $element);
        if (is_array($chps_rech)){
                $requete_ldap .= "(|";
                foreach ($chps_rech as $chp_rech) {
                        $requete_ldap .= "(".$chp_rech."=".$valeur.$joker.")";
                }
                $requete_ldap .= ")";
        }

    }
    $requete_ldap .= ")";
    return auth_get_users($requete_ldap,$nbre_ligne);
}

/**
 * renvoie la liste des champs en base pour cet etablissement
 * @param $id_etab  etablissement
 * @param $liste : comme une chaine a afficher ou un tableau d'objet
 */

 function get_champs_recherche_ldap($id_etab,$liste=false) {
     $lignes=get_records("ldap","id_etab=".$id_etab." AND modifiable='OUI'","ordre",0,0,false);

    if (empty($lignes)) $lignes=get_champs_recherche_ldap_defaut();
     if (!$liste)
        return $lignes;
     else {
        $champs_LDAP="";
        foreach($lignes as $ligne)
             $champs_LDAP .= $ligne->nom_champ.":".$ligne->champ_LDAP.", ";
            //rev 1.5 enleve la virgule de fin
        if ($champs_LDAP) $champs_LDAP = substr($champs_LDAP, 0, -2);
        return $champs_LDAP;
     }
 }

function get_champs_recherche_ldap_defaut() {
    global $USER;
    $ret=array();
    $ligne=new StdClass();
    $ligne->id_etab=$USER->id_etab_perso;
    $ligne->champ_LDAP="sn";
    $ligne->nom_champ="nom";
    $ligne->ordre=0;
    $ret[]=$ligne;
    $ligne2=new StdClass();
    $ligne2->id_etab=$USER->id_etab_perso;
    $ligne2->champ_LDAP="givenName";
    $ligne2->nom_champ="prenom";
    $ligne2->ordre=1;
    $ret[]=$ligne2;
    return $ret;
}

function get_champs_synchronisables() {
    global $CFG;
    return explode (",",$CFG->champs_synchro_ldap);
}




/**
 * set $CFG-values for ldap_module
 *
 * Get default configuration values with auth_ldap_getdefaults()
 * and by using this information $CFG-> values are set
 * If $CFG->value is alredy set current value is honored.
 *
 *Version retouch�e PP pour gerer les serveurs LDAP par composantes !
 *
 */
function auth_ldap_init ($ide=false) {
    global $CFG,$USER;
     if (!$ide) $ide=$USER->id_etab_perso;
    if (isset($CFG->auth_ldap_init) && $ide==$USER->id_etab_perso) return true; //deja fait pour cet etablissement

    if (!$et=get_etablissement ($ide,false))
       return false;  //etab inconnu ?

    if (! $et->param_ldap || ! $et->base_ldap) return false; // laisser tomber
//print("ali");
//print_r($et);
    //on place dans CFG les parametres requis par le code "moodle" ci-dessous
    $CFG->ldap_host_url=$et->param_ldap; //serveur
    $CFG->ldap_bind_dn=$et->rdn_ldap;
    $CFG->ldap_bind_pw=$et->passe_ldap;
    $CFG->ldap_version=$et->ldap_version;
    $CFG->ldap_user_attribute=$et->ldap_login_attribute;
    $CFG->ldap_contexts=$et->base_ldap;
    $CFG->ldap_memberattribute=$et->ldap_group_attribute;
    $CFG->ldap_groupclass=$et->ldap_group_class;

    $CFG->field_map_prenom=$et->ldap_prenom_attribute;
    $CFG->field_map_nom=$et->ldap_nom_attribute;
    $CFG->field_map_numetudiant=$et->ldap_id_attribute;
    $CFG->field_map_email=$et->ldap_mail_attribute;

    $CFG->ldap_search_sub=1;
    //$CFG->unicodedb= $CFG->encodage =="UTF-8";

    $CFG->ldap_longueur_mini=2;  //tempo
    $CFG->cas_enabled=0;
    $CFG->cas=0;

   // rev 1012
   //if (empty($CFG->ldap_user_type)) $CFG->ldap_user_type="rfc2307bis";
   $CFG->ldap_user_type=$et->ldap_user_type;


    // defauts supplementaires
    $default = auth_ldap_getdefaults();

    foreach ($default as $key => $value) {
        //set defaults if overriding fields not set
        if(empty($CFG->{$key})) {
            if (!empty($CFG->ldap_user_type) && !empty($default[$key][$CFG->ldap_user_type])) {
                $CFG->{$key} = $default[$key][$CFG->ldap_user_type];
            }else {
                //use default value if user_type not set
                if(!empty($default[$key]['default'])){
                    $CFG->$key = $default[$key]['default'];
                }else {
                    unset($CFG->$key);
                }
            }
        }
    }
    //hack prefix to objectclass
    if (empty($CFG->ldap_objectclass)) {        // Can't send empty filter
        $CFG->ldap_objectclass = 'objectClass=*';
    } else if (stripos($CFG->ldap_objectclass, 'objectClass=') !== 0) {
        $CFG->ldap_objectclass = 'objectClass='.$CFG->ldap_objectclass;
    }
    $CFG->auth_ldap_init=1; // ne pas refaire ...
if ($CFG->debug_ldap_groupes) print_object("config utilis�e", $CFG);
    return true;
}



/**
 * retuns user attribute mappings between moodle and ldap
 * @return array
 */

function auth_ldap_attributes (){
    global $CFG;

    $config = (array)$CFG;
    $fields = array("prenom", "nom", "email", "numetudiant" ); //pf C2I

    //$pcfg = get_config('auth/ldap');

    $moodleattributes = array();
    foreach ($fields as $field) {
        if (!empty($CFG->{"field_map_$field"})) {
            $moodleattributes[$field] = $CFG->{"field_map_$field"};
            if (preg_match('/,/',$moodleattributes[$field])) {
                $moodleattributes[$field] = explode(',', $moodleattributes[$field]);
            }
        }
    }
    $moodleattributes['login']=$config["ldap_user_attribute"];
    // ajout PP pour forcer sa lecture ...
    //$moodleattributes['eduPersonAffiliation']= 'eduPersonAffiliation';
    return $moodleattributes;
}



////////////////////////////////////////////// partie inchnag�e du code Moodle

/**
 * connects to ldap server
 *
 * Tries connect to specified ldap servers.
 * Returns connection result or error.
 *
 * @return connection result
 */
function auth_ldap_connect($binddn='',$bindpwd='',$die=true){
/// connects  and binds to ldap-server
/// Returns connection result

    global $CFG;
    auth_ldap_init();

    //Select bind password, With empty values use
    //ldap_bind_* variables or anonymous bind if ldap_bind_* are empty
    if ($binddn == '' AND $bindpwd == '') {
        if (!empty($CFG->ldap_bind_dn)){
           $binddn = $CFG->ldap_bind_dn;
        }
        if (!empty($CFG->ldap_bind_pw)){
           $bindpwd = $CFG->ldap_bind_pw;
        }
    }

    $urls = explode(";",$CFG->ldap_host_url);

    foreach ($urls as $server){
        $server = trim($server);
        if (empty($server)) {
            continue;
        }

        $connresult = ldap_connect($server);
        //ldap_connect returns ALWAYS true

        if (!empty($CFG->ldap_version)) {
            ldap_set_option($connresult, LDAP_OPT_PROTOCOL_VERSION, $CFG->ldap_version);
        }
        // Fix MDL-10921
        if ($CFG->ldap_user_type =='ad') {
             ldap_set_option($connresult, LDAP_OPT_REFERRALS, 0);
        }
        if (!empty($binddn)){
            //bind with search-user
            //$debuginfo .= 'Using bind user'.$binddn.'and password:'.$bindpwd;
            $bindresult=ldap_bind($connresult, $binddn,$bindpwd);
        } else {
            //bind anonymously
            $bindresult=@ldap_bind($connresult);
        }

        if (!empty($CFG->ldap_opt_deref)) {
            ldap_set_option($connresult, LDAP_OPT_DEREF, $CFG->ldap_opt_deref);
        }
        ldap_set_option($connresult, LDAP_OPT_SIZELIMIT, 9999); //PP Marche pas

        if ($bindresult) {
            return $connresult;
        }

       // $debuginfo .= "<br/>Server: '$server' <br/> Connection: '$connresult'<br/> Bind result: '$bindresult'</br>";
    }

    //If any of servers are alive we have already returned connection
    if ($die) erreur_fatale("err_connexion_ldap","");
    else return false;
}

/**
 * code recup�r� de la V1.4 a revoir
 * en principe on devrait avoir le login dans la cl� $membre sans retourner dans le ldap
 * c'est ce qui a �t� fait
 * on ne prend que 'uid' ou ce qui a �t� mis dans la config !
 *
 *TODO dans certains endroits le groupe LDAP contient des lignes du genre
 *  member=CN=nom prenom numero,ou=xxxx,dc=zzzz ...
 * c'est a dire que l'idenfiant est un DN (g�r� ici) ET que sa valeur n'est pas celle du login
 * il faut le gerer !!!
 *
 * TODO gerer les groupes de plus de 1000 avec ActiveDirectory
 * voir
 *
 */
function get_group_members_rfc ($groupe) {
	global $CFG;


// $CFG->debug_ldap_groupes=1;
	$ret=array();
	$ldapconnection = auth_ldap_connect();  //a fait l'init voulu
    if ($CFG->debug_ldap_groupes) print_object("connexion ldap: ",$ldapconnection);
	if (! $ldapconnection) return $ret;

	$queryg = "(&(cn=".trim($groupe).")(objectClass=$CFG->ldap_groupclass))";
    if ($CFG->debug_ldap_groupes) print_object("queryg: ",$queryg);

	//attention 1 seul pour l'instant pas de liste avec point virgule ...
	$resultg=ldap_search($ldapconnection, $CFG->ldap_contexts, $queryg);
   // if ($CFG->debug_ldap_groupes) print_object("resultg: ",$resultg);

	if (!empty($resultg) AND ldap_count_entries($ldapconnection, $resultg)) {
		$groupe = ldap_get_entries($ldapconnection, $resultg);
       if ($CFG->debug_ldap_groupes) print_object("groupe: ",$groupe);

		//todo tester existence du groupe !!!
		for ($g=0 ; $g < ( sizeof($groupe[0][$CFG->ldap_memberattribute]) - 1) ; $g++){

			$membre = trim($groupe[0][$CFG->ldap_memberattribute][$g]);
			if ($membre !=""){//*3
                if ($CFG->debug_ldap_groupes) print_object("membre : ",$membre);

				// la cle count contient le nombre de membres
				// 3 cas : 1 - membre est de la forme  cn=xxxxxx, ou =zzzz ....
				//         2 - membre est de la forme uid=login,ou=people,dc=insa_lyon,dc=fr
				//         3 - membre est simplement un login
				// r�cup�ration de la cha�ne membre
				// v�rification du format
				$membre_tmp1 = explode (",",$membre);
				if (count($membre_tmp1) >1) {
					// normalement le premier �l�ment est soir cn=..., soit uid=...

                    if ($CFG->debug_ldap_groupes) print_object("membre_tpl1: ",$membre_tmp1);
                    //essaie de virer la suite
					$membre_tmp2 = explode ("=", trim($membre_tmp1[0]));
                    if ($CFG->debug_ldap_groupes) print_object("membre_tpl2: ",$membre_tmp2);
                    //pas le peine d'aller dans le ldap si c'est bon
					if ($membre_tmp2[0] == $CFG->ldap_user_attribute)  //celui de la config
						$ret[]=$membre_tmp2[1];
                    else {
                      //intervenir ici !!!
                      if ($CFG->debug_ldap_groupes) print_object("attribut trouv� different de ",$CFG->ldap_user_attribute);
                      // rev 1012 Lyon1 (AD)
                      if ($CFG->ldap_memberattribute_isdn) {
                        // allez chercher son "login" (uid)
                        if ($cpt=ldap_get_compte_bydn($membre_tmp2[0],$membre_tmp2[1]))
                           $ret[]=$cpt->login;
                      }
                    }

				}else
					$ret[]=$membre;

			}
		}
	}
    if ($CFG->debug_ldap_groupes) print_object("retour get_g_m ",$ret);
    ldap_close($ldapconnection);
	return $ret;
}


/**
 * recherche pagin�e voir http://forums.sun.com/thread.jspa?threadID=578347
 */

function get_group_members_ad ($groupe) {
	global $CFG;


	// $CFG->debug_ldap_groupes=1;
	$ret=array();
	$ldapconnection = auth_ldap_connect();  //a fait l'init voulu
	if ($CFG->debug_ldap_groupes) print_object("connexion ldap: ",$ldapconnection);
	if (! $ldapconnection) return $ret;

	$queryg = "(&(cn=".trim($groupe).")(objectClass=$CFG->ldap_groupclass))";
	if ($CFG->debug_ldap_groupes) print_object("queryg: ",$queryg);

    $size=999;
	$start=0;
	$end=$size;
	$fini=false;

	while (! $fini) {
		//recherche pagin�e par paquet de 1000
		$attribut=$CFG->ldap_memberattribute.";range=".$start.'-'.$end;
		$resultg=ldap_search($ldapconnection, $CFG->ldap_contexts, $queryg,array($attribut));
		// if ($CFG->debug_ldap_groupes) print_object("resultg: ",$resultg);

		if (!empty($resultg) AND ldap_count_entries($ldapconnection, $resultg)) {
			$groupe = ldap_get_entries($ldapconnection, $resultg);
			if ($CFG->debug_ldap_groupes) print_object("groupe: ",$groupe);

            // a la derniere passe, AD renvoie member;Range=numero-* !!!
			if (empty($groupe[0][$attribut])) {
                $attribut= $CFG->ldap_memberattribute.";range=".$start.'-*';
                $fini=true;
            }

			for ($g=0 ; $g < ( sizeof($groupe[0][$attribut]) - 1) ; $g++){

				$membre = trim($groupe[0][$attribut][$g]);
				if ($membre !=""){//*3
					if ($CFG->debug_ldap_groupes) print_object("membre : ",$membre);


					$membre_tmp1 = explode (",",$membre);
					if (count($membre_tmp1) >1) {
						// normalement le premier �l�ment est soir cn=..., soit uid=...

						if ($CFG->debug_ldap_groupes) print_object("membre_tpl1: ",$membre_tmp1);
						//essaie de virer la suite
						$membre_tmp2 = explode ("=", trim($membre_tmp1[0]));
						if ($CFG->debug_ldap_groupes) print_object("membre_tpl2: ",$membre_tmp2);
						//pas le peine d'aller dans le ldap si c'est bon
						if ($membre_tmp2[0] == $CFG->ldap_user_attribute)  //celui de la config
							$ret[]=$membre_tmp2[1];
						else {
							//intervenir ici !!!
							if ($CFG->debug_ldap_groupes) print_object("attribut trouv� different de ",$CFG->ldap_user_attribute);
							// rev 1012 Lyon1 (AD)
							if ($CFG->ldap_memberattribute_isdn) {
								// allez chercher son "login" (uid)
								if ($cpt=ldap_get_compte_bydn($membre_tmp2[0],$membre_tmp2[1]))
									$ret[]=$cpt->login;
							}
						}

					}else
						$ret[]=$membre;

				}
			}
		} else $fini=true;
        $start =$start+$size;
        $end =$end+$size;

	}
	if ($CFG->debug_ldap_groupes) print_object("retour get_g_m ",$ret);
    ldap_close($ldapconnection);
	return $ret;
}


/**
 * rev 1012 traitement de l'execption avec active directory pour des groupes >1000 membres
 * voir http://forums.sun.com/thread.jspa?threadID=578347
 */
function get_group_members ($groupe) {
    global $CFG;

    // rev 1013  pb avec les caract�res unicodes qui ont �t� convertis en iso
    // lors de la r�cup�ration des noms de groupes (pb avec Lyon1)
      if (empty($CFG->unicodedb))
          $groupe=utf8_encode($groupe);

     if ($CFG->ldap_user_type=="ad")
        return get_group_members_ad($groupe);
     else
        return get_group_members_rfc($groupe);
}





?><?PHP
//partie ldap de moodle debarass�e de la partie ecriture vers le ldap et des synchros ldap/moodle
/**
 *
 * @author Petri Asikainen
 * @version $Id: lib_ldap.php 1258 2011-06-03 14:16:50Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodleauth

 * LDAPA-authentication functions
 *
 * 30.09.2004 Removed outdated documentation
 * 24.09.2004 Lot of changes:
 *           -Added usertype configuration, this removes need for separate obejcclass and attributename configuration
 *            Overriding values is still supported
 *
 * 21.09.2004 Added support for multiple ldap-servers.
 *          Theres no nedd to use auth_ldap_bind,
 *          Anymore auth_ldap_connect does this for you
 * 19.09.2004 Lot of changes are coming from Martin Langhoff
 *          Current code is working but can change a lot. Be warned...
 * 15.08.2004 Added support for user syncronization
 * 24.02.2003 Added support for coursecreators
 * 20.02.2003 Added support for user creation
 * 12.10.2002 Reformatted source for consistency
 * 03.10.2002 First version to CVS
 * 29.09.2002 Clean up and splitted code to functions v. 0.02
 * 29.09.2002 LDAP authentication functions v. 0.01
 */

/**
 * authenticates user againt external userdatabase
 *
 * Returns true if the username and password work
 * and false if they don't
 *
 * @param string  $username
 * @param string  $password
 *
*/

// LDAP functions are reused by other auth libs
if (!defined('AUTH_LDAP_NAME')) {
    define('AUTH_LDAP_NAME', 'ldap');
}

function auth_user_login ($username, $password) {

    global $CFG;

    if (!$username or !$password) {    // Don't allow blank usernames or passwords
        return false;
    }

    // CAS-supplied auth tokens override LDAP auth
    /**
    if ($CFG->auth == "cas" && !empty($CFG->cas_enabled)) {
        return cas_ldap_auth_user_login($username, $password);
    }
    **/
    $ldapconnection = auth_ldap_connect();
    if ($ldapconnection) {
        $ldap_user_dn = auth_ldap_find_userdn($ldapconnection, $username);
        //if ldap_user_dn is empty, user does not exist
        if(!$ldap_user_dn){
            ldap_close($ldapconnection);
            return false;
        }
        // Try to bind with current username and password
        $ldap_login = @ldap_bind($ldapconnection, $ldap_user_dn, stripslashes($password));
        ldap_close($ldapconnection);
        if ($ldap_login) {
            return true;
        }
    } else {
        //@ldap_close($ldapconnection);
        error("LDAP-module cannot connect to server: $CFG->ldap_host_url");
    }
    return false;
}

/**
 * reads userinformation from ldap and return it in array()
 *
 * Read user information from external database and returns it as array().
 * Function should return all information available. If you are saving
 * this information to moodle user-table you should honor syncronization flags
 *
 * @param string $username username
 * @param boolean getMulti (PP) return multivalued attrs as a CSV string
 * @return array
 */
function auth_get_userinfo($username,$getMulti=false){
    global $CFG;
    if (!$ldapconnection=auth_ldap_connect()) return false;  //rev 936
   // $config = (array)$CFG;
    $attrmap = auth_ldap_attributes();

    $result = array();
    $search_attribs = array();

    foreach ($attrmap as $key=>$values) {
        if (!is_array($values)) {
            $values = array($values);
        }
        foreach ($values as $value) {
            if (!in_array($value, $search_attribs)) {
                array_push($search_attribs, $value);
            }
        }
    }

    $user_dn = auth_ldap_find_userdn($ldapconnection, $username);
    if (!$user_dn) {
         @ldap_close($ldapconnection); //rev 978
         return false; // rev 936 pas trouv�
    }
    if (empty($CFG->ldap_objectclass)) {        // Can't send empty filter
        $CFG->ldap_objectclass="objectClass=*";
    }

    $user_info_result = ldap_read($ldapconnection,$user_dn,$CFG->ldap_objectclass, $search_attribs);
   // print_r($user_info_result);
    if ($user_info_result) {
        $user_entry = ldap_get_entries($ldapconnection, $user_info_result);
        //print_r($user_entry);
        foreach ($attrmap as $key=>$values){
            if (!is_array($values)) {
                $values = array($values);
            }
            $ldapval = NULL;
            foreach ($values as $value) {
                // rev 798 valeur non retourn�e dans le LDAP ignorer
                // cas de l'INSA ou l'attribut supannetuid n'est pas defini pour tous ...
                if (empty($user_entry[0][strtolower($value)]))
                    continue;
                if(is_array($user_entry[0][strtolower($value)])) {
		  // PP multivalu<E9>e donc retourne la derniere ?
                        if (! $getMulti) {  //traitement normal Moodle
                    		if (!empty($CFG->unicodedb)) {
                        		$newval = addslashes(stripslashes($user_entry[0][strtolower($value)][0]));
                    		} else {
                        		$newval = addslashes(stripslashes(utf8_decode($user_entry[0][strtolower($value)][0])));
                    		}
                         }
                         else { //traitement special PP
                                $cnt=$user_entry[0][strtolower($value)]['count'];
                                $newval=$user_entry[0][strtolower($value)][0];
                                if ($cnt >1)  // plus d'une liste : s<E9>par<E9>e par virgule ...
                                        for ($i=1;$i<$cnt;$i++) $newval.=",".$user_entry[0][strtolower($value)][$i];
                                 if (!empty($CFG->unicodedb)) {
                                        $newval = addslashes(stripslashes($newval));
                                 } else {
                                        $newval =addslashes(stripslashes(utf8_decode($newval)));
                                }
                        }
                 }
                 else {
                    if (!empty($CFG->unicodedb)) {
                        $newval = addslashes(stripslashes($user_entry[0][strtolower($value)]));
                    } else {
                        $newval = addslashes(stripslashes(utf8_decode($user_entry[0][strtolower($value)])));
                    }
                }
                if (!empty($newval)) { // favour ldap entries that are set
                    $ldapval = $newval;
                }
            }
            if (!is_null($ldapval)) {
            	if ($key=='numetudiant'){
			$prefixe=explode(",",$CFG->numetudiant_prefixes_ldap);
            		$ldapval=str_replace($prefixe,"",$ldapval);
            	}
                $result[$key] = $ldapval;
            }
        }
    }

    @ldap_close($ldapconnection);

    return $result;
}

/**
 * reads userinformation from ldap and return it in an object
 *
 * @param string $username username
 * @param boolean getMulti (PP) return multivalued attrs as an array
 * @return array
 */
function auth_get_userinfo_asobj($username,$getMulti=false){
	//$user_array = truncate_userinfo(auth_get_userinfo($username,$getMulti));
	if ($user_array =auth_get_userinfo($username,$getMulti)) { //rev 936
		$user = new stdClass();
		foreach($user_array as $key=>$value){
			$user->{$key} = $value;
		}
		return $user;
	}
	return false;
}

/**
 * returns all usernames from external database
 * auth_get_userlist returns all usernames from external database
 * @return array
 */
function auth_get_userlist () {
    global $CFG;
    auth_ldap_init();
    return auth_ldap_get_userlist("($CFG->ldap_user_attribute=*)");
}

/**
 * checks if user exists on external db
 */
function auth_user_exists ($username) {
   global $CFG;
   auth_ldap_init();
   //returns true if given usernname exist on ldap
   $users = auth_ldap_get_userlist("($CFG->ldap_user_attribute=$username)");
   return count($users);
}


/*/
 *
 * auth_get_users() returns userobjects from external database
 *
 * Function returns users from external databe as Moodle userobjects
 * If filter is not present it should return ALL users in external database
 * rev 978 pas de warning LDAP en cas de depassement de la limite ....
 * @param mixed $filter substring of username
 * @returns array of userobjects
 */
function auth_get_users($filter='*', $limit=100) {
    global $CFG;


    $fresult = array();
    if (!$ldapconnection = auth_ldap_connect())
        return $fresult;

    if ($filter=="*") {
       $filter = "(&(".$CFG->ldap_user_attribute."=*)(".$CFG->ldap_objectclass."))";
    }

    $contexts = explode(";",$CFG->ldap_contexts);
    /*

    if (!empty($CFG->ldap_create_context) and empty($dontlistcreated)){
          array_push($contexts, $CFG->ldap_create_context);
    }
    */
    $attrmap = auth_ldap_attributes();

    $search_attribs = array();

    foreach ($attrmap as $key=>$values) {
        if (!is_array($values)) {
            $values = array($values);
        }
        foreach ($values as $value) {
            if (!in_array($value, $search_attribs)) {
                array_push($search_attribs, $value);
            }
        }
    }
    /*
    print_r($filter);
    print_r($search_attribs);
    print_r($attrmap);
    */
    foreach ($contexts as $context) {
        $context = trim($context);
        if (empty($context)) {
            continue;
        }

        if ($CFG->ldap_search_sub) {
           // print ($filter);
            //use ldap_search to find first user from subtree
            $ldap_result = @ldap_search($ldapconnection, $context,
                                       $filter,
                                       $search_attribs,0,$limit);
        } else {
            //search only in this context
            $ldap_result = @ldap_list($ldapconnection, $context,
                                     $filter,
                                     $search_attribs,0,$limit);
        }

        $users = auth_ldap_get_entries($ldapconnection, $ldap_result);
        //print_r($users);
        //add found users to list  au format C2I pas ldap !!!!
        foreach ($users as $ldapuser=>$attribs) {
            $user = new stdClass();
            foreach ($attrmap as $key=>$value){
                if(isset($users[$ldapuser][$value][0])){
                    $user->$key=$users[$ldapuser][$value][0];
                }
            }
            //quick way to get around binarystrings
           // $user->guid=bin2hex($user->guid);
            //add authentication source stamp
            $user->auth = AUTH_LDAP_NAME;
            $fresult[]=$user;
        }
    }
    ldap_close($ldapconnection);
    return $fresult;
}



//PRIVATE FUNCTIONS starts
//private functions are named as auth_ldap*

/**
 * returns predefined usertypes
 *
 * @return array of predefined usertypes
 */

function auth_ldap_suppported_usertypes (){
// returns array of supported usertypes (schemas)
// If you like to add our own please name and describe it here
// And then add case clauses in relevant places in functions
// iauth_ldap_init, auth_user_create, auth_check_expire, auth_check_grace
    $types['edir']='Novell Edirectory';
    $types['rfc2307']='posixAccount (rfc2307)';
    $types['rfc2307bis']='posixAccount (rfc2307bis)';
    $types['samba']='sambaSamAccount (v.3.0.7)';
    $types['ad']='MS ActiveDirectory';
    return $types;
}

/**
 * initializes needed variables for ldap-module
 *
 * Uses names defined in auth_ldap_supported_usertypes.
 * $default is first defined as:
 * $default['pseudoname'] = array(
 *                      'typename1' => 'value',
 *                      'typename2' => 'value'
 *                      ....
 *                      );
 *
 * @return array of default values
 */

function auth_ldap_getdefaults(){
    $default['ldap_objectclass'] = array(
                        'edir' => 'User',
                        'rfc2703' => 'posixAccount',
                        'rfc2703bis' => 'posixAccount',
                        'samba' => 'sambaSamAccount',
                        'ad' => 'user',
                        'default' => '*'
                        );
    $default['ldap_user_attribute'] = array(
                        'edir' => 'cn',
                        'rfc2307' => 'uid',
                        'rfc2307bis' => 'uid',
                        'samba' => 'uid',
                        'ad' => 'cn',
                        'default' => 'cn'
                        );
    $default['ldap_memberattribute'] = array(
                        'edir' => 'member',
                        'rfc2307' => 'member',
                        'rfc2307bis' => 'member',
                        'samba' => 'member',
                        'ad' => 'member',
                        'default' => 'member'
                        );
    $default['ldap_memberattribute_isdn'] = array(
                        'edir' => '1',
                        'rfc2307' => '0',
                        'rfc2307bis' => '1',
                        'samba' => '0', //is this right?
                        'ad' => '1',
                        'default' => '0'
                        );
    $default['ldap_expireattr'] = array (
                        'edir' => 'passwordExpirationTime',
                        'rfc2307' => 'shadowExpire',
                        'rfc2307bis' => 'shadowExpire',
                        'samba' => '', //No support yet
                        'ad' => '', //No support yet
                        'default' => ''
                        );
    $default['ldap_groupclass'] = array(
                        'edir' => 'groupOfNames',
                        'rfc2307' => 'groupOfNames',
                        'rfc2307bis' => 'groupOfNames',
                        'samba' => 'groupOfNames',
                        'ad' => 'groupOfNames',
                        'default' => 'groupOfNames'
                        );
    return $default;
}

/**
 * return binaryfields of selected usertype
 *
 *
 * @return array
 */

function auth_ldap_getbinaryfields () {
    global $CFG;
    $binaryfields = array (
                        'edir' => array('guid'),
                        'rfc2703' => array(),
                        'rfc2703bis' => array(),
                        'samba' => array(),
                        'ad' => array(),
                        'default' => '*'
                        );
    if (!empty($CFG->ldap_user_type)) {
        return $binaryfields[$CFG->ldap_user_type];
    } else {
        return $binaryfields['default'];
    }
    }

function auth_ldap_isbinary ($field) {
    if (!isset($field)) {
        return null ;
    }
    return array_search($field, auth_ldap_getbinaryfields());
}


/*
 * checks if user belong to specific group(s)
 *
 * Returns true if user belongs group in grupdns string.
 *
 * @param mixed $username    username
 * @param mixed $groupdns    string of group dn separated by ;
 *
 */
function auth_ldap_isgroupmember ($username='', $groupdns='') {
// Takes username and groupdn(s) , separated by ;
// Returns true if user is member of any given groups

    global $CFG ;
    $result = false;
   if (!$ldapconnection = auth_ldap_connect())
    return false;

    if (empty($username) OR empty($groupdns)) {
        return $result;
        }

    if ($CFG->ldap_memberattribute_isdn) {
        $username=auth_ldap_find_userdn($ldapconnection, $username);
    }
    if (! $username ) {
        return $result;
    }

    $groups = explode(";",$groupdns);

    foreach ($groups as $group){
        $group = trim($group);
        if (empty($group)) {
            continue;
        }
        //echo "Checking group $group for member $username\n";
        $search = @ldap_read($ldapconnection, $group,  '('.$CFG->ldap_memberattribute.'='.$username.')',
            array($CFG->ldap_memberattribute));

        if (!empty($search) AND ldap_count_entries($ldapconnection, $search)) {
            $info = auth_ldap_get_entries($ldapconnection, $search);

            if (count($info) > 0 ) {
                // user is member of group
                $result = true;
                break;
            }
    }
}

    ldap_close($ldapconnection);
    return $result;
}


/**
 * retuns dn of username
 *
 * Search specified contexts for username and return user dn
 * like: cn=username,ou=suborg,o=org
 *
 * @param mixed $ldapconnection  $ldapconnection result
 * @param mixed $username username
 *
 */

function auth_ldap_find_userdn ($ldapconnection, $username){

    global $CFG;

    //default return value
    $ldap_user_dn = FALSE;

    //get all contexts and look for first matching user
    $ldap_contexts = explode(";",$CFG->ldap_contexts);

    if (!empty($CFG->ldap_create_context)){
      array_push($ldap_contexts, $CFG->ldap_create_context);
    }

    foreach ($ldap_contexts as $context) {

        $context = trim($context);
        if (empty($context)) {
            continue;
        }

        if ($CFG->ldap_search_sub){
            //use ldap_search to find first user from subtree
            $ldap_result = ldap_search($ldapconnection, $context, "(".$CFG->ldap_user_attribute."=".$username.")",array($CFG->ldap_user_attribute));

        } else {
            //search only in this context
            $ldap_result = ldap_list($ldapconnection, $context, "(".$CFG->ldap_user_attribute."=".$username.")",array($CFG->ldap_user_attribute));
        }

        $entry = ldap_first_entry($ldapconnection,$ldap_result);

        if ($entry){
            $ldap_user_dn = ldap_get_dn($ldapconnection, $entry);
            break ;
        }
    }

    return $ldap_user_dn;
}


/**
 * return all usernames from ldap
 *
 * @return array
 */

function auth_ldap_get_userlist($filter="*") {
/// returns all users from ldap servers
    global $CFG;

    $fresult = array();

    $ldapconnection = auth_ldap_connect();

    if ($filter=="*") {
       $filter = "(&(".$CFG->ldap_user_attribute."=*)(".$CFG->ldap_objectclass."))";
    }

    $contexts = explode(";",$CFG->ldap_contexts);

    if (!empty($CFG->ldap_create_context)){
          array_push($contexts, $CFG->ldap_create_context);
    }

    foreach ($contexts as $context) {

        $context = trim($context);
        if (empty($context)) {
            continue;
        }

        if ($CFG->ldap_search_sub) {
            //use ldap_search to find first user from subtree
            $ldap_result = ldap_search($ldapconnection, $context,$filter,array($CFG->ldap_user_attribute));
        } else {
            //search only in this context
            $ldap_result = ldap_list($ldapconnection, $context,
                                     $filter,
                                     array($CFG->ldap_user_attribute));
        }

        $users = auth_ldap_get_entries($ldapconnection, $ldap_result);

        //add found users to list
        for ($i=0;$i<count($users);$i++) {
            array_push($fresult, ($users[$i][$CFG->ldap_user_attribute][0]) );
        }
    }
     ldap_close($ldapconnection);
    return $fresult;
}

function auth_ldap_get_grouplist($filter="*") {
/// returns all groups from ldap servers
    global $CFG;

    $fresult = array();

    $ldapconnection = auth_ldap_connect();

    $CFG->ldap_group_attribute="cn";

    if ($filter=="*") {
       $filter = "(&(".$CFG->ldap_group_attribute."=*)(objectclass=".$CFG->ldap_groupclass."))";
    }

    $contexts = explode(";",$CFG->ldap_contexts);

    foreach ($contexts as $context) {
        $context = trim($context);
        if (empty($context)) {
            continue;
        }

        if ($CFG->ldap_search_sub) {
            //use ldap_search to find first user from subtree
            $ldap_result = ldap_search($ldapconnection, $context,$filter,array($CFG->ldap_group_attribute));
        } else {
            //search only in this context
            $ldap_result = ldap_list($ldapconnection, $context,
                                     $filter,
                                     array($CFG->ldap_group_attribute));
        }

        $groups = auth_ldap_get_entries($ldapconnection, $ldap_result);

        //add found users to list
        for ($i=0;$i<count($groups);$i++) {
         if (!empty($CFG->unicodedb))
            array_push($fresult, ($groups[$i][$CFG->ldap_group_attribute][0]) );
         else
           array_push($fresult, utf8_decode($groups[$i][$CFG->ldap_group_attribute][0]) );
        }
    }
    ldap_close($ldapconnection);
    return $fresult;
}


/**
 * return entries from ldap
 *
 * Returns values like ldap_get_entries but is
 * binary compatible and return all attributes as array
 *
 * @return array ldap-entries
 */

function auth_ldap_get_entries($conn, $searchresult){
//Returns values like ldap_get_entries but is
//binary compatible
    $i=0;
    $fresult=array();
    $entry = ldap_first_entry($conn, $searchresult);
    do {
        $attributes = @ldap_get_attributes($conn, $entry);
        for($j=0; $j<$attributes['count']; $j++) {
            $values = ldap_get_values_len($conn, $entry,$attributes[$j]);
            if (is_array($values)) {
            $fresult[$i][$attributes[$j]] = $values;
            } else {
                $fresult[$i][$attributes[$j]] = array($values);
            }
        }
        $i++;
    }
    while ($entry = @ldap_next_entry($conn, $entry));
    //were done
    return ($fresult);
}


/**
 * test recherche ldap par idnumber
 */
if (0) {
    auth_ldap_init(65);
    $res=ldap_get_compte_byidnumber("209313",65);
    print("ldap");
    print_r($res);
      $res=ldap_get_compte_byidnumber("2711518",65);
    print("ldap");
    print_r($res);
}

if (0) {
 $CFG->universite_serveur=35;
 $USER->id_etab_perso=35;
  auth_ldap_init(35);
 $CFG->debug_ldap_groupes=0;
 $CFG->ldap_user_type="ad";
// $ret=get_group_members('initiale-Z');
  $ret=get_group_members('1052 APO-UFR STAPS');
print_r($ret);
  die();

}

if (0) {
     $CFG->universite_serveur=35;
 $USER->id_etab_perso=35;
  auth_ldap_init(35);
   $res=auth_ldap_get_grouplist();
   print_r($res);

}

