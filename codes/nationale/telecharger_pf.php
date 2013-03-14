<?php

/**
 * @author Patrick Pollet
 * @version $Id: telecharger_pf.php 1307 2012-09-21 16:14:18Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	Page de t�l�chargement
//ce code s'execute uniquement sur la nationale' .
 //       'donc ne pas diffuser aux locales
//
////////////////////////////////

set_time_limit(0);
$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres
require_login('P'); //PP
v_d_o_d("plpt");

$quoi=required_param("quoi",PARAM_ALPHA);

if ($quoi !='p' && $quoi !='c' && $quoi !='pc' && $quoi !='maj' )
    erreur_fatale( "err_param_requis");





require_once("lib_telechargements.php");

require_once( $chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates

$tpl = new C2IMiniPopup();	//cr�er une instance
//inclure d'autre block de templates

$fiche=<<<EOF
<form name="monform" id="monform" action="$CFG->chemin_commun/send_csv.php">
<input type="hidden" name="idf" value="{idf}"/>
<input type="hidden" name="ide" value="{ide}"/>
<input type="hidden" name="dir" value="telechargement"/>
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->

<div class="information">
{info_telecharger}<br/>
{bouton_telecharger} {bouton:annuler}
</div>
</form>
EOF;

$tpl->assignInclude("contenu",$fiche,T_BYVAR);	// le template g�rant la configuration



$tpl->prepare($chemin);

$tpl->assign("titre_popup" , traduction("telechargement"));


//creation du fichier
$ide=$USER->id_etab_perso;

$sqlfin = ""; // requ�te finale � ajouter

// rev 908 CFG->version contient aussi le num�ro de r�vision subversion
$ver=return_version_majeure_pf();


// rev 971 chaque nationale 'connait' son type de c2i et l'envoie au local dans c2iconfig (non modifiable)
$source=$CFG->chemin_ressources.'/telechargement/'.$CFG->c2i.".pf/$ver/plate-forme/";
//print $source;
if (!is_dir($source)) {
    erreur_fatale ("err_lecture_fichier",$source);
}

$repertoire_univ=$CFG->chemin_ressources."/telechargement/".$ide;


$fichier_zip="{$CFG->c2i}_".$quoi.".zip";

supprimer_dossier($repertoire_univ);
cree_dossier_si_absent($repertoire_univ);

$destination=$repertoire_univ."/{$CFG->c2i}" ; //TODO parametrer
cree_dossier_si_absent($destination);
copier_dossier($source,$destination);

menage_locale ($destination,$quoi);
if ($quoi=="maj") {
    $cpt=get_utilisateur($USER->id_user);
    if ($cpt->limite_positionnement=='O')
        menage_locale($destination, "c");
}
//rev 1026 selon le type de C2i niveau1, niveau 2 ...
personnalise_plateforme($destination,$quoi);


if ($quoi !="maj") {


	gen_constantes($destination,"constantes_dist_v15.php");

	$fp = fopen($destination."/installation/initbase.php", "w");
	if (!$fp)
		erreur_fatale ("err_ecriture_fichier",$destination."/installation/initbase.php");

	$prologue=<<<EOP
		<?php
set_time_limit(0);

//la connexion a la base

error_reporting(53);


EOP;
fwrite ($fp,$prologue);

/**
 * todo
 * etre moins bavard dans les inserts
 * donner les nb de lignes ajout�es ou des ...
 * bug avc chemin_ressources envoy� par la nationale !!!
 */

//cr�ation de la base  NON c'est aux locaux de la faire
 /*****************************************************
  $inserts=gen_db_create("test");
  $sqls=explode(";",$inserts);
  foreach($sqls as $sql)
  if (trim($sql) )
  fwrite ($fp,  gen_php_sql($sql));
  ******************************************************/
//creation des tables
$result = mysql_listtables ($ines_base);

$cnt=mysql_num_rows ($result);
for ($i=0;$i<$cnt;$i++) {
	$tb_names[$i] = mysql_tablename ($result, $i);
	fwrite($fp,"\necho \"creation de la table $tb_names[$i].<br>\";\n");
	$inserts=gen_create_table($tb_names[$i]);
	$sqls=explode(";",$inserts);
	foreach($sqls as $sql)
	if (trim($sql) )
		fwrite ($fp,  gen_php_sql($sql));
}

 $loginsl=addslashes($USER->id_user); // rev 984..
//////////////////////////////////////////////////////////////////
//creation des donnees  apr�s !
//////////////////////////////////////////////////////////////////
for ($i=0;$i<$cnt;$i++) {
     fwrite($fp,"\necho \"remplissage  $tb_names[$i].<br>\";\n");
	//lecture de la table
	switch($tb_names[$i]){
	//on n'envoie des donn�es que pour celles ci :

		case "c2ietablissement" :
		case "c2iprofils" :
		case "c2ireferentiel" :
		case "c2ialinea" :
		case "c2ifamilles" :
		//case "c2inotions" :
		case "c2iressources" :
        //case "c2iliens" :
        //ajout 14/02/2013
        case 'c2ievents' :    

			$inserts= gen_insert_data($tb_names[$i]);
			$sqls=explode("\n",$inserts);
			foreach($sqls as $sql)
			if (trim($sql) )
				fwrite ($fp,  gen_php_sql($sql));
			break;


		case "c2iquestions" :
			$liste_q=array();   // envoyer seulement les r�ponses et documents associ�s
            // avec les questions qui sont maintenant dans les DEUX PF il faut faire tres attention aux cl�s dupliqu�es !
            // donc g�rer les 3 cas a part !!!!   rev 869
			if ($quoi=="p") {
				$criteres='SELECT * FROM ' . PMA_backquote($ines_base) . '.' . PMA_backquote($tb_names[$i]). " where POSITIONNEMENT='oui'  and etat='valid�e' order by id_etab,id";
				if ($CFG->nombre_questions_a_envoyer_pos>0) $criteres .=" limit 0,".$CFG->nombre_questions_a_envoyer_pos;
                $questions=get_records_sql($criteres);
				foreach ($questions as $q) {
					$liste_q[]= "( id=$q->id and id_etab=$q->id_etab )";




				}
				$inserts= gen_insert_data($tb_names[$i],$criteres);
				$sqls=explode("\n",$inserts);
				foreach($sqls as $sql)
				if (trim($sql) )
					fwrite ($fp,  gen_php_sql($sql));
				foreach ($questions as $q) {   //copie el XML et les eventuels documents
					$cleq=$q->id_etab."_".$q->id;
					if (is_dir($CFG->chemin_ressources."/questions/".$cleq)){
						copier_dossier($CFG->chemin_ressources."/questions/".$cleq,
							$destination."/ressources/questions/".$cleq);
					}
				}
			}
			if ($quoi=="pc") {
				$criteres='SELECT * FROM ' . PMA_backquote($ines_base) . '.' . PMA_backquote($tb_names[$i]). " where  etat='valid�e' order by id_etab,id ";
                $limit=$CFG->nombre_questions_a_envoyer_cert+$CFG->nombre_questions_a_envoyer_pos;

                if ($limit>0) $criteres .=" limit 0,".$limit;
				$questions=get_records_sql($criteres);
				foreach ($questions as $q) {
					$liste_q[]= "( id=$q->id and id_etab=$q->id_etab )";
				}
				$inserts= gen_insert_data($tb_names[$i],$criteres);
				$sqls=explode("\n",$inserts);
				foreach($sqls as $sql)
				if (trim($sql) )
					fwrite ($fp,  gen_php_sql($sql));
				foreach ($questions as $q) {   //copie el XML et les eventuels documents
					$cleq=$q->id_etab."_".$q->id;
					if (is_dir($CFG->chemin_ressources."/questions/".$cleq)){
						copier_dossier($CFG->chemin_ressources."/questions/".$cleq,
							$destination."/ressources/questions/".$cleq);
					}
				}
			}

            if ($quoi=="c") {
                $criteres='SELECT * FROM ' . PMA_backquote($ines_base) . '.' . PMA_backquote($tb_names[$i]). " where   CERTIFICATION='oui' and etat='valid�e' order by id_etab,id ";
                if ($CFG->nombre_questions_a_envoyer_cert>0) $criteres .=" limit 0,".$CFG->nombre_questions_a_envoyer_cert;
                $questions=get_records_sql($criteres);
                foreach ($questions as $q) {
                    $liste_q[]= "( id=$q->id and id_etab=$q->id_etab )";
                }
                $inserts= gen_insert_data($tb_names[$i],$criteres);
                $sqls=explode("\n",$inserts);
                foreach($sqls as $sql)
                if (trim($sql) )
                    fwrite ($fp,  gen_php_sql($sql));
                foreach ($questions as $q) {   //copie les XML et les eventuels documents
                    $cleq=$q->id_etab."_".$q->id;
                    if (is_dir($CFG->chemin_ressources."/questions/".$cleq)){
                        copier_dossier($CFG->chemin_ressources."/questions/".$cleq,
                            $destination."/ressources/questions/".$cleq);
                    }
                }
            }

            //envoi des r�ponses aux questions
            if (! empty($liste_q)) {  // rev 962 attention si aucune question valid�e ...
	            $liste_q =implode(" or ", $liste_q);
	            $criteres='SELECT * FROM ' . PMA_backquote($ines_base) . '.' . PMA_backquote("c2ireponses"). " where ".$liste_q ." order by id_etab,id,num ";
	            $inserts= gen_insert_data("c2ireponses",$criteres);
	            $sqls=explode("\n",$inserts);
	            foreach($sqls as $sql)
				if (trim($sql) )
					fwrite ($fp,  gen_php_sql($sql));

	            $criteres='SELECT * FROM ' . PMA_backquote($ines_base) . '.' . PMA_backquote("c2iquestionsdocuments"). " where ".$liste_q ." order by id_etab,id,id_doc";

	            //envoi des documents associ�s aux questions
	            $inserts= gen_insert_data("c2iquestionsdocuments",$criteres);
	            $sqls=explode("\n",$inserts);
	            foreach($sqls as $sql)
				if (trim($sql) )
					fwrite ($fp,  gen_php_sql($sql));

            }




			break;

		case "c2iutilisateurs" :
			// cr�er un compte et un profil
			$criteres='SELECT * FROM ' . PMA_backquote($ines_base) . '.' . PMA_backquote("c2iutilisateurs"). " where login='$loginsl'"; // rev 984
			$inserts= gen_insert_data("c2iutilisateurs",$criteres);
			fwrite ($fp,  gen_php_sql($inserts)); // 1 seul
			//TODO son role admin dans son �tablissement ????
            // rev 970 certains comptes ont le droits telecharger_pc mais ne sont pas admin de leur �tablissement
            // ce qui pose pb lors de la 1ere execution, voir FAQ sur le wiki
            $sql="update c2iutilisateurs set est_admin_univ='O'  where login='$loginsl'";
            fwrite ($fp,  gen_php_sql($sql));


			break;

         case "c2idroits" : // rev 843
            // cr�er un profil  local identique a celui de la nationale (admin ?)
            // FAUX les admins locaux n'ont normalement pas le profil administrateurs sur la nationale .

            /**********************************
            $criteres='SELECT * FROM ' . PMA_backquote($ines_base) . '.' . PMA_backquote("c2idroits"). " where login='$loginsl'";
            $inserts= gen_insert_data("c2idroits",$criteres);
            fwrite ($fp,  gen_php_sql($inserts)); // 1 seul
            ************************************/
            // donne lui de profil administrateur sur sa PF ( doblon pour l'instant avec est_admin_univ
            $sql ="INSERT INTO `c2idroits` (`login` ,`id_profil`)VALUES ('$loginsl', '1')"; // rev 984
            fwrite ($fp,gen_php_sql($sql)); // 1 seul
            break;

		case "c2iconfig" :
           //ne pas envoyer les options de config avec drapeau=0 (sp�ciales nationale)
           //voir lib_telechargements.php )
			$criteres='SELECT * FROM ' . PMA_backquote($ines_base) . '.' . PMA_backquote("c2iconfig"). " where drapeau =1";
			$inserts= gen_insert_data("c2iconfig",$criteres);
			$sqls=explode("\n",$inserts);
			foreach($sqls as $sql)
			if (trim($sql) )
				fwrite ($fp,  gen_php_sql($sql));

            // rev 977 attention on peut avoir DEUX pf nationale selon la version du referentiel
            // attention a bien forcer le slash final !
            $sql="update c2iconfig set defaut='".add_slash_url($locale_url_univ)."' where cle='adresse_pl_nationale'";
            fwrite ($fp,  gen_php_sql($sql));


            // les mettre toutes a la valeur par d�faut sur la locale
			$sql="update c2iconfig set valeur=defaut where drapeau=1";
			fwrite ($fp,  gen_php_sql($sql));
            // important la maj de CFG->universite_serveur sur la locale (plus dans constantes.php)

			$sql="update c2iconfig set valeur='".$USER->id_etab_perso."' where cle='universite_serveur'";
			fwrite ($fp,  gen_php_sql($sql));
			//TODO une date de telechargement pour connaitre sa version plus tard
            // c'est fait par installer.php ou maj.php





            break;
        default:
            //les donn�es des autres tables ne sont pas �mises

            break;
	}
}

fwrite($fp,$sqlfin."\n");

fwrite($fp,"mysql_close();\n");
fwrite($fp,"?>\n");
//fermeture du fichier
fclose($fp); mysql_close();

}

zip_dossier ($repertoire_univ,"{$CFG->c2i}",$repertoire_univ."/".$fichier_zip);

supprimer_dossier($destination);

$tpl->assign("elt", traduction ("pl".$quoi,true));
$tpl->assign ("info_telecharger",traduction("info_telecharger",false, traduction ("pl".$quoi,false)));
$tpl->assign("ide",$ide);
$tpl->assign("idf",$fichier_zip);

print_bouton ($tpl,"bouton_telecharger","telecharger","","","submit");


//tracking :
espion2("telechargement","plateforme",$quoi);

$sql = "update {$CFG->prefix}etablissement set nb_telechargements = nb_telechargements + 1 where id_etab=$ide";
$res = ExecRequete ($sql);

$tpl->printToScreen();										//affichage
?>
