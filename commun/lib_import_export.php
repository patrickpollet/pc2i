<?php


/**
 * @author Patrick Pollet
 * @version $Id: lib_import_export.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
 * bibliotheque de manipulations des imports export
 * ne pas inclure syst�matiquement mais juste quand besoin
 * @uses global $CFG configuration g�n�rale
 * @uses $CFG->chemin_ressources   (par d�faut en 1.4 $chemin/ressources en relatif)
 */

require_once($CFG->chemin_commun."/lib_ldap.php");
require_once($CFG->chemin."/commun/lib_rapport.php");
require_once ("$chemin/commun/lib_resultats.php");
require_once ($CFG->chemin_commun."/lib_xml.php");



$currdir = $CFG->chemin_ressources;
if (($res = verifie_droits_fichiers($currdir)) != '')
	erreur_fatale($res,realpath($currdir));


/**
 * import de questions au format QCM direct
 * TODO meilleur controle syntaxe avec un AEF
 */

function from_qcmdirect($file, $type_quest) {
////////////////////////////////
//
//	Fonction d'import de QCM � partir d'un fichier
// Structure:
//		1. (competence,alin�a) intitul� premi�re question
//		A. intitul� premi�re r�ponse
//		B. intitul� deuxi�me r�ponse(tab)V
//		C. intitul� troisi�me r�ponse
//	Pierre Raynaud
//	pierre.raynaud@u-clermont1.fr
//
////////////////////////////////
	global $USER,$CFG;

	// partie commune des nouvelles questions
		$ligne=new StdClass();
		$ligne->auteur = get_fullname($USER->id_user);
		$ligne->auteur_mail = get_mail($USER->id_user);
		$ligne->id_etab=$USER->id_etab_perso;
	//	partie commune des nouvelles r�ponses
		$reponse=new StdClass();
		$reponse->id_etab=$ligne->id_etab;

		$ligne->certification = "NON";
		$ligne->positionnement = "NON";
		
	if (strstr($type_quest,"P")){
		$ligne->positionnement = "OUI";		
	}
	if (strstr($type_quest,"C")){
		$ligne->certification = "OUI";
	}

	$tab_questions=array();  //tableau questions trait�es
	$resultats=array();
	$handle = fopen("$file", "rb");
	if ($handle) {
		$id_question=0;
		$ajout = false;
		$num_qst = 0;
		$num_rep=0;
		while (!feof($handle)) {
			$contents = fgets($handle, 4096);
			if (trim($contents) == "") continue;
			// On test le type d'encodage de la chaine, si c'est du UTF8 on decode pour l'insertion correcte
			//mb pas forcement install� !
			//if (mb_detect_encoding($contents, "auto") == "UTF-8") $contents = utf8_decode ($contents);
			if (preg_match('/^(\d*)(\ *)[\.-](\ *)\(/', $contents)) {
			//FAUX cf mail raschid Janvier 2012	
			//if (preg_match('/^([A-Z0-9])(\ *)[\.-](\ *)\(/', $contents)) {
				$num_qst++;
				$id_question=0;  // !important
				$num_rep=0;
				preg_match_all('|\((.*)\)(.*)$|U', $contents, $competence_alinea, PREG_SET_ORDER);
				$domal = explode(".", $competence_alinea[0][1]);
				$competence = $domal[0];
				$alinea = $domal[1];
				$question = $competence_alinea[0][2];

				// On v�rifie que la comp�tence et l'alin�a existent
				if (!get_alinea($alinea,$competence,false)) {
					set_erreur(traduction("err_ref_ou_comp_inconnu",false, $num_qst,$competence,$alinea), $resultats);
					$ajout = false;
				} else {
					// V�rifie si la question n'a pas d�j� �t� rentr�e
					if (get_record("questions","titre='".addslashes($question)."'",false)) {
						set_erreur(traduction ("err_question_existe",false,$num_qst),$resultats);
						$ajout = false;
					} else {
						$ajout = true;
						$ligne->titre=$question;

						$ligne->referentielc2i=$competence;
						$ligne->alinea=$alinea;
                      
						$ligne->ts_datecreation=$ligne->ts_datemodification=time();

						$id_question=insert_record("questions",$ligne,true,'id');
						espion3("importation", "question", $ligne->id_etab.".".$id_question,$ligne); //rev 922
						$tab_questions[]=$id_question;

						set_ok(traduction ("info_qst_importe_ok",false,$num_qst,addslashes($question),$ligne->id_etab,$id_question),$resultats);

					}
				}
			} else {			
				if ($ajout) { // si pas erreur doamine ou question connue
					// une ligne de r�ponse commenece par une lettre de A a Z
					if (preg_match('/^([A-Z])(\ *)[\.-](\ *)/', $contents)){

						preg_match_all('|\??[\.-]\ (.*)$|U', $contents, $rep, PREG_SET_ORDER);  //pas fiable !
						$num_rep++;
						$reponse->reponse = $rep[0][1];
						$pos_bonne = strpos($reponse->reponse, chr(9) . "V");
						if ($pos_bonne) {
							$reponse->bonne = "OUI";
							$reponse->reponse = substr($reponse->reponse, 0, $pos_bonne);
						} else
							$reponse->bonne = "NON";
						$reponse->id=$id_question;
						$id_rep=insert_record("reponses",$reponse,true,false);  //attention a id qui n'est PAS la cl�'
						set_ok(traduction( "info_rep_importe_ok",false,$num_rep,$ligne->id_etab,$id_question,$id_rep),$resultats);
					}else {
						set_erreur (traduction ("err_qcm_direct_bad_format",false,$contents),$resultats);
                    	break; // rev 902 rat� il a oubli� le referentiel
                	}
				}
			}
		}
		fclose($handle);
		if ($CFG->generer_xml_qti) {
						foreach ($tab_questions as $id){
				to_xml($id, $USER->id_etab_perso);
				set_ok(traduction( "info_trad_xml",false,$id),$resultats);
			}
		}
	} else
		set_erreur(traduction( "err_fichier_non_trouve").$file,$resultats);
	return $resultats;
}




function from_xml_pfc2i($file, $type_quest) {
	global $CFG, $USER;

	$resultats=array();
	$data = implode("",file($file));
	$xmltext = file_get_contents($file);
	$pattern = '/&(?!\w{2,6};)/';
	$replacement = '&amp;';
	$xmltext = preg_replace($pattern, $replacement, $xmltext);


	$objXML = new xml2Array();
	$questions = $objXML->parse($xmltext, $CFG->encodage);
	$paramQuestion = new StdClass();
	if (count($questions) > 0) {
		$num_qst = 0;
		foreach ($questions as $lstQuestion) {
			foreach($lstQuestion["children"] as $subelement) {
				list($paramQuestion, $paramReponses,$paramDocuments) = qst_get_info($subelement["children"]);
				$num_qst++;
				$id_question=0;
				$ajout = false;
                $referentielc2i=$paramQuestion->referentielc2i;
                $alinea=$paramQuestion->alinea;
				$num_rep=0;
		      if (!empty($referentielc2i) && !empty($alinea) && !empty($paramQuestion->titre))  {
					// On v�rifie que la comp�tence et l'alin�a existent
					if (!get_alinea($alinea, $referentielc2i, false)) {
						set_erreur(traduction("err_ref_ou_comp_inconnu",false, $num_qst,$referentielc2i,$alinea), $resultats);
						$ajout = false;
					} else {
						// V�rifie si la question n'a pas d�j� �t� rentr�e
						if (get_record("questions","titre='".addslashes($paramQuestion->titre)."'",false)) {
							set_erreur(traduction ("err_question_existe",false, $num_qst), $resultats);
							$ajout = false;
						} else {
							if ($type_quest == "P"){
								$paramQuestion->positionnement = "OUI";
								$paramQuestion->certification = "NON";
							}
							elseif ($type_quest == "C"){
								$paramQuestion->positionnement = "NON";
								$paramQuestion->certification = "OUI";
							}
							else{
								$paramQuestion->positionnement = "OUI";
								$paramQuestion->certification = "OUI";
							}
							$ajout = true;
							if (!empty($paramQuestion->id_etab)) $paramQuestion->id_etab = $USER->id_etab_perso;
							if (!empty($paramQuestion->auteur)) $paramQuestion->auteur = get_fullname($USER->id_user);
							if (!empty($paramQuestion->auteur_mail)) $paramQuestion->auteur_mail = get_mail($USER->id_user);
							if (!empty($paramQuestion->ts_datecreation)) $paramQuestion->ts_datecreation =time();
							$paramQuestion->ts_datemodification = $paramQuestion->ts_datecreation;
							
							$id_question=insert_record("questions",$paramQuestion, true,'id');
							espion3("importation", "question", $paramQuestion->id_etab.".".$id_question,$paramQuestion); //rev 922
							$tab_questions[]=$id_question;
							set_ok(traduction ("info_qst_importe_ok",false,$num_qst, $paramQuestion->titre,$paramQuestion->id_etab,$id_question),$resultats);
							if ($ajout) {
								foreach ($paramReponses as $reponse) {
									$num_rep++;
									$reponse->id=$id_question;
									$reponse->id_etab = $paramQuestion->id_etab;
									$id_rep=insert_record("reponses",$reponse, true, false);  //attention a id qui n'est PAS la cl�'
									set_ok(traduction( "info_rep_importe_ok",false, $num_rep, $paramQuestion->id_etab,$id_question,$id_rep),$resultats);
								}
								$doc=new StdClass();
								$doc->id=$id_question;
								$doc->id_etab=$paramQuestion->id_etab;
								$doc->id_doc=1;
								foreach ($paramDocuments as $document) {
									$idf=$document->document;
									if (decode_document( $idf, $document->image,$id_question,$paramQuestion->id_etab )) {
										$tmp=explode('.',$idf);
										$doc->extension=$tmp[1];

										insert_record("questionsdocuments",$doc,false,false,false);

										set_ok(traduction( "info_doc_importe_ok",false, $idf, $paramQuestion->id_etab,$id_question,$doc->id_doc),$resultats);
                                        $doc->id_doc++;
									}
								}
							}


						}
					}
				}
				else {
					set_erreur (traduction ("err_xml_pfc2i_bad_format",false),$resultats);
					break; // rev 902 rat� il a oubli� le referentiel
				}
			}
		}
	}
	return $resultats;
}


/**
 * relecture des questions au format xml_pfc2i
 */


function qst_get_info($blocks){
	global $CFG, $USER;
	$info = new StdClass(); $reponses=array(); $documents=array();
	if (count($blocks) > 0) {
		foreach ($blocks as $block) {
			if (isset($block['tagData']) || isset($block['children'])){
			switch (strtoupper($block['name'])) {
				case 'AUTEUR':
					$info->auteur = $block['tagData'];
					break;
				case 'AUTEUR_MAIL':
					$info->auteur_mail = $block['tagData'];
					break;
				case 'ETABLISSEMENT':
					$info->id_etab = $block['tagData'];
					if (!$etab = get_etablissement($info->id_etab,false))
					        $info->id_etab = $USER->id_etab_perso;
					// si l'�tablissement n'est pas connu on retourne l'etablissement du connect�
					break;
				case 'DATE_CREATION':
                    // lors de l'export on envoie le timestamp
                    $info->ts_datecreation = !empty($block['tagData'])? $block['tagData']:time();
					break;
                case 'DATE_MODIFICATION':
                    // lors de l'export on envoie le timestamp
                    $info->ts_datemodification = !empty($block['tagData'])? $block['tagData']: time();
                    break;
				case 'FAMILLE':
                    // nb dans lm'export on a envoy� la familel valid�e ...
					get_famille($block['tagData'], false) ? $info->id_famille_proposee = $block['tagData'] : $info->famille_proposee = $block['tagData'];
					break;
				case 'DOMAINE':
					$info->referentielc2i = strtoupper($block['tagData']);
					break;
				case 'ALINEA':
					$info->alinea = $block['tagData'];
					break;
				case 'TITRE':
					$info->titre = $block['tagData'];
					break;

				case 'REPONSES':
					if (isset($block['children']) ) {
						$reponses = reponses_get_info($block['children']);
					}
					break;
                case 'DOCUMENTS':
                    if (isset($block['children']) ) {
                        $documents = documents_get_info($block['children']);
                    }
                    break;
				default:
					break;
			}
			}
		}
	}
	return array($info,$reponses,$documents);
}

function reponses_get_info($blocks){
	global $CFG, $USER;
	$reponses=array();
	if (count($blocks) > 0) {
		$i = 0;
		foreach ($blocks as $block) {
			switch (strtoupper($block['name'])) {
				case 'REPONSE':
					$reponses[$i]->reponse = $block['tagData'];
					break;
				case 'BONNE':
					($block['tagData'] != "OUI" && $block['tagData'] != "NON") ? $reponses[$i]->bonne = "NON" : $reponses[$i]->bonne = $block['tagData'];
					$i++;
					break;
			}
		}
	}
	return $reponses;
}

function documents_get_info($blocks){
    global $CFG, $USER;
    $documents=array();
    if (count($blocks) > 0) {
        $i = 0;
        foreach ($blocks as $block) {
            switch (strtoupper($block['name'])) {
                case 'DOCUMENT':
                    $documents[$i]->document = $block['tagData'];
                    break;
                case 'IMAGE_BASE64':
                    $documents[$i]->image= $block['tagData'];
                    $i++;
                    break;
            }
        }
    }
    return $documents;
}

/**
 * pas termin�e
 */
function from_xml_moodle($file, $type_quest) {
    global $CFG, $USER;
    $resultats=array();
    return $resultats;
}

/**
 * conversion ligne csv selon format en un objet pret � filer dans la base via insert_record
 */
function conversion_ligne($infos, $ide, $format) {
	global $CFG;

    //tres important
    /**  ce code ne fonctionne pas il ne modifie pas le tableau mais la locale $ligne ...
    foreach ($infos as $ligne)
        $ligne=vire_guillemets($ligne);
    */
     for ($i=0; $i<count($infos); $i++)
              $infos[$i]=vire_guillemets(stripslashes($infos[$i]));
              //stripslashes important si guillemets tap�s dans la liste
              // et attention � l'ordre !
	$cpt=new StdClass();
	$cpt->login=$ide."_".$infos[0];  //infos[0] ne peut pas etre vide
	$cpt->numetudiant=$infos[0]; //important pour le retrouver ensuite !
	if (!isset($infos[1])) $infos[1]="";
	if (!isset($infos[2])) $infos[2]="";
	if (!isset($infos[3])) $infos[3]="";

	switch ($format) {
		case "format_inpm":
			$cpt->nom=$infos[1];
			$cpt->prenom=$infos[2];
			$cpt->email=$infos[3];
			break;
		case "format_inmp":
			$cpt->nom=$infos[1];
			$cpt->prenom=$infos[3];
			$cpt->email=$infos[2];
			break;
		case "format_ipnm":
			$cpt->nom=$infos[2];
			$cpt->prenom=$infos[1];
			$cpt->email=$infos[3];
			break;
		case "format_ipmn":
			$cpt->nom=$infos[3];
			$cpt->prenom=$infos[1];
			$cpt->email=$infos[2];
			break;
		case "format_imnp":
			$cpt->nom=$infos[2];
			$cpt->prenom=$infos[3];
			$cpt->email=$infos[1];
			break;
		case "format_impn":
			$cpt->nom=$infos[3];
			$cpt->prenom=$infos[2];
			$cpt->email=$infos[1];
			break;
		case "format_apogee":
			$cpt->nom=$infos[1];
			$cpt->prenom=$infos[2];
			$cpt->email="";
			break;
		default :
			$cpt=false;
		break;
	}


	if ($cpt && !$CFG->compte_sans_nom_prenom_ok && (empty($cpt->nom) || empty($cpt->prenom)))
		return false;
	if ($cpt && !$CFG->compte_sans_mail_ok && empty($cpt->email) )
		return false;

	return $cpt;
}


/**
 * lecture d'un fichier d'import apogee dans le format suivant
 * on saute donc les ent�tes jusqu'a la ligne XX-APO_VALEURS-XX
 * separateur tabulation
 * apparemment pas de mail dans apogee ...
 * @return un tableau de ligne CSV numetudiant;nom;prenom
 *
 */

/*
XX-APO_TITRES-XX
apoC_annee  2005/2006
apoC_cod_dip    C2I1
apoC_Cod_Exp    88
apoC_cod_vdi    253
apoC_Fichier_Exp    C:\C2I1.TXT
apoC_lib_dip    C2i niveau 1Droit et Sc.J
apoC_Titre1 Export Apog�e du 19/06/2006 � 11:44
apoC_Titre2

XX-APO_TYP_RES-XX
18  .   ABI ABJ AC  ADCD    ADIN    ADM ADMI    ADST    AJ  AJAC    CFE DAS DEF DEM DFL EXC RED
25  .   1VAL    ABI ABJ ADIN    ADM ADMI    ADMO    ADMS    ADN ADST    AJ  AJAC    AJEL    AJOR    AUT2    CFE DAS DEF DEM DEX DFL ELIM    EXC RED
61  1VAL    ABI ABJ ADCO    ADIN    ADM ADMC    ADME    ADMI    ADMO    ADMS    ADN ADOR    ADS AFAV    AJ  AJ1E    AJEC    AJEL    AJOR    AJSE    AJTP    AKDJ    AKI AKI2    AKIC    AKID    AKIE    AKIN    AUT2    CFE DAS DD  DEF DEX DFL DRES    ELIM    EQDA    EQDE    FAV NA1E    NAAE    NAAN    NAEL    NAKI    NAKN    NAOR    NDN NDP NVAL    PAOP    POS RED RSV SAV SCI TFAV    VAL VDC VDJ
4   ABI ABJ DEF DIS
. : .   ABI : Absence injustifi�e   ABJ : Absence justifi�e AC : Admis conditionnel ADCD : Attente d�cision commission discipline   ADIN : Admis avec indulgence    ADM : Admis ADMI : Admissible   ADST : Admis sous condition de stage    AJ : Ajourn�    AJAC : Ajourn� mais acc�s autoris� � �tape sup. CFE : Certificat de fin d'�tudes    DAS : D�lib�ration en attente de soutenance DEF : D�faillant    DEM : D�mission DFL : D�faillant    EXC : Exclu RED : Redoublant
. : .   1VAL : Premi�re ann�e valid�e   ABI : Absence injustifi�e   ABJ : Absence justifi�e ADIN : Admis avec indulgence    ADM : Admis ADMI : Admissible   ADMO : Admis odontologie    ADMS : Admis sage-femme ADN : Admis ADST : Admis sous condition de stage    AJ : Ajourn�    AJAC : Ajourn� mais acc�s autoris� � �tape sup. AJEL : Ajourn� par notes �liminatoires  AJOR : Ajourn� par l'oral   AUT2 : Autoris�(e) � poursuivre en 2�me ann�e   CFE : Certificat de fin d'�tudes    DAS : D�lib�ration en attente de soutenance DEF : D�faillant    DEM : D�mission DEX : D�faillant Exclu  DFL : D�faillant    ELIM : Elimin�  EXC : Exclu RED : Redoublant
1VAL : Premi�re ann�e valid�e   ABI : Absence injustifi�e   ABJ : Absence justifi�e ADCO : Admis conditionnel   ADIN : Admis avec indulgence    ADM : Admis ADMC : ADMIS PAR COMPENSATION   ADME : Admis par validation � l'�tranger    ADMI : Admissible   ADMO : Admis odontologie    ADMS : Admis sage-femme ADN : Admis ADOR : Admis � l'oral de rattrapage ADS : Admis AFAV : Assez Favorable  AJ : Ajourn�    AJ1E : Ajourn� par 1 seule note �liminatoire    AJEC : Ajourn� par les �crits   AJEL : Ajourn� par notes �liminatoires  AJOR : Ajourn� par l'oral   AJSE : Ajourn� sans note �liminatoire   AJTP : Ajourn� par les TP   AKDJ : Acquis par D�cision du Jury  AKI : Acquis    AKI2 : Acquis � la 2�me session AKIC : Acquis par compensation  AKID : Acquis dans le dipl�me   AKIE : Acquis par �quivalence   AKIN : Admis    AUT2 : Autoris�(e) � poursuivre en 2�me ann�e   CFE : Certificat de fin d'�tudes    DAS : D�lib�ration en attente de soutenance DD : D�cision Diff�r�e  DEF : D�faillant    DEX : D�faillant Exclu  DFL : D�faillant    DRES : Des r�serves ELIM : Elimin�  EQDA : Equivalence DEUG - Ajourn�   EQDE : Equivalence DEUG - Elimin�   FAV : Favorable NA1E : Non acquis par 1 seule note �liminatoire NAAE : Non acquis avec note �liminatoire    NAAN : Non acquis avec note �liminatoire / note NAEL : Non acquis par note �liminatoire NAKI : Non acquis   NAKN : Non acquis / note �liminatoire   NAOR : Non acquis par l'oral    NDN : Non d�termin� n�gatif NDP : Non d�termin� positif NVAL : Non valid�   PAOP : Pas d'opposition POS : Possible  RED : Redoublant    RSV : R�serv�   SAV : Sans avis SCI : Scolarit� incompl�te  TFAV : Tr�s favorable   VAL : Valid�    VDC : Validation par compensation   VDJ : Validation par d�cision de Jury
ABI : Absence injustifi�e   ABJ : Absence justifi�e DEF : D�faillant    DIS : Dispense examen

XX-APO_COLONNES-XX
apoL_a01_code   Type Objet  Code    Version Ann�e   Session Admission/Admissibilit� Type R�s.           Etudiant    Num�ro
apoL_a02_nom                                            Nom
apoL_a03_prenom                                         Pr�nom
apoL_a04_naissance                                  Session Admissibilit�   Naissance
APO_COL_VAL_DEB
apoL_c0001  ELP C2I1T       2005    0   1   N   C2I1T - C2i niveau 1 Th�orie    0   1   Note
apoL_c0002  ELP C2I1T       2005    0   1   B       0   1   Bar�me
apoL_c0003  ELP C2I1P       2005    0   1   N   C2I1P - C2i niveau 1 Pratique   0   1   Note
apoL_c0004  ELP C2I1P       2005    0   1   B       0   1   Bar�me
APO_COL_VAL_FIN
apoL_c0005  APO_COL_VAL_FIN

XX-APO_VALEURS-XX
apoL_a01_code   apoL_a02_nom    apoL_a03_prenom apoL_a04_naissance  apoL_c0001  apoL_c0002  apoL_c0003  apoL_c0004
1234567 RAYNAUD PIERRE   07/05/1981

*/

function lecture_apogee ($fichier) {
    $res=array();
    $handle = fopen($fichier, "rb");
    if ($handle) {
            $in_data=false;
           while (!feof($handle))  {
                $ligne = trim(fgets ($handle,4096));
                if (!$ligne) continue;
                if (trim($ligne)=='XX-APO_VALEURS-XX' && !$in_data) {
                    $ligne=fgets($handle,4096); //sauter ligne entete esperons qu'elle y est
                    $in_data=true;
                    continue;
                }
                if (!$in_data) continue;
                $tmp=explode("\t",$ligne);
                //print_r($tmp);
                if (count($tmp) <3) continue; //lignes vides ?
                $res[]=implode(";",$tmp); //conversion csv comme les autres
                //print("*");
           }
           fclose($handle);
    } else
        erreur_fatale("err_lecture_fichier",$fichier);

    return $res;
}




/**
 * nouveau code selon specif Marseille
 * les lignes d'import peuvent d�ja contenir des notes recues d'apog�e si le candidat � d�ja pass� le test
 * dans une session pr�c�dente , il faut alors les remplacer seulement si il a pass� cet examen, sinon on les laissent


si a pass�
20 valeurs : 9 scores par domaine r�f�rentiel ramen� � 0 si note n�gative, chacun suivi du bar�me (=100), suivi du score global et de son bar�me sur 100, chaque item �tant s�par� par des tabulations
sinon
20 tabulations sans aucun autre texte (sans rien indiquer). On ne stocke pas de r�sultat � ce niveau !
*/

/*
 * @param ligne : une ligne telle que recue d'apog�e explos�e en un tableau de chaine
 * @param res   : les resultats de cet �tudiant AVEC des BLANCS pour lkes domaines non test�s
 * @return      : une ligne tabul�e compl�te a emettre
 * prerequis : le candidat DOIT etre inscrit � l'examen (non test� ici)
 */
function emet_resultats_apogee($ligne,$res) {
	global $CFG;
	$arrondi=$CFG->nombre_decimales_score;
	if ($res->score_global ==-1) {  // pas de notes pour cet examen...
		if (count($ligne)==4) {     // n'en avait pas non plus dans apog�e
			foreach ($res->tabref_score as $s) {  // emettre blanc tab 100 pour chaque referentiel
				$ligne[]="";
				$ligne[]=100;
			}
			$ligne[]="";    // score global vide
			$ligne[]=100;
		}
		//sinon on laisse la ligne telle quelle (garde anciennes notes)

	} else {
		if (count($ligne)>4) {   // avait d�ja des notes oublie les
			$tmp=array($ligne[0],$ligne[1],$ligne[2],$ligne[3]);
			$ligne=$tmp;
		}
		foreach ($res->tabref_score as $s) {  // emettre note tab 100 pour chaque referentiel, pas n�gative
			if (!empty($s)) {   // rev 843 attention aux domaines NON TESTES (probablement jamais en certif pour apog�e ?)
				if ($s <0) $s=0;
				$ligne[]=sprintf("%.{$arrondi}f",$s);
			}else
				$ligne[]="";

			$ligne[]=100;
		}
		$ligne[]=sprintf("%.{$arrondi}f",$res->score_global);    // score global jamais n�gatif
		$ligne[]=100;
	}
	return implode("\t",$ligne); // contruit la ligne tabul�e
}





/**
 * on relit le fichier d'entr�e jusqu'� la ligne XX-APO_VALEURS-XX
 * on le recopie dans fichier_sortie
 * puis on complete les lignes de donn�es avec les scores
 * ligne a ligne car peut etre tr�s gros
 * @return un tableau ok/erreurs usuel
 */

function ecriture_apogee ($idq,$ide,$fichier_entree,$fichier_sortie) {
    $resultats=array(); //�tats des op�rations
    $comptes=array();   //comptes a cr�er
    $nb=0;

	$handle1 = fopen($fichier_entree, "rb");
	$handle2 = fopen($fichier_sortie, "wb");
	$tmp=array();
	if ($handle1 && $handle2) {
		$in_data=false;
		while (!feof($handle1))  {
			$ligne = fgets ($handle1,4096);  //pas de trim !
			if (!$in_data) {
				fputs($handle2,$ligne);
				if (trim($ligne)=='XX-APO_VALEURS-XX' && !$in_data) {
					$ligne=fgets($handle1); // traiter ligne entete
					fputs($handle2,$ligne); // traiter ligne entete
					$in_data=true;
				}
			} else {
                $tmp=explode("\t",$ligne);
                if (count($tmp) <3) continue;
                $comptes[]=trim($ligne); //vire le cr de fin
			}
		}
		fclose($handle1);
        if (count($comptes)==0)
            set_erreur(traduction ("err_fichier_apogee_sans_donnee",false,$fichier_entree),$resultats);
		foreach ($comptes as $compte) {
            //retrouver notes et emettre la ligne
            $infos=explode("\t",$compte);
            $numetudiant=trim($infos[0]); //seule info vraiment requise
            if (!$numetudiant) continue;
            //print_r($infos);
            if ($cpt=get_compte_byidnumber($numetudiant,false)) {  //connu ? pas d'EF !
                if (est_inscrit_examen($idq,$ide,$cpt->login)) {
                    $res=get_resultats($idq,$ide,$cpt->login,false);  //relire depuis la BD  SANS renoter
                    //print_r($res);
                    $ligne=emet_resultats_apogee($infos,$res);  //algo Marseille
                    fputs($handle2,$ligne."\n");
                    $nb++;
                    set_ok (traduction ("info_export_de",false,$numetudiant).":".str_replace("\t"," ",$ligne),$resultats);
                    //break;
                } else set_erreur(traduction ("err_compte_pas_inscrit",false,$numetudiant,$idq,$ide),$resultats);
            }else set_erreur(traduction ("err_compte_inconnu",false,$numetudiant),$resultats);
		}
        fclose($handle2);
        if ($nb) set_ok (traduction ("info_scores_traites",false,$nb),$resultats);

	}else {
        if (!$handle1)
          set_erreur(traduction ("err_lecture_fichier",false,$fichier_entree),$resultats); //pb fichiers
       if (!$handle2)
          set_erreur(traduction ("err_ecriture_fichier",false,$fichier_sortie),$resultats); //pb fichiers
    }
    return $resultats;
}


function lecture_fichier_csv ($fichier,$format) {
	global $CFG;
	$res=array();
    //print($fichier);
    if ($format=="format_apogee") return lecture_apogee($fichier);
	$handle = fopen($fichier, "rb");
	if ($handle) {
			$contents=fread($handle,filesize($fichier));
           // rev 1038 pb avec des fichiers issus de mac
            $contents = preg_replace('/\r\n|\r/', "\n", $contents);
			$res=explode("\n",$contents);
		fclose($handle);
	} else
      erreur_fatale("err_lecture_fichier",$fichier);
	return $res;
}




function inscription_massive_csv($idq,$ide,$liste,$format,$fichier,$format_fic) {

    global $CFG;
    $resultats=array(); //�tats des op�rations
    $comptes=array();   //comptes a cr�er
    $nb=0;
    // rev 937 pas de LDAP ici
    // LDAP possible (on n'essaye PAS la connexion si ca merde ensuite il y aura erreur fatale
    // et PAS de cr�ation de comptes parasites ...
    // uniquement avec Apog�e sinon il faut passer par inscription massivs LDAP
    $utiliserLDAP=auth_ldap_init($ide) && $format=='format_apogee';
    if ($liste || $fichier) {
        if ($liste){
            $comptes=explode("\r\n",$liste); //liste de ligne
            set_ok (traduction ("info_inscription_liste",false,$format),$resultats);
        }
        if ($fichier) {
            $comptes=lecture_fichier_csv($fichier,$format_fic);// tous en m�moire dans un tableau
            set_ok (traduction ("info_inscription_fichier",false,$fichier,$format_fic),$resultats);
            $format=$format_fic; //important pour le d�coupage plus bas !!!
        }
        /**
         if (!$comptes) {
         set_erreur (traduction("err_lecture_fichier",false,$fichier),$resultats);
         return $resultats;
         }
         **/
        if (count($comptes)==0) {
            set_erreur (traduction("err_fichier_sans_donnee",false,$fichier),$resultats);
            return $resultats;
        }
        //print_r($comptes);
        //ici on a une liste de lignes TOUTES avec s�parateur point virgule et numetudiant en 1er
        //maintenant on y va
        $tags='inscription '.($format=='format_apogee'?"apogee":"csv").' '.$fichier.' '.time();
        foreach ($comptes as $compte) {
            //print $compte."<br/>";
            $compte=trim($compte); //vire les eventuels \r non trait�s par explode
            // REV 971 POUR POUVOIR UTILISER un fichier issu d'apog�e avec les tabulations !
            // convert separators , or \t to ;
            $compte = preg_replace('/\t|,/', ';', $compte);

            $infos=explode(";",$compte);
            $numetudiant=vire_guillemets(stripslashes($infos[0])); //seule info vraiment requise vire_guillemets est important !
            // print $numetudiant."<br>";
            //print_r($infos);
            if (!$numetudiant) continue;
            //print_r($infos);
            if ($cpt=get_compte_byidnumber($numetudiant,false)) {  //connu ? pas d'EF !
                if (!est_inscrit_examen($idq,$ide,$cpt->login)) {
                    $nb++;
                    inscrit_candidat($idq,$ide,$cpt->login,$tags);
                    set_ok (traduction("info_candidat_inscrit",false,$cpt->login,$ide,$idq),$resultats);
                } else
                    set_erreur (traduction("info_candidat_deja_inscrit",false,$cpt->login, $ide,$idq),$resultats);
            } else {
                /*** rev 937 plus de LDAP en inscription CSV
                 * afin de pouvoir g�rer des comptes "exterieurs"
                 * pour inscrire des comptes LDAP passer par l'option fichier dans Inscription LDAP
                 * Cons�quence : plus de cr�ation de comptes Apoage depuis LDAP !!!!
                 * rev 971 (svn 1077)
                 * support LDAP possible mais si inconnu alors passer en manuel, important pour apog�e  !
                 */
                if ($utiliserLDAP) {
                    // inconnu PF mais connu LDAP
                    if ($cpt=ldap_get_compte_byidnumber($numetudiant,$ide))  { //erreur fatale si LDAP HS, null si pas trouv�
                        $cpt->origine=($format=='format_apogee')?"apogee":"csv"; // rev 980
                        if (! cree_candidat($cpt,$ide))
                            set_erreur(traduction("err_creation_compte",$cpt->login),$resultats);
                        else {
                            set_ok (traduction("info_candidat_ldap_cree",false,$cpt->login),$resultats);
                            if (!est_inscrit_examen($idq,$ide,$cpt->login)) {
                                $nb++;
                                inscrit_candidat($idq,$ide,$cpt->login,$tags);
                                set_ok (traduction("info_candidat_inscrit",false,$cpt->login,$ide,$idq),$resultats);
                            } else // probablement impossible
                                set_erreur (traduction("info_candidat_deja_inscrit",false,$cpt->login, $ide,$idq),$resultats);
                        }
                        continue;
                    }
                }
                //manuel ou pas trouv� en LDAP
                $cpt =conversion_ligne($infos,$ide,$format);  //erreur possibles si incomplet et renvoi false....
                if ($cpt) {  //forcement sauf si incomplet et interdit par CFG
                    $cpt->auth="manuel";
                    $cpt->origine=($format=='format_apogee')?"apogee":"csv"; // rev 980
                    $cpt->password= mot_de_passe_a($CFG->longueur_mot_de_passe_aleatoire);
                    if (! cree_candidat($cpt,$ide))
                        set_erreur(traduction("err_creation_compte",false, $cpt->login),$resultats);
                    else {
                        set_ok (traduction("info_candidat_cree",false,$cpt->login),$resultats);
                        inscrit_candidat($idq,$ide,$cpt->login,$tags);  //peut pas �chouer existait pas
                        set_ok (traduction("info_candidat_inscrit",false,$cpt->login, $ide,$idq),$resultats);
                        $nb++;
                    }
                } else set_erreur (traduction ("err_compte_incomplet",false,$numetudiant),$resultats);
                // } // nouveau ldap ou non
            } //connu ou non local
        } //foreach
        if ($nb) set_ok (traduction ("info_comptes_traites",false,$nb),$resultats);
    } else set_erreur (traduction ("err_rien_a_faire"),$resultats);

    if ($format=='format_apogee')
        set_ok(traduction ("info_noter_nom_fichier_apogee",false,basename($fichier)),$resultats);
    return $resultats; // pr�s a �tre affich�s par print_details ...
}



/**
 * renvoie les m�thodes d'import de r�sulats dans un tableau d'objets
 * pr�t � l'emploi dans un select
 */
function get_import_resultats_methodes () {
    $ret= array();
    $ret[]=new option_select ('amc','Auto Multiple Choice');
    $ret[]=new option_select ('qcmdirect','QCM Direct');
    //$ret[]=new option_select ('icr','Scanner ICR');   pas encore support�e


    return $ret;
}


// Scanner ICR/formassistant
// code non test� . Semble n'avoir qu'un �tudiant par fichier ???
// donc comme il y en auar des milliers c'est inutilisable
/***********************************

function resultats_lecture_optique_ICR($idq, $ide, $file){
    $depth = array();
    $questions = array();

    function debutElement($parser, $name, $attrs)    {
        // R�cup�ration du num�ro de la question
        global $questions;
        if (isset($attrs[N]))
        $questions[] = $attrs[N];
    }

    function finElement($parser, $name)    {
        return true;
    }

    function characterData ($parser, $data)    {
        global $questions;

        if(strstr($data,"O") || strstr($data,"X"))
        $questions[] = $data;
    }

    $xml_parser = xml_parser_create();
    xml_set_element_handler($xml_parser, "debutElement", "finElement");
    xml_set_character_data_handler($xml_parser, "characterData");
    if (!($fp = fopen($file, "r")))
    return "Impossible d'ouvrir le fichier XML";


    while ($data = fread($fp, filesize ($file))) {
        if (!xml_parse($xml_parser, $data, feof($fp)))        {
            return sprintf("erreur XML : %s � la ligne %d",
                    xml_error_string(xml_get_error_code($xml_parser)),
                    xml_get_current_line_number($xml_parser));
        }
    }
    xml_parser_free($xml_parser);

    // Traitement des r�ponses

    // Fonction pour faire un strpos r�curent
    // return array
    function cherche_chaine($haystack, $needle, $position=0, $result_array=false)    {
        $position_X  = strpos($haystack, $needle, $position);

        if ($position_X)        {
            $result_array[$position_X] = true;
            return cherche_chaine($haystack, $needle, $position_X+1, $result_array);
        }
        else
        return $result_array;
    }


    for ($i=0; $i<count($questions); $i++)    {
        // Tri du tableau $questions

        // L'�l�ment de base est une question
        if (strstr($questions[$i],"q") || strstr($questions[$i],"digit"))
        $element_precedent = $questions[$i];


        // Si on a affaire � une r�ponse
        elseif ((strstr($questions[$i],"O") || strstr($questions[$i],"X")) && strstr($element_precedent,"q")){
            $positions_X = cherche_chaine($questions[$i], "X");
            $reponses[$element_precedent] = $positions_X;
        }

        // Si on a affaire � un chiffre (num�ro d'�tudiant)
        elseif ((strstr($questions[$i],"O") || strstr($questions[$i],"X")) && strstr($element_precedent,"digit")){
            $digit[$element_precedent][] = $questions[$i];
        }

    }

    // GESTION DU N� ETUDIANT
    krsort($digit);
    foreach ($digit as $value)     {
        // Teste si le num�ro d'�tudiant est lisible
        $nbre_croix = array_count_values($value);
        if ($nbre_croix[X] != 1)
        return "Le num�ro d'�tudiant n'a pas �t� saisi correctement";

        $numero_etudiant .= array_search("X", $value)+1;
    }

    $login = cherche_login($numero_etudiant, $ide);
    if (!$login)
    return "L'�tudiant n'est pas rentr� dans la base";

    $inscription_ok = cherche_inscription($login,$idq,$ide);
    if (!$inscription_ok)
    return "L'�tudiant n'est pas inscrit � cet examen";

    enregistrement_resultats_etudiant($login,$idq,$ide,$reponses,"icr",$resultats);

return true;
}
********************************************************************************/







/**
 * V 1.5 avec un fichier de donn�es au format utilis� par l'universite de versailles
 * voir wiki http://c2i.education.fr/wikipfc2i-X/images/3/3d/QCMDirect_03-Export_QCMD-PF.pdf
 * format attendu separateur tabulation
1234567 NOM1    Prenom1     "0;0;0;0;1" "1;0;0;0;0" "1;0;0;0;1" "0;0;0;1;0" "0;0;1;0;0" "0;1;0;0;0" "0;1;0;0;0" "1;0;0;0;0" "0;1;0;0;0" "0;0;1;0;0" "0;0;0;1"   "0;1;1;0;0" "0;0;0;0;1" "0;0;0;0;1" "0;0;0;1"   "0;0;0;1;0" "0;0;0;0;1" "0;0;0;0;1" "1;0;0;0;0" "1;1;0;1;0" "1;0;0;0"   "0;1;0;0;0" "0;0;0;0;1" "0;1;0;0;0" "0;0;0;0;1" "0;0;0;1;0" "1;0;0;0;0" "0;0;1;0;0" "0;0;0;0;1" "0;0;0;0;1" "0;1;1;1;0" "0;0;0;0;1" "1;0;0;0;0" "0;0;1;0"   "0;0;0;0;1" "0;0;1;0"   "1;0;1;0"   "1;0;0;0;0" "0;0;1;0;0" "1;0;0;0;0" "1;0;0;0"   "0;0;1;0;0" "0;0;0;1;0" "0;1;0;0;0" "1;0;0;0;0"
1234568 NOM2    Prenom2     "0;0;0;0;1" "1;0;0;0;0" "1;0;0;0;1" "0;0;0;1;0" "0;0;1;0;0" "0;1;0;0;0" "0;1;0;0;0" "1;0;0;0;0" "0;0;1;0;0" "0;1;0;0;0" "0;0;1;0"   "0;1;0;0;0" "0;0;1;0;0" "0;1;0;0;0" "0;0;0;1"   "0;0;0;1;0" "0;0;0;0;1" "0;0;0;0;1" "1;0;0;0;0" "1;1;0;1;0" "1;0;0;0"   "0;1;0;0;0" "0;0;0;0;1" "0;1;0;0;0" "0;0;0;0;1" "0;0;0;1;0" "1;0;0;0;0" "0;0;1;0;0" "0;0;0;0;1" "0;0;0;0;1" "0;1;1;1;0" "0;0;0;0;1" "1;0;0;0;0" "0;0;1;0"   "0;0;0;0;1" "0;0;1;0"   "1;0;1;0"   "1;0;0;0;0" "0;0;1;0;0" "1;0;0;0;0" "1;0;0;0"   "0;1;0;0;0" "0;0;0;1;0" "0;1;0;0;0" "0;0;0;1;0"
 les guillemets sont optionnels et doivent �tre vir�s
 **/

/**
 * rev 1016 traiter a part le cas d'un examen membre d'un pool !!!
 */
function resultats_lecture_optique_QCMdirect($idq, $ide, $fichier){
    $ligne=get_examen($idq,$ide); // erreur fatale si inconnu

    if ($ligne->est_pool)
          erreur_fatale('err_pas_pool');

    if ($ligne->pool_pere==0) // cas normal
        return resultats_lecture_optique_QCMdirect_normal($ligne, $fichier);
    else      //cas d'un membre d'un pool
        return resultats_lecture_optique_QCMdirect_pool($ligne, $fichier);
}

/**
 * cas normal �tait celui de la 1.5 jusqu'a la revision svn 967
 */
function resultats_lecture_optique_QCMdirect_normal($examen, $fichier){

	global $CFG;
    $idq=$examen->id_examen;
    $ide=$examen->id_etab;

	$resultats=array(); //�tats des op�rations
	$cle_examen=$ide.".".$idq;
     if ($examen->pool_pere)
        erreur_fatale('err_membre_pool',$cle_examen);
	$questions=get_questions($idq,$ide,false,false);
	$nb_questions=count($questions); //pour controle
    $nb=0; //nombre lignes trait�es

	$handle1 = fopen($fichier, "rb");
	if ($handle1) {
		while (!feof($handle1))  {
			$ligne = fgets ($handle1,4096);
			$ligne=trim($ligne); //vire les eventuels \r non trait�s par explode
			$infos=explode("\t",$ligne);
            //print_r($infos);
			$numero=vire_guillemets(stripslashes($infos[0]));
			if (!$numero) continue;
			// V�rification inscription �tudiant
			if (!$cpt=get_compte_byidnumber($numero,false)){   //connu ? pas d'EF !
				set_erreur (traduction("info_candidat_inconnu",false,$numero),$resultats);
				continue;
			}
			if (!est_inscrit_examen($idq,$ide,$cpt->login)) {
				set_erreur (traduction("info_candidat_pas_inscrit",false,$cpt->login,$ide,$idq),$resultats);
				continue;
			}
			//v�rifier pas d�ja import� ...sinon erreur de cl� plus loin
			if (count_records("resultats","examen='$cle_examen' and login='".$cpt->login."'")) {
				set_erreur (traduction("info_candidat_deja_importe",false,$cpt->login,$ide,$idq),$resultats);
			    continue;
            }
			if (count($infos) != $nb_questions+3) {
                set_erreur(traduction("err_ligne_import_incomplete",false).$ligne." ".count($infos)." ".$nb_questions,$resultats);
                continue;
            }

            $reponses=array();
            for ($num_q=0;$num_q<$nb_questions; $num_q++) {
                $reps=explode(";",vire_guillemets($infos[$num_q+3]));
                foreach($reps as $num=>$rep) {
                        $reponses["q".$num_q][$num]=$rep; //on les met toutes 0 et 1 =>pas de notice php ensuite
                }
            }

			// ENREGISTRER LES REPONSES (FONCTION COMMUNE)
			enregistrement_resultats_etudiant($cpt->login,$idq,$ide,$reponses,"qcmdirect",$resultats);
            $nb++;
		}

	}   else  erreur_fatale("err_lecture_fichier",$fichier);
    set_ok (traduction ("info_resultats_importes",false,$nb),$resultats);
	fclose($handle1);
	return $resultats;

}

/**
 * cas membre d'un pool introduit r�vision 1.5 svn 968
 */
function resultats_lecture_optique_QCMdirect_pool($examen, $fichier){

    global $CFG;

    $idq=$examen->id_examen;
    $ide=$examen->id_etab;
    $cle_examen=$ide.".".$idq;
     if (!$examen->pool_pere)
        erreur_fatale('err_pas_membre_pool',$cle_examen);
    $pere=get_examen($examen->pool_pere,$ide);  //fatale si inconnu
    $idq_pere=$pere->id_examen;
    $ide_pere=$pere->id_etab;  // ou $ide

    $resultats=array(); //�tats des op�rations

    $questions=get_questions($idq,$ide,false,false);
    $nb_questions=count($questions); //pour controle
    $nb=0; //nombre lignes trait�es

    $handle1 = fopen($fichier, "rb");
    $tags='inscription import QCMDirect pool '.$cle_examen.' '. time();
    if ($handle1) {
        while (!feof($handle1))  {
            $ligne = fgets ($handle1,4096);
            $ligne=trim($ligne); //vire les eventuels \r non trait�s par explode
            $infos=explode("\t",$ligne);
            //print_r($infos);
            $numero=vire_guillemets(stripslashes($infos[0]));
            if (!$numero) continue;
            // V�rification inscription �tudiant
            if (!$cpt=get_compte_byidnumber($numero,false)){   //connu ? pas d'EF !
                set_erreur (traduction("info_candidat_inconnu",false,$numero),$resultats);
                continue;
            }
            // si pas inscrit au p�re
            if (!est_inscrit_examen($idq_pere,$ide_pere,$cpt->login)) {
                set_erreur (traduction("info_candidat_pas_inscrit",false,$cpt->login,$ide,$idq),$resultats);
                continue;
            }
            //v�rifier pas d�ja import� ...sinon erreur de cl� plus loin
            /**  rev 1022 FAUX ne traite pas le cas ouun fichier d'import a �t� import� dans un autre membre du pool !
            if (count_records("resultats","examen='$cle_examen' and login='".$cpt->login."'")) {
                set_erreur (traduction("info_candidat_deja_importe",false,$cpt->login,$ide,$idq),$resultats);
                continue;
            }
            */
              //v�rifier pas d�ja import� dans un autre membre (ou celui la) !!!!
           if (compte_passages($idq_pere,$ide_pere,$cpt->login) >0){
                set_erreur (traduction("info_candidat_deja_importe",false,$cpt->login,$ide_pere,$idq_pere),$resultats);
                continue;
            }

             // ne devrait PAS etre d�ja inscrit a cet examen membre !!!
            if (est_inscrit_examen($idq,$ide,$cpt->login)) {
                set_erreur (traduction("info_candidat_deja_importe",false,$cpt->login,$ide,$idq),$resultats);
                continue;
            }


            // si la ligne est mal form�e surtout ne pas l'inscrire
            if (count($infos) != $nb_questions+3) {
                set_erreur(traduction("err_ligne_import_incomplete",false).$ligne." ".count($infos)." ".$nb_questions,$resultats);
                continue;
            }

            // voila la cas particulier du pool
            // le candidat ne peut pas �tre inscrit car pour la PF il ne l' pas encore pass� !
            inscrit_candidat ($idq,$ide,$cpt->login,$tags);

            $reponses=array();
            for ($num_q=0;$num_q<$nb_questions; $num_q++) {
                $reps=explode(";",vire_guillemets($infos[$num_q+3]));
                foreach($reps as $num=>$rep) {
                        $reponses["q".$num_q][$num]=$rep; //on les met toutes 0 et 1 =>pas de notice php ensuite
                }
            }

            // ENREGISTRER LES REPONSES (FONCTION COMMUNE)
            enregistrement_resultats_etudiant($cpt->login,$idq,$ide,$reponses,"qcmdirect",$resultats);
            $nb++;
        }

    }   else  erreur_fatale("err_lecture_fichier",$fichier);
    set_ok (traduction ("info_resultats_importes",false,$nb),$resultats);
    fclose($handle1);
    return $resultats;

}

/**
 * ajout� rev 969
 * cette fonction ne doit pas �tre appel�e pour un examen de type pool
 */

function resultats_lecture_optique_AMC($idq, $ide, $fichier){

	global $CFG;

	//obligatoirement tabulation car les r�ponses sont dans une colonne sous la forme 0;1;0;0;1
	$CFG->AMC_separateur="\t";

    $ligne=get_examen($idq,$ide); // erreur fatale si inconnu
    if ($ligne->est_pool)
           erreur_fatale('err_pas_pool');
    if ($ligne->pool_pere==0) // cas normal
        return resultats_lecture_optique_AMC_normal($ligne, $fichier);
    else      //cas d'un membre d'un pool
        return resultats_lecture_optique_AMC_pool($ligne, $fichier);
}

/**
 * cas normal
 * revision 986 certains tableurs modifient les noms des colonnes en mettant le 1ere lettre en majuscule
 * (nom -> Nom,  note ->Note, copie ->Copie)
 * on utilise donc dans la 'map' des cl�s converties en majuscule  
 */

function resultats_lecture_optique_AMC_normal($examen, $fichier){

	global $CFG;
    $arrondi=$CFG->nombre_decimales_score;
	$idq=$examen->id_examen;
	$ide=$examen->id_etab;

	$resultats=array(); //�tats des op�rations
	$cle_examen=$ide.".".$idq;
	if ($examen->pool_pere)
		erreur_fatale('err_membre_pool',$cle_examen);
	$questions=get_questions($idq,$ide,false,'id_etab,id');

	//tableau indic� par id de questions de cet examen
	$questionsmap=array();
	foreach($questions as $question)
	   $questionsmap [$question->id_etab.'_'.$question->id]=1;

	//nom de la colonne ou est stock� le num�ro d'�tudiant
	$colonneNumEtudiant=strtoupper($CFG->AMC_nom_colonne_numetudiant);

	$handle = fopen($fichier, "rb");
	if ($handle) {
		$ligne = vire_guillemets(trim(fgets ($handle))); // rev 975 on ne sait jamais !!!
		//analyse 1ere ligne
		$colonnes=explode($CFG->AMC_separateur,$ligne);
		$map=array();
		foreach ($colonnes as $num=>$nom)
		  $map[strtoupper($nom)]=$num;
        // verifier que toutes les questions de cet examen sont bien dans le CSV

        $q0=array(); $q1=array();
        foreach ($questionsmap as $id=>$tmp) {
            if (!isset($map[$id])) {
                   erreur_fatale (traduction("err_amc_question_non_trouvee",false,$id,$cle_examen));
                   $q0[]=$id;
            }
        	else $q1[]=$id;
        	// rev 976 et AMC >=0.326 (export des r�ponses coch�es)
        	if (!isset($map['TICKED:'.$id]))
        		erreur_fatale (traduction("err_amc_oubli_export_cases_cochees",false));

        }
        //print ("trouv�es ".print_r($q1,true));
        //print ("pas trouv�es ".print_r($q0,true));

//print_object("map",$map);
		$nb=0;
        // juste ventiler les notes d�ja calcul�es par domaine
		$noteuse= new noteuseAMC($idq,$ide);

	    // v�rification calcul AMC
	    if ($CFG->AMC_verif_score)
        	$noteuseVerif = new noteuse($idq, $ide);

		while (!feof($handle))  {
			$ligne = vire_guillemets(trim(fgets ($handle))); // rev 975 on ne sait jamais

			$valeurs=explode($CFG->AMC_separateur,$ligne);
			//print_object("",$valeurs);
			// ignorer les lignes contenant la moyenne et le max
			if (empty($valeurs[$map['NOM']]))
				continue;
			//print "nom ok";
            //attention OOo emet souvent une virgule a la plce d'un point d�cimal ...
            $score=str_replace(',','.',$valeurs[$map['NOTE']]);
            //pas empty() sinon saute les scores de 0 !
            if (!is_numeric($score))
                continue;
			//print "score ok";
			if (!empty($valeurs[$map[$colonneNumEtudiant]]))
			// tr�s important la conversion en int pour les instructions possibles
			// de cocher les cases vides par de 0  (ex 2711962 --> 02711962)
				$numero=(int) $valeurs[$map[$colonneNumEtudiant]];
			else continue;
			//print "numet ok";
             // V�rification inscription �tudiant
            if (!$cpt=get_compte_byidnumber($numero,false)){   //connu ? pas d'EF !
                set_erreur (traduction("info_candidat_inconnu",false,$numero),$resultats);
                continue;
            }
			if (!est_inscrit_examen($idq,$ide,$cpt->login)) {
                set_erreur (traduction("info_candidat_pas_inscrit",false,$cpt->login,$ide,$idq),$resultats);
                continue;
            }
            //v�rifier pas d�ja import� ...sinon erreur de cl� plus loin


            // attention AMC ne renvoie pas les coches, donc rien dans c2iresultats !
           // if (count_records("resultats","examen='$cle_examen' and login='".$cpt->login."'")) {
           if (compte_passages($idq,$ide,$cpt->login) >0){
                set_erreur (traduction("info_candidat_deja_importe",false,$cpt->login,$ide,$idq),$resultats);
                continue;
            }

			$nb++;
			$scores=array();
			foreach ($questionsmap  as $id=>$tmp) {
				//AMC ne veut pas de num�ro de question avec un point
				// on lui a donc envoy� sous la forem xx_yy
				// on remet le point pour �tre compatible avec les autres noteuses
				$idpoint=str_replace('_','.',$id);
				if (isset($map[$id])) {  //forcement
					$mapid=$map[$id];
					$mapcoches=$map['TICKED:'.$id];
					//attention � la virgule d�cimale !!!
					if (isset($valeurs[$mapid])) $scores[$idpoint]=str_replace(',','.',$valeurs[$mapid]);
					else $scores[$idpoint]=0;
					// rev 1107 il faut enregsitrer ses coches pour retrouver les r�ponses par �tudiant
					enregistrement_coches_etudiant($cpt->login,$idq,$ide,$idpoint,$valeurs[$mapcoches],'amc',$resultats);

				}
				else $scores[$idpoint]=0;
			}
            //repartition des scores par domaine
			$res= $noteuse->note_etudiant($cpt,$score,$scores);
           // print_r($res);
            enregistre_resultats($idq,$ide,$cpt->login,$res);
            $score=sprintf("%.{$arrondi}f",$res->score_global)." %";
            set_ok (traduction("info_score_importe",false,$score,$cpt->login,$ide,$idq),$resultats);

            // verification calculs par AMC identiques a nous
            if($CFG->AMC_verif_score) {
            	  $resVerif = $noteuseVerif->note_etudiant($cpt);
            	  if (meme_resultats($res,$resVerif))
            	  	set_ok(traduction("info_scores_identiques",false,$cpt->login,'pf','amc'),$resultats);
            	  else
            	   	set_erreur(traduction("info_scores_non_identiques",false,$cpt->login,'pf','amc'),$resultats);
            }


			unset($scores); // sauve mem
			unset($res);
            unset($valeurs);
		}
         set_ok (traduction ("info_resultats_importes",false,$nb),$resultats);

		fclose($handle);
	} else
		erreur_fatale("err_lecture_fichier",$fichier);

	return $resultats;
}

/**
 * cas pool
 * revision 986 certains tableurs modifient les noms des colonnes en mettant le 1ere lettre en majuscule
 * (nom -> Nom,  note ->Note, copie ->Copie)
 * on utilise donc dans la 'map' des cl�s converties en majuscule  
 */

function resultats_lecture_optique_AMC_pool($examen, $fichier){

    global $CFG;
    $arrondi=$CFG->nombre_decimales_score;
    $idq=$examen->id_examen;
    $ide=$examen->id_etab;

    $resultats=array(); //�tats des op�rations
    $cle_examen=$ide.".".$idq;
    if (!$examen->pool_pere)
        erreur_fatale('err_pas_membre_pool',$cle_examen);

    $pere=get_examen($examen->pool_pere,$ide);  //fatale si inconnu
    $idq_pere=$pere->id_examen;
    $ide_pere=$pere->id_etab;  // ou $ide

    $questions=get_questions($idq,$ide,false,'id_etab,id');

    //tableau indic� par id de questions de cet examen
    $questionsmap=array();
    foreach($questions as $question)
       $questionsmap [$question->id_etab.'_'.$question->id]=1;

    //nom de la colonne ou est stock� le num�ro d'�tudiant
    $colonneNumEtudiant=strtoupper($CFG->AMC_nom_colonne_numetudiant);

    $handle = fopen($fichier, "rb");
    if ($handle) {
        $ligne = vire_guillemets(trim(fgets ($handle)));
        //analyse 1ere ligne
        $colonnes=explode($CFG->AMC_separateur,$ligne);
        $map=array();
        foreach ($colonnes as $num=>$nom)
          $map[strtoupper($nom)]=$num;

         // verifier que toutes les questions de cet examen sont bien dans le CSV
         $q0=array(); $q1=array();
        foreach ($questionsmap as $id=>$tmp) {
            if (!isset($map[$id])) {
                   erreur_fatale (traduction("err_amc_question_non_trouvee",false,$id,$cle_examen));
                   $q0[]=$id;
            }
        	else $q1[]=$id;
        	// rev 976 et AMC >=0.326 (export des r�ponses coch�es)
        	if (!isset($map['TICKED:'.$id]))
        		erreur_fatale (traduction("err_amc_oubli_export_cases_cochees",false));

        }
        //print ("trouv�es ".print_r($q1,true));
        //print ("pas trouv�es ".print_r($q0,true));


        $nb=0;
        // juste ventiler les notes d�ja calcul�es par domaine
        $noteuse= new noteuseAMC($idq,$ide);
    	// v�rification calcul AMC
	    if ($CFG->AMC_verif_score)
        	$noteuseVerif = new noteuse($idq, $ide);
        $tags='inscription import AMC pool '.$cle_examen.' '. time();

        while (!feof($handle))  {
            $ligne = vire_guillemets(trim(fgets ($handle)));

            $valeurs=explode($CFG->AMC_separateur,$ligne);
            if (empty($valeurs[$map['NOM']]))
                continue;
            //attention OOo emet souvent une virgule a la plce d'un point d�cimal ...
            $score=str_replace(',','.',$valeurs[$map['NOTE']]);
            //pas empty() sinon saute les scores de 0 !
            if (!is_numeric($score))
                continue;
            if (!empty($valeurs[$map[$colonneNumEtudiant]]))
            // tr�s important la conversion en int pour les instructions possibles
			// de cocher les cases vides par de 0  (ex 2711962 --> 02711962)
                $numero=(int) $valeurs[$map[$colonneNumEtudiant]];
            else continue;
             // V�rification inscription �tudiant
            if (!$cpt=get_compte_byidnumber($numero,false)){   //connu ? pas d'EF !
                set_erreur (traduction("info_candidat_inconnu",false,$numero),$resultats);
                continue;
            }
            // doit �tre inscrit au pool p�re (pas encore � ce membre !)
            if (!est_inscrit_examen($idq_pere,$ide_pere,$cpt->login)) {
                set_erreur (traduction("info_candidat_pas_inscrit",false,$cpt->login,$ide,$idq),$resultats);
                continue;
            }
            //v�rifier pas d�ja import� dans un autre membre (ou cleui la) !!!!
            // attention AMC ne renvoie pas les coches, donc rien dans c2iresultats !
           // if (count_records("resultats","examen='$cle_examen' and login='".$cpt->login."'")) {
           if (compte_passages($idq_pere,$ide_pere,$cpt->login) >0){
                set_erreur (traduction("info_candidat_deja_importe",false,$cpt->login,$ide_pere,$idq_pere),$resultats);
                continue;
            }

             // voila la cas particulier du pool
            // le candidat ne peut pas �tre inscrit car pour la PF il ne l' pas encore pass� !
            // donc on l'inscrit maintenant
            inscrit_candidat ($idq,$ide,$cpt->login,$tags);

            $nb++;
            $scores=array();
            foreach ($questionsmap  as $id=>$tmp) {
            	//AMC ne veut pas de num�ro de question avec un point
				// on lui a donc envoy� sous la forem xx_yy
				// on remet le point pour �tre compatible avec les autres noteuses
				$idpoint=str_replace('_','.',$id);

                if (isset($map[$id])) {  //forcement
                    $mapid=$map[$id];
                    $mapcoches=$map['TICKED:'.$id];
                    //attention � la virgule d�cimale !!!
                    if (isset($valeurs[$mapid])) $scores[$idpoint]=str_replace(',','.',$valeurs[$mapid]);
                    else $scores[$idpoint]=0;
                    // rev 1107 il faut enregsitrer ses coches pour retrouver les r�ponses par �tudiant
					enregistrement_coches_etudiant($cpt->login,$idq,$ide,$idpoint,$valeurs[$mapcoches],'amc',$resultats);
                }
                else $scores[$idpoint]=0;
            }
            //repartition des scores par domaine
            $res= $noteuse->note_etudiant($cpt,$score,$scores);
            //print_r($res);
           enregistre_resultats($idq,$ide,$cpt->login,$res);
            $score=sprintf("%.{$arrondi}f",$res->score_global)." %";
            set_ok (traduction("info_score_importe",false,$score,$cpt->login,$ide,$idq),$resultats);


             // verification calculs par AMC identiques a nous
            if($CFG->AMC_verif_score) {
            	  $resVerif = $noteuseVerif->note_etudiant($cpt);
            	  if (meme_resultats($res,$resVerif))
            	  	set_ok(traduction("info_scores_identiques",false,$cpt->login,'pf','amc'),$resultats);
            	  else
            	   	set_erreur(traduction("info_scores_non_identiques",false,$cpt->login,'pf','amc'),$resultats);
            }

            unset($scores); // sauve mem
            unset($res);
            unset($valeurs);
        }
         set_ok (traduction ("info_resultats_importes",false,$nb),$resultats);

        fclose($handle);
    } else
        erreur_fatale("err_lecture_fichier",$fichier);

    return $resultats;
}


/**
 * rev 976
 * fonction sp�cifique AMC  enregistre les coches d'un candidat a une question '
 * recue sous la forme 0;1;0;1;... (dans l'ordre exact d'emission des r�ponses (par num, non m�lang�es))
 * n�cessaire pour pouvoir ensuite consulter ses r�sulatst et son corrig�
 * TODO simplifier les divers scanners en passant toujours par ici
 * au passage on pourra alors ignorer les scores calcul�s par AMC et les calculer nous m�me
 * @param $login login du candidat
 * @param $idq , $ide identifiants num�riques de l'examen
 * @param $qid identifiant de la question sous la form etab.id
 * @param  $reponses_etudiant
 * @param $origine
 * @param $resultats
 */
function enregistrement_coches_etudiant($login,$idq,$ide,$qid,$reponses_etudiant,$origine,&$resultats){
    global $REMOTE_ADDR,$CFG;

    $ligne=new StdClass();
    $ligne->login=$login;
    $ligne->examen=$ide.".".$idq;
    $ligne->question=$qid;
    $ligne->origine=$origine;
    $ligne->ts_date=time();
    $ligne->ip=$REMOTE_ADDR;
    $question=get_question_byidnat($qid);
    //meme ordre (par num ) que lors de l'emission
    $reponses=get_reponses($question->id,$question->id_etab,false,false);

    $coches=explode(";",$reponses_etudiant);
    $num_r=0;
    foreach ($reponses as $reponse) {
    	if (!empty($coches[$num_r])) {
    		 $ligne->reponse=$reponse->num;
			 insert_record("resultats",$ligne,false);
    	}
    	$num_r++;
    }
}


/**
 * fonction commune au differents scanners sauf AMC qui renvoie des scores et pas des cases coch�es
 * on garni la table c2iresultats comme si il avait pass� l'examen normalement sur la plateforme
 * puis on le 'renote' pour transf�rer ses r�sultats dans les 4 tables
 */
function enregistrement_resultats_etudiant($login,$idq,$ide,$reponses_etudiant,$origine,&$resultats){
    global $REMOTE_ADDR,$CFG;
    $arrondi=$CFG->nombre_decimales_score;
    $num_q = 0;
     //dans quel ordre reviennent elles ? est-ce le meme que lors de l'emission ???
     // oui si les parametres melenge_questions,melange_reponse sont vides dans codes/examen/exportQCMDirect
     // ce qui doit �tre le cas !
    $questions=get_questions($idq,$ide,false,false);
    $ligne=new StdClass();
    $ligne->login=$login;
    $ligne->examen=$ide.".".$idq;
    $ligne->origine=$origine;
    $ligne->ts_date=time();
    $ligne->ip=$REMOTE_ADDR;
    foreach ($questions as $ligne_q) {

            //meme ordre (par num ) que lors de l'emission
            $reponses=get_reponses($ligne_q->id,$ligne_q->id_etab,false,false);
            $num_r = 0;
            $ligne->question=$ligne_q->id_etab.".".$ligne_q->id;
            foreach($reponses as $ligne_r) {
	            if (isset($reponses_etudiant["q".$num_q][$num_r])) {
		            if ($reponses_etudiant["q".$num_q][$num_r]) {  //seulement les coch�es
			            $ligne->reponse=$ligne_r->num;
			            insert_record("resultats",$ligne,false);

		            }
	            }
                // nb de r�ponses ne corresponds pas (impossible sauf avec des fichiers de tests ou erreur d'import ?)
                else set_erreur (traduction("err_nb_reponses_qcmdirect",false,($num_r+1),$ligne->question),$resultats);
                $num_r++;
            }
            $num_q++;
    }
    $res=note_examen($idq,$ide,QCM_NORMAL,$login,false,false,false,false);
    $score=sprintf("%.{$arrondi}f",$res->score_global)." %";
    set_ok (traduction("info_score_importe",false,$score,$login,$ide,$idq),$resultats);
}

