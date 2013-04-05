<?php
/**
 * @version $Id: chercheuse.class.php 612 2009-03-30 17:23:52Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
/*********************************************
*
cette classe s'occupe(ra) de gerer les
criteres de recherche dans les listes

*
**********************************************/


class critereInfo {
    var $id;
    var $type;
    var $valeur;
    var $SQLOui,$SQLNon;

    function critereInfo ($id,$type,$SQLOui,$SQLNon,$valeur) {
        $this->id=$id;
        $this->type=$type;
        $this->SQLOui=$SQLOui;
        $this->SQLNon=$SQLNon;
        $this->valeur=$valeur;
    }
}

class chercheuse {

    var $criteres=array();
    var $tpl;
    var $varname;



    function chercheuse ($tpl,$varname) {
        $this->tpl=$tpl;
        $this->varname=$varname;
        $this->criteres=array();

    }

    function addCritere ($id,$type,$SQLOui,$SQLNon,$valeur) {
        $this->criteres[]=new critereInfo($id,$type,$SQLOui,$SQLNon,$valeur);
    }


    function printToScreen () {
        $this->tpl->gotoBlock("_ROOT");
        foreach ($this->criteres as $critere) {
            switch ($critere->type) {
                case 'select': break;
                case 'checkbox': break;
                case 'text': break;
            }
        }

    }

    function getCritereSQL() {
        $ret="";
        foreach ($this->criteres as $critere) {
            if ($critere->valeur)
                $ret=concatAvecSeparateur($ret,$critere->SQLOui," and ");
            else
                $ret=concatAvecSeparateur($ret,$critere->SQLNon," and ");
        }
        return $ret;
    }

    function getCritereHTTP() {
        $ret="";
        foreach ($this->criteres as $numero=>$critere) {
            if ($critere->valeur && $critere->type !="force")
                $ret=concatAvecSeparateur($ret,concatAvecSeparateur($critere->id,$critere->valeur,"="),"&");
        }
        return $ret;
    }


}

