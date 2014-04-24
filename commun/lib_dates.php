<?php
/**
 * @author Patrick Pollet
 * @version $Id: lib_dates.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/*
 * bibliotheque de manipulations des dates/heures
 */


//la fonction strtotime semble tr�s capricieuse renvoie de temps en temps une valeur vide ????

function mon_strtotime ($tmp) {
    $tmp=explode(" ",$tmp); //enleve le nom du jour devant
    list($h,$min)=explode(":",$tmp[2]);
    list($j,$m,$a)=explode("/",$tmp[1]);
    return mktime($h,$min,0,$m,$j,$a);
}

/**
* microtime_diff
*
* @param string $a ?
* @param string $b ?
* @return string
* @todo Finish documenting this function
*/
function microtime_diff($a, $b) {
    list($a_dec, $a_sec) = explode(' ', $a);
    list($b_dec, $b_sec) = explode(' ', $b);
    return $b_sec - $a_sec + $b_dec - $a_dec;
}

/*
 * debut V 1.5
 */
//if (is_admin()) {   // toujours pour l'instant
	maj_bd_dates();
//}
/// Calculate and set $CFG->ostype to be used everywhere. Possible values are:
/// - WINDOWS: for any Windows flavour.
/// - UNIX: for the rest
/// Also, $CFG->os can continue being used if more specialization is required
    if (stristr(PHP_OS, 'win') && !stristr(PHP_OS, 'darwin')) {
        $CFG->ostype = 'WINDOWS';
    } else {
        $CFG->ostype = 'UNIX';
    }
    $CFG->os = PHP_OS;
moodle_setlocale();
//pour eviter des warnings php en mode E_strict
// rev 928 l'appel pour europe/paris a �t� deplac� dans l'accolade !!! sinon pb en PHP < 5.1
if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get")) {
	@date_default_timezone_set(@date_default_timezone_get());
	//print_r(date_default_timezone_get()); // bizarre me renvoie europe/berlin ???
	//@date_default_timezone_set("Europe/Paris");
}

function maj_bd_dates () {


}

 /**
 * recupere la date renvoy�e par jscalendar
 * et la convertit
 */



/**
 * ces fonctions sont celles utilis�es dans Moodle cf moodle/lib/moodlelib.php
 * seule les appels a traduction() ont �t� transform�s en traduction();get
 */

/**
 * Time constant - the number of seconds in a year
 */

define('YEARSECS', 31536000);

/**
 * Time constant - the number of seconds in a week
 */
define('WEEKSECS', 604800);

/**
 * Time constant - the number of seconds in a day
 */
define('DAYSECS', 86400);

/**
 * Time constant - the number of seconds in an hour
 */
define('HOURSECS', 3600);

/**
 * Time constant - the number of seconds in a minute
 */
define('MINSECS', 60);

/**
 * Time constant - the number of minutes in a day
 */
define('DAYMINS', 1440);

/**
 * Time constant - the number of minutes in an hour
 */
define('HOURMINS', 60);

 /**
 * moodle_setlocale
 *
 * @uses $CFG
 * @param string $locale ?
 * @todo Finish documenting this function
 */
function moodle_setlocale($locale='') {

    global $CFG;

    static $currentlocale = ''; // last locale caching

    $oldlocale = $currentlocale;

/// Fetch the correct locale based on ostype
    if($CFG->ostype == 'WINDOWS') {
        $stringtofetch = 'localewin';
    } else {
        $stringtofetch = 'locale';
    }

/// the priority is the same as in get_string() - parameter, config, course, session, user, global language
    if (!empty($locale)) {
        $currentlocale = $locale;
    } else if (!empty($CFG->locale)) { // override locale for all language packs
        $currentlocale = $CFG->locale;
    } else {
        $currentlocale = traduction($stringtofetch,false ); //attention pas de majuscule !!!!
    }

   // print ($currentlocale);

/// do nothing if locale already set up
    if ($oldlocale == $currentlocale) {
        return;
    }

/// Due to some strange BUG we cannot set the LC_TIME directly, so we fetch current values,
/// set LC_ALL and then set values again. Just wondering why we cannot set LC_ALL only??? - stronk7
/// Some day, numeric, monetary and other categories should be set too, I think. :-/

/// Get current values
    $monetary= setlocale (LC_MONETARY, 0);
    $numeric = setlocale (LC_NUMERIC, 0);
    $ctype   = setlocale (LC_CTYPE, 0);
    if ($CFG->ostype != 'WINDOWS') {
        $messages= setlocale (LC_MESSAGES, 0);
    }
/// Set locale to all
    setlocale (LC_ALL, $currentlocale);
/// Set old values
    setlocale (LC_MONETARY, $monetary);
    setlocale (LC_NUMERIC, $numeric);
    if ($CFG->ostype != 'WINDOWS') {
        setlocale (LC_MESSAGES, $messages);
    }
    if ($currentlocale == 'tr_TR' or $currentlocale == 'tr_TR.UTF-8') { // To workaround a well-known PHP problem with Turkish letter Ii
        setlocale (LC_CTYPE, $ctype);
    }
}

/// FUNCTIONS FOR HANDLING TIME ////////////////////////////////////////////

/**
 * Given date parts in user time produce a GMT timestamp.
 *
 * @param int $year The year part to create timestamp of
 * @param int $month The month part to create timestamp of
 * @param int $day The day part to create timestamp of
 * @param int $hour The hour part to create timestamp of
 * @param int $minute The minute part to create timestamp of
 * @param int $second The second part to create timestamp of
 * @param float $timezone ?
 * @param bool $applydst ?
 * @return int timestamp
 * @todo Finish documenting this function
 */
function make_timestamp($year, $month=1, $day=1, $hour=0, $minute=0, $second=0, $timezone=99, $applydst=true) {

    $strtimezone = NULL;
    if (!is_numeric($timezone)) {
        $strtimezone = $timezone;
    }

    $timezone = get_user_timezone_offset($timezone);

    if (abs($timezone) > 13) {
        $time = mktime((int)$hour, (int)$minute, (int)$second, (int)$month, (int)$day, (int)$year);
    } else {
        $time = gmmktime((int)$hour, (int)$minute, (int)$second, (int)$month, (int)$day, (int)$year);
        $time = usertime($time, $timezone);
        if($applydst) {
            $time -= dst_offset_on($time, $strtimezone);
        }
    }

    return $time;

}

/**
 * Given an amount of time in seconds, returns string
 * formatted nicely as weeks, days, hours etc as needed
 *
 * @uses MINSECS
 * @uses HOURSECS
 * @uses DAYSECS
 * @uses YEARSECS
 * @param int $totalsecs  un time stamp unix
 * @param array $str ?
 * @param depuisMaintenant (def = vrai)  soustraire l'heure actuelle
 *  * @return string
 */






 function format_time($totalsecs, $str=NULL,$depuisMaintenant=true) {

    $totalsecs = abs($totalsecs);
    if ($depuisMaintenant) {
          if (!$totalsecs) return traduction ("jamais");
          $totalsecs=-$totalsecs+time();
    } else
         if (!$totalsecs) return "";

    if (!$str) {  // Create the str structure the slow way
        $str = new StdClass(); // fix un PHP warning 24/04/2014
        /*
[Thu Apr 24 14:50:54 2014] [error] [client 80.14.92.232] PHP Warning:  Creating default object from empty value in /web/c2isrv.univ-r
ennes1.fr/https/pfv3/commun/lib_dates.php on line 245, referer: https://c2i.education.fr/pfv3/codes/qcm/passage.php?idq=28&ide=1&c2iv
3=pd3i0oo281qn5nt9gcecf1co94
         */
        $str->day   = traduction('jour');
        $str->days  = traduction('jours');
        $str->hour  = traduction('heure');
        $str->hours = traduction('heures');
        $str->min   = traduction('minute');
        $str->mins  = traduction('minutes');
        $str->sec   = traduction('seconde');
        $str->secs  = traduction('secondes');
        $str->year  = traduction('annee');
        $str->years = traduction('annees');
    }


    $years     = floor($totalsecs/YEARSECS);
    $remainder = $totalsecs - ($years*YEARSECS);
    $days      = floor($remainder/DAYSECS);
    $remainder = $totalsecs - ($days*DAYSECS);
    $hours     = floor($remainder/HOURSECS);
    $remainder = $remainder - ($hours*HOURSECS);
    $mins      = floor($remainder/MINSECS);
    $secs      = $remainder - ($mins*MINSECS);

    $ss = ($secs == 1)  ? $str->sec  : $str->secs;
    $sm = ($mins == 1)  ? $str->min  : $str->mins;
    $sh = ($hours == 1) ? $str->hour : $str->hours;
    $sd = ($days == 1)  ? $str->day  : $str->days;
    $sy = ($years == 1)  ? $str->year  : $str->years;

    $oyears = '';
    $odays = '';
    $ohours = '';
    $omins = '';
    $osecs = '';

    if ($years)  $oyears  = $years .' '. $sy;
    if ($days)  $odays  = $days .' '. $sd;
    if ($hours) $ohours = $hours .' '. $sh;
    if ($mins)  $omins  = $mins .' '. $sm;
    if ($secs)  $osecs  = $secs .' '. $ss;

    if ($years) return trim($oyears .' '. $odays);
    if ($days)  return trim($odays .' '. $ohours);
    if ($hours) return trim($ohours .' '. $omins);
    if ($mins)  return trim($omins .' '. $osecs);
    if ($secs)  return $osecs;
    if ($depuisMaintenant)
        return traduction('maintenant');
    else
        return '';
}

/**
 * Returns a formatted string that represents a date in user time
 * <b>WARNING: note that the format is for strftime(), not date().</b>
 * Because of a bug in most Windows time libraries, we can't use
 * the nicer %e, so we have to use %d which has leading zeroes.
 * A lot of the fuss in the function is just getting rid of these leading
 * zeroes as efficiently as possible.
 *
 * If parameter fixday = true (default), then take off leading
 * zero from %d, else mantain it.
 *
 * @uses HOURSECS
 * @param  int $date timestamp in GMT
 * @param string $format strftime format
 * @param float $timezone
 * @param bool $fixday If true (default) then the leading
 * zero from %d is removed. If false then the leading zero is mantained.
 * @return string
 */
function userdate($date, $format='', $timezone=99, $fixday = true) {

    global $CFG;

    if (!$date) return traduction("jamais");

    $strtimezone = NULL;
    if (!is_numeric($timezone)) {
        $strtimezone = $timezone;
    }

    if (empty($format)) {
        $format = traduction('strftimedaydatetime',false);

    } else $format=traduction ($format,false);

    if (isset($CFG->fixday) && ! empty($CFG->nofixday)) {  // Config.php can force %d not to be fixed.
        $fixday = false;
    } else if ($fixday) {
        $formatnoday = str_replace('%d', 'DD', $format);
        $fixday = ($formatnoday != $format);
    }

    $date += dst_offset_on($date, $strtimezone);

    $timezone = get_user_timezone_offset($timezone);

    if (abs($timezone) > 13) {   /// Server time
        if ($fixday) {
            $datestring = strftime($formatnoday, $date);
            $daystring  = str_replace(' 0', '', strftime(' %d', $date));
            $datestring = str_replace('DD', $daystring, $datestring);
        } else {
          $datestring = strftime($format, $date);
        }
    } else {
        $date += (int)($timezone * 3600);
        if ($fixday) {
            $datestring = gmstrftime($formatnoday, $date);
            $daystring  = str_replace(' 0', '', gmstrftime(' %d', $date));
            $datestring = str_replace('DD', $daystring, $datestring);
        } else {
            $datestring = gmstrftime($format, $date);
        }
    }

/// If we are running under Windows convert from windows encoding to UTF-8
/// (because it's impossible to specify UTF-8 to fetch locale info in Win32)
/**  nous sommes encore en ISO ....
   if ($CFG->ostype == 'WINDOWS') {
       if ($localewincharset = traduction('localewincharset',false)) {
           $textlib = textlib_get_instance();
           $datestring = $textlib->convert($datestring, $localewincharset, 'utf-8');
       }
   }
**/

    return $datestring;
}

/**
 * Given a $time timestamp in GMT (seconds since epoch),
 * returns an array that represents the date in user time
 *
 * @uses HOURSECS
 * @param int $time Timestamp in GMT
 * @param float $timezone ?
 * @return array An array that represents the date in user time
 * @todo Finish documenting this function
 */
function usergetdate($time, $timezone=99) {

    $strtimezone = NULL;
    if (!is_numeric($timezone)) {
        $strtimezone = $timezone;
    }

    $timezone = get_user_timezone_offset($timezone);

    if (abs($timezone) > 13) {    // Server time
        return getdate($time);
    }

    // There is no gmgetdate so we use gmdate instead
    $time += dst_offset_on($time, $strtimezone);
    $time += intval((float)$timezone * HOURSECS);

    $datestring = gmstrftime('%S_%M_%H_%d_%m_%Y_%w_%j_%A_%B', $time);

    list(
        $getdate['seconds'],
        $getdate['minutes'],
        $getdate['hours'],
        $getdate['mday'],
        $getdate['mon'],
        $getdate['year'],
        $getdate['wday'],
        $getdate['yday'],
        $getdate['weekday'],
        $getdate['month']
    ) = explode('_', $datestring);

    return $getdate;
}

/**
 * Given a GMT timestamp (seconds since epoch), offsets it by
 * the timezone.  eg 3pm in India is 3pm GMT - 7 * 3600 seconds
 *
 * @uses HOURSECS
 * @param  int $date Timestamp in GMT
 * @param float $timezone
 * @return int
 */
function usertime($date, $timezone=99) {

    $timezone = get_user_timezone_offset($timezone);

    if (abs($timezone) > 13) {
        return $date;
    }
    return $date - (int)($timezone * HOURSECS);
}

/**
 * Given a time, return the GMT timestamp of the most recent midnight
 * for the current user.
 *
 * @param int $date Timestamp in GMT
 * @param float $timezone ?
 * @return ?
 */
function usergetmidnight($date, $timezone=99) {

    $userdate = usergetdate($date, $timezone);

    // Time of midnight of this user's day, in GMT
    return make_timestamp($userdate['year'], $userdate['mon'], $userdate['mday'], 0, 0, 0, $timezone);

}

/**
 * Returns a string that prints the user's timezone
 *
 * @param float $timezone The user's timezone
 * @return string
 */
function usertimezone($timezone=99) {

    $tz = get_user_timezone($timezone);

    if (!is_float($tz)) {
        return $tz;
    }

    if(abs($tz) > 13) { // Server time
        return traduction('heure_locale_serveur');
    }

    if($tz == intval($tz)) {
        // Don't show .0 for whole hours
        $tz = intval($tz);
    }

    if($tz == 0) {
        return 'UTC';
    }
    else if($tz > 0) {
        return 'UTC+'.$tz;
    }
    else {
        return 'UTC'.$tz;
    }

}

/**
 * Returns a float which represents the user's timezone difference from GMT in hours
 * Checks various settings and picks the most dominant of those which have a value
 *
 * @uses $CFG
 * @uses $USER
 * @param float $tz If this value is provided and not equal to 99, it will be returned as is and no other settings will be checked
 * @return int
 */
function get_user_timezone_offset($tz = 99) {

    global $USER, $CFG;

    $tz = get_user_timezone($tz);

    if (is_float($tz)) {
        return $tz;
    } else {
        $tzrecord = get_timezone_record($tz);
        if (empty($tzrecord)) {
            return 99.0;
        }
        return (float)$tzrecord->gmtoff / HOURMINS;
    }
}

/**
 * Returns an int which represents the systems's timezone difference from GMT in seconds
 * @param mixed $tz timezone
 * @return int if found, false is timezone 99 or error
 */
function get_timezone_offset($tz) {
    global $CFG;

    if ($tz == 99) {
        return false;
    }

    if (is_numeric($tz)) {
        return intval($tz * 60*60);
    }

    if (!$tzrecord = get_timezone_record($tz)) {
        return false;
    }
    return intval($tzrecord->gmtoff * 60);
}

/**
 * Returns a float or a string which denotes the user's timezone
 * A float value means that a simple offset from GMT is used, while a string (it will be the name of a timezone in the database)
 * means that for this timezone there are also DST rules to be taken into account
 * Checks various settings and picks the most dominant of those which have a value
 *
 * @uses $USER
 * @uses $CFG
 * @param float $tz If this value is provided and not equal to 99, it will be returned as is and no other settings will be checked
 * @return mixed
 */
function get_user_timezone($tz = 99) {
    global $USER, $CFG;

    $timezones = array(
        $tz,
        isset($CFG->forcetimezone) ? $CFG->forcetimezone : 99,
        isset($USER->timezone) ? $USER->timezone : 99,
        isset($CFG->timezone) ? $CFG->timezone : 99,
        );

    $tz = 99;

    while(($tz == '' || $tz == 99 || $tz == NULL) && $next = each($timezones)) {
        $tz = $next['value'];
    }

    return is_numeric($tz) ? (float) $tz : $tz;
}

/**
 * ?
 *
 * @uses $CFG
 * @uses $db
 * @param string $timezonename ?
 * @return object
 */
function get_timezone_record($timezonename) {

	return false;
	/***************************************** non implement�
    global $CFG, $db;
    static $cache = NULL;

    if ($cache === NULL) {
        $cache = array();
    }

    if (isset($cache[$timezonename])) {
        return $cache[$timezonename];
    }

    return $cache[$timezonename] = get_record_sql('SELECT * FROM '.$CFG->prefix.'timezone
                                      WHERE name = '.$db->qstr($timezonename).' ORDER BY year DESC', true);
    *****************************************************/
}


/**
 * ?
 *
 * @uses $CFG
 * @uses $USER
 * @param ? $fromyear ?
 * @param ? $to_year ?
 * @return bool
 */
function calculate_user_dst_table($from_year = NULL, $to_year = NULL, $strtimezone = NULL) {
    global $CFG, $SESSION;

    $usertz = get_user_timezone($strtimezone);

    if (is_float($usertz)) {
        // Trivial timezone, no DST
        return false;
    }

    if (!empty($SESSION->dst_offsettz) && $SESSION->dst_offsettz != $usertz) {
        // We have precalculated values, but the user's effective TZ has changed in the meantime, so reset
        unset($SESSION->dst_offsets);
        unset($SESSION->dst_range);
    }

    if (!empty($SESSION->dst_offsets) && empty($from_year) && empty($to_year)) {
        // Repeat calls which do not request specific year ranges stop here, we have already calculated the table
        // This will be the return path most of the time, pretty light computationally
        return true;
    }

    // Reaching here means we either need to extend our table or create it from scratch

    // Remember which TZ we calculated these changes for
    $SESSION->dst_offsettz = $usertz;

    if(empty($SESSION->dst_offsets)) {
        // If we 're creating from scratch, put the two guard elements in there
        $SESSION->dst_offsets = array(1 => NULL, 0 => NULL);
    }
    if(empty($SESSION->dst_range)) {
        // If creating from scratch
        $from = max((empty($from_year) ? intval(date('Y')) - 3 : $from_year), 1971);
        $to   = min((empty($to_year)   ? intval(date('Y')) + 3 : $to_year),   2035);

        // Fill in the array with the extra years we need to process
        $yearstoprocess = array();
        for($i = $from; $i <= $to; ++$i) {
            $yearstoprocess[] = $i;
        }

        // Take note of which years we have processed for future calls
        $SESSION->dst_range = array($from, $to);
    }
    else {
        // If needing to extend the table, do the same
        $yearstoprocess = array();

        $from = max((empty($from_year) ? $SESSION->dst_range[0] : $from_year), 1971);
        $to   = min((empty($to_year)   ? $SESSION->dst_range[1] : $to_year),   2035);

        if($from < $SESSION->dst_range[0]) {
            // Take note of which years we need to process and then note that we have processed them for future calls
            for($i = $from; $i < $SESSION->dst_range[0]; ++$i) {
                $yearstoprocess[] = $i;
            }
            $SESSION->dst_range[0] = $from;
        }
        if($to > $SESSION->dst_range[1]) {
            // Take note of which years we need to process and then note that we have processed them for future calls
            for($i = $SESSION->dst_range[1] + 1; $i <= $to; ++$i) {
                $yearstoprocess[] = $i;
            }
            $SESSION->dst_range[1] = $to;
        }
    }

    if(empty($yearstoprocess)) {
        // This means that there was a call requesting a SMALLER range than we have already calculated
        return true;
    }

    // From now on, we know that the array has at least the two guard elements, and $yearstoprocess has the years we need
    // Also, the array is sorted in descending timestamp order!

    // Get DB data

    static $presets_cache = array();
    if (!isset($presets_cache[$usertz])) {
        $presets_cache[$usertz] = get_records('timezone', 'name', $usertz, 'year DESC', 'year, gmtoff, dstoff, dst_month, dst_startday, dst_weekday, dst_skipweeks, dst_time, std_month, std_startday, std_weekday, std_skipweeks, std_time');
    }
    if(empty($presets_cache[$usertz])) {
        return false;
    }

    // Remove ending guard (first element of the array)
    reset($SESSION->dst_offsets);
    unset($SESSION->dst_offsets[key($SESSION->dst_offsets)]);

    // Add all required change timestamps
    foreach($yearstoprocess as $y) {
        // Find the record which is in effect for the year $y
        foreach($presets_cache[$usertz] as $year => $preset) {
            if($year <= $y) {
                break;
            }
        }

        $changes = dst_changes_for_year($y, $preset);

        if($changes === NULL) {
            continue;
        }
        if($changes['dst'] != 0) {
            $SESSION->dst_offsets[$changes['dst']] = $preset->dstoff * MINSECS;
        }
        if($changes['std'] != 0) {
            $SESSION->dst_offsets[$changes['std']] = 0;
        }
    }

    // Put in a guard element at the top
    $maxtimestamp = max(array_keys($SESSION->dst_offsets));
    $SESSION->dst_offsets[($maxtimestamp + DAYSECS)] = NULL; // DAYSECS is arbitrary, any "small" number will do

    // Sort again
    krsort($SESSION->dst_offsets);

    return true;
}

function dst_changes_for_year($year, $timezone) {

    if($timezone->dst_startday == 0 && $timezone->dst_weekday == 0 && $timezone->std_startday == 0 && $timezone->std_weekday == 0) {
        return NULL;
    }

    $monthdaydst = find_day_in_month($timezone->dst_startday, $timezone->dst_weekday, $timezone->dst_month, $year);
    $monthdaystd = find_day_in_month($timezone->std_startday, $timezone->std_weekday, $timezone->std_month, $year);

    list($dst_hour, $dst_min) = explode(':', $timezone->dst_time);
    list($std_hour, $std_min) = explode(':', $timezone->std_time);

    $timedst = make_timestamp($year, $timezone->dst_month, $monthdaydst, 0, 0, 0, 99, false);
    $timestd = make_timestamp($year, $timezone->std_month, $monthdaystd, 0, 0, 0, 99, false);

    // Instead of putting hour and minute in make_timestamp(), we add them afterwards.
    // This has the advantage of being able to have negative values for hour, i.e. for timezones
    // where GMT time would be in the PREVIOUS day than the local one on which DST changes.

    $timedst += $dst_hour * HOURSECS + $dst_min * MINSECS;
    $timestd += $std_hour * HOURSECS + $std_min * MINSECS;

    return array('dst' => $timedst, 0 => $timedst, 'std' => $timestd, 1 => $timestd);
}

// $time must NOT be compensated at all, it has to be a pure timestamp
function dst_offset_on($time, $strtimezone = NULL) {
    global $SESSION;

    if(!calculate_user_dst_table(NULL, NULL, $strtimezone) || empty($SESSION->dst_offsets)) {
        return 0;
    }

    reset($SESSION->dst_offsets);
    while(list($from, $offset) = each($SESSION->dst_offsets)) {
        if($from <= $time) {
            break;
        }
    }

    // This is the normal return path
    if($offset !== NULL) {
        return $offset;
    }

    // Reaching this point means we haven't calculated far enough, do it now:
    // Calculate extra DST changes if needed and recurse. The recursion always
    // moves toward the stopping condition, so will always end.

    if($from == 0) {
        // We need a year smaller than $SESSION->dst_range[0]
        if($SESSION->dst_range[0] == 1971) {
            return 0;
        }
        calculate_user_dst_table($SESSION->dst_range[0] - 5, NULL, $strtimezone);
        return dst_offset_on($time, $strtimezone);
    }
    else {
        // We need a year larger than $SESSION->dst_range[1]
        if($SESSION->dst_range[1] == 2035) {
            return 0;
        }
        calculate_user_dst_table(NULL, $SESSION->dst_range[1] + 5, $strtimezone);
        return dst_offset_on($time, $strtimezone);
    }
}

function find_day_in_month($startday, $weekday, $month, $year) {

    $daysinmonth = days_in_month($month, $year);

    if($weekday == -1) {
        // Don't care about weekday, so return:
        //    abs($startday) if $startday != -1
        //    $daysinmonth otherwise
        return ($startday == -1) ? $daysinmonth : abs($startday);
    }

    // From now on we 're looking for a specific weekday

    // Give "end of month" its actual value, since we know it
    if($startday == -1) {
        $startday = -1 * $daysinmonth;
    }

    // Starting from day $startday, the sign is the direction

    if($startday < 1) {

        $startday = abs($startday);
        $lastmonthweekday  = strftime('%w', mktime(12, 0, 0, $month, $daysinmonth, $year, 0));

        // This is the last such weekday of the month
        $lastinmonth = $daysinmonth + $weekday - $lastmonthweekday;
        if($lastinmonth > $daysinmonth) {
            $lastinmonth -= 7;
        }

        // Find the first such weekday <= $startday
        while($lastinmonth > $startday) {
            $lastinmonth -= 7;
        }

        return $lastinmonth;

    }
    else {

        $indexweekday = strftime('%w', mktime(12, 0, 0, $month, $startday, $year, 0));

        $diff = $weekday - $indexweekday;
        if($diff < 0) {
            $diff += 7;
        }

        // This is the first such weekday of the month equal to or after $startday
        $firstfromindex = $startday + $diff;

        return $firstfromindex;

    }
}

/**
 * Calculate the number of days in a given month
 *
 * @param int $month The month whose day count is sought
 * @param int $year The year of the month whose day count is sought
 * @return int
 */
function days_in_month($month, $year) {
   return intval(date('t', mktime(12, 0, 0, $month, 1, $year, 0)));
}

/**
 * Calculate the position in the week of a specific calendar day
 *
 * @param int $day The day of the date whose position in the week is sought
 * @param int $month The month of the date whose position in the week is sought
 * @param int $year The year of the date whose position in the week is sought
 * @return int
 */
function dayofweek($day, $month, $year) {
    // I wonder if this is any different from
    // strftime('%w', mktime(12, 0, 0, $month, $daysinmonth, $year, 0));
    return intval(date('w', mktime(12, 0, 0, $month, $day, $year, 0)));
}
