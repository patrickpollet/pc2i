<?php
/**
 * @author Patrick Pollet
 * @version $Id: lib_tirages.php 1264 2011-09-19 17:43:05Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
 * divers algorithmes de tirage des questions dans les examens
 */


/**
 * renvoie un jeu de questions tir�es au hasard (sans respecter les r�gles d'equilibre referentiel et/ou famille'
 * c'�atit ainsi en V <1.5  .....
 * utilis�e pour un examen de type "tirage lors du passage")
 * TODO revoir ceci devrait �tre le m�me tirage que lors de la g�n�ration d'un examen al�atoire !'
 * sauf qu'il n'y a pas m�morisation en BD
 * rev 944
 *    on reintroduit les qcm par comp�tences (examen->referentielc2i non vide)
 *    on permet un nombre de questions dans la fiche de l'examen
 */


function tire_questions($id_examen,$id_etabexamen){
    global $USER,$CFG;

    $ligne=get_examen($id_examen,$id_etabexamen);

    //rev 944
    if (empty($ligne->nbquestions)) {
        //nombre de questions selon l'�tablissement '
        $nb=config_nb_aleatoire($id_etabexamen);
    }  else
        $nb=abs($ligne->nbquestions);

    $criteres= $USER->type_plateforme. " ='oui' and etat!=".QUESTION_REFUSEE;
    // rev 855 sur la nationale meme un examen de postionnement doit prendre les validées
    // rev 944 idem si validees forcées en positionnement
    if ($USER->type_plateforme == "certification" || $CFG->universite_serveur==1 || $CFG->seulement_validee_en_positionnement)
        $criteres = $USER->type_plateforme. " ='oui' and etat=".QUESTION_VALIDEE;

    // rev 944 referentiels concern�s
    if ($ligne->referentielc2i && $ligne->referentielc2i != -1) {
        $refs=explode(',',$ligne->referentielc2i);
    //reconstitue comme une liste SQL ex 'A1'  ou 'A1','A2'
       $refs_string = "'". implode("', '", $refs) ."'";
       $criteres .= ' and referentielc2i in ('.$refs_string.' )' ;
    }

    // rev 1079
    $criteres.= ' and  not est_filtree';

    //print $criteres;
    return get_records("questions",$criteres,"RAND()",0,$nb);
}


/**
 * tire aleatoirement les questions en respectant les crit�res actuels (familles)
 * et les affecte � l'examen
 */

function tirage_questions ($id,$id_etab) {
    global $CFG;
    
    switch ($CFG->algo_tirage){
    	
    	case 1 : __tirage_questions_V1($id,$id_etab); break;
    	case 2 : __tirage_questions_V2($id,$id_etab); break;
    	case 3 : __tirage_questions_V3($id,$id_etab); break;
    }	
   
}

/**
 * tirage avec equilibre par DOMAINE et evnetuellement la r�gle d'une question par famille
 * algorithme utilis� couramment.
 * Semble poser des probl�mes car
 * -  ne couvre pas l'ensemble des comp�tences
 * - certaines questions reviennent tr�s frequement ?
 */
//define ('DEBUG_TIRAGE',1);



function __tirage_questions_V1 ($id,$id_etab) {

    global $CFG,$USER;

    $typepf=$USER->type_plateforme;
    $ligne=get_examen($id,$id_etab);

    $est_pool=$ligne->est_pool;

 // nombre de questions al�atoires par qcm
    if ($est_pool == 1)
        // nombre de questions fix� par le pool
        $nbq_aleatoire = $ligne->nb_q_pool;
    else {
        //rev 944
    if (empty($ligne->nbquestions))
        // rev 944 pour l'�tablissement pas pour  le login !!!
        $nbq_aleatoire = config_nb_aleatoire($id_etab);
    else
        $nbq_aleatoire=abs($ligne->nbquestions);
    }
    /////////////////////////////////////////////////////
    // crit�res d'examen pour la s�lection de questions
    /////////////////////////////////////////////////////

    $criteres_examen = "not est_filtree ";  // rev 1079
    $criteres_examen .= " and etat!=".QUESTION_REFUSEE;

    //ignorer les questions sans referentiel/alinea
     $criteres_examen .= " and referentielc2i !='' and alinea !=0 ";

    $criteres_examen = "(" . $criteres_examen . ") and ";
    $validation = "";
    // rev 977  dans quel champ de la question doit-on regarder ?
    $nomref='referentielc2i';

   // rev 852 sur la nationale un positionnement n'est qu'avec des valid�es
   // rev 940 $CFG->seulement_validee_en_positionnement
    if ($typepf == "certification" || $CFG->universite_serveur==1 || $CFG->seulement_validee_en_positionnement)
        $validation = ' and etat='.QUESTION_VALIDEE ;

    // v�rification de questions d�j� existantes

    $questions=get_questions($id,$id_etab, false);
//    print_r($questions);


    // rev 944 si le nombre actuel de questions est > a celui demand�
    // c'est qu'on a r�duit le nombre de questions dans l'�cran de modification
    // donc tout retirer !

    if (count($questions) > $nbq_aleatoire) {
        // suppression des attachements de questions
         delete_records('questionsexamen','id_examen='.$id." and id_examen_etab=".$id_etab);
        $questions=array();
        //print("on recommence tout");
    }

    $liste_q = "1";

    foreach ($questions as $q)
        $liste_q .= " and (id_etab!=" . $q->id_etab . " or id!=" . $q->id . ")";



    if (count($questions) < $nbq_aleatoire) {
        $nbq_util = count($questions); // nombre de questions d�j� affect�es

        // r�partition dans les diff�rents r�f�rentiels
        // rev 944 on reintroduit les examens par comp�tences (positionnement seulement !)
        $refs=get_referentiels_liste($ligne->referentielc2i); //tri� par referentiel
//print_r($ligne);
//print_r($refs);
        $nb_lignes_r = count($refs); // nombres de referentiels restant � traiter

        $nbq = $nbq_aleatoire - $nbq_util; // nombre de questions restant � ajouter
        $nbqa = floor($nbq / $nb_lignes_r); // nombre de questions � choisir par r�f�rentiel

        foreach($refs as $ligner) {
            // essai d'ajout d'un argument � RAND(). ne change rien
            //$rnd=srand((double) microtime() * 10000000);
            $rnd='';
            if ($typepf == "certification") {
                if ($est_pool == 1) {
                    // doublon de famille possible pour avoir assez de question dans le pool
                    $chwherefamille = "and id_famille_validee!=0";
                } else {
                    // pas de doublon de famille
                    $chwherefamille = "and id_famille_validee!=0 group by id_famille_validee";
                }
                // tirage al�atoire avec au plus une question par famille d'un m�me r�f�rentiel
                $requete =<<<EOR
                 SELECT id_famille_validee , id, id_etab
                 FROM {$CFG->prefix}questions
                 WHERE $criteres_examen $typepf ='oui' $validation
                 and $nomref='$ligner->referentielc2i'
                 and ( $liste_q )
                 $chwherefamille
                order by RAND($rnd) limit 0,$nbqa
EOR;
            } else {
                // postionnement, tirage al�atoire peu importe la famille
                $requete =<<<EOR
                 SELECT id, id_etab
                 FROM {$CFG->prefix}questions
                 WHERE $criteres_examen $typepf ='oui'  $validation
                 and $nomref='$ligner->referentielc2i'
                 and ( $liste_q )
                 order by RAND($rnd) limit 0, $nbqa
EOR;
            }
            //print $requete;
            $resultat = get_records_sql($requete,false); //pas d'erreur fatale !!!
            if (count($resultat) < $nbqa){
                //que fait on si nous n'avons pas assez de questions ?
            }


            foreach($resultat as $ligne) {
                // ajout
                 ajoute_question_examen($ligne->id,$ligne->id_etab,$id,$id_etab);
                $liste_q .= " and (id_etab!=" . $ligne->id_etab . " or id!=" . $ligne->id . ")";
                $nbq_util++;
                $nbq--; //PP
            }

            if (defined('DEBUG_TIRAGE')) {
                echo "nbq : $nbq<br>\n";
                echo "nbqa : $nbqa<br>\n";
                echo "questions trouvées  : ".count($resultat)."<br>\n";
                echo "nbq_util : $nbq_util<br>\n";
                echo "requete : $requete<br>\n";
            }


        }// fin de boucle sur les referentiels

        // il manque des questions. actuellement on compl�te en positionnement seulement
        if (($nbq > 0) && ($typepf == "positionnement")) {
            $requete=<<<EOR
              SELECT id, id_etab
              FROM {$CFG->prefix}questions
              WHERE  $criteres_examen $typepf='oui'  $validation
              and ( $liste_q )
              order by RAND($rnd) limit 0,$nbq
EOR;
            $resultat = get_records_sql($requete);
            if (defined('DEBUG' )) {
                echo "nbq= $nbq<br>\n";
                echo "req : $requete<br>\n";
            }
            foreach($resultat as $ligne) {
                ajoute_question_examen($ligne->id,$ligne->id_etab,$id,$id_etab);
                //probablement inutile (liste_q=celle d�ja tir�e au d�but de ce tirage
                // comme on sort par referentiel, les noiuvelles tir�es ne pourront �tre en double
                $liste_q .= "and (id_etab!=" . $ligne->id_etab . " or id!=" . $ligne->id . ")";
            }
        }
    }
}






function change_question ($idq,$ide, $idex,$idexe) {
    global $CFG;
    switch ($CFG->algo_tirage) {
    	case 1 : return __change_question_V1($idq,$ide,$idex,$idexe);break;
    	case 2 : return __change_question_V2($idq,$ide,$idex,$idexe);break;
    	case 3 : return __change_question_V3($idq,$ide,$idex,$idexe);break;
    }
}

/**
 * remplace la question par une voisine dans le meme domaine
 * @param $idq  $idq question a changer
 * @param $idex  $idexe examen concern�
 * @return l'id de la nouvelle question
 */

function __change_question_V1 ($idq,$ide, $idex,$idexe) {

    global $CFG,$USER;

    $typepf=$USER->type_plateforme;

    // v�rification du r�f�rentiel de la question � retirer
    $ligne=get_question ($idq,$ide);
    
    $criteres_recherche = $typepf ."='OUI'";
    $criteres_recherche .= " and not est_filtree ";  // rev 1079
    $criteres_recherche .= " and etat!=".QUESTION_REFUSEE;
       
    
    // rev 855 sur la nationale meme un examen de postionnement doit prendre les valid�es
    if ($typepf == "certification" || $CFG->universite_serveur==1  || $CFG->seulement_validee_en_positionnement) {
        $criteres_recherche .= ' and etat='.QUESTION_VALIDEE ;
     // pas encore possible !
     // $criteres_recherche .=" and id_famille_validee=$ligne->id_famille_validee";
    }

    $liste_q = "1";
    // liste des questions d�j� s�lectionn�es a exclure (y compris celle que l'on veut virer)
    $questions=get_questions ($idex,$idexe,false,false);
    foreach($questions as $question)
            $liste_q .= " and (id_etab!=".$question->id_etab." or id!=".$question->id.")";

    // recherche d'une question disponible dans le m�me r�f�rentiel
    // rev 978  16/12/2010 attention au nouveau referentiel
    $critere_referentiel="referentielc2i='".$ligne->referentielc2i."'";

      $requete =<<<EOR
            SELECT id, id_etab
            FROM {$CFG->prefix}questions
            WHERE  $criteres_recherche
            and $critere_referentiel
            and ( $liste_q )
            order by RAND() limit 0,1
EOR;
      if (!$res=get_record_sql($requete,false)) {   //pas trouv� essaie autres referentiels
           $requete=<<<EOR
              SELECT id, id_etab
              FROM {$CFG->prefix}questions
              WHERE $criteres_recherche
              and ($liste_q)
              order by RAND() limit 0,1;
EOR;

              $res=get_record_sql($requete,false);
     }
     if ($res) {
           ajoute_question_examen($res->id,$res->id_etab,$idex,$idexe,false);
           $new_question = $res->id_etab.".".$res->id;
           supprime_question_examen($idq,$ide,$idex,$idexe,false);
           //tracking :
            espion2("relancer","tirage", $idexe.".".$idex);
           

     } else $new_question =false;  // il n'y en a pas d'autre disponible
   return $new_question;
}



/**
 * remplace la question par une voisine dans la meme competence
 * @param $idq  $idq question a changer
 * @param $idex  $idexe examen concern�
 * @return l'id de la nouvelle question
 */
function __change_question_V2 ($idq,$ide, $idex,$idexe) {

    global $CFG,$USER;

    $typepf=$USER->type_plateforme;

    // v�rification du r�f�rentiel de la question � retirer
    $ligne=get_question ($idq,$ide);

    $criteres_recherche = $typepf ."='OUI'";
    $criteres_recherche .= " and not est_filtree ";  // rev 1079
    $criteres_recherche .= " and etat!=".QUESTION_REFUSEE;
     
    
    // rev 855 sur la nationale meme un examen de postionnement doit prendre les valid�es
    if ($typepf == "certification" || $CFG->universite_serveur==1  || $CFG->seulement_validee_en_positionnement) {
        $criteres_recherche .= ' and etat='.QUESTION_VALIDEE ;
     // pas encore possible !
     // $criteres_recherche .=" and id_famille_validee=$ligne->id_famille_validee";
    }

    $liste_q = "1";
    // liste des questions d�j� s�lectionn�es a exclure (y compris celle que l'on veut virer)
    $questions=get_questions ($idex,$idexe,false,false);
    foreach($questions as $question)
            $liste_q .= " and (id_etab!=".$question->id_etab." or id!=".$question->id.")";

    // recherche d'une question disponible dans le m�me r�f�rentiel
    // rev 978  16/12/2010 attention au nouveau referentiel

     $critere_referentiel="referentielc2i='".$ligne->referentielc2i."'";
     $critere_competence="alinea='".$ligne->alinea."'";
      $requete =<<<EOR
            SELECT id, id_etab
            FROM {$CFG->prefix}questions
            WHERE  $criteres_recherche
            and $critere_referentiel
            and $critere_competence
            and ( $liste_q )
            order by RAND() limit 0,1
EOR;
      if (!$res=get_record_sql($requete,false)) {   //pas trouv� essaie  meme referentiel
           $requete=<<<EOR
              SELECT id, id_etab
              FROM {$CFG->prefix}questions
              WHERE $criteres_recherche
              and $critere_referentiel
              and ($liste_q)
              order by RAND() limit 0,1;
EOR;

              $res=get_record_sql($requete,false);
     }
     if ($res) {
           ajoute_question_examen($res->id,$res->id_etab,$idex,$idexe,false);
           $new_question = $res->id_etab.".".$res->id;
           supprime_question_examen($idq,$ide,$idex,$idexe,false);
           //tracking :
            espion2("relancer","tirage", $idexe.".".$idex);

     } else $new_question =false;  // il n'y en a pas d'autre disponible
   return $new_question;
}



/**
 * remplace la question par une voisine dans le meme th�me (ex famille)
 * @param $idq  $idq question a changer
 * @param $idex  $idexe examen concern�
 * @return l'id de la nouvelle question
 */
function __change_question_V3 ($idq,$ide, $idex,$idexe) {
	
	global $CFG,$USER;
	
	$typepf=$USER->type_plateforme;
	
	// v�rification du th�me de la question � retirer
	$ligne=get_question ($idq,$ide);
	
	$criteres_recherche = $typepf ."='OUI'";
	$criteres_recherche .= " and not est_filtree ";  // rev 1079
	$criteres_recherche .= " and etat!=".QUESTION_REFUSEE;
	 
	// rev 855 sur la nationale meme un examen de postionnement doit prendre les valid�es
	if ($typepf == "certification" || $CFG->universite_serveur==1  || $CFG->seulement_validee_en_positionnement) {
		$criteres_recherche .= ' and etat='.QUESTION_VALIDEE;
	}
	
	$liste_q = "1";
	// liste des questions d�j� s�lectionn�es a exclure (y compris celle que l'on veut virer)
	$questions=get_questions ($idex,$idexe,false,false);
	foreach($questions as $question)
	$liste_q .= " and (id_etab!=".$question->id_etab." or id!=".$question->id.")";
	
	// recherche d'une question disponible dans le m�me th�me
	$critere_famille="id_famille_validee=".$ligne->id_famille_validee;

	$requete =<<<EOR
		SELECT id, id_etab
		FROM {$CFG->prefix}questions
		WHERE  $criteres_recherche
		and $critere_famille
		and ( $liste_q )
		order by RAND() limit 0,1
EOR;

		if ($res=get_record_sql($requete,false)) {   //si pas trouv� tant pis
			ajoute_question_examen($res->id,$res->id_etab,$idex,$idexe,false);
			$new_question = $res->id_etab.".".$res->id;
			supprime_question_examen($idq,$ide,$idex,$idexe,false);
			//tracking :
			espion2("relancer","tirage", $idexe.".".$idex);
			//print($new_question);
			
		} else $new_question =false;  // il n'y en a pas d'autre disponible
		return $new_question;
}




/**
 * tirage avec equilibre par COMPETENCE et evnetuellement la r�gle d'une question par famille
 * algorithme experimental propos� par les experts en Fev. 2011
 */


/**
 * retourne un tableau de la forme
 * @param $ref le referenteil a traiter
 * @param $nbqa nombre de questions � trouver dans ce referenteil
 */
function cree_table_nbquestions_par_competence ($ref,$nbqa) {
    $competences = get_alineas($ref->referentielc2i);
    // melanger les comp�tences
    shuffle($competences);
    //print_object('', $competences);
    // table du nombre de questions PAR comp�tences
    $table = array ();
    $nb = 0;
    $i = 0;
    while ($nb < $nbqa) {
        $competence = $competences[$i];
        if (empty ($table[$competence->id_nat]))
            $table[$competence->id_nat] = 0;
        $table[$competence->id_nat]++;
        $nb++;
        $i++;
        if ($i == count($competences))
            $i = 0;
    }
    //print_object('', $table);
    /*
     * la table ressemble alors � ceci
     * comme les comeptences ont �t� m�lang�es, les nombres sont differents � chaque fois
     = Array
     (
     [D1.4] => 3
     [D1.1] => 3
     [D1.3] => 2
     [D1.2] => 2
     )
     = Array
     (
     [D2.2] => 3
     [D2.3] => 3
     [D2.1] => 2
     [D2.4] => 2
     )
     = Array
     (
     [D3.2] => 2
     [D3.4] => 2
     [D3.3] => 2
     [D3.5] => 2
     [D3.1] => 2
     )
     = Array
     (
     [D4.2] => 3
     [D4.3] => 3
     [D4.4] => 2
     [D4.1] => 2
     )
     = Array
     (
     [D5.1] => 4
     [D5.2] => 3
     [D5.3] => 3
     )
     */
    return $table;
}

/**
 * tirage avec equilibre par COMPETENCE et evnetuellement la r�gle d'une question par famille
 * algorithme utilis� couramment.
 * Semble poser des probl�mes car
 * - certaines comp�tences sont deficientes en question

 */
function __tirage_questions_V2 ($id,$id_etab) {

    global $CFG,$USER;
    $typepf = $USER->type_plateforme;

    $ligne = get_examen($id, $id_etab);

    $est_pool = $ligne->est_pool;

    // nombre de questions al�atoires par qcm
    if ($est_pool == 1)
        // nombre de questions fix� par le pool
        $nbq_aleatoire = $ligne->nb_q_pool;
    else {
        //rev 944
        if (empty ($ligne->nbquestions))
            // rev 944 pour l'�tablissement pas pour  le login !!!
            $nbq_aleatoire = config_nb_aleatoire($id_etab);
        else
            $nbq_aleatoire = abs($ligne->nbquestions);
    }

    /////////////////////////////////////////////////////
    // crit�res d'examen pour la s�lection de questions
    /////////////////////////////////////////////////////

    $criteres_examen = "not est_filtree "; // rev 1079
    $criteres_examen .= ' and etat!='.QUESTION_REFUSEE;

    //ignorer les questions sans referentiel/alinea
    $criteres_examen .= " and referentielc2i !='' and alinea !=0 ";


    $criteres_examen = "(" . $criteres_examen . ") and ";
    $validation = "";

    // rev 852 sur la nationale un positionnement n'est qu'avec des valid�es
    // rev 940 $CFG->seulement_validee_en_positionnement
    if ($typepf == "certification" || $CFG->universite_serveur == 1 || $CFG->seulement_validee_en_positionnement)
        $validation = ' and etat='.QUESTION_VALIDEE;

    // v�rification de questions d�j� existantes

    $questions = get_questions($id, $id_etab, false);
    //    print_r($questions);

    // rev 944 si le nombre actuel de questions est > a celui demand�
    // c'est qu'on a r�duit le nombre de questions dans l'�cran de modification
    // donc tout retirer !

    if (count($questions) > $nbq_aleatoire) {
        // suppression des attachements de questions
        delete_records('questionsexamen', 'id_examen=' . $id . " and id_examen_etab=" . $id_etab);
        $questions = array ();
        //print("on recommence tout");
    }

    if (count($questions) < $nbq_aleatoire) {
        $nbq_util = count($questions); // nombre de questions d�j� affect�es

        $liste_q = "1";

        foreach ($questions as $q)
        $liste_q .= " and (id_etab!=" . $q->id_etab . " or id!=" . $q->id . ")";

        // r�partition dans les diff�rents r�f�rentiels
        // rev 944 on reintroduit les examens par comp�tences (positionnement seulement !)
        $refs = get_referentiels_liste($ligne->referentielc2i); //tri� par referentiel
        //print_r($ligne);
        //print_object('', $refs);
        $nb_lignes_r = count($refs); // nombres de referentiels restant � traiter

        $nbq = $nbq_aleatoire - $nbq_util; // nombre de questions restant � ajouter
        $nbqa = floor($nbq / $nb_lignes_r); // nombre de questions � choisir par r�f�rentiel

        foreach ($refs as $ligner) {
            $table=cree_table_nbquestions_par_competence ($ligner,$nbqa);
            foreach ($table as $cle => $nombre) {
                // rev 977  dans quels champs de la question doit-on regarder ?
                 $critere_competence = " concat(referentielc2i,'.',alinea)='$cle'";
 
                // essai d'ajout d'un argument � RAND(). ne change rien
                //$rnd=srand((double) microtime() * 10000000);
                $rnd = '';
                if ($typepf == "certification") {
                    if ($est_pool == 1) {
                        // doublon de famille possible pour avoir assez de question dans le pool
                        $chwherefamille = "and id_famille_validee!=0";
                    } else {
                        // pas de doublon de famille
                        $chwherefamille = "and id_famille_validee!=0 group by id_famille_validee";
                    }
                    // tirage al�atoire avec au plus une question par famille d'un m�me r�f�rentiel
                    $requete =<<<EOR
                        SELECT id_famille_validee , id, id_etab
                        FROM {$CFG->prefix}questions
                        WHERE $criteres_examen $typepf ='oui' $validation
                            and $critere_competence
                            and ( $liste_q )
                        $chwherefamille
                        order by RAND($rnd) limit 0,$nombre
EOR;
                } else {
                    // postionnement, tirage al�atoire peu importe la famille
                    $requete =<<<EOR
                        SELECT id, id_etab
                        FROM {$CFG->prefix}questions
                        WHERE $criteres_examen $typepf ='oui'  $validation
                            and $critere_competence
                            and ( $liste_q )
                        order by RAND($rnd) limit 0, $nombre
EOR;
                }
                //print $requete;
                $resultat = get_records_sql($requete, false); //pas d'erreur fatale !!!
                if (count($resultat) < $nbqa) {
                    //que fait on si nous n'avons pas assez de questions ?
                }

                foreach ($resultat as $ligne) {
                    // ajout
                    ajoute_question_examen($ligne->id, $ligne->id_etab, $id, $id_etab);
                    $liste_q .= " and (id_etab!=" . $ligne->id_etab . " or id!=" . $ligne->id . ")";
                    $nbq_util++;
                    $nbq--; //PP
                }

            } // fin boucle sur le nombre de questions par comp�tence

        } // fin boucle par referentiel

    } // count($questions) < $nbq_aleatoire)
}




/**
 * tirage avec equilibre par THEME (1 question par th�me si respect de la circulaire)
 * algorithme propos� par les experts du GT en sept 2011
 *a valider 
 
 */
function __tirage_questions_V3 ($id,$id_etab) {
	
	global $CFG,$USER;
	$typepf = $USER->type_plateforme;
	
	$ligne = get_examen($id, $id_etab);
	
	$est_pool = $ligne->est_pool;
	
	// nombre de questions al�atoires par qcm
	if ($est_pool == 1)
		// nombre de questions fix� par le pool
		$nbq_aleatoire = $ligne->nb_q_pool;
	else {
		//rev 944
		if (empty ($ligne->nbquestions))
			// rev 944 pour l'�tablissement pas pour  le login !!!
			$nbq_aleatoire = config_nb_aleatoire($id_etab);
		else
			$nbq_aleatoire = abs($ligne->nbquestions);
	}
	
	/////////////////////////////////////////////////////
	// crit�res d'examen pour la s�lection de questions
	/////////////////////////////////////////////////////
	
	$criteres_examen = "not est_filtree "; // rev 1079
	$criteres_examen .= ' and etat!='.QUESTION_REFUSEE;
	
	//ignorer les questions sans domaine/comp�tence
    $criteres_examen .= " and referentielc2i !='' and alinea !=0 ";
	
	$criteres_examen = "(" . $criteres_examen . ") and ";
	$validation = "";
	
	// rev 852 sur la nationale un positionnement n'est qu'avec des valid�es
	// rev 940 $CFG->seulement_validee_en_positionnement
	if ($typepf == "certification" || $CFG->universite_serveur == 1 || $CFG->seulement_validee_en_positionnement)
		$validation = ' and etat='.QUESTION_VALIDEE;
	
	// v�rification de questions d�j� existantes
	
	$questions = get_questions($id, $id_etab, false);
	//    print_r($questions);
	
	// rev 944 si le nombre actuel de questions est > a celui demand�
	// c'est qu'on a r�duit le nombre de questions dans l'�cran de modification
	// donc tout retirer !
	
	if (count($questions) > $nbq_aleatoire) {
		// suppression des attachements de questions
		delete_records('questionsexamen', 'id_examen=' . $id . " and id_examen_etab=" . $id_etab);
		$questions = array ();
		//print("on recommence tout");
	}
	
	if (count($questions) < $nbq_aleatoire) {
		$nbq_util = count($questions); // nombre de questions d�j� affect�es
		$nbq = $nbq_aleatoire - $nbq_util; // nombre de questions restant � ajouter
		
		$liste_q = "1";
		
		foreach ($questions as $q)
		$liste_q .= " and (id_etab!=" . $q->id_etab . " or id!=" . $q->id . ")";
		
		// listes des familles/th�mes � traiter 
		// rev 944 on reintroduit les examens par comp�tences (positionnement seulement !)
		$familles = get_familles_liste($ligne->referentielc2i,'rand()',false); // dans le d�sordre
		//print_r($ligne);
		//print_object('', $familles);
		
		$pFamille=0; // indice famille � traiter
		
		$nbq_util=0; // nombre de questions � chaque tour de tous les th�mes
		while ($nbq >0) {  // tant qu'il manque des questions'
			$idf=$familles[$pFamille]->idf ; // id de la famille
			// tirage al�atoire d'une question dans cette famille
			$requete =<<<EOR
				SELECT id_famille_validee , id, id_etab
				FROM {$CFG->prefix}questions
				WHERE $criteres_examen $typepf ='oui' $validation
					and id_famille_validee=$idf
						and ( $liste_q )
				order by RAND() limit 0,1
EOR;
				//print $requete;
				if ($resultat = get_records_sql($requete, false)) {  //pas d'erreur fatale !!!
					foreach ($resultat as $ligne) {
						// ajout
						ajoute_question_examen($ligne->id, $ligne->id_etab, $id, $id_etab);
						$liste_q .= " and (id_etab!=" . $ligne->id_etab . " or id!=" . $ligne->id . ")";
						$nbq_util++;
						$nbq--; //PP
					}
				}  
				$pFamille ++;  // famille suivante
				if ($pFamille == count($familles)) { //boucle au d�but si plus de questions que de familles dispos
					// si aucune question n'a �t� trouv�e � ce tour, pas assez de questions 
					if ($nbq_util ==0) break;
					$pFamille=0; 
					$nbq_util=0; // rest pour le tour suivant 
				}	
				
		} // fin boucle sur le nombre � tirer
		
	}
	
}
