<?php // $Id: cron.php,v 1.126.2.22 2011/08/30 23:43:18 moodlerobot Exp $

/// This script looks through all the module directories for cron.php files
/// and runs them.  These files can contain cleanup functions, email functions
/// or anything that needs to be run on a regular basis.
///
/// This file is best run from cron on the host system (ie outside PHP).
/// The script can either be invoked via the web server or via a standalone
/// version of PHP compiled for CGI.
///
/// eg   wget -q -O /dev/null 'http://votreplateforme/admin/cron.php'
/// or   php /web/moodle/admin/cron.php
    set_time_limit(0);
    $starttime = microtime();

/// The following is a hack necessary to allow this script to work well
/// from the command line.

    define('FULLME', 'cron');



/// The current directory in PHP version 4.3.0 and above isn't necessarily the
/// directory of the script when run from the command line. The require_once()
/// would fail, so we'll have to chdir()

    if (!isset($_SERVER['REMOTE_ADDR']) && isset($_SERVER['argv'][0])) {
        chdir(dirname($_SERVER['argv'][0]));
    }

    
    $chemin = '..';
    $chemin_commun = $chemin . "/commun";
    require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres
    require_once ($chemin_commun . "/lib_ldap.php");
    require_once ($chemin_commun . "/lib_mail.php");
    require_once ($chemin_commun . "/lib_cron.php");

//TODO uniquement si appel par le web ou wget 
//    require_login('P'); //PP
//    v_d_o_d("config");

    define ('LOCK_FILE','/cronrunning');

    if (file_exists($CFG->chemin_ressources.LOCK_FILE)) {
        $time=file_get_contents($CFG->chemin_ressources.LOCK_FILE);
        $err="previous cron is still running started at ".userdate($time);
        mtrace ($err);
       // add_to_log(SITEID, 'cron', 'cron', '', $err);
        espion3("cron","systeme",0,$err);
       // $admin=get_record('user','username','ppollet');
        $subject = "execution cron moodle ".$CFG->wwwroot;
       // email_to_user($admin,$admin,$subject,$err);

        exit;
    }

    set_time_limit(0);   //important
    file_put_contents($CFG->chemin_ressources.LOCK_FILE,time());



/// Extra debugging (set in config.php)


/// check if execution allowed
    if (isset($_SERVER['REMOTE_ADDR'])) { // if the script is accessed via the web.
        if (!empty($CFG->cronclionly)) {
            // This script can only be run via the cli.
            print_error('cronerrorclionly', 'admin');
            exit;
        }
        // This script is being called via the web, so check the password if there is one.
        if (!empty($CFG->cronremotepassword)) {
            $pass = optional_param('password', '', PARAM_RAW);
            if($pass != $CFG->cronremotepassword) {
                // wrong password.
                print_error('cronerrorpassword', 'admin');
                exit;
            }
        }
    }

/// emulate normal session
    $SESSION = new StdClass();
  //  $USER = get_admin();      /// Temporarily, to provide environment for this script


/// send mime type and encoding

    if (check_browser_version('MSIE')) {
        //ugly IE hack to work around downloading instead of viewing
        @header("Content-Type: text/html; charset={$CFG->encodage}");
        echo "<xmp>"; //<pre> is not good enough for us here
    } else {
        //send proper plaintext header
        @header("Content-Type: text/plain; charset={$CFG->encodage}");
    }

/// no more headers and buffers
   while(@ob_end_flush());


/// increase memory limit (PHP 5.2 does different calculation, we need more memory now)
  

/// Start output log

    $timenow  = time();

    mtrace("Server Time: ".date('r',$timenow)."\n\n");


    mtrace('Starting processing the event queue...');
    //events_cron();
    mtrace('done.');

/// Run all core cron jobs, but not every time since they aren't too important.
/// These don't have a timer to reduce load, so we'll use a random number
/// to randomly choose the percentage of times we should run these jobs.

    srand ((double) microtime() * 10000000);
    $random100 = rand(0,100);

    if ($random100 < 20) {     // Approximately 20% of the time.
        mtrace("Running clean-up tasks...");

       

        mtrace("Finished clean-up tasks...");

    } // End of occasional clean-up tasks


 
 

 
    // run any customized cronjobs, if any
    // looking for functions in lib/local/cron.php
  if ($CFG->universite_serveur !=1) {  
    
    if (file_exists($CFG->dirroot.'/codes/locale/cron.php')) {
        mtrace('Processing customized cron script ...');
        include_once($CFG->dirroot.'/codes/locale/cron.php');
        mtrace('done.');
    }
  } else {
      if (file_exists($CFG->dirroot.'/codes/nationale/cron.php')) {
          mtrace('Processing customized cron script ...');
          include_once($CFG->dirroot.'/codes/nationale/cron.php');
          mtrace('done.');
      }     
  }


    //Unset session variables and destroy it
    @session_unset();
    @session_destroy();

    mtrace("Cron script completed correctly");

    $difftime = microtime_diff($starttime, microtime());
    mtrace("Execution took ".$difftime." seconds");

/// finish the IE hack
    if (check_browser_version('MSIE')) {
        echo "</xmp>";
    }

    unlink($CFG->chemin_ressources.LOCK_FILE);

?>
