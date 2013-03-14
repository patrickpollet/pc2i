<?php
/**
 * @author Patrick Pollet
 * @version $Id: local_events.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */


 function local_evt_examen_verouillage ($data) {
     dump_event(__FUNCTION__,$data);
     return true;
 }

 function local_evt_examen_deverouillage ($data) {
     dump_event(__FUNCTION__,$data);
     return true;
 }


?>
