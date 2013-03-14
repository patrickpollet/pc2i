<?php


set_time_limit(0);
$chemin = '../../../';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");                 //fichier de paramètres
require_login('P'); //PP
if (!is_admin(false,$CFG->universite_serveur)) die("pas admin");


//inscriptions sans examens

$sql =<<<EOS
SELECT Q.id_etab, Q.id_examen FROM `c2iqcm` Q left join c2iexamens E
on (Q.id_etab=E.id_etab and Q.id_examen=E.id_examen)
where E.id_examen is null
EOS;

$res=get_records_sql($sql);
print "inscriptions sans examens :".count($res)."<br/>";
foreach ($res as $ligne){
    print($ligne->id_etab."_".$ligne->id_examen."<br/>");
    delete_records("qcm","id_etab=".$ligne->id_etab." and id_examen=".$ligne->id_examen);
}


print "<hr/>";
//questions dans des examens inconnus

$sql =<<<EOS
SELECT Q.id_examen_etab, Q.id_examen FROM `c2iquestionsexamen` Q left join c2iexamens E
on (Q.id_examen_etab=E.id_etab and Q.id_examen=E.id_examen)
where E.id_examen is null
EOS;

$res=get_records_sql($sql);
print "questions dans des examens inconnus : " . count($res)."<br/>";
foreach($res as $ligne) {
    print $ligne->id_examen_etab." ".$ligne->id_examen."<br/>";
       delete_records("questionsexamen","id_examen=".$ligne->id_examen." and id_examen_etab=".$ligne->id_examen_etab);
}


print "<hr/>";
//questions inconnues dans des examens
$sql =<<<EOS
SELECT QE.id_etab, QE.id FROM `c2iquestionsexamen` QE left join c2iquestions Q
on (QE.id_etab=Q.id_etab and QE.id=Q.id)
where Q.id_etab is null or Q.id is null
EOS;

$res=get_records_sql($sql);
print "questions inconnues dans des examens : " . count($res)."<br/>";
foreach($res as $ligne) {
    print $ligne->id_etab." ".$ligne->id."<br/>";
    delete_records("questionsexamen","id_etab=".$ligne->id_etab." and id=".$ligne->id);
}



print "<hr/>";
//resultats dans des examens inconnus

$sql =<<<EOS
SELECT examen, count(login)as nb  from c2iresultats  group by examen order by examen
EOS;

$res=get_records_sql($sql);
$nb=0;
foreach($res as $ligne){
    $tmp=explode(".",$ligne->examen);
    if (!get_examen($tmp[1],$tmp[0],false))  {
        print $ligne->examen." ".$ligne->nb."<br/>";
       delete_records("resultats","examen='".$ligne->examen."'");
      $nb++;
    }
}

print "resultats d'examens inconnus : $nb"."/".count($res)."<br/>";

print "<hr/>";

$sql =<<<EOS
SELECT examen, count(login)as nb  from c2iresultatsexamens  group by examen order by examen
EOS;

$res=get_records_sql($sql);
$nb=0;
foreach($res as $ligne){
    $tmp=explode("_",$ligne->examen);
    if (!get_examen($tmp[1],$tmp[0],false))  {
       print $ligne->examen." ".$ligne->nb."<br/>";
       delete_records("resultatsexamens","examen='".$ligne->examen."'");
      $nb++;
    }
}

print "resultats après migration V1.5 : $nb"."/".count($res)."<br/>";


//reponses sans questions
$sql =<<<EOS
select R.* from c2ireponses R left join c2iquestions Q
on (R.id=Q.id and R.id_etab=Q.id_etab)
where Q.id is null or Q.id_etab is null

EOS;

$res=get_records_sql($sql);
print "reponses sans questions : " . count($res)."<br/>";
foreach($res as $ligne){
   print($ligne->id_etab."_".$ligne->id." ".$ligne->num."<br/>");
   delete_records("reponses","num=".$ligne->num);

}

//suite purge directe table c2iinscrits nationale  (fausse toutes les stats)

$sql="delete FROM `{$CFG->prefix}resultats` WHERE login not in (select login from {$CFG->prefix}inscrits)";
$res=ExecRequete($sql);

$sql="delete FROM `{$CFG->prefix}resultatsexamens` WHERE login not in (select login from {$CFG->prefix}inscrits)";
$res=ExecRequete($sql);


$sql="delete FROM `{$CFG->prefix}resultatsreferentiels` WHERE login not in (select login from {$CFG->prefix}inscrits)";
$res=ExecRequete($sql);


$sql="delete FROM `{$CFG->prefix}resultatscompetences` WHERE login not in (select login from {$CFG->prefix}inscrits)";
$res=ExecRequete($sql);

$sql="delete FROM `{$CFG->prefix}resultatsdetailles` WHERE login not in (select login from {$CFG->prefix}inscrits)";
$res=ExecRequete($sql);



/*resultats obtenus le 29/04/2008 suite aux bugs bizarres

inscriptions sans examens :8761
questions dans des examens inconnus : 10879
questions inconnues dans des examens : 20
4 1453
1 1529
1 1530
1 1531
1 1544
1 1554
1 1567
1 1568
1 1572
1 1576
1 1577
1 1578
1 1581
1 1587
1 1588
1 1589
1 1593
1 1600
1 1612
1 1620
resultats d'examens inconnus : 145/189
resultats aprés migration V1.5 : 0/42
reponses sans questions : 10
1_348 1227
1_348 1228
1_348 1229
1_348 1230
1_348 1231
1_1522 13758
1_1522 13760
1_1522 13759
1_1522 13756
1_1522 13757
*/
?>
