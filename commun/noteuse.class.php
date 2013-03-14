<?php

/**
 * @author Patrick Pollet
 * @version $Id: noteuse.class.php 1119 2010-09-09 07:03:45Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf

 *
 * Cette classe est un essai de regroupement
 *  des op�ratiosn de calculs de notes, scores, bilans
 *  qui sont actuellement tripliqu�s dans
 *  plusieurs scripts de codes/examens/resultats
 *  et config/statistiques
 *
 *   peut etre utilis�e pour un examen "a la vol�e"
 *
 *   d�ja utilis�e par lexport via le web service
 *
 *
*/


/**
* une ligne de resultat pour mise en forme HTML ou CVS
*/
class resultat {
	// une ligne extraite de la table c2iinscrits
	var $etudiant;
	// score en % par referentiel
	var $tabref_score;
	// score en % par referentiel ET alinea (une competence ) // demande EVALCOMP
	var $tabcomp_score;
	// score en points par question
	var $tab_points;
    // detail calculs points par question
    var $tab_debug;

	// score en % ramene au nombre de questions
	var $score_global;
	// score en % ramene au nombre de questions non arrondi
	var $score_global_na;

	// somme des points par question
	var $score_brut;
	// somme des points par question non arrondi
	var $score_brut_na;
        // nombre de reponses enregistrees si 0, n'est pas venu
        var $nb_reponses;
	// heure et ip de la derniere r�ponse
	var $heure_max;
	var $ip_max;
	var $ts_date_max;
    var $origine; // rev 804 vide=passage normal sinon qcmdirect icr ...
    var $examen; //  rev 808 code examen r��l (cas des pools)

	function resultat($etudiant) {
		$this->etudiant=$etudiant;
		$this->tabref_score=array();
		$this->tabcomp_score=array();
		$this->tab_points=array();
        $this->tab_debug=array();
		$this->score_global=0;
		$this->score_global_na=0;
		$this->score_brut=0;
		$this->score_brut_na=0;
		$this->nb_reponses=0;
		$this->heure_max = "";
		$this->ip_max = "";
        $this->origine = "";
        $this->examen="";
		$this->ts_date_max=0;
        $this->ts_date_min=time();  // rev 1025 pour calcul temsp de passage


	}
}

/**
* s'occupe de tout pour un vrai examen avec infos dans la BD (questions, r�ponses et r�ponses �tudiant)
*/

class noteuse {
	// tableau du nombre d'utilisation de chaque r�f�rentiel dans le questionnaire
	var $tabref_nbqr = array();
	// tableau du nombre d'utilisation de chaque competence (r�f�rentiel.alinea) dans le questionnaire
	var $tabcomp_nbqr = array();
	// tableau associatif donnant le r�f�rentiel a laquelle est associ�e une question
	var $tab_q_ref = array();
	// tableau associatif donnant la competence (ref�rentiel+alinea)  a laquelle est associ�e une question
	var $tab_q_comp = array();
	// recherche des r�ponses OUI aux questions :
	var $tabqc = array();
	// recherche des r�ponses NON aux questions :
	var $tabqm = array();
	// nombre de r�ponses � la question
	var $tabqn = array();


	var $id_examen,$id_etab;

	/**
	* constructeur .
	* construit et initiale une instance
	* � partir d'un examen
	* @param  int $id_examen : code unique de l'examen (PF locale)
	* @param  int $id_etab   : code etablissement
	*/
	function noteuse ($id_examen,$id_etab) {
		$this->id_examen=$id_examen;
		$this->id_etab=$id_etab;
		$this->init_tables($id_examen,$id_etab);
	}

	function reinit_table($t) {
		if (isset($this->$t))
			unset($this->$t);
		$this->$t=array();
	}

	function reinit_tables(){
		$this->reinit_table ("tab_q_ref");
		$this->reinit_table ("tabref_nbqr");
		$this->reinit_table ("tab_q_comp");
		$this->reinit_table ("tabcomp_nbqr");
		$this->reinit_table ("tabqc");
		$this->reinit_table ("tabqm");
		$this->reinit_table ("tabqn");
	}

	/**
	* m�thode de recherche des questions (en BD)
	* surcharg�e dans noteuseALaVolee ou les questions sont dans une "liste"
	* puisque dans ce cas un examen n'a pas �t� cr�� dans la BD
	*/
	function get_questions() {
		$res=get_questions($this->id_examen,$this->id_etab,false,false,0, //acc�s BD  pas d'erreur fatale ni de tri sp�cial'
                      "err_examen_sans_question",$this->id_etab.".".$this->id_examen);

		return $res;
	}
	/**
	* m�thode de recherche des r�ponses etudiant (en BD)
	* surcharg�e dans noteuseALaVolee ou les reponses dans dans une liste
	* et pas dans la BD
	*/
	function get_reponses_etudiant($login,$idq,$ideq) {
		$res=get_reponses_etudiant($login,$idq,$ideq,$this->id_examen,$this->id_etab); // acc�s BD

		//print_r($res);
		return $res;
	}
	/**
	 * m�thode de recherche des r�ponses a une question
	 * pourrait �tre surcharg�e ???
	 */
	function get_reponses ($idq,$ide) {
		return get_reponses($idq,$ide,false,0);  //pas de tri, pas d'erreur (quoique)'
	}

	function init_tables($idq,$ide){

        global $CFG;

		$this->reinit_tables();
		$referentiels=get_referentiels();
		foreach($referentiels as $rowr) {
			$this->tabref_nbqr[$rowr->referentielc2i] = 0;
		}
		$alineas=get_alineas(false); //tous les alienas tries ref+alin
		foreach($alineas as $alinea) {
            //$this->tabcomp_nbqr[$alinea->id] = 0;  //id= referentiel.alinea attention structire table c2ialinea chang�e
            $this->tabcomp_nbqr[$alinea->id_nat] = 0;  //id_nat = referentiel.alinea  rev 891 cf get_alineas
		}
		// s�lection des questions utilis�es
		$resq=$this->get_questions();
		//print("quest=".print_r($resq,true));
		foreach($resq as $rowq) {
			$this->tabref_nbqr[$rowq->referentielc2i]++;
			$cle=$rowq->id_etab.".".$rowq->id;
            $this->tab_q_ref[$cle] = $rowq->referentielc2i;
			$this->tabqc[$cle] = array();
			$this->tabqm[$cle] = array();
			$this->tabqn[$cle] = 0;
   	        $id=$rowq->referentielc2i.".".$rowq->alinea;

            $this->tabcomp_nbqr[$id]++;  //rev 892 donnait des notices depuis ajoiut colonne id a la table c2ialinea
			$this->tab_q_comp[$cle] = $id;
			// r�ponses attendues
			$reponses=$this->get_reponses($rowq->id,$rowq->id_etab);
			//print_r($reponses);
			foreach ($reponses as $lignec) {
				switch ($lignec->bonne){
				case "OUI" :
					$this->tabqc[$cle][] = $lignec->num;
					break;
				case "NON" :
					$this->tabqm[$cle][] = $lignec->num;
				}
				$this->tabqn[$cle] ++;
			}
		}
	}
	// renvoie la liste des questions dans l'ordre de traitement
	// cad tri�e par  id_etab,id_q
	function liste_questions() {
		return array_keys($this->tabqn);
	}
	// renvoie la liste des referentiel dans l'ordre de traitement
	// cad tri�e par  referentiel
	function liste_referentiels() {
		return array_keys($this->tabref_nbqr);
	}

	// renvoie la liste des referentiel dans l'ordre de traitement
	// cad tri�e par  referentiel+alinea
	function liste_competences() {
		return array_keys($this->tabcomp_nbqr);
	}
	/**
	* renvoie le nombre de questions dans un  referentiel
	* @param string $ref
	* @return
	*/
	function nbquestions_par_referentiel($ref) {
		return $this->tabref_nbqr[$ref];
	}

	/**
	* renvoie le nombre de questions dans une competence  referentiel+alinea
	* @param string $ref
	* @return
	*/
	function nbquestions_par_competence($comp) {
		return $this->tabcomp_nbqr[$comp];
	}




	/**
	 * fait tous les calculs pour un galopin inscrit
	 * @param $etudiant : un objet extrait de la BD
	 */
	function note_etudiant ($etudiant) {
        global $CFG;

		$ret=new resultat($etudiant);
		$nbReponses=0;  //nombre de r�ponses
		foreach( $this->liste_questions() as $question) {
			// nombre de r�ponses a OUI dans la question
			$B = count($this->tabqc[$question]);
			// nombre de r�ponses a NON dans la question
			$M = count($this->tabqm[$question]);

			$X = 0; // nombre de r�ponses a OUI par l'�tudiant parmis les B
			$Y = 0; // nombre de r�ponses a NON par l'�tudiant parmis les M

			$nbpoints = 0; // nombre de points obtenus � la question
			$ex = explode (".",$question); //redecoupe la cl�
			//ses r�ponses
			// donne une rafale de notices php avec un anonyme sans compte
			if ($ressq=$this->get_reponses_etudiant($etudiant->login,$ex[1],$ex[0])) {
				$nbReponses += count($ressq);  // augmente
				foreach($ressq as $rowsq) {
					if (in_array($rowsq->reponse, $this->tabqc[$question])) $X++;
					if (in_array($rowsq->reponse, $this->tabqm[$question])) $Y++;
					$ret->nb_reponses++; //juste pout etre sur qu'il est venu ...
                    /**
					if (isset($rowsq->date) && isset($rowsq->heure)) {
						$dheuretmp = $rowsq->date." ".$rowsq->heure;
						if ($dheuretmp > $ret->heure_max){
							$ret->heure_max = $dheuretmp;
							$ret->ip_max = $rowsq->ip;
						}
                        $ret->origine=$rowsq->origine; // 21/05/2009
					}
                    **/
                    //rev 1025 calcul plus simple des heures de passage
                    if (isset($rowsq->ts_date)) {
                         if ($rowsq->ts_date < $ret->ts_date_min)
                            $ret->ts_date_min=$rowsq->ts_date;
                         if ($rowsq->ts_date > $ret->ts_date_max) {
                            $ret->ts_date_max=$rowsq->ts_date;
                            $ret->ip_max = $rowsq->ip;
                         }
                         $ret->origine=$rowsq->origine; // 21/05/2009
                    }
                    //rev 1099 compatibilt� V 1.4 (uniquement en migration)
                    // pas valable pour une noteuse � la vol�e
					if (isset($rowsq->date) && isset($rowsq->heure)) {
						$dheuretmp = $rowsq->date." ".$rowsq->heure;
						if ($dheuretmp > $ret->heure_max){
							$ret->heure_max = $dheuretmp;
							$ret->ip_max = $rowsq->ip;
						}
					}
 				} //foreach
			} // if
			if ($B > 0) {
				if ($M > 0){
					$nbpoints = ($X / $B) - ($Y / $M);
				}
				else {
					$nbpoints = ($X / $B);
				}
			}
			else if ($Y == 0){
				$nbpoints = 1;
			}
			else {
				if ($M > 0){
					$nbpoints = -($Y / $M);
				}
				else {
					$nbpoints = 1;
				}
			}
            $ret->tab_debug[$question]=sprintf("B=%d M=%d X=%d Y=%d ",$B,$M,$X,$Y);


            //rev 904 point 23 du cahier des charges V2)
            if (!empty($CFG->pas_de_scores_negatifs)) {
                if ($nbpoints <0) $nbpoints=0;
            }

			//REPRODUCTION DU BUG v 1.2.1
			//$ret->tab_points[$question]=$nbpoints;
			$ret->score_brut_na +=$nbpoints;
			$nbpoints = sprintf("%.2f",$nbpoints);

			$ret->tab_points[$question]=$nbpoints;
			$ret->score_brut += $nbpoints;
			$ref=$this->tab_q_ref[$question]; //referentiel trait�
			if (!isset($ret->tabref_score[$ref]))
				$ret->tabref_score[ $ref ] = 0;
			$ret->tabref_score[ $ref] += $nbpoints;

			$comp=$this->tab_q_comp[$question]; //competence trait�
			if (!isset($ret->tabcomp_score[$comp]))
				$ret->tabcomp_score[ $comp ] = 0;
			$ret->tabcomp_score[ $comp] += $nbpoints;
		}
		// conversion score global en %
		// pas de score global <0
		//si nb_reponses=0 tout mettre a blanc
		if ($nbReponses>0) {
			$nbq=count($this->liste_questions());
			$ret->nbq=$nbq;
			$ret->score_brut_bis=0;
			foreach ($ret->tab_points as $pts)
			$ret->score_brut_bis +=$pts;
			if ($nbq) {
				$ret->score_global=   max(0,$ret->score_brut*100/$nbq);
				$ret->score_global_na=max(0,$ret->score_brut_na*100/$nbq);
			}else
				$ret->score_global=0;  //impossible (pas de questions ?)
			// converti les resultats par referentiel TESTES en %  avec arrondi a 2
			foreach ($ret->tabref_score as $ref=>$score) {
				$nbq=$this->tabref_nbqr[$ref];
				if ($nbq)
					$ret->tabref_score[$ref]= sprintf("%.2f",$score*100/$nbq);
				else // CECI n'arrive jamais (on regarde $ret->tab_ref_score
					$ret->tabref_score[$ref]=-1; // pas de question dessus;
			}
			// converti les resultats par competence TESTEES en % avec arrondi a 2
			foreach ($ret->tabcomp_score as $comp=>$score) {
				$nbq=$this->tabcomp_nbqr[$comp];
				if ($nbq)
					$ret->tabcomp_score[$comp]= sprintf("%.2f",$score*100/$nbq);
				else  // CECI n'arrive jamais (on regarde $ret->tab_ref_comp
					$ret->tabcomp_score[$comp]=-1; // pas de question dessus;
			}

			ksort($ret->tabref_score); //tri par ordre des referentiels, plus simple ensuite
			ksort($ret->tabcomp_score); //tri par ordre des competences plus simple ensuite


            //TODO timestamp on l'a ....
            /** rev 1026 inutile desormais , deja calcul� plus haut
            $tmp=explode(" ",$ret->heure_max); // YYYY-MM-JJ HH:MM:SS
			if (count($tmp)==2) {
				list($h,$min,$s)=explode(":",$tmp[1]);
				list($a,$m,$j)=explode("-",$tmp[0]);
				$ret->ts_date_max=mktime($h,$min,$s,$m,$j,$a);
			}
            ****************************************************/
		} else {
            //rev 970 trier quand m�me par referentiel
            ksort($ret->tabref_score); //tri par ordre des referentiels, plus simple ensuite
            ksort($ret->tabcomp_score); //tri par ordre des competences plus simple ensuite

            $ret->score_global=-1; //ne la pas pass� !
        }
		return $ret;
        /**
         * IMPORTANT $ret ne contient (heureusement QUE les domaines et comp�tences TESTEES
         * par toutes. Donc on ne met que ca en BD
         * pour des exports tabul�s, il faut prendre des mesures pour les domaines NON test�s
         */
	}
}

/**
 * correction examen de type tirage lors du passage
 * les questions sont dans une liste en m�moire
 * les r�ponses attendues en BD
 * les r�ponses �tudiants ont �t� enregistr�es via Ajax pour reprise
 */

class noteuseTiragePassage  extends noteuse {

	/**
	* @param $listeQuestions les questions pos�es (une chaine 1.12_2.12_
	* @param $listeReponses les r�ponses recues un tableau associatif de chaines
	*       de la forme [ide_idq_numreponse]=>1 pour les cases coch�es
	*       en principe les cases non coch�es sont absentes

	* @param $typep 'certification' ou 'positionnement'
	*/

	function noteuseTiragePassage ($id_examen,$id_etab,$listeQuestions) {
		//m�morise la liste des questions (une chaine de la forme 1.1 1.2 1.56 ...)
		$this->listeQuestions=$listeQuestions;
		//appel du constructeur parent qui initailise ses tables ...
		noteuse::noteuse($id_examen,$id_etab);
	}


	/**
	* m�thode de recherche des questions
	* surcharg�e ici ou les questions sont dans une "liste"
	* puisque dans ce cas un examen n'a pas �t� cr�� dans la BD
	* @return un tableau d'objet
	*/
	function get_questions() {
		// d�coupe la liste des questions pos�es 1.1_1.25_4.56_ ...
		// et va les chercher dans la BD
		// gere les erreurs de format par une erreur fatale
		return retrouve_questions ($this->listeQuestions);
	}


}

/**
* s'occupe de tout pour un "faux" examen avec g�n�ration des questions � la vol�e
* et qui a �t� pass� SANS m�morisation ajax des r�ponses de l'�tudiant
* typiquement un prof qui teste son examen '
* sans aucune info dans la BD dans la BD
*/


class noteuseALaVolee extends noteuseTiragePassage {
	//les questions pos�es (une chaine 1.12_2.12_ ...
	var $listeQuestions;
	// les r�ponses recues un tableau associatif de tableau de r�ponses coch�es
	// de la forme [ide.idq]=> [numreponse,numreponse...]
	// voir la m�thode prepare
	var $listeReponses;
	// obligatoire (pour l'instant pour l'appel a fonctions_diverses::get_question
	// probablement inutile ?
	var $typep;


	/**
	* @param $listeQuestions les questions pos�es (une chaine 1.12_2.12_
	* @param $listeReponses les r�ponses recues un tableau associatif de chaines
	*       de la forme [ide_idq_numreponse]=>1 pour les cases coch�es
	*       en principe les cases non coch�es sont absentes

	* @param $typep 'certification' ou 'positionnement'
	*/

	function noteuseALaVolee ($listeQuestions,$listeReponses,$typep=false) {
		//m�morise la liste des questions (une chaine de la forme 1.1 1.2 1.56 ...)
		$this->listeQuestions=$listeQuestions;
		$this->listeReponses=$this->prepare($listeReponses);

		//$this->typep=$typep;
		//appel du constructeur parent qui initailise ses tables ...
		noteuseTiragePassage::noteuseTiragePassage(0,0,$listeQuestions);
	}

	function prepare ($listeReponses) {
		$reps=array();
        //print_r($listeReponses);
		foreach ($listeReponses as $cle=>$valeur) {
			if ($valeur) {// si coch�e)
				$tmp=explode("_",$cle); //idetab_idquestion_numrep
				if (sizeof($tmp==3)) {
					$cle=$tmp[0].".".$tmp[1];
					if (!isset ($reps[$cle]))
						$reps[$cle]=array();
					$reps[$cle][]=$tmp[2];
				}
			}
		}
		return $reps;
	}


	/**
	* m�thode de recherche des r�ponses
	* surcharg�e ici  ou les reponses sont aussi en m�moire
	* et pas dans la BD
	*/
	function get_reponses_etudiant($login,$idq,$ide) {

		$res= array(); //vide
		//print_r($this->listeReponses);
		$cle=$ide.".".$idq;
		if (isset($this->listeReponses[$cle])) {  //si au moins une r�ponse a cette question
			foreach($this->listeReponses[$cle] as $numrep) {
				$rep=new StdClass;
				$rep->reponse=$numrep;
				/* inutile car non test� dans evalue_etudiant
				$rep->login=$this->etudiant->login;
				$rep->question=$tmp[0].".".$tmp[1];
				$rep->examen="";
				$rep->date ="";
				$rep->heure="";
				*/
				$res[]=$rep;
			}
		}
		return $res;
	}

}

/**
 * revision 1020 AMC a d�ja not� les questions selon le bar�me C2I
 * nous n'avons donc qu'a les reventiler par referentiel eyt domaine
 */

class noteuseAMC extends noteuse {


    function noteuseAMC ($id_examen,$id_etab) {
        noteuse::noteuse($id_examen,$id_etab);

    }

    /**
     * @param $etudiant un record estrait de la BD
     * @param $scoreAMC le score envoy� par AMC (=somme des scores, peut �tre <0)
     * @param $notes : un tableau des notes obtenues � chaque question (relu dans un CSV)
     */
    function note_etudiant ($etudiant,$scoreAMC, $notes) {

         global $CFG;
        $ret=new resultat($etudiant);
        $ret->origine='amc';
        $ret->ts_date_min=$ret->ts_date_max=time(); // peut pas mieux

        $ret->ip_max=getremoteaddr();
        $ret->scoreAMC=$scoreAMC; //pour controle

        $nbReponses=0;
        /**
         * meme code que la noteuse sauf qu'on en calcule pas les points, on les a d�ja ...
         */
        foreach( $notes as $question=>$nbpoints) {
             //rev 904 point 23 du cahier des charges V2)
            if (!empty($CFG->pas_de_scores_negatifs)) {
                if ($nbpoints <0) $nbpoints=0;
            }

            $ret->score_brut_na +=$nbpoints;
            $nbpoints = sprintf("%.2f",$nbpoints);
            $ret->tab_points[$question]=$nbpoints;
            $ret->score_brut += $nbpoints;
            $ref=$this->tab_q_ref[$question]; //referentiel trait�
            if (!isset($ret->tabref_score[$ref]))
                $ret->tabref_score[ $ref ] = 0;
            $ret->tabref_score[ $ref] += $nbpoints;

            $comp=$this->tab_q_comp[$question]; //competence trait�
            if (!isset($ret->tabcomp_score[$comp]))
                $ret->tabcomp_score[ $comp ] = 0;
            $ret->tabcomp_score[ $comp] += $nbpoints;
        }
        // conversion score global en %
        // pas de score global <0
            $nbq=count($notes);
            $ret->nbq=$nbq;
            $ret->score_brut_bis=0;
            foreach ($ret->tab_points as $pts)
            $ret->score_brut_bis +=$pts;
            if ($nbq) {
                $ret->score_global=   max(0,$ret->score_brut*100/$nbq);
                $ret->score_global_na=max(0,$ret->score_brut_na*100/$nbq);
            }else
                $ret->score_global=0;  //impossible (pas de questions ?)
            // converti les resultats par referentiel TESTES en %  avec arrondi a 2
            foreach ($ret->tabref_score as $ref=>$score) {
                $nbq=$this->tabref_nbqr[$ref];
                if ($nbq)
                    $ret->tabref_score[$ref]= sprintf("%.2f",$score*100/$nbq);
                else // CECI n'arrive jamais (on regarde $ret->tab_ref_score
                    $ret->tabref_score[$ref]=-1; // pas de question dessus;
            }
            // converti les resultats par competence TESTEES en % avec arrondi a 2
            foreach ($ret->tabcomp_score as $comp=>$score) {
                $nbq=$this->tabcomp_nbqr[$comp];
                if ($nbq)
                    $ret->tabcomp_score[$comp]= sprintf("%.2f",$score*100/$nbq);
                else  // CECI n'arrive jamais (on regarde $ret->tab_ref_comp
                    $ret->tabcomp_score[$comp]=-1; // pas de question dessus;
            }

            ksort($ret->tabref_score); //tri par ordre des referentiels, plus simple ensuite
            ksort($ret->tabcomp_score); //tri par ordre des competences plus simple ensuite



        return $ret;
    }


}

//test noteuse
if (0) {
	//test
	$chemin = '..';
	$chemin_commun = $chemin."/commun";
	$chemin_images = $chemin."/images";
require_once("$chemin/commun/c2i_params.php");



	$idq=19;
	$ide=65;

	$noteuse= new noteuse($idq,$ide);
	print_r($noteuse);
	print_r ($noteuse->liste_questions());
	print_r ($noteuse->liste_referentiels());

	$cnt=0;
	if ($res= get_inscrits($idq,$ide)) {
		foreach($res as $etudiant) {
			print_r($noteuse->note_etudiant($etudiant));
			if (++$cnt >=6) break;
		}
	}
}
//test noteuse à la vol�e
if (0) {
	$chemin = '..';
	$chemin_commun = $chemin."/commun";
	$chemin_images = $chemin."/images";
	require_once($chemin_commun."/c2i_params.php"); //fichier de param�tres

	$etudiant=get_inscrit("bafare");
	$q="1.710_1.594_1.313_1.96_1.332";
	$r=array ();
	$r["1_710_7144"]=1;  //bonne et coch�e
	$r["1_710_7145"]=1;  // fausse et coch�e
	$r["1_594_2959"]=1;  // fausse et coch�e
	//print_r($r);

	$noteuse= new noteuseALaVolee($q,$r,'positionnement');
	print_object($noteuse);
	print_r ($noteuse->note_etudiant($etudiant));
}

?>
