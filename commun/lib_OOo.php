<?php


/**
 * @author Patrick Pollet
 * @version $Id: lib_OOo.php 1099 2010-06-20 19:35:55Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/*
 * taches de maintenance
 * n'est pas charg�es dans les pages courantes
 * utiliser require_once quand n�cessaire
 */
if (is_admin()) { //utilisateur courant uniquement
    maj_bd_OOo();
}

function maj_bd_OOo() {
    global $CFG, $USER;
}

//repare un bug dans php2odt qui a un pb quand un texte contient un symbole $
// ce qui perturbe esnuite le parser XML d'OOo
function trim_OOo($string) {
    $string = fix_special_chars(trim($string)); // rev 975 m�nage caract�res cp-1252
    $string = str_replace('$', '{dollar}', $string);
    //  $string=str_replace ('>','\&gt;',$string);
    return $string;
}

/**
 * rev 973
 * export d'un examen en OOo writer
 * @param
 * @param
 * @param $mode QCM_CORRIGE ou QCM_NORMAL
 */

function examen_to_OOo($idq, $ide, $mode) {

    global $CFG;

    $ligne = get_examen($idq, $ide);
    $nom_examen = nom_complet_examen($ligne);

    // Make sure you have Zip extension or PclZip library loaded
    require_once ($CFG->chemin_commun . '/OOo/odtphp/library/odf.php');

    if ($mode != QCM_CORRIGE)
        $filename = '/templates2/OOo/examen_imprimable.odt';
    else
        $filename = '/templates2/OOo/examen_corrige.odt';

    //essayer d'utiliser un mod�le local
    if (file_exists($CFG->chemin . '/locale' . $filename))
        $odf = new odf($CFG->chemin . '/locale' . $filename);
    else // si pas trouv� alors standard
        $odf = new odf($CFG->chemin . $filename);

    $odf->setVars('nom_examen', $ligne->nom_examen);

    $odf->setVars('date_examen', date_examen($ligne));
    if ($duree = duree_examen($ligne))
        $odf->setVars('duree_examen', '(' .
        $duree . ' mn)');
    else
        $odf->setvars('duree_examen', '');

    if ($mode != QCM_CORRIGE)
        $odf->setVars('message', traduction('texte_choix_multiple'));
    else
        $odf->setVars('message', '');

    //pas de m�lange !
    $questions = get_questions($idq, $ide, false, 'id_etab,id');
    
   // print_r($questions); die();
    
    
    $compteur_ligne = 1;

    $row = $odf->setSegment('question');

    $images = array (
        'jpg',
        'jpeg',
        'png',
        'gif'
    ); //gif non reconnu par latex

    foreach ($questions as $ligne_q) {
        //$row->numero($compteur_ligne++);
        //$row->texte($ligne_q->titre);
        $row->setVars('numero', $compteur_ligne++);
        $row->setVars('texte', trim_OOo($ligne_q->titre));
        $docs = get_documents($ligne_q->id, $ligne_q->id_etab, false);
        if (!empty ($docs)) {
            foreach ($docs as $doc) {
                //print_r($doc); print('<br/>');
                if (in_array($doc->extension, $images)) {
                    $dir = get_document_location($ligne_q->id, $ligne_q->id_etab);
                    $fic = $dir . '/' . $doc->id_doc . '.' . $doc->extension;
                    if (file_exists($fic)) {
                        $nomFic = $ligne_q->id_etab . '_' . $ligne_q->id . '_' . $doc->id_doc . '.' . $doc->extension;
                        copier_element($fic, $dir . '/' . $nomFic);
                        $row->setImage('image', $dir . '/' . $nomFic);
                        break; //une seule
                    }
                }
            }

        } else
            $row->setVars('image', '');
        $lettre_r = ord("A");
        $num_r = 1;
        $reponses = get_reponses($ligne_q->id, $ligne_q->id_etab, false, false);
        foreach ($reponses as $ligne_r) {
            if ($mode == QCM_CORRIGE)
                if ($ligne_r->bonne == 'OUI')
                    $row->reponse->setImage('case', $CFG->chemin_images .
                    "/i_valide_a.gif");
                else
                    $row->reponse->setImage('case', $CFG->chemin_images .
                    "/i_croix_a.gif");

            if ($CFG->numerotation_reponses == 2)
                $row->reponse->lettre(chr($lettre_r++));
            else
                $row->reponse->lettre($num_r++);
            $row->reponse->reponse(trim_OOo($ligne_r->reponse));

            $row->reponse->merge();
        }

        $row->merge();

    }
    $odf->mergeSegment($row);
    return $odf;

}

function listeemargement_OOo($idq, $ide) {
    global $CFG;

    $ligne = get_examen($idq, $ide);
    $nom_examen = nom_complet_examen($ligne);

    // Make sure you have Zip extension or PclZip library loaded
    require_once ($CFG->chemin_commun . '/OOo/odtphp/library/odf.php');

    $filename = '/templates2/OOo/liste_emargement.odt';

    //essayer d'utiliser un mod�le local
    if (file_exists($CFG->chemin . '/locale' . $filename))
        $odf = new odf($CFG->chemin . '/locale' . $filename);
    else // si pas trouv� alors standard
        $odf = new odf($CFG->chemin . $filename);

    $odf->setVars('titre', traduction('liste_emargement'));
    $odf->setVars('nom_examen', $ligne->nom_examen);
    $odf->setVars('date_examen', date_examen($ligne));
    if ($duree = duree_examen($ligne))
        $odf->setVars('duree_examen', '(' .
        $duree . ' mn)');
    else
        $odf->setvars('duree_examen', '');

    $odf->setVars('t_numero', traduction('t_numero'));
    $odf->setVars('t_nom', traduction('t_nom'));
    $odf->setVars('t_prenom', traduction('t_prenom'));
    $odf->setVars('t_numetud', traduction('t_numetud'));
    $odf->setVars('t_signature', traduction('t_signature'));

    // liste des inscrits � l'examen

    $inscrits = get_inscrits($idq, $ide, 'nom,prenom');
    $compteur_ligne = 1;
    $nb_inscrits = count($inscrits);

    $candidat = $odf->setSegment('candidat');
    foreach ($inscrits as $inscrit) {
        $candidat->numero($compteur_ligne++);
        $candidat->nom($inscrit->nom);
        $candidat->prenom($inscrit->prenom);
        $candidat->numetudiant($inscrit->numetudiant);
        $candidat->signature('');
        $candidat->merge();

    }
    $odf->mergeSegment($candidat);
    return $odf;
}

/**
 * @param
 * @param
 * @param  $type  0 tous 1 absents
 */

function liste_inscrits_to_OOo($idq, $ide, $type) {
    global $CFG;
    require_once($CFG->chemin_commun .'/lib_resultats.php');
    // 1ere ligne csv a traduire
    $entete_csv = array (
        "t_examen",
        "t_id",
        "t_nom",
        "t_prenom",
        "t_mdp",
        "t_numetud",
        "t_mail",
        "t_score",
        "t_date",
        "t_ip"
    );
    // ligne suivantes les noms des attributs dans $ligne dans cet ordre
    $ligne_csv = array (
        "examen",
        "login",
        "nom",
        "prenom",
        "password",
        "numetudiant",
        "email",
        "score",
        "date",
        "ip_max"
    );

    global $CFG;
    if ($type == 0) {
        $filename_ods = "inscrits_" . $ide . "_" . $idq . ".ods";
        $feuille = "inscrits";
    } else {
        $filename_ods = "absents_" . $ide . "_" . $idq . ".ods";
        $feuille = "absents";
    }
    require_once ($CFG->chemin_commun . '/OOo/odslib.class.php');
    /// Creating a workbook
    $workbook = new MoodleODSWorkbook("-");
    /// Send HTTP headers
    $workbook->send($filename_ods);
    /// Creating the first worksheet
    $myods = & $workbook->add_worksheet($feuille);
    $row = 0;
    $col = 0;
    foreach ($entete_csv as $e) {
        $myods->write_string($row, $col++, traduction($e, false));
    }
    $row = 1;
    // liste des inscrits � l'examen
    $lignes = get_inscrits($idq, $ide, 'nom,prenom');
    $cle = $ide . "_" . $idq; //ajout�e au csv
    foreach ($lignes as $ligne) { // V 1.41
        $cp = compte_passages($idq, $ide, $ligne->login);
        if ($type == 1 && $cp > 0)
            continue;

        if ($ligne->auth == 'manuel') {
            // rev 944 ne pas montrer les pwd meme md5 des utilisateurs personnels
            if (!$CFG->montrer_password_inscrits || $ligne->genre == 'P')
                $ligne->password = '********';
        } else
            $ligne->password = $ligne->auth;

        if ($res = get_resultats_examen($idq, $ide, $ligne->login)) {
            $ligne->score = $res->score;
            // $ligne->examen=$res->examen;
            $ligne->ip_max = $res->ip_max;
            //retouche origine notes �cran et csv 21/05/2009
            if ($res->origine) {
                $ligne->ip_max = $res->origine;
            }
            $ligne->date = userdate($res->date, 'strftimedatetimeshort');

        } else {
            $ligne->score = $ligne->date = $ligne->ip_max = ""; //$ligne->examen="";
        }
        $ligne->examen = $cle;
        $pos = 0;
        foreach ($ligne_csv as $col) {
            switch ($col) {
                case 'score' :
                    $myods->write_number($row, $pos++, $ligne-> $col);
                    break;
                case 'date_xxx' : //marche pas
                    $myods->write_date($row, $pos++, $ligne-> $col);
                    break;
                default :
                    $myods->write_string($row, $pos++, $ligne-> $col);
                    break;
            }
        }
        $row++;
    }

    $full_filename_ods = $workbook->close(); // fait le zip et dire ou il l'a mis

    return $full_filename_ods;

}

/**
 * rev 973 cr�e les fichiers ods et csv et renvoie leur nom complet
 */
function resultats_synthetiques_to_OOo ($idq,$ide) {
    global $CFG;
    require_once ($CFG->chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
    require_once($CFG->chemin_commun .'/lib_resultats.php');

    //fait tout le travail html+csv+ods mais n'affiche pas le template ;-)
    $tmptpl=return_tableau_resultats_examen($ide, $idq ,"synthetiques",0,0);

    $filename="synthetiques_".$ide."_".$idq;
    return array(
             "{$CFG->chemin_ressources}/tmp/ods/".$filename.'.ods',
             "{$CFG->chemin_ressources}/csv/".$filename.'.csv');

}

/**
 * probleme avec les pools
 */
function resultats_complets_to_OOo ($idq,$ide) {
    global $CFG;
    require_once ($CFG->chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
    require_once($CFG->chemin_commun .'/lib_resultats.php');

    $tmptpl=return_tableau_resultats_examen($ide, $idq ,"complets",0,0);

    $filename="resultats_".$ide."_".$idq;
    return array(
             "{$CFG->chemin_ressources}/tmp/ods/".$filename.'.ods',
             "{$CFG->chemin_ressources}/csv/".$filename.'.csv');

}



/**
 * archive un examen comme un zip (questions, corrigé, resultats ...)
 * @author ppollet
 * @param $ligne : un record de la table c2iexamen
 * @return nom du fichier créé dans chemin_ressources/archives
 *
 */
function archiver_examen ($ligne) {
    
    global $CFG; 
    
    $ide = $ligne->id_etab;
    $idq = $ligne->id_examen;
    
    
    $dir = $CFG->chemin_ressources . '/tmp/';
    cree_dossier_si_absent($dir);
    
    $dir .= time();
    cree_dossier_si_absent($dir);
    
    
    if ($ligne->est_pool == 1) {
        //pas le choix
        $groupes = liste_groupe_pool($idq, $ide);
        // TODO envoyer dans le CSV plus d'info en cas de pool ($ide, $idq ,$indice?)
        foreach ($groupes as $groupe) {
            $odf = examen_to_OOo($groupe->id_examen, $groupe->id_etab, QCM_PREVISUALISATION);
            $odf->saveToDisk($dir . "/examen_{$groupe->id_etab}_{$groupe->id_examen}.odt");
    
            $odf = examen_to_OOo($groupe->id_examen, $groupe->id_etab, QCM_CORRIGE);
            $odf->saveToDisk($dir . "/corrige_examen_{$groupe->id_etab}_{$groupe->id_examen}.odt");
        }
    } else {
        $odf = examen_to_OOo($idq, $ide, QCM_PREVISUALISATION);
        $odf->saveToDisk($dir . "/examen_{$ide}_{$idq}.odt");
    
        $odf = examen_to_OOo($idq, $ide, QCM_CORRIGE);
        $odf->saveToDisk($dir . "/corrige_examen_{$ide}_{$idq}.odt");
    }
    
   // print 1;
    
    $filename = liste_inscrits_to_OOo($idq, $ide, 0);
    copy($filename, $dir . '/' . basename($filename));
    unlink($filename);
    //print 2;
    $filename = liste_inscrits_to_OOo($idq, $ide, 1);
    copy($filename, $dir . '/' . basename($filename));
    unlink($filename);
    //print 3;
    list ($filename_ods, $filename_csv) = resultats_synthetiques_to_OOo($idq, $ide);
    copy($filename_ods, $dir . '/' . basename($filename_ods));
    unlink($filename_ods);
    copy($filename_csv, $dir . '/' . basename($filename_csv));
    unlink($filename_csv);
    //print 4;
    if ($ligne->est_pool == 1) {
        //pas le choix
        $groupes = liste_groupe_pool($idq, $ide);
        // TODO envoyer dans le CSV plus d'info en cas de pool ($ide, $idq ,$indice?)
        foreach ($groupes as $groupe) {
            list ($filename_ods, $filename_csv) = resultats_complets_to_OOo($groupe->id_examen, $groupe->id_etab);
            copy($filename_ods, $dir . '/' . basename($filename_ods));
            unlink($filename_ods);
            copy($filename_csv, $dir . '/' . basename($filename_csv));
            unlink($filename_csv);
        }
    } else {
        list ($filename_ods, $filename_csv) = resultats_complets_to_OOo($idq, $ide);
        copy($filename_ods, $dir . '/' . basename($filename_ods));
        unlink($filename_ods);
        copy($filename_csv, $dir . '/' . basename($filename_csv));
        unlink($filename_csv);
    }
    //print 5;
    // fichier zip final
    $filename = "archive_{$ide}_{$idq}.zip";
    //supprimer eventuel ancien
    if (file_exists($CFG->chemin_ressources . '/archives/'.$filename))
    unlink ($CFG->chemin_ressources . '/archives/'.$filename);
    
    zip_dossier($dir, '.', "../../archives/" . $filename);
    supprimer_dossier($dir); // cleanup the temp directory
    
    espion2("archivage", "examen", $ide . "." . $idq);
    return $filename;
}


class OdsExporter {
    // 1ere ligne csv a traduire
    var  $entete_csv = array ();
    // ligne suivantes les noms des attributs dans $ligne dans cet ordre
    var  $ligne_csv = array ();
    // rev 948 conversion num�rique du score
    var  $ligne_cvt = array ();
    var $filename_ods='';
    var $feuille='';
    var $myods;
    var $workbook;

    function OdsExporter ($filename_ods,$feuille,$entete_csv,$ligne_csv,$ligne_cvt) {

        global $CFG;
        require_once ($CFG->chemin_commun . '/OOo/odslib.class.php');
        $this->filename_ods=$filename_ods;
        $this->feuille=$feuille;
        $this->entete_csv=$entete_csv;
        $this->ligne_csv=$ligne_csv;
        $this->ligne_cvt=$ligne_cvt;




        /// Creating a workbook
        $this->workbook = new MoodleODSWorkbook("-");
        /// Send HTTP headers
        $this->workbook->send($this->filename_ods);
        /// Creating the first worksheet
        $this->myods = & $this->workbook->add_worksheet($this->feuille);
        $this->row = 0;
        if (!empty($this->entete_csv)) {
            $col = 0;
            foreach ($this->entete_csv as $e) {
                $this->myods->write_string($this->row, $col++, traduction($e, false));
            }
            $this->row = 1;
        }
    }

    function add_ligne($ligne) {

        foreach ($this->ligne_csv as $pos=>$col) {
            if ($this->ligne_cvt [$pos])
                $this->myods->write_number($this->row, $pos, ($ligne-> $col));
            else
                $this->myods->write_string($this->row, $pos, $ligne-> $col);
        }
        $this->row++;
    }

    function close () {
        $full_filename_ods = $this->workbook->close(); // fait le zip et dire ou il l'a mis
        return $this->filename_ods;
    }
}
