<?php
/**
 * @author Patrick Pollet
 * @version $Id: lib_pool.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */



function attrib_q_exam($id,$ide,$origine,$nombre='tout'){
    global $CFG;
    switch ($CFG->algo_tirage) { 
    	case 1 :  __attrib_q_exam_V1($id,$ide,$origine,$nombre);break;
    	case 2 :  __attrib_q_exam_V2($id,$ide,$origine,$nombre);break;
    	case 3 :  __attrib_q_exam_V3($id,$ide,$origine,$nombre);break;
    }	
}

/**
* attrib_q_exam permet d'attribuer al�atoirement des questions � l'examen $ide.$idq.
* La source des questions est d�finie par le tableau $origine via la variable $origine['type']. Les autres indices de ce tableau peuvent permettre de pr�ciser l'�l�ment d'origine
* par exemple si $origine['type'] == 'examen', on a besoin de savoir les identifiants de cet examen
* dans ce cas on aura $origine['idq'] et $origine['ide'] d�finis
* sinon, si les questions viennent de la bdd $origine['type'] == 'certification' ou $origine['type'] == 'positionnement'
* et $origine['criteres'] contient les crit�res � utiliser dans ce cas
* $nombre d�fini le nombre de questions � attribuer (vaut 'tout' si toutes les questions doivent �tre attribu�es)
* renvoi 0 en cas d'�chec, 1 en cas de succ�s
*
* PP v 1.5 cette fonction ne respecte pas la r�gle d'�quilibre entre familles, mais l'ancienne d'�quilibre entre referentiels.
* donc ne plus utiliser pour g�n�rer un examen nouveau ou le modifier as partir de la bd '
*
* on peut l'utiliser pour generer les membres d'un pool
* en effet le pool p�re respecte deja la r�gle des familles, donc n�cessaireement les membres aussi puisque l'on
* prend leurs questions dans celles du p�re
*/

//equilibrage par domaine
function __attrib_q_exam_V1($id,$ide,$origine,$nombre='tout'){
    global $CFG;

    if (!isset($origine['criteres'])) $origine['criteres'] = "";
    if ($origine['type'] == 'examen'){
        $select = "Q.id, Q.id_etab from {$CFG->prefix}questionsexamen QE,{$CFG->prefix}questions Q";
        $where = "QE.id_examen=".$origine['idq']." and QE.id_examen_etab=".$origine['ide']." and QE.id=Q.id and QE.id_etab=Q.id_etab" ;
    }
    /***************************************  V 1.4 OUT
    else if (($origine['type'] == 'certification') || ($origine['type'] == 'positionnement')){
        if ($origine['type'] == 'certification'){
            $validation = " and etat='valid�e'";
        }
        else if ($origine['type'] == 'positionnement'){
            $validation = " and etat!='refus�e'";
        }

        $texte_id = "id";
        $texte_id_etab = "id_etab";
        $select = "id, id_etab FROM c2iquestions";
        $where = $origine['criteres'].$origine['type']."='oui'".$validation." and (isnull(duree_de_vie) or duree_de_vie='0000-00-00' or duree_de_vie >='".date("Y-m-d",time())."')";
    }
    *******************************************/
    else erreur_fatale('impossible de trouver la source des questions',print_r($origine,true));
    $nbq_util = 0;
    if ($nombre == 'tout'){   //cas d�ja trait� dans lib_examen/copie_examen
        $requete = "SELECT $select WHERE $where";
        $resultat = ExecRequete ($requete);
        while ($ligne = LigneSuivante ($resultat)){
            // ajout
             ajoute_question_examen($ligne->id,$ligne->id_etab,$id,$ide);
            $nbq_util++;
        }
        return $nbq_util;
    }
    // cas diff�rent
    // r�partir �quitablement par r�f�rentiel  et pas par famille
    $nbq_util = 0;

    // rev 978 10/01/2010  dans quel champ de la question doit-on regarder ?
    $nomref='Q.referentielc2i';

    //$resr=get_referentiels();
    // rev 944 ok pour des pools par domaine
    $ligne_e=get_examen($origine['idq'],$origine['ide']);
    $resr=get_referentiels_liste($ligne_e->referentielc2i);

    $nb_lignes_r = count($resr); // nombres de referentiels restant � traiter
    $nbq = $nombre; // nombre de que stions restant � ajouter
    $nbqa = floor($nbq / $nb_lignes_r); // nombre de questions � choisir par r�f�rentiel
    $liste_q = "1";
    foreach($resr as $ligner){

        $requete=<<<EOR
        SELECT $select
        WHERE $where
        and $nomref='$ligner->referentielc2i'
        and ($liste_q)
        order by RAND()
        limit 0,$nbqa
EOR;

        $resultat = ExecRequete ($requete);
        while ($ligne = LigneSuivante ($resultat)){
            // ajout
             ajoute_question_examen($ligne->id,$ligne->id_etab,$id,$ide);
             $liste_q .= " and (Q.id_etab!=" . $ligne->id_etab . " or Q.id!=" . $ligne->id . ")" ;
            $nbq_util++;
            $nbq--; //PP
        }
    }

    if ($nbq>0){
        $requete=<<<EOR
        	SELECT  $select
        	WHERE $where
        	and ($liste_q)
        	order by RAND() limit 0,$nbq
EOR;
    $resultat = ExecRequete ($requete);
        while ($ligne = LigneSuivante ($resultat)){
            // ajout
            ajoute_question_examen($ligne->id,$ligne->id_etab,$id,$ide);
            $liste_q .= " and (Q.id_etab!=" . $ligne->id_etab . " or Q.id!=" . $ligne->id . ")" ;
            $nbq_util++;
        }
    }

    return $nbq_util;

}

//equilibrage par comp�tence
function __attrib_q_exam_V2($id,$ide,$origine,$nombre='tout'){
    global $CFG;
    if (!isset($origine['criteres'])) $origine['criteres'] = "";
    if ($origine['type'] == 'examen'){
        $select = "Q.id, Q.id_etab from {$CFG->prefix}questionsexamen QE,{$CFG->prefix}questions Q";
        $where = "QE.id_examen=".$origine['idq']." and QE.id_examen_etab=".$origine['ide']." and QE.id=Q.id and QE.id_etab=Q.id_etab" ;
    }
    else erreur_fatale('impossible de trouver la source des questions',print_r($origine,true));

    // r�partir �quitablement par r�f�rentiel  et comp�tence ET pas encore par famille
    $nbq_util = 0;

    $ligne_e=get_examen($origine['idq'],$origine['ide']);
    $refs=get_referentiels_liste($ligne_e->referentielc2i);

    $nb_lignes_r = count($refs); // nombres de referentiels restant � traiter
    $nbq = $nombre; // nombre de questions restant � ajouter
    $nbqa = floor($nbq / $nb_lignes_r); // nombre de questions � choisir par r�f�rentiel
    $liste_q = "1";

    foreach ($refs as $ligner) {
         $table=cree_table_nbquestions_par_competence ($ligner,$nbqa);
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
         ...
         */

        foreach ($table as $cle => $nombre) {

            // rev 977  dans quels champs de la question doit-on regarder ?
            $critere_competence = " concat(referentielc2i,'.',alinea)='$cle'";

            $requete=<<<EOR
                SELECT $select
                WHERE $where
                and $critere_competence
                and ($liste_q)
            order by RAND()
            limit 0,$nombre
EOR;

            $resultat = ExecRequete ($requete);
            while ($ligne = LigneSuivante ($resultat)){
                // ajout
                ajoute_question_examen($ligne->id,$ligne->id_etab,$id,$ide);
                $liste_q .= " and (Q.id_etab!=" . $ligne->id_etab . " or Q.id!=" . $ligne->id . ")" ;
                $nbq_util++;
                $nbq--; //PP
            }
        } // fin boucles sur les comp�tences

    } // fin boucle sur le referentiel


    if ($nbq>0 ){   // pas assez de questions trouv�es ?
        $requete=<<<EOR
            SELECT  $select
            WHERE $where
            and ($liste_q)
            order by RAND() limit 0,$nbq
EOR;
    $resultat = ExecRequete ($requete);
        while ($ligne = LigneSuivante ($resultat)){
            // ajout
            ajoute_question_examen($ligne->id,$ligne->id_etab,$id,$ide);
            $liste_q .= " and (Q.id_etab!=" . $ligne->id_etab . " or Q.id!=" . $ligne->id . ")" ;
            $nbq_util++;
        }
    }

     return $nbq_util;

}

//equilibrage par th�me
function __attrib_q_exam_V3($id,$ide,$origine,$nombre='tout'){
	global $CFG;
	if (!isset($origine['criteres'])) $origine['criteres'] = "";
	if ($origine['type'] == 'examen'){
		$select = "Q.id, Q.id_etab from {$CFG->prefix}questionsexamen QE,{$CFG->prefix}questions Q";
		$where = "QE.id_examen=".$origine['idq']." and QE.id_examen_etab=".$origine['ide']." and QE.id=Q.id and QE.id_etab=Q.id_etab" ;
	}
	else erreur_fatale('impossible de trouver la source des questions',print_r($origine,true));
	// r�partir �quitablement par famille
	
	$ligne_e=get_examen($origine['idq'],$origine['ide']);
	$nbq = $nombre; // nombre de questions restant � ajouter
	$liste_q = "1";
	
	// listes des familles/th�mes � traiter 
	// rev 944 on reintroduit les examens par comp�tences (positionnement seulement !)
	$familles = get_familles_liste($ligne_e->referentielc2i,'rand()',false); // dans le d�sordre
	//print_object('', $familles);
	
	$pFamille=0; // indice famille � traiter
	$nbq_util=0; // nombre de questions � chaque tour de tous les th�mes
	while ($nbq >0) {  // tant qu'il manque des questions'
		$idf=$familles[$pFamille]->idf ; // id de la famille
		// tirage al�atoire d'une question dans cette famille
		$requete =<<<EOR
			SELECT $select
			WHERE $where
			and id_famille_validee=$idf
			and ( $liste_q )
		order by RAND() limit 0,1
EOR;
		//print $requete;
		if ($resultat = get_records_sql($requete, false)) {  //pas d'erreur fatale !!!
			foreach ($resultat as $ligne) {
				// ajout
				ajoute_question_examen($ligne->id, $ligne->id_etab, $id, $ide);
				$liste_q .= " and (Q.id_etab!=" . $ligne->id_etab . " or Q.id!=" . $ligne->id . ")";
				$nbq_util++;
				$nbq--; //PP
			}
		}  
		$pFamille ++;  // famille suivante
		if ($pFamille == count($familles)) { //boucle au d�but si plus de questions que de familles dispos
		// si aucune qustion n'a �t� trouv�e � ce tour
			if ($nbq_util ==0) break;
			$pFamille=0; 
			$nbq_util=0; // rest pour le tour suivant 
		}			
		
	} // fin boucle sur le nombre � tirer
	return $nbq_util;	
}




/** la fontion affecte_groupe_pool permet d'inscrire l'�tudiant connect� � un groupe ayant pour p�re le pool ($ide.$idq)
* on v�rifie qu'il n'est pas d�j� inscrit � un groupe
* si non on l'inscrit � un groupe dans le cr�neau horaire actuel
* s'il n'y en a pas on retourne un tableau vide
* sinon on retourne un tableau contenant ide et idq du groupe

 * @param maintenant laisser vide sauf pour des tests de cette fonction
 * @return le meilleur examen trouv� ou false
 */
function affecte_groupe_pool($idq,$ide,$login,$maintenant=false){

	if (!$maintenant) $maintenant=time();

    $fils = liste_groupe_pool($idq, $ide); // examens fils
    if (count($fils) == 0) return false;   // aucun rat�

    $meilleur_fils=false;
    $meilleur_temps = -1;

    foreach($fils as $examen){
        if (est_inscrit_examen($examen->id_examen,$examen->id_etab,$login))
        	return $examen; // il y revient on verra bien si il est toujours en cours

		if (!examen_en_cours($examen,$maintenant))  // ce fils n'est pas pour aujourdhui'
			continue;

		$restant=$examen->ts_datefin -$maintenant;  // temps restant
		if ($restant >$meilleur_temps) {  //on garde le meilleur
			$meilleur_temps=$restant;
			$meilleur_fils=$examen;
		}
    }

    if ($meilleur_fils) {
         $tags='passage pool $ide.$idq '.time();
    	 inscrit_candidat($meilleur_fils->id_examen,$meilleur_fils->id_etab,$login,$tags);
    }
    return $meilleur_fils;
}


/**************************************
/*rev 1.5 script inclus generer_groupes.php rappatri� ici
 *
 */
function generer_groupes($idq,$ide) {
	global $CFG,$USER;

	$ligne=get_examen($idq,$ide);
	$groupes = liste_groupe_pool($idq, $ide);

	if ($ligne->est_pool && count($groupes) == 0) { //si pas d�ja fait !
		$titre=$ligne->nom_examen; //titre commun
		$nb=$ligne->pool_nb_groupes;
        // pour un pool par doamine (possible) prendre le nombre de questions
        // retenues pour cet examen
		$nombre_questions = $ligne->nbquestions?$ligne->nbquestions:config_nb_aleatoire($ide); //rev 944 ($USER->id_user);
		for ($i=1; $i<=$nb; $i++) {
			if ($CFG->pool_marqueur)
				$ligne->nom_examen=$titre.sprintf($CFG->pool_marqueur,$i);

			$ligne->pool_pere=$idq; //important
			$ligne->est_pool=$ligne->nb_q_pool=$ligne->pool_nb_groupes=0;
			// faut il changer la date des fils ???
			$ligne->ts_datecreation=$ligne->ts_datemodification=time();
			
			//insert_record va virer ligne->id_examen actuel et renvoyer l'autonum '
			$idfils=insert_record("examens",$ligne,true,'id_examen');
			//TODO revoir en passant $idq et $ide puisque attrib_q_examen n'accepte plus
			// des questions issues de la BD'
            $origine=array();
			$origine['type'] = 'examen';
			$origine['idq'] = $idq;
			$origine['ide'] = $ide;
			attrib_q_exam($idfils, $ide, $origine, $nombre_questions);
			espion2("pool","generation_membre_pool $i", $ide.".".$idq);
		}
		return  liste_groupe_pool($idq, $ide);  //la liste des fils cr��s
	}
    return $groupes; // la liste des fils existants
}


/** retourne sous forme de tableau bidimentionnel la liste des examens utilisant le pool d'identifiant idq pour l'examen, ide pour l'�tablissement
 * les groupes sont forc�ment du m�me �tablissement que le pool
 * v 1.5 attention tableau indice de 0 a ... et plus par id_examen
 */
function liste_groupe_pool($idq, $ide){
	return get_records("examens","id_etab=$ide and pool_pere=$idq",'id_examen');

}


/**
 * test affectation des etudiants a un pool
 * si ca marche on doit voir dans la liste des examens
 * 1 examen test pool auto avec 10 inscrits
 * 5 examens pool fils avec chacun 2 inscrits et 0 passages ...
 */

if (is_admin() && 0) {

	require_once($CFG->chemin_commun."/lib_tests.php");

	//$ex1=cree_examen ("test normal",false,"al�atoire");
	$ex2=cree_examen_test ("test pool auto",true,"al�atoire");
	generer_groupes($ex2->id_examen,$ex2->id_etab);

	// met leur des dates de 1 heure en 1 heure
	$fils=liste_groupe_pool($ex2->id_examen,$ex2->id_etab);
	$debut=$ex2->ts_datedebut;
	foreach($fils as $f) {
		//vire les anciennes inscriptions aux fils (on est en test !)
		$critere =<<<EOS
			id_examen=$f->id_examen
				and id_etab=$f->id_etab
EOS;
		delete_records("qcm",$critere,false);
		//attention virer les inscrits avant car update_records vire les champs criteres !
		$f->ts_datedebut=$debut;
		$f->ts_datefin=$debut+HOURSECS; // 1 par heure
		// exemple d'usage d'update_record avec deux cl�s identifiant le bon record ...
		update_record("examens",$f, 'id_etab','id_examen',true);
		$debut=$f->ts_datefin;

	}
	//prend 10 candidats
	for ($i=1; $i<=10; $i++) {
		$cand=cree_candidat_test ("test_".$i,false,$i,"nom $i","prenom $i","test$i@patrickpollet.net");
		inscrit_candidat($ex2->id_examen,$ex2->id_etab,$cand->login,'test '.time());
	}
	//simule des passage par heure
	$debut=$ex2->ts_datedebut;
	for ($i=1; $i<=10; $i++) {
		affecte_groupe_pool($ex2->id_examen,$ex2->id_etab,"test_".$i,$debut); //$debut date de passage simul�e
		$debut=$debut+HOURSECS/2 ;  //10 candidats et 5  fils donc 2 par heure
	}


}

if (0) {
	$fils=generer_groupes(368,65);
	print_r($fils);
}



?>
