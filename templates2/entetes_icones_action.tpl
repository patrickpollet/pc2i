<!-- START COMMENT -->

/**
 * @author Patrick Pollet
 * @version $Id: entetes_icones_action.tpl 333 2008-11-29 16:45:44Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */


modeles des entetes des colonnes
les classes (entete_triable, entete_nontriable,entete_action) 
doivent etre dans la feuille de style 
 NON UTILISE
 REMPLACE PAR une colonne simple Actions et des icones dans des div 
<!-- END COMMENT -->



<!-- START BLOCK : entetes_icones_action -->

<!-- START BLOCK : entete_texte_triable -->
 <td><a class="entete_triable"
            href="{url}" title="{alt}">{entete}</a>
	{tri}
</td> 
<!-- END BLOCK : entete_texte_triable -->

<!-- START BLOCK : entete_texte_nontriable -->
 <td class="entete_nontriable">{entete} </td> 
<!-- END BLOCK : entete_texte_nontriable -->


<!-- START BLOCK : entete_consulter -->
            <td width="40" class="entete_action">{entete_consulter}</td>
<!-- END BLOCK : entete_consulter -->
<!-- START BLOCK : entete_dupliquer -->
            <td width="40" class="entete_action">{entete_dupliquer}</td>
<!-- END BLOCK : entete_dupliquer -->
<!-- START BLOCK : entete_modifier -->
            <td width="40" class="entete_action">{entete_modifier}</td> 
<!-- END BLOCK : entete_modifier -->
<!-- START BLOCK : entete_supprimer -->
            <td width="40" class="entete_action">{entete_supprimer}</td>
<!-- END BLOCK : entete_supprimer -->
<!-- START BLOCK : entete_filtrer -->
            <td width="40" class="entete_action">{entete_filtrer}</td>
<!-- END BLOCK : entete_filtrer -->
<!-- START BLOCK : entete_valider -->
            <td width="40" class="entete_action">{entete_valider}</td>
<!-- END BLOCK : entete_valider -->
<!-- START BLOCK : entete_invalider -->
            <td class="entete_action">{entete_invalider}</td>
<!-- END BLOCK : entete_invalider -->


<!-- END BLOCK : entetes_icones_action -->




