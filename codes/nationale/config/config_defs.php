<?php

	function maj_defs_telechargements($node) {
		global $CFG;
/***************************************************************************************************		
		//rev 940 la nationale n'est peut-�tre pas a jour
		//donc aller chercher la derniere et l'afficher dans le template config.html'
		$version_svn = get_version_svn();
 
		if (a_capacite("plct") && a_capacite("plpt")) {

			$node->addItem(cree_item_lien(traduction('telechargement_cp').' '.$version_svn,
					$CFG->chemin .'/codes/nationale/telecharger_pf.php?quoi=pc',true));
		}
		if (a_capacite("plct")) {
			$node->addItem(cree_item_lien(traduction('telechargement_c').' '.$version_svn,
					$CFG->chemin .'/codes/nationale/telecharger_pf.php?quoi=c',true));

		}
		if (a_capacite("plpt")) {
			$node->addItem(cree_item_lien(traduction('telechargement_p').' '.$version_svn,
					$CFG->chemin .'/codes/nationale/telecharger_pf.php?quoi=p',true));

		}
          if (a_capacite("plct") || a_capacite("plpt")) {
        	 $node->addItem(cree_item_lien(traduction('telechargement_maj').' '.$version_svn,
					$CFG->chemin .'/codes/nationale/telecharger_pf.php?quoi=maj',true));
		}
********************************************************************************************************/

        //rev 1009
        if (is_admin()) {
            $node->addItem(cree_item_lien_direct(traduction('export_xml_moodle'), $CFG->chemin . "/codes/questions/export_xml_moodle.php?"));
            $node->addItem(cree_item_lien_direct(traduction('export_xml_moodle_20'), $CFG->chemin . "/codes/questions/export_xml_moodle.php?moodleversion=20"));
          //   $node->addItem(cree_item_lien_direct(traduction('export_xml_moodle_21'), $CFG->chemin . "/codes/questions/export_xml_moodle.php?moodleversion=21"));

            $node->addItem(cree_item_lien_direct(traduction('export_referentiel_xml'), $CFG->chemin . "/codes/questions/export_referentiel_xml.php?"));

            $node->addItem(cree_item_lien_direct(traduction('export_referentiel_xml_moodle'), $CFG->chemin . "/codes/questions/export_referentiel_xml_moodle.php?"));
			$node->addItem(cree_item_lien_direct(traduction('export_referentiel_objectifs_moodle'), $CFG->chemin . "/codes/questions/export_referentiel_objectifs_moodle.php?"));

        }



	}

	function maj_defs_avancees($node) {
		global $CFG;
		$node->addItem(cree_item_lien(traduction('gestion_etablissements'),
		               $CFG->chemin . "/codes/nationale/etablissements/liste.php?"));
		$node->addItem(cree_item_lien(traduction('gestion_familles'),
		               $CFG->chemin . "/codes/nationale/familles/liste.php?"));
		$node->addItem(cree_item_lien(traduction('gestion_referentiels'),
		               $CFG->chemin . "/codes/nationale/referentiels/liste.php?"));
		$node->addItem(cree_item_lien(traduction('gestion_alineas'),
		               $CFG->chemin . "/codes/nationale/alineas/liste.php?"));

	}


?>
