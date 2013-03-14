<?php

    /**
    * ces fonctions permettent de ne pas exposer la structure reelle de la BD en renommant
    * certains attributs ou de filtrer des lignes ou des colonnes a ne pas envoyer selon les droits du client
    * ne pas oublier de les appeler plus haut !
    * TODO activer dedans l'attribut error pour ne plus oublier (voir get_qcm)
    */

    function filter_inscrit($client, $user) {
        $user->password = ''; //no way, even in  md5, can be cracked by reverse dictionnary
        return $user;
    }

    function filter_inscrits($client, $users) {
        $res = array ();
        foreach ($users as $user) {
            $user = filter_inscrit($client, $user);
            if ($user)
                $res[] = $user;
        }
        return $res;
    }

    function filter_utilisateur($client, $user) {
        $user->password = ''; //no way, even in  md5, can be cracked by reverse dictionnary
        return $user;
    }

    function filter_utilisateurs($client, $users) {
        $res = array ();
        foreach ($users as $user) {
            $user = filter_utilisateur($client, $user);
            if ($user)
                $res[] = $user;
        }
        return $res;
    }

    function filter_etablissement($client, $et) {
        //renommage des attributs
        $et->id = $et->id_etab;
        $et->nom = $et->nom_etab;
        return $et;
    }

/**
 * rev 939 filtrage par date de passage
 */
    function filter_note($client, $note,$timestart=0) {

        if ($note->date >=$timestart)
          return $note;
        return false;
    }

    function filter_examen($client, $ex) {
        // revision 1035 pourquoi ce filtrage était activé ????
       // if ($ex->pool_pere) return false; //pas les membres d'un pool '
       // if ($ex->anonyme) return false; //ni l'anonyme
        $ex->eid = $ex->id_etab . "." . $ex->id_examen; //identifiant national

        //rev 979 cf forum http://www.c2i.education.fr/forum/viewtopic.php?f=4&t=159
        $ex->nbinscrits=compte_inscrits($ex->id_examen, $ex->id_etab);;
        $ex->nbpassages=compte_passages($ex->id_examen, $ex->id_etab);

        return $ex;
    }

    function filter_question($client, $q) {
        $q->qid = $q->id_etab . "." . $q->id; //identifiant national
        return $q;
    }

    function filter_notion($client, $q) {
        $q->nid = $q->id_etab . "." . $q->id_notion; //identifiant national
        return $q;
    }

    function filter_referentiel($client, $q) {
        return $q;
    }

    function filter_alinea($client, $q) {
        return $q;
    }

    function filter_famille($client, $q) {
        return $q;
    }


    function filter_lien($client, $q) {
        return $q;
    }





    function filter_reponse($client, $q,$pasDeReponse=false) {
        $q->qid = $q->id_etab . "." . $q->id; //identifiant national question
        $q->bonne = $q->bonne == 'OUI'; // booleen attendu
        // rev 1003 option pour ne pas envoyer la bonne réponse
        if ($pasDeReponse)
             unset($q->bonne);
        return $q;
    }

    function filter_document($client, $q) {
        $q->qid = $q->id_etab . "." . $q->id; //identifiant national question
        return $q;
    }

    function filter_questions($client, $qs) {
        $res = array ();
        foreach ($qs as $q) {
            $q = filter_question($client, $q);
            if ($q)
                $res[] = $q;
        }
        return $res;
    }


    function filter_reponses($client, $qs) {
        $res = array ();
        foreach ($qs as $q) {
            $q = filter_reponse($client, $q);
            if ($q)
                $res[] = $q;
        }
        return $res;
    }

    function filter_documents($client, $qs) {
        $res = array ();
        foreach ($qs as $q) {
            $q = filter_document($client, $q);
            if ($q)
                $res[] = $q;
        }
        return $res;
    }

    function filter_referentiels($client, $qs) {
        $res = array ();
        foreach ($qs as $q) {
            $q = filter_referentiel($client, $q);
            if ($q)
                $res[] = $q;
        }
        return $res;
    }

    function filter_alineas($client, $qs) {
        $res = array ();
        foreach ($qs as $q) {
            $q = filter_alinea($client, $q);
            if ($q)
                $res[] = $q;
        }
        return $res;
    }

    function filter_familles($client, $qs) {
        $res = array ();
        foreach ($qs as $q) {
            $q = filter_famille($client, $q);
            if ($q)
                $res[] = $q;
        }
        return $res;
    }

    function filter_notions($client, $qs) {
        $res = array ();
        foreach ($qs as $q) {
            $q = filter_notion($client, $q);
            if ($q)
                $res[] = $q;
        }
        return $res;
    }


    function filter_liens($client, $qs) {
        $res = array ();
        foreach ($qs as $q) {
            $q = filter_lien($client, $q);
            if ($q)
                $res[] = $q;
        }
        return $res;
    }

    function filter_etablissements($client, $qs) {
        $res = array ();
        foreach ($qs as $q) {
            $q = filter_etablissement($client, $q);
            if ($q)
                $res[] = $q;
        }
        return $res;
    }

    function filter_examens($client, $qs) {
        $res = array ();
        foreach ($qs as $q) {
            $q = filter_examen($client, $q);
            if ($q)
                $res[] = $q;
        }
        return $res;
    }

?>
