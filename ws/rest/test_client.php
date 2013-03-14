<?php

$chemin = "../..";

//detruire_session();


require_once ($chemin . "/commun/c2i_params.php");

$triche = optional_param("triche", '0', PARAM_INT);


//obligation d'ouvrir une session pour les documents
//il faut donc un compte existant !sinon pb a la lecture du config_nb_item ...'

$compte=new StdClass();
$compte->login='ANONYME0000006';
$compte->auth='manuel';
$compte->type_user='E';
$compte->ts_derniere_connexion=time();
$compte->email='patrick.pollet@insa-lyon.fr';

$email='patrick.pollet@insa-lyon.fr';

register_user_data($compte,'bdd','positionnement','test_client.php');

$url=$CFG->wwwroot.'/ws/rest/get_anonyme.php?format=php&email=pp@patrickpollet.net';

$urlaction=$CFG->wwwroot.'/ws/rest/corrige_examen.php?formatin=html&formatout=json&dump=1';

$CFG->utiliser_curl=0;
$CFG->encodage='UTF-8';
$CFG->unicodedb=1;

 if (!empty($CFG->utiliser_curl)) {
            $session = curl_init();
            curl_setopt($session, CURLOPT_URL, $url);
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($session);
            curl_close($session);
            $via ="curl";
        }
        else {
            $data='';
            if ($fp = fopen($url,"r")){
                while ($ligne=fgets($fp))
                    $data .=$ligne;
                fclose($fp);
            }
        }
$data=unserialize($data);

//print_r($data);
$nom_examen=$data['nom'];
$ts_datedebut=$data['ts_datedebut'];
$ts_datefin=$data['ts_datefin'];

$questionsids=$data['questionsids'];

$user=$data['user'];

$questions=$data['questions'];
$idnat=$data['idnat'];
$nbquestions=count($questions);

$date_debut=time();

require_once( $chemin."/templates/class.TemplatePower.inc.php");    //inclusion de moteur de templates

$tpl = new C2IPopup( ); //cr�er une instance
//inclure d'autre block de templates


//template de saisie du nom du fichier

$modele=<<<EOF

<form id="form" method="post"
    action="$urlaction">

<p><span class="rouge">{texte_choix_multiple}</span></p>
<table class="qcm">
    <thead>
      <tr>
        <th>{liste_questions}</th>
      </tr>
    </thead>
    <tbody>
 <!-- START BLOCK : question -->
   {question}
 <!-- END BLOCK : question -->
    </tbody>
</table>



 <!-- START BLOCK : id_session -->
  <input   name="{session_nom}" type="hidden" value="{session_id}" />
  <!-- END BLOCK : id_session -->

<input name="data" type="hidden" value="$idnat" />
<input type="hidden" name="questionsids" value="$questionsids"/>
<input type="hidden" name="nbquestions" value="$nbquestions" />
<input type="hidden" name="date_debut" value="$date_debut" />
<input type="hidden" name="user" value="$user" />
<input type="hidden" name="email" value="$email" />




<input name="valider" type="submit" class="saisie_bouton" id="valider" value="{terminer}" />


</form>
EOF;

$tpl->assignInclude("corps",$modele,T_BYVAR);
$tpl->prepare($chemin);

$tpl->assign("_ROOT.titre_popup" ,$nom_examen);
$tpl->assign("terminer", traduction("bouton_terminer"));
//$tpl->assign("texte_choix_multiple", utf8_encode(traduction("texte_choix_multiple")));




$num_q=0;
$num_reponses=0;
    foreach ($questions as $ligne_q) {
        $num_q++;
        $tpl->newBlock("question");
        list($fiche,$nbr)=imprime_une_question ($num_q,(object)$ligne_q,true,QCM_PASSAGE,'',$triche);
        $tpl->assign("question",$fiche);
        $num_reponses+=$nbr;

    } // end questions



$tpl->printToScreen();



function get_reponse_byidnat($rid) {
  $tmp=explode("_", $rid);
    if (sizeof($tmp) == 3) {

        if ($rep=get_record ("reponses","num=".(int)$tmp[2] . ' and id='.$tmp[1]. ' and id_etab='.$tmp[0] ))
            return $rep->bonne=='OUI';
        else return false;

    }else return false;

}

/**
 * imprime une question jolie
 * sortie de imprime_examen pour �tre utilis�e ailleurs (bilan question)
 *
 * @param $montreref 0 non 1 oui 3  n�question  4 les 2
 */
function imprime_une_question ($num_q,$ligne_q,$montre_ref,$mode,$login,$triche) {
    global $CFG;

    $modele=<<<EOM
       <tr class="question_entete">
           <td class="question_entete"> {question} {num_q} {id_q}
           <!-- START BLOCK : ref -->
             &nbsp; {referentiel}
           <!-- END BLOCK : ref -->
            </td>
        </tr>
        <tr class="question">
           <td class="question"> {intitule_q} </td>
        </tr>



<!-- START BLOCK : docs -->
           <tr class="docs">
            <td><ul>
               <!-- START BLOCK : doc -->
                <li  class="doc"><img src='{url_doc}'/></li>
                <!-- END BLOCK : doc -->
            </ul>
            </td></tr>
<!-- END BLOCK : docs -->
 <!-- START BLOCK : reponses -->
             <tr class="reponses">
             <td>
             <ul class="reponses">
 <!-- START BLOCK : reponse -->
                <li class="reponse">
                    <!-- START BLOCK : checkbox -->
                        {corrige}
                                <input type="checkbox" name="r[{numrep}]" value="1" {checked}  />
                    <!-- END BLOCK : checkbox -->
                    <!-- START BLOCK : img -->
                        {corrige} <img src='{url_image}' alt=""/>
                    <!-- END BLOCK : img -->

                {reponse} {num_r}  : {intitule_r}</li>
<!-- END BLOCK : reponse -->
              </ul>
             </td>
            </tr>
 <!-- END BLOCK : reponses -->
EOM;


                $tpl= new SubTemplatePower($modele,T_BYVAR);    //cr�er une instance

                $tpl->prepare($CFG->chemin);


                $tpl->assign("num_q", $num_q);
                //identifiant unique de la question (pour controle)
                if ($mode==QCM_CORRECTION) {
                    $tpl->assign ("id_q"," [".$ligne_q->idnat."]");

                }

                else
                    $tpl->assign ("id_q","");
                $titre = trim($ligne_q->texte);
                $tpl->assign("intitule_q", chaine_xml($titre));
                if ($montre_ref) {
                    $referentiel = trim($ligne_q->domaine) . "." . trim($ligne_q->alinea);
                    $id=$ligne_q->idnat;
                    $tpl->newBlock("ref");
                    switch ($montre_ref) {
                        case 1: $tpl->assign("referentiel", "($referentiel)"); break;
                        case 2: $tpl->assign("referentiel", $id); break;
                        case 3: $tpl->assign("referentiel", "($referentiel)  <b>".$id."</b>"); break;
                    }
                }


                $docs=$ligne_q->documents;
                if (count($docs)>0) {
                    // print_r($docs);
                    $tpl->newBlock("docs");

                    foreach ($docs as $doc){
                        if (!empty($doc["url"])) {
                            $tpl->newBlock("doc");
                            $tpl->assign("url_doc",$doc["url"]);
                        }
                    }
                }

                // r�ponses
                $tpl->newBlock("reponses");
                $reps=$ligne_q->reponses;
                $num_r = 0;
                foreach($reps as $ligne_r) {
                    $num_r++;

                    $tpl->newBlock("reponse");
                    //   $tpl->assign("reponse", utf8_encode(traduction("reponse")));
                    // rev 963 num�rotation des r�ponses en chiffres ou lettres
                    if ($CFG->numerotation_reponses==1)
                        $tpl->assign("num_r", $num_r);
                    else
                        $tpl->assign("num_r", chr(ord('A')+$num_r-1));

                    $reponse = trim($ligne_r["texte"]);
                    $tpl->assign("intitule_r", chaine_xml($reponse));
                    $numrep= $ligne_r["id"]; //$ligne_q->id_etab . "_" . $ligne_q->id . "_" . $ligne_r->num;
                    //triche
                    if ($triche)
                    	//$rep=$ligne_r["checked"];
                        $rep=get_reponse_byidnat($numrep);

                    else
                    	$rep='';
                    $checked=$rep? "checked":"";
                    $tpl->newBlock("checkbox");
                    $tpl->assign ("corrige","");
                    $tpl->assign("checked",$checked); // ancienne r�ponse
                    $tpl->assign ("numrep",$numrep);  // indice dans le tableau des r�ponses envoy�s

                }  //end reponses

                return array($tpl->getOutputContent(),$num_r);

}



?>

