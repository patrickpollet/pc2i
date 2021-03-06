<?php
/**
 * adapt� PP avec pclzip deja install�
 * et definition de PCLZIP_TEMPORARY_DIR forc�e sinon pb de droits !
 */
//require_once 'pclzip/pclzip.lib.php';
//require_once '../../../pclzip-2-7/pcltrace.lib.php';
//require_once('../../../pclzip-2-7/pclzip-trace.lib.php');



require_once 'ZipInterface.php';
class PclZipProxyException extends Exception
{ }
/**
 * Proxy class for the PclZip library
 * You need PHP 5.2 at least
 * You need Zip Extension or PclZip library
 * Encoding : ISO-8859-1
 * Last commit by $Author: neveldo $
 * Date - $Date: 2009-05-29 10:05:11 +0200 (ven., 29 mai 2009) $
 * SVN Revision - $Rev: 28 $
 * Id : $Id: odf.php 28 2009-05-29 08:05:11Z neveldo $
 *
 * @copyright  GPL License 2008 - Julien Pauli - Cyril PIERRE de GEYER - Anaska (http://www.anaska.com)
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL License
 * @version 1.3
 */
class PclZipProxy implements ZipInterface
{


    //const TMP_DIR = '.tmp/';
	protected $openned = false;
	protected $filename;
	protected $pclzip;
    /**
     * Class constructor
     *
     * @throws PclZipProxyException
     */
	public function __construct()
	{
        //PP adaptation a la plate-forme
        global $CFG;
        //gros pb si $dir contient alors  un double slash dedans car il y en aurait un a la fin de cfg->chemin_ressources
        //la bibliotheque pclzip supprime alors le 1er caractere des noms de fichiers dans le zip
        //voir en mode debug pclzip-trace.lib.php lignes 3264 et 3267
        $dir = add_slash_url($CFG->chemin_ressources).'tmp/odt/';
        cree_dossier_si_absent($dir);
        $dir .= time();
        cree_dossier_si_absent($dir);

       //attention ce constructeur peut �tre appel� plusieurs fois

       if (!defined('TMP_DIR')) define ('TMP_DIR',$dir);

        if(!defined ('PCLZIP_TEMPORARY_DIR'))
            define( 'PCLZIP_TEMPORARY_DIR', $dir  ); //important

       //a faire apr�s le define de PCLZIP_TEMPORARY_DIR
       require_once($CFG->chemin_commun."/pclzip-2-7/pcltrace.lib.php");
       require_once($CFG->chemin_commun."/pclzip-2-7/pclzip-trace.lib.php");
        if (! class_exists('PclZip')) {
            throw new PclZipProxyException('PclZip class not loaded - PclZip library
             is required for using PclZipProxy'); ;
        }
	}
	/**
	 * Open a Zip archive
	 *
	 * @param string $filename the name of the archive to open
	 * @return true if openning has succeeded
	 */
	public function open($filename)
	{

		if (true === $this->openned) {
			$this->close();
		}
		if (!file_exists(TMP_DIR)) {
			mkdir(TMP_DIR);
		}
		$this->filename = $filename;
		

		$this->pclzip = new PclZip($this->filename);
		$this->openned = true;
		return true;
	}
	/**
	 * Retrieve the content of a file within the archive from its name
	 *
	 * @param string $name the name of the file to extract
	 * @return the content of the file in a string
	 */
	public function getFromName($name)
	{
		if (false === $this->openned) {
			return false;
		}
		$name = preg_replace("/(?:\.|\/)*(.*)/", "\\1", $name);
		$extraction = $this->pclzip->extract(PCLZIP_OPT_BY_NAME, $name,
			PCLZIP_OPT_EXTRACT_AS_STRING);
		if (!empty($extraction)) {
			return $extraction[0]['content'];
		}
		return false;
	}
	/**
	 * Add a file within the archive from a string
	 *
	 * @param string $localname the local path to the file in the archive
	 * @param string $contents the content of the file
	 * @return true if the file has been successful added
	 */
	public function addFromString($localname, $contents)
	{
		if (false === $this->openned) {
			return false;
		}
		if (file_exists($this->filename) && !is_writable($this->filename)) {
			return false;
		}

        $localname = preg_replace("/(?:\.|\/)*(.*)/", "\\1", $localname);
		$localpath = dirname($localname);
		$tmpfilename = TMP_DIR . '/' . basename($localname);
        if (false !== file_put_contents($tmpfilename, $contents)) {
        	$this->pclzip->delete(PCLZIP_OPT_BY_NAME, $localname);
			$add = $this->pclzip->add($tmpfilename,
				PCLZIP_OPT_REMOVE_PATH, TMP_DIR,
				PCLZIP_OPT_ADD_PATH, $localpath
                );
			unlink($tmpfilename);
			if (!empty($add)) {
				return true;
			}
		}
        return false;
	}
	/**
	 * Add a file within the archive from a file
	 *
	 * @param string $filename the path to the file we want to add
	 * @param string $localname the local path to the file in the archive
	 * @return true if the file has been successful added
	 */
	public function addFile($filename, $localname = null)
	{
		if (false === $this->openned) {
			return false;
		}
		if ((file_exists($this->filename) && !is_writable($this->filename))
			|| !file_exists($filename)) {
			return false;
		}
		if (isSet($localname)) {
			$localname = preg_replace("/(?:\.|\/)*(.*)/", "\\1", $localname);
			$localpath = dirname($localname);
			$tmpfilename = TMP_DIR . '/' . basename($localname);
		} else {
			$localname = basename($filename);
			$tmpfilename = TMP_DIR . '/' . $localname;
			$localpath = '';
		}
		if (file_exists($filename)) {
			copy($filename, $tmpfilename);
			$this->pclzip->delete(PCLZIP_OPT_BY_NAME, $localname);
			$this->pclzip->add($tmpfilename,
				PCLZIP_OPT_REMOVE_PATH, TMP_DIR,
				PCLZIP_OPT_ADD_PATH, $localpath);
			unlink($tmpfilename);
			return true;
		}
		return false;
	}
	/**
	 * Close the Zip archive
	 * @return true
	 */
	public function close()
	{
		if (false === $this->openned) {
			return false;
		}
		$this->pclzip = $this->filename = null;
		$this->openned = false;

		if (file_exists(TMP_DIR)) {
			$this->_rrmdir(TMP_DIR);
			rmdir(TMP_DIR);
		}

		return true;
	}
	/**
	 * Empty the temporary working directory recursively
	 * @param $dir the temporary working directory
	 * @return void
	 */
	private function _rrmdir($dir)
	{
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != '.' && $file != '..') {
					if (is_dir($dir . '/' . $file)) {
						$this->_rrmdir($dir . '/' . $file);
						rmdir($dir . '/' . $file);
					} else {
						unlink($dir . '/' . $file);
					}
				}
			}
			closedir($handle);
		}
	}
}

?>