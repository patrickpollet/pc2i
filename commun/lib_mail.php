<?php
/**
 * @author Patrick Pollet
 * @version $Id: lib_mail.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

require_once($chemin_commun."/lib_rapport.php");

/*
 * bibliotheque de manipulations des couriels a envoyer
 */
 if (is_admin()) {   //utilisateur courant uniquement
    maj_bd_mail();
 }

 function maj_bd_mail () {
	 global $CFG,$USER;
 }




/**  message standard tel que defini dans fr.php
"sujet_standard_convocation"=>"convocation � l'examen de [type_p]",
"message_standard_convocation"=>
"Bonjour [prenom] [nom],\r\n".
"Vous  �tes convoqu� � l'examen de [type_p] qui aura lieu le [date_debut] de [heure_debut] �  [heure_fin].\r\n".
"Votre identifiant est [login] et votre mot de passe [password]\r\n".
 */


  function substitue($sujet,$message,$ex,$compte) {
    global $CFG;

      $sujet_p=$sujet;
      $message_p=$message;
	  if ($ex) {
		  if ($ex->positionnement=='OUI') $type_ex='positionnement';
		  else if ($ex->certification=='OUI') $type_ex='certification';
		  else $type_ex=""; //???
		  $sujet_p=str_replace('[type_p]',$type_ex,$sujet_p);
		  $message_p=str_replace('[type_p]',$type_ex,$message_p);
		  $sujet=str_replace('[date_debut]',userdate($ex->ts_datedebut,'strftimedaydate'),$sujet_p);
		  $message_p=str_replace('[date_debut]',userdate($ex->ts_datedebut,'strftimedaydate'),$message_p);
		  $sujet=str_replace('[heure_debut]',userdate($ex->ts_datedebut,'strftimetime'),$sujet_p);
		  $message_p=str_replace('[heure_debut]',userdate($ex->ts_datedebut,'strftimetime'),$message_p);
		  $sujet_p=str_replace('[heure_fin]',userdate($ex->ts_datefin,'strftimetime'),$sujet_p);
		  $message_p=str_replace('[heure_fin]',userdate($ex->ts_datefin,'strftimetime'),$message_p);
	  }

	  if ($compte) {
		  $message_p=str_replace('[nom]',$compte->nom,$message_p);
		  $message_p=str_replace('[prenom]',$compte->prenom,$message_p);
		  $message_p=str_replace('[login]',$compte->login,$message_p);
		  $message_p=str_replace('[adresse]',$CFG->wwwroot,$message_p);

		  if ($compte->auth!="ldap") {
			  $message_p=str_replace('[password]',$compte->password,$message_p);
		  } else {
			  $message_p=str_replace('[password]',traduction("votre_passe_usuel_ent"),$message_p);
		  }
	  }
      return array($sujet_p,$message_p);

  }


/**
 * envoi un texte simple !
 */
 function convoque_mail ($idq,$ide,$liste,$sujet,$message) {
    $resultats=array();
    $nb=0;
    $ex=get_examen($idq,$ide);
    list($sujet,$message)=substitue($sujet,$message,$ex,false);


    foreach ($liste as $login) { //liste de login
        //TODO substitution
        $compte=get_compte($login,false);
        list($sujet_perso,$message_perso)=substitue($sujet,$message,false,$compte);
       if (send_mail($login,$sujet_perso,'',$message_perso))
            set_ok ("mail a\t".$compte->nom."\t".$compte->prenom."\t".$compte->email,$resultats);
        else
            set_erreur ("erreur mail pour\t".$compte->login,$resultats);
    }
    return $resultats;
 }



/**
 * selon la config
 */

function send_mail ($login,$sujet,$html_to_send,$text_to_send="") {
    global $CFG; 
    return __send_mail_phpmailer($login,$sujet,$html_to_send,$text_to_send);
}




/**
 * V 1.5 avec la classe php mailer
 * @param compte q aui peut etre un somple login ou un objet compte d�ja renseign�
 */

function __send_mail_phpmailer($compte,$sujet,$html_to_send,$text_to_send='') {
    global $CFG;
     $from=get_admin($CFG->universite_serveur);
    // print ("phpmailer");
     //print_r($from);
     if (is_string($compte)) $compte=get_compte($compte,false);
     //print_r($compte);
     $ret= email_to_user($compte,$from,$sujet,$text_to_send,$html_to_send);
     if ($ret)
        espion2("envoi_phpmailer",traduction("succes").' : '.$sujet,$compte->email);
     //else  rien fait par email_to_user

     return $ret;

}



/**
 * Send an email to a specified user
 * version Moodle 1.9 reduite
 *
 * @uses $CFG
 * @param user $user  A {@link $USER} object
 * @param user $from A {@link $USER} object
 * @param string $subject plain text subject line of the email
 * @param string $messagetext plain text version of the message
 * @param string $messagehtml complete html version of the message (optional)
 * @param string $attachment a file on the filesystem, relative to $CFG->dataroot
 * @param string $attachname the name of the file (extension indicates MIME)
 * @param bool $usetrueaddress determines whether $from email address should
 *          be sent out. Will be overruled by user profile setting for maildisplay
 * @return bool|string Returns "true" if mail was sent OK, "emailstop" if email
 *          was blocked by user and "false" if there was another sort of error.
 */
function email_to_user($user, $from, $subject, $messagetext,
                       $messagehtml='', $attachment='', $attachname='',
                       $usetrueaddress=false, $replyto='', $replytoname='') {

    global $CFG;

    include_once($CFG->chemin_commun .'/phpmailer/class.phpmailer.php');

    if (empty($user)) {
	    espion2('envoi_phpmailer', traduction('err_pas_de_destinataire'),'');
	    return false;
    }

    if (empty($user->email)) {
	    espion2('envoi_phpmailer',traduction("err_pas_de_mail"),$user->login);
	    return false;
    }
    $mail = new phpmailer();
	// rev 921 un saut de ligne dans CFG->version perturbe gravement le parser
    $mail->Version = 'C2I '. clean($CFG->version);           // mailer version
    $mail->PluginDir = $CFG->chemin_commun .'/phpmailer/';      // plugin directory (eg smtp plugin)

    $mail->CharSet = $CFG->encodage;

    if ($CFG->smtphosts == 'qmail') {
        $mail->IsQmail();                              // use Qmail system

    } else if (empty($CFG->smtphosts)) {
        $mail->IsMail();                               // use PHP mail() = sendmail

    } else {
        $mail->IsSMTP();                               // use SMTP directly
        if ($CFG->smtp_debugging) {
            echo '<pre>' . "\n";
            $mail->SMTPDebug = true;
        }
        $mail->Host = $CFG->smtphosts;               // specify main and backup servers

        if ($CFG->smtpuser) {                          // Use SMTP authentication
            $mail->SMTPAuth = true;
            $mail->Username = $CFG->smtpuser;
            $mail->Password = $CFG->smtppass;
        }
    }

    $adminuser = get_admin($CFG->universite_serveur);

   if ($adminuser)  $mail->Sender   = $adminuser->email;
   else $mail->Sender=$CFG->noreplyaddress;


    if (is_string($from)) { // So we can pass whatever we want if there is need
        $mail->From     = $CFG->noreplyaddress;
        $mail->FromName = $from;
    } else {
            if ($usetrueaddress) {
                $mail->From     = $from->email;
                $mail->FromName = get_fullname($from->login);
            } else {
                $mail->From     = $CFG->noreplyaddress;
                $mail->FromName = get_fullname($from->login);

        }
    }

    if (!empty($replyto)) {
        $mail->AddReplyTo($replyto,$replytoname);
    } else
        $mail->AddReplyTo($CFG->noreplyaddress,traduction("ne_pas_repondre"));

    $mail->Subject = substr(stripslashes($subject), 0, 900);

    $mail->AddAddress($user->email, get_fullname($user->login) );

    $mail->WordWrap = 79;                               // set word wrap

    if (!empty($from->customheaders)) {                 // Add custom headers
        if (is_array($from->customheaders)) {
            foreach ($from->customheaders as $customheader) {
                $mail->AddCustomHeader($customheader);
            }
        } else {
            $mail->AddCustomHeader($from->customheaders);
        }
    }

    if (!empty($from->priority)) {
        $mail->Priority = $from->priority;
    }

    if ($messagehtml) {
        $mail->IsHTML(true);
        $mail->Encoding = 'quoted-printable';           // Encoding to use
        $mail->Body    =  $messagehtml;
        $mail->AltBody =  "\n$messagetext\n";
    } else {
        $mail->IsHTML(false);
        $mail->Body =  "\n$messagetext\n";
    }

    if ($attachment && $attachname) {
/************************  non g�r�
        if (ereg( "\\.\\." ,$attachment )) {    // Security check for ".." in dir path
            $mail->AddAddress($adminuser->email, fullname($adminuser) );
            $mail->AddStringAttachment('Error in attachment.  User attempted to attach a filename with a unsafe name.', 'error.txt', '8bit', 'text/plain');
        } else {
            require_once($CFG->libdir.'/filelib.php');
            $mimetype = mimeinfo('type', $attachname);
            $mail->AddAttachment($CFG->dataroot .'/'. $attachment, $attachname, 'base64', $mimetype);
        }
**************************/
    }

    if ($mail->Send()) {
        return true;
    } else {
        espion2('envoi_phpmailer',traduction("erreur").' : '.$mail->ErrorInfo,$user->email);
        return false;
    }
}






if (0) {
//passer p�r phpmailer
$CFG->smtphosts="smtp.free.fr";
 $CFG->smtp_debugging=1;
 send_mail("pollet","test phpmailer","<html><body>ca roule ma poule éçà  ???</body><html>","ca roule ma poule çççççççç ???");
 //passer par la fonction mail de php
 $CFG->smtphosts="";
//send_mail("pollet","test mail php","<html><body>ca roule ma poule ???</body><html>");


}

