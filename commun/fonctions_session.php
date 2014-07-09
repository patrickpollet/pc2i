<?php
//////////////////////////////////////////////////
//////////////////////////////////////////////////
// gestion personnalis�e des variables de session
//////////////////////////////////////////////////
//////////////////////////////////////////////////

/*----------------REVISIONS----------------------
v 1.1 : PP 16/10/2006 ajout de
      require_login, var_get_session, var_exist_session
        SB 17/10/2006
	  modification de var_get_session
        PP 18/10/2006
	  modification de var_get_session
------------------------------------------------*/

// ouvrir une session
function ouvrir_session(){
	global $session_nom;
	session_name($session_nom);
    // bizarrement avec CAS 1.1 l'include de enter.php dans caslogin donne une notice PHP
    // de session d�ja ouverte alors qu'on ne l'avait pas avec CAS 0.4 ????
	return @session_start();
}

// d�truire la session courante et ses variables
function detruire_session()
{
	global $session_nom;
	session_name($session_nom);
    //rev 978  fait expirer le cookie associ� � la PF
    // rev 981 pas de warning PHP si activ� ...
    @setcookie($session_nom, '', time() - 3600);
	return session_destroy();
}

// d�finir une variable � conserver en session

function var_register_session($var,$val)
{
	global $session_nom;
	session_name($session_nom);
	if (isset($_SESSION)){
		$_SESSION[$var] = $val;
	}
	else if (isset($HTTP_SESSION_VARS)){
		$HTTP_SESSION_VARS[$var] = $val;
	}
	else{
		//depreci�e en php 5.3
		session_register($var);
	}
}

function var_unregister_session($var)
{
	global $session_nom;
	session_name($session_nom);
	if (isset($_SESSION)){
		unset( $_SESSION[$var]);
	}
	else if (isset($HTTP_SESSION_VARS)){
		unset( $HTTP_SESSION_VARS[$var]);
	}
	else{
        //depreci�e en php 5.3
		session_unregister($var);
	}
}


function get_session_param() {
    global $session_nom;
    session_name($session_nom);
    return $session_nom."=".session_id();
}

/**
 * rev 981 si l'URL est dans une balise script src='javascript' il FAUt utiliser et & et pas &amp;
 * (cas de l'�diteur HTML en ligne)
 */
function p_session($url,$js=0, $sep='&amp;'){
	// retourne l'url $url avec un ajout de la variable de session si le serveur ne la transmet pas tout seul.
	// si js = 1 , l'url est dans un script javascript ou un header donc on l'ajoute quoiqu'il arrive


	if ( ($js==1) || (ini_get("session.use_trans_sid") == 0) ){

		$ch_tmp=explode("?",$url);
		if (sizeof($ch_tmp)==1)
			{
				$url=$url."?".get_session_param();
				return $url;
			}

		else
			{
				if (sizeof($ch_tmp)==2){
					if ($ch_tmp[1]=="") $url=$url.get_session_param(); // l'url se terminait par un ?
					else $url=$url.$sep.get_session_param();
				}
				else $url=$url.$sep.get_session_param();
				return $url;
			}
	}
	else {
		return $url;
	}
}



function form_session(&$tpl, $bloc="id_session"){
// g�n�re un input dans le formulaire contenant la variable de session � passer si le serveur ne le fait pas tout seul

	global $session_nom;
	session_name($session_nom);
	if ( (ini_get("session.use_trans_sid") == 0) ){
		$tpl->newBlock($bloc);
		$tpl->assign("session_nom",$session_nom);
		$tpl->assign("session_id",session_id());
	}
}


//PP v�rifie qu'une variable est dans la session uniquement !
function var_get_session ($var, $default='') {
	global $session_nom;
    session_name($session_nom);

	// rev 1.41 double test isset pour eviter les notices PHP
	if (isset($_SESSION) && isset($_SESSION[$var]))
		return  $_SESSION[$var];
	else if (isset($HTTP_SESSION_VARS) && isset($HTTP_SESSION_VARS[$var]))
			return $HTTP_SESSION_VARS[$var];
	return $default;
}

//PP v�rifie qu'une variable est dans la session uniquement !
function var_exist_session ($var) {
	return var_get_session ($var);
}






/**
 * V 1.5 ajout param die pour ajax (false)
 *  v�rifie dans la session uniquement que l'utilisateur connect� � bien
 *  le type requis (P=utilisateur, E= etudiant
 * a appeler au d�but de chaque page et fichier inclus .
 */

function require_login ($typeU='P',$die=true) {

	global $CFG,$PHP_SELF,$REMOTE_ADDR;
	if (!var_exist_session ('id_user')) {
		@espion2 ("acces_sans_authentification",$PHP_SELF,$REMOTE_ADDR);
		if ($die) erreur_fatale("err_session_expire","",true);
		else return false;
	}
	$id_user=var_get_session ('id_user');
	if (!var_exist_session ('type_user')) {
		@espion2 ("droits_introuvables",$PHP_SELF,$id_user."@".$REMOTE_ADDR);
		if ($die) erreur_fatale ("err_droits",var_get_session('id_user'));
		else return false;
	}
	$tmp=var_get_session ('type_user');
	if ($tmp !='P') {
		if ($tmp!=$typeU) {
			@espion2 ("err_acces",$PHP_SELF,$id_user."@".$REMOTE_ADDR);
			if ($die) erreur_fatale("err_acces");
            else return false;
		}
	}
    return true;
}


 	//definir le type d'utilisateur personnel
    //definir les variables � enregistrer en session : identifiant d'utilisateur, son type P ou E pour personnel ou �tudiant,
    // le type de plateforme en cours d'utilisation positionnement ou certification

/**
 * garder en session les varaibles critiques
 * cette manip n'est faites qu'� la 1ere connexion dans entrer.php
 * ces valeurs sont ensuite relues dans c2i_params et transfer�es dans la globale $USER
 * a partir de la V 1.5 il ne doit plus y avoirt de variables globales cr��es automatiquement
 * soit avec registrer_globals ou par c2i_parms. On DOIT les ignorer si elles existent
 */

function register_user_data($compte,$verif,$typepf,$page_origine) {

    var_register_session("id_user", $compte->login);
    var_register_session("type_user", $compte->type_user);
    var_register_session("type_p", $typepf);
    var_register_session("verif", $verif); //PP on se rappelle comment il est entr�
    var_register_session("adresse_ip", getremoteaddr());
    var_register_session("page_origine", $page_origine);
    var_register_session("auth", $compte->auth);
    var_register_session("derniere_connexion", $compte->ts_derniere_connexion);
    //r�vision pour UVT
    var_register_session("email", $compte->email);

}



/**
 * relire depuis la session les valeurs critiques
 */
function get_user_data () {
    global $USER;
   // print_r($_SESSION);
    $USER->id_user=var_get_session("id_user");
    $USER->type_plateforme=var_get_session("type_p");
    $USER->type_user=var_get_session("type_user");
    $USER->verif=var_get_session("verif");
    $USER->derniere_connexion=var_get_session("derniere_connexion");
    $USER->auth=var_get_session("auth");
    $USER->page_origine=var_get_session("page_origine");
    $USER->adresse_ip=var_get_session("adresse_ip");
    //r�vision pour UVT
    $USER->email=var_get_session("email");
}

