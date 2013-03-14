<!-- START COMMENT -->
/**
 * @author Patrick Pollet
 * @version $Id: C2Iglobales.tpl 452 2009-02-15 10:12:49Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

- variables globales visibles de tous les templates crees par C2ITemplate ou 
une classe descendante. Le prefix global_ est juste pour la lisibilité des
templates 
- d'après http://templatepower.codocad.com/phpBB/viewtopic.php?t=19
- il a fallu modifier la classe TemplatePowerParser . Notez que le séparateur
  entre la nom de la variable et sa valeur est le ':=',comme en Pascal,et plus
 le simple '=' afin de permettre d'avoir des symboles '=' dans la valeur ( voir 
l'exemple ci-dessous) 
- il est imperatif d'encadrer ces déclarations par un block qui ne sera 
pas créé pour éviter que ces lignes n'apparaissent dans le navigateur. 

- pour acceder a ces variables, juste encadrer leur nom par des accolades et
C2ITemplate fera le reste ex : {pp} (voir la méthode LasttMinuteFixUp. 

<!-- END COMMENT -->

<!-- START BLOCK : GLOBALES -->
<!-- VAR : global_popupsrwh:=scrollbars=yes,resizable=yes,width={lp},height={hp} -->
<!-- VAR : global_minipopupsrwh:=scrollbars=yes,resizable=yes,width={lpm},height={hpm} -->

<!-- VAR : global_jsvoid:=javascript:void(0);  -->
<!-- VAR : pp := Patrick Pollet -->
<!-- END BLOCK : GLOBALES -->