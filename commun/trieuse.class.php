<?php
/**
 * @version $Id: trieuse.class.php 1113 2010-09-07 11:23:04Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */


/*********************************************
s'occupe des colonnes triables dans les listes

**********************************************/


class triInfo {
    var $id;
    var $colonne;
    var $SQLasc,$SQLdesc;
    var $altTPL;
    var $nomBlock; //rev 977 dans le cas d'unh bloc de tri conditionnel

    function triInfo ($id,$colonne,$SQLasc="",$SQLdesc="",$altTPL="",$nomBlock="") {
        $this->id=$id;
        $this->colonne=$colonne;
        $this->SQLasc=$SQLasc? $SQLasc : ($colonne." asc");
        $this->SQLdesc=$SQLdesc? $SQLdesc : ($colonne." desc");
        $this->altTPL=$altTPL; // agerer
        $this->nomBlock=$nomBlock;
        if (!empty($this->nomBlock)) $this->nomBlock .='.'; //ajoute le point
    }
}

class trieuse {

    var $colonnes=array();
    var $tpl;
    var $varname;
    var $urlTri;
    var $critereTri;
    var $critereTriDefaut=0;
    var $critereSQL;




    function trieuse ($tpl,$varname,$urlTri,$critereTri=0) {
        $this->tpl=$tpl;
        $this->varname=$varname;  //non utilise encore


        $this->urlTri=$urlTri;
        $this->critereTri=$critereTri;
        $this->critereTriDefaut=1;  //1ere colonne
        $this->critereSQL="";
        $this->colonnes=array(new TriInfo(0,""));// les Numeros commenceront a 1 !

    }

    function addColonne ($id,$colonneSQL,$SQLasc="",$SQLdesc="",$altTPL="",$nomBlock="") {
        $this->colonnes[]=new TriInfo($id,$colonneSQL,$SQLasc,$SQLdesc,$altTPL,$nomBlock);
    }

    function setTriDefaut ($id,$asc=true) {
        foreach ($this->colonnes as $numero=>$colonne)
            if ($colonne->id == $id) {
                $this->critereTriDefaut= $asc ? $numero : -$numero;
            }
    }

    function printToScreen () {
        $this->tpl->gotoBlock("_ROOT");
        $this->critereTri= $this->critereTri ? $this->critereTri :$this->critereTriDefaut;
        foreach ($this->colonnes as $numero=>$colonne) {
	        if ($numero) //saute ligne 0
		        if (abs($this->critereTri)==$numero) {
			        $this->critereSQL= $this->critereTri>0 ? $colonne->SQLasc : $colonne->SQLdesc;
			        $this->printIconesTri($colonne,$this->critereTri);
			        $this->tpl->assignURL($colonne->nomBlock."url_".$colonne->id,
				        concatAvecSeparateur($this->urlTri,"tri=".(-$this->critereTri),"&amp;")); //pour inversion

		        } else {
			        $this->tpl->assignURL($colonne->nomBlock."url_".$colonne->id,
				        concatAvecSeparateur($this->urlTri,"tri=$numero","&amp;")); // ascendant par défaut
			        $this->printIconesTri($colonne,0);
		        }
        }

    }

    function printIconesTri($colonne,$critere) {
        if (!$colonne->altTPL)
            print_icones_tri($this->tpl,$colonne->nomBlock."tri_".$colonne->id,$critere);
        else {
            //TODO
        }
    }

    function getCritereSQL() {
        $this->printToScreen(); //
        foreach ($this->colonnes as $numero=>$colonne) {
	        if ($numero) //saute ligne 0
		        if (abs($this->critereTri)==$numero) {
			        $this->critereSQL= $this->critereTri>0 ? $colonne->SQLasc : $colonne->SQLdesc;
                    break; //un seul
		        }
        }
        return $this->critereSQL;
    }

    function getParametreTri() {
        if ($this->critereTri)
            return "tri=".$this->critereTri;
        if ($this->critereTriDefaut)
            return "tri=".$this->critereTriDefaut;
        return "";
    }

}



?>
