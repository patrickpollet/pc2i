<?php

function get_flq_discipline(){
	$sql=<<<EOS
		   select * from {$CFG->prefix}c2inotions;
EOS;
		$table= get_records_sql($sql);
		
		foreach($table as $elm){
			$elm->libelle=($elm->referentielc2i)." - ".($elm->libelle);
		}	
	return $table;
	
}

function get_flq_cours_from_disc($id_notion){
	$sql=<<<EOS
		   select * from {$CFG->prefix}c2iliens WHERE id_notion=$id_notion;
EOS;
		return get_records_sql($sql);
	
}

function get_flq_questions_from_cours($id_exam,$id_lien,$id_notion){
	if(empty($id_lien)){
	$sql=<<<EOS
	select * from {$CFG->prefix}c2iflq_questions WHERE id_exam='$id_exam'  ORDER BY id_lien,nb_int DESC   ;
EOS;
		
	}elseif(empty($id_notion)){
	$sql=<<<EOS
	select * from {$CFG->prefix}c2iflq_questions WHERE id_exam='$id_exam' AND id_lien='$id_lien' ORDER BY id_lien,nb_int DESC   ;
EOS;

	}else{
$sql=<<<EOS
	select * from {$CFG->prefix}c2iflq_questions WHERE id_exam='$id_exam' AND id_notion='$id_notion' AND id_lien='$id_lien' ORDER BY id_lien,nb_int DESC   ;
EOS;
	
	}
	
		return get_records_sql($sql);	
	
}
function get_flq_questions_from_cours_exam($id_exam){
	$sql=<<<EOS
		   select * from {$CFG->prefix}c2iflq_questions WHERE id_exam='$id_exam'ORDER BY id_lien,nb_int DESC   ;
EOS;
		return get_records_sql($sql);	
	
}

function increment_flq_question($id_question,$login_user){
	$sql=<<<EOS
		   select * from {$CFG->prefix}c2iflq_questions_user WHERE id_question=$id_question AND login LIKE '$login_user';
EOS;

	$tmp=get_records_sql($sql);
	if(empty($tmp)){
	$sql=<<<EOS
		   update {$CFG->prefix}c2iflq_questions SET nb_int = nb_int + 1 WHERE id=$id_question;
EOS;
	ExecRequete($sql);	
	$sql=<<<EOS
		   INSERT INTO {$CFG->prefix}c2iflq_questions_user (id_question,login) VALUES ('$id_question','$login_user');
EOS;
ExecRequete($sql);	
	}else{
	}

}

function add_flq_question($id_lien,$libelle,$id_exam,$id_notion,$login_user){
	$date_creation=date("Y-m-d");
	$sql=<<<EOS
	 INSERT INTO {$CFG->prefix}c2iflq_questions (id_exam,id_notion,id_lien,libelle,nb_int,date_creation) VALUES ('$id_exam','$id_notion','$id_lien','$libelle','1','$date_creation');
EOS;

ExecRequete($sql);		
$sql=<<<EOS
		   INSERT INTO {$CFG->prefix}c2iflq_questions_user (id_question,login) VALUES (LAST_INSERT_ID(),'$login_user');
EOS;
ExecRequete($sql);	
}

function get_lien_libelle_from_question($id_question){
	$sql=<<<EOS
	 select id_lien from {$CFG->prefix}c2iflq_questions WHERE id = '$id_question';
EOS;

$o_temp = get_records_sql($sql);

$id_lien=$o_temp[0]->id_lien;

	$sql=<<<EOS
	 select id_lien,origine from {$CFG->prefix}c2iliens WHERE id_lien = '$id_lien';
EOS;
return get_records_sql($sql);	
}

function delete_flq_question($id_question){
	$sql=<<<EOS
	 DELETE FROM {$CFG->prefix}c2iflq_questions WHERE `id` = '$id_question';
EOS;

ExecRequete($sql);		
}

function get_nom_prenom_from_idquestion($id_question){
	$sql=<<<EOS
	 select login from {$CFG->prefix}c2iflq_questions_user WHERE id_question = '$id_question';
EOS;

	$o_temp = get_records_sql($sql);
	$login=$o_temp[0]->login;
	$sql=<<<EOS
	select nom,prenom from {$CFG->prefix}c2iinscrits WHERE login LIKE '$login';
EOS;

return get_records_sql($sql);
}

function get_auteur_flq($idexamen){
	$sql=<<<EOS
	 select auteur,auteur_mail from {$CFG->prefix}c2iexamens WHERE id_examen = '$idexamen';
EOS;
return get_records_sql($sql);
}

function get_date_flq($idexamen){
	$sql=<<<EOS
	 select date, heure from {$CFG->prefix}c2iflq WHERE id_examen = '$idexamen';
EOS;
return get_records_sql($sql);
}

function envoi_par_mail_flq($idq){
	$o_auteur=get_auteur_flq($idq);
	mail($o_auteur->auteur_mail,"","");
}

