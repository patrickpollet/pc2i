<?php
/**
 * @author Patrick Pollet
 * @version $Id: lib_ressources.php 1276 2011-11-05 09:00:59Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/*
 * bibliotheque de manipulations de l'entit� ressource
 */
if (is_admin()) {   //utilisateur courant uniquement
	maj_bd_ressources();
}


$CFG->url_ressources_nationales='http://c2i.education.fr/ressources/';

function maj_bd_ressources () {
	  global $CFG,$USER;
}


/**
 * @param int $id
 * @param string $die 	si vrai declenche une erreur fatale en cas d'erreur sinon return null
 * @return objet 		la ligne extraite de la BD ou null en cas d'erreur (et $die=0)

 */
function get_ressource ($id,$die=1) {
	 return get_record('ressources', 'id='.(int)$id,$die,"err_ressource_inconnu" ,$id);
}



function get_ressources ($ref=false,$alinea=false, $tri='id',$die=0) {
    global $CFG;
    $critere='';
    if ($ref) $critere=concatAvecSeparateur($critere, "domaine='$ref'"," and ");
    if ($alinea) $critere=concatAvecSeparateur($critere, "competence='$alinea'"," and ");
 
    return get_records('ressources',$critere, $tri,0,0,$die,"err_pas_de_ressources","????");
}




function get_ressources_bytags($tags,$sort='',$page=0, $recordsperpage=-1, &$totalcount=null) {
    return search_table_bytags('ressources',$tags, $sort, $page, $recordsperpage,$totalcount);
}



function copie_ressource ($copie_id) {
	global $USER,$CFG;

	$ligne=get_ressource($copie_id);
    $ligne->titre = traduction ("copie_de")." ".$ligne->titre;
   // $ligne->id_etab=$USER->id_etab_perso;
    $ligne->modifiable=1;
    $ligne->ts_datecreation=$ligne->ts_datemodification=time();
    $ligne->filtree=0;
    $id=insert_record("ressources",$ligne,true,'id');
    espion3("duplication", "ressource", $id,$ligne);

	return $id;
}


/**
 * si pas utilis�e dans un parcours connu ????
 */
function supprime_ressource ($id) {
	global $CFG;
	//TODO virer les ressources locales associ�es ...
	 // suppression du dossier contenant les �l�ments de la question
	 $ligne=get_ressource($id);
	 if ($ligne->modifiable)
        delete_records("ressources","id=". $id );
     else 
        filtre_ressource($id);
    //tracking :
    espion3("suppression", "ressource", $id,$ligne);
}

function filtre_ressource($id) {
    global $CFG;
    v_d_o_d("qs");
    $sql=<<<EOS
        update {$CFG->prefix}ressources
        set filtree=not filtree
            where id=$id
EOS;
    $res = ExecRequete ($sql);
    //tracking
    $tmp=get_ressource($id);
    if ($tmp->filtree) 
        espion3("filtrage","ressource",$id,$tmp);
    else
        espion3("defiltrage","ressource",$id,$tmp);
}




/**
 * @param int $id
 * @param string $die 	si vrai declenche une erreur fatale en cas d'erreur sinon return null
 * @return objet 		la ligne extraite de la BD ou null en cas d'erreur (et $die=0)

 */
function get_parcours ($id,$die=1) {

	 return get_record('parcours', 'id_parcours='.(int)$id,$die,"err_parcours_inconnu" ,$id);
}


function get_parcours_s ($tri='id_parcours',$die=1) {

     $ret=get_records('parcours', '1','',0,0,$die,"err_pas_de_parcours" ,"???");
     //print_r($ret);
     return $ret;
}


function get_parcours_bytags($tags,$sort='',$page=0, $recordsperpage=-1, &$totalcount=null) {
    return search_table_bytags('parcours',$tags, $sort, $page, $recordsperpage,$totalcount);
}

/**
 * renvoie les parcours utilisant la ressource
 * @param id_ressource
 * @param tous tous les parcours ou les miens seulement
 *
 */

function get_parcours_ressource ($id_ressource,$tous=false,$tri='P.id_parcours',$die=0){
	global $CFG,$USER;
    if (!$tous) $and= " and P.login='".addslashes($USER->id_user)."' ";else $and="";
	$sql=<<<EOS
	   select P.*
	   from {$CFG->prefix}parcours P inner join {$CFG->prefix}ressourcesparcours NP
	   on P.id_parcours=NP.id_parcours
	   where NP.id_ressource=$id_ressource
       $and
	   order by $tri
EOS;
return get_records_sql($sql,$die);
}

/**
 * renvoie le nb de  parcours utilisant la ressource
 */
function count_parcours_ressource ($id_ressource){
    return count(get_parcours_ressource($id_ressource,true)); //tous les parcours
}

/*
 * renvoie les ressources d'un parcours'
 */
function get_ressources_parcours($id_parcours,$tri='id_ressource',$die=0) {
	return get_records("ressourcesparcours","id_parcours=".$id_parcours,$tri,$die);
}


/*
 * renvoie les ressources d'un parcours'
 */
function get_ressources_parcours_detailles($id_parcours,$tri='',$die=0) {
    global $CFG;
    if (empty($tri))
        $tri='domaine,competence,ordre';

    $sql=<<<EOS
        select N.* from {$CFG->prefix}ressources N, {$CFG->prefix}ressourcesparcours NP
        where N.id=NP.id_ressource
        and NP.id_parcours=$id_parcours
        order by $tri
EOS;
    return get_records_sql($sql,$die);
}

function count_ressources_parcours($id_parcours) {
    return count(get_ressources_parcours($id_parcours));
}
/**
 * renvoie tous les parcours d'un utilisateur'
 */

function get_parcours_utilisateur($login,$tri="id_parcours",$die=0) {
	return get_records("parcours","login='".addslashes($login)."' ",$tri,$die)	;
}


function copie_parcours($copie_id) {
	$ligne=get_parcours($copie_id);
	$ligne->titre = traduction ("copie_de")." ".$ligne->titre;
    $ligne->type="creation";
	$ligne->ts_datecreation=$ligne->ts_datemodification=time();
	$ligne->date=date('d-m-Y', $ligne->ts_datecreation);  // compat V 1.4  bof

	 // on indique a insert_record de renvoyer le nouvel id ET de virer la cle id avant (autonum)...
	$newid=insert_record("parcours",$ligne,true,'id_parcours',true); //,"err_duplication",$copie_id);

	$nps=get_ressources_parcours($copie_id);
	foreach( $nps as $np) {
		$np->id_parcours=$newid;
		insert_record("ressourcesparcours",$np,false,false,true);
	}
	espion3("duplication", "parcours", $newid,$ligne);
	return $newid;
}

function supprime_parcours($supp_id)  {
	// suppression de la ressource r�ponses
	delete_records("ressourcesparcours","id_parcours=" . (int)$supp_id);
	delete_records("parcours","id_parcours=" . (int) $supp_id );
		//tracking :
    espion3("suppression", "parcours", $supp_id,null);
}

/**
 * a partir des r�sultats de l'examen qui vient d'�tre not�
 * cr��� un parcours a partir des domaines pass�es ET "non valid�es"
 * question referentiel ou competence ? pour l'instant en C2i niveau 1 le scrore
 * s'applique au referentiel, donc on y met toutes les ressources associ�es ...
 * ca pourra �voluer...
 * @param idex idexe l'examen
 * @param login le compte concern�
 * @return id du parcours
 */
function cree_parcours_croisement_examen ($idex,$idexe,$login) {

    $examen=get_examen($idex,$idexe);
    $cle=$idexe."_".$idex;
    $critere="examen='".$cle."' and login='".addslashes($login)."'";
    $tmp=get_records("resultatsreferentiels",$critere,"referentielc2i",false);
    $ligne_p=new StdClass();
    $ligne_p->type="croisement creation/examen";
    $ligne_p->titre=nom_complet_examen($examen);
    $ligne_p->login=$login;
    $ligne_p->examen=$cle;
    $ligne_p->ts_datecreation=$ligne_p->ts_datemodification=time();
    $idp=insert_record("parcours",$ligne_p,true,'idp');

    $ligne_np=new StdClass(); //partie comune
    $ligne_np->id_parcours=$idp;
    $ligne_np->ts_datecreation=$ligne_n->ts_datemodification=time();
    $nb=0;
    foreach ($tmp as $ligne) {
        if ($ligne->score < $examen->resultat_mini) {
            $ressources=get_ressources($ligne->referentielc2i,false,'domaine,competence,ordre',false);
            foreach ($ressources as $ressource) {
                if (!$ressource->filtree) {
                    $ligne_np->id_ressource=$ressource->id;
                    insert_record("ressourcesparcours",$ligne_np,false,false);
                    $nb++;
                }    
            }
        }
    }
    if (!$nb) {
        //on devrait le virer si vide ???
    }
    espion3("creation", "parcours", $idp,$ligne_p);
    return $idp;
}



/**
 * eviter d'en cr�er plusieurs si il recharge la page des r�sultats du QCm
 * @see commun/ajax/cree_parcours.php
 */
function existe_parcours_croisement_examen ($idex,$idexe,$login) {
    $cle=$idexe."_".$idex;
    $critere="type='croisement creation/examen' and login='".addslashes($login)."' and examen='$cle'";
    $ret=get_record("parcours",$critere,false);
    if ($ret) return $ret->id_parcours;
    else return false;

}

/**
 * renvoie un parcours comme un menu HTML_TreeMenu depliable
 * TODO des liens d'actions sur les branches (supprimer, valider ...)
 */
function parcours_en_menu ($idp,$debug=0,$avecLiens=true) {
	global $CFG;

	$icon         = 'folder.gif';
	$expandedIcon = 'folder-expanded.gif';

	$parc=get_parcours($idp);
	$ressources=get_ressources_parcours_detailles($idp,'domaine,competence,ordre',false);// ordre important
	$menu  = &new HTML_TreeMenu();
	$ref_courant=false;
	$alin_courant=false;
	foreach ($ressources as $ressource) {
        // rev 977 retouche
       

		if ($ressource->domaine != $ref_courant) {
            $ref=get_referentiel($ressource->domaine);
			if ($debug) print ($ressource->domaine."<br/>");
                $noderef   = &new HTML_TreeNode(array('text' => "<b>".clean($ref->referentielc2i." ".$ref->domaine)."</b>",
            'icon' => $icon, 'expandedIcon' => $expandedIcon,
            'expanded' => true));
			$menu->addItem($noderef);
			$ref_courant=$ressource->domaine;
			$alin_courant=false;
		}

		if ($ressource->competence != $alin_courant) {
			if ($debug) print ("----".$ressource->competence."<br/>");
            $alinea=get_alinea($ressource->competence,$ressource->domaine);
            $nodealin   = &new HTML_TreeNode(array('text' => "<i>".clean($ref->referentielc2i.".".$alinea->alinea." ".$alinea->aptitude)."</i>",
                'icon' => $icon, 'expandedIcon' => $expandedIcon,
                'expanded' => true));
			$noderef->addItem($nodealin);
			$alin_courant=$ressource->competence;
		}

        // avec false comme template et nom de balise renvoie l'html complet une loupe et un lien
        // a simplifier car une ressourcs n'a plus qu'un lien 
        if ($avecLiens) {
          //  $link= print_menu_item(false, false,get_menu_item_consulter("../ressources/fiche.php?id=".$ressource->id));
            $lien=get_lien($ressource);
            if ($lien->URL)
                //lien externe, donc pas d'id de session ni urlRetour
                $link= print_menu_item(false, false,get_menu_item_consulter($lien->URL,false));
            else
                $link="";
        }
        else
            $link="";
        if ($link) {
            $noderessource= &new HTML_TreeNode(array('text' => clean($ressource->titre. " ".$link,9999),
                    'icon' => 'document2.png',
                    'expanded' => true ));
				$nodealin->addItem($noderessource);
        }		
		if ($debug) print ("--------".$ressource->id."<br/>");

	}
	return $menu;

}

/**
 * @param idp   -1 un nouveau parcours sinon id du parcours a montrer
 *
 * coche les cases existantes dans le parcours id si pas nouveau
 */
function nouveau_parcours_en_menu ($idp) {
	global $CFG;
	$icon         = 'folder.gif';
	$expandedIcon = 'folder-expanded.gif';

	$menu  = &new HTML_TreeMenu();
	$refs=get_referentiels();
    $ressources_presentes=array();
    if ($idp!=-1) {
        $old_ressources=get_ressources_parcours($idp);
        foreach($old_ressources as $n)
               $ressources_presentes[]=$n->id_ressource;
    }
	foreach ($refs as $ref) {
		$noderef   = &new HTML_TreeNode(array('text' => "<b>".clean($ref->referentielc2i." ".$ref->domaine)."</b>",
			'icon' => $icon, 'expandedIcon' => $expandedIcon,
			'expanded' => true));
		$menu->addItem($noderef);
		$alineas=get_alineas($ref->referentielc2i);
		foreach($alineas as $alinea) {
			$nodealin   = &new HTML_TreeNode(array('text' => "<i>".clean($ref->referentielc2i.".".$alinea->alinea." ".$alinea->aptitude)."</i>",
				'icon' => $icon, 'expandedIcon' => $expandedIcon,
				'expanded' => false));
			$noderef->addItem($nodealin);
			$ressources=get_ressources($ref->referentielc2i,$alinea->alinea,'ordre',false);

			foreach($ressources as $ressource) {
                $checked=in_array($ressource->id,$ressources_presentes) ? "checked":"";
                $expanded=$checked? "true":"false";
                 // avec false comme template et nom de balise renvoie l'html complet une loupe et un lien
                $lien=get_lien($ressource);
               // $link= print_menu_item(false, false,get_menu_item_consulter("../ressources/fiche.php?id=".$ressource->id));
                //lien externe, donc pas d'id de session ni urlRetour
                $link= print_menu_item(false, false,get_menu_item_consulter($lien->URL,false));
                $noderessource= &new HTML_TreeNode(array('text' => clean($ressource->titre." ".$link,9999).
                          " <input type='checkbox' name='ressource[]' value='$ressource->id' $checked />" ,
                    'icon' => 'document2.png',
                    'expanded' => $expanded ));
                $nodealin->addItem($noderessource);
                
			}
		}

	}
    return $menu;
}


/**
 * renvoie une simple liste de liens HTML sans cr�ation d'un parcours en BD
 * rappel : la noteuse ne renvoie QUE les domaines et comp�tences �valu�es (tri�s par ordre alpha)
 * @param examen l'examen en cours
 * @param res les scores a l'examen tel que renvoy� par la noteuse
 */
function cree_parcours_HTML($examen,$res){

    global $CFG;
    $modele=<<<EOM
 <table width="98%" class="listing">
 <tr>
 <th colspan="7" class="centre"> {msg_info_parcours_html} </th>
 </tr>
 <tr>
 <th></th><th>{domaine}</th>
 <th></th><th>{competence}</th>
 <th></th><th>{ressource}</th>
 <th width="70%">{lien}</th>


 </tr>
<!-- START BLOCK : ligne -->
<tr>
<td>{ref}</td> <td>{domaine}</td>
<td>{alinea}</td> <td> {aptitude}</td>
<td>{ressource}</td> <td> {libelle} </td>
<td><a href="{URL}" target="_BLANK" title="{tags}">{origine}</a><br/>
<!--
<span class='commentaire1'>{URL}<br/></span>
--></td>
</tr>



<!-- END BLOCK : ligne -->
</table>

EOM;
$tmptpl= new SubTemplatePower($modele,T_BYVAR);    //cr�er une instance
$tmptpl->prepare($CFG->chemin);

// rev 2.0 seuelement referentiels concern�s
foreach ($res->tabref_score as $ref=>$score) {
	if (empty($score))
		continue;
	// en certification resultat_mini est 0 (pas grave car non affiché dans ce cas)
	if ($score <= $examen->resultat_mini) { // on pourrait donner toujours les ressources ?
		$referentiel=get_referentiel($ref);
		$ligne=new StdClass();
		$ligne->ref=$ref;
		$ligne->domaine=$referentiel->domaine;
		$alineas=get_alineas ($ref,'alinea');

        foreach($alineas as $alinea) {
            $ligne->alinea=$alinea->alinea;
            $ligne->aptitude=clean($alinea->aptitude,45); //rev 936

			//liste des ressources associ�es tri� par alinea
			$ressources=get_ressources($ref,$alinea->alinea,false);
			foreach ($ressources as $ressource) {
                if ($ressource->filtree) 
                    continue;
				$ligne->ressource=$ressource->id;
				$ligne->libelle=$ressource->titre;
				$lien=get_lien($ressource);
	        	if ($lien->URL) {
		        		$ligne->URL=$lien->URL;
		        		$ligne->tags=$ressource->tags;
		        		$ligne->origine=$ressource->titre?$ressource->titre." ".$ressource->version:$lien->URL;
		        		$tmptpl->newBlock("ligne");
		        		$tmptpl->assignObjet($ligne);
		        		//raz premieres colonnes des lignes suivantes
		        		$ligne->ref=$ligne->domaine="";
		        		$ligne->alinea=$ligne->aptitude="";
		        		$ligne->ressource=$ligne->libelle="";
	        		
				}

			}

		}
	 }
   }


    return $tmptpl->getOutputContent();
}

/**
 * 
 * renvoie un objet de type lien a partir de la ressource ...
 * @param ressource $ressource
 */

function get_lien ($ressource) {
    global $CFG;
    $lien=new StdClass();
    $lien->origine='';
    if (is_url($ressource->fichier))
        $lien->URL=$ressource->fichier;
    else
        if ($ressource->id_etab==1)
            $lien->URL=add_slash_url($CFG->url_ressources_nationales).$ressource->fichier;
        else
            $lien->URL=add_slash_url($CFG->chemin_ressources).'ressources_locales/'.$ressource->fichier;
     return $lien;
}



/**
 * renvoie une simple liste de liens XML sans cr�ation d'un parcours en BD
 * rappel : la noteuse ne renvoie QUE les domaines et comp�tences �valu�es (tri�s par ordre alpha)
 * @param examen l'examen en cours
 * @param res les scores a l'examen tel que renvoy� par la noteuse
 */
function cree_parcours_XML($examen,$res){

    global $CFG;
    $modele=<<<EOM
<?xml version="1.0" encoding="{$CFG->encodage}"?>
<parcours>
  <domaines>
 <!-- START BLOCK : ligne_domaine -->
    <domaine>
      <id>{referentielc2i}</id>
      <libelle> {domaine}</libelle>
      <score>{score_domaine} </score>
      <competences>
  <!-- START BLOCK : ligne_competence -->
        <competence>
          <id>{alinea}</id>
          <libelle>{aptitude} </libelle>
          <score>{score_competence} </score>
          <ressources>
  <!-- START BLOCK : ligne_ressource -->
            <ressource>
              <libelle>{titre}</libelle>
              <tags>{tags}</tags>
              <url>{URL}</url>
            </ressource>
  <!-- END BLOCK : ligne_ressource -->
          </ressources>
        </competence>
   <!-- END BLOCK : ligne_competence -->
      </competences>
    </domaine>
<!-- END BLOCK : ligne_domaine -->
  </domaines>
</parcours>

EOM;
  $tmptpl= new SubTemplatePower($modele,T_BYVAR);    //cr�er une instance
  $tmptpl->prepare($CFG->chemin);
  foreach ($res->tabref_score as $ref=>$score) {
      if ($score < $examen->resultat_mini) {
          $tmptpl->newBlock ('ligne_domaine');
          $referentiel=get_referentiel($ref);
          $referentiel->score_domaine=$score;
          $tmptpl->assignObjet($referentiel);
          $alineas=get_alineas ($ref,'alinea');

          foreach($alineas as $alinea) {
              $tmptpl->newBlock('ligne_competence');
              $cle=$referentiel->referentielc2i.'.'.$alinea->alinea;
              $tmp=empty($res->tabcomp_score[$cle])? "":$res->tabcomp_score[$cle];
              $alinea->score_competence=$tmp;
              $tmptpl->assignObjet($alinea);
              //liste des ressources associ�es tri� par alinea
              $ressources=get_ressources($ref,$alinea->alinea,false);
              foreach ($ressources as $ressource) {
                  if ($ressource->filtree)
                      continue;  
                  $ligne->ressource=$ressource->id;
                  $ligne->libelle=$ressource->titre;
                  $lien=get_lien($ressource);

                      if ($lien->URL) {
                          $tmptpl->newBlock('ligne_ressource');
                          $ligne->URL=$lien->URL;
                          $ligne->origine=$lien->origine?$lien->origine:$lien->URL;

                          $tmptpl->assignObjet($ligne);

                      }
                  

              }

          }
      }
  }


  return $tmptpl->getOutputContent();
  }



function evt_ressource_modification ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_ressource_ajout ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_ressource_suppression ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}




if (0) {
    require_once($CFG->chemin_commun."/pear/HTML_TreeMenu/TreeMenu.php");
    //$idp=cree_parcours_croisement_examen (551,65003,'cabachin');
    $idp=2;
    require_once ($chemin . "/templates/class.TemplatePower.inc.php");
    parcours_en_menu($idp,1);
}

if (0) {
require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

   require_once("$CFG->chemin_commun/lib_resultats.php");
    $res=relit_resultats(551,65003,'','cabachin');
    print cree_parcours_HTML(get_examen(551,65003),$res);
}

