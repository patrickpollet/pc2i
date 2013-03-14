<!-- START COMMENT -->
/**
 * @author Patrick Pollet
 * @version $Id: icones_action_liste.tpl 342 2008-12-01 22:38:04Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf

 */
ce template contient les modeles des differentes icones d'action possibles
pouvant apparaitre dans les lignes des listes.

mettre dans le template liste cette ligne
<!-- INCLUDE BLOCK : icones_action_liste -->
et dans le script avant le prepare()

$tpl->assignInclude("icones_action_liste",$CFG->chemin_templates."/icones_action_liste.tpl");

Elles sont instanciées et ajoutées selon les droits



//voir le script codes/notions/notions.php dès la version 1.5



Pour ajouter des icones, dupliquer un des blocs et adapter son identifiant
(ex "consulter" -> "xxxxx" en respectant imperativement les conventions
de nommage :
      le block doit s'appeler : td_xxxxx
      le titre et la chaine alt : alt_xxxxx  (a reporter dans fr.php)
      l'url : url_xxxxx
      la version visible : td_xxxxx_oui et l'invisible td_xxxxx_non

      et les deux fichiers d'images i_xxxxx.gif et i_xxxxx_a.gif
      ne pas oublier d'ajouter une entrée pour les entetes des colonnes (voir
      le template entetes_icones_action.tpl)
l'ordre d'apparition des icones est fixé par l'ordre des blocs ici

les classes (icone_action) doivent étre dans la feuille de style

version 2 sans les scripts MM_swapimage
on met systématiquement une image transparente de 20x20 et c'est via le CSS
que l'on affiche la bonne image, avec un effet via la balise css hover



<!-- END COMMENT -->

<!-- START BLOCK : icones_action_liste -->



<!-- START BLOCK : td_valider_oui -->
<td class="icone_action">
<div class="icone_{etatv}">
	<!-- START BLOCK : deb_a_v -->
	<a href="{global_jsvoid}"
		title="{alt_valider}"
		onclick="openPopup('{url_valider}','','{lp}','{hp}')" >
	<!-- END BLOCK : deb_a_v -->
	<img src="{chemin_images}/i_blank.gif"
         alt="{alt_valider}" title="{alt_valider}" />
	<!-- START BLOCK : fin_a_v -->
	</a>
	<!-- START BLOCK : fin_a_v -->
</div>
</td>
<!-- END BLOCK : td_valider_oui -->

<!-- START BLOCK : td_valider_non -->
<td>
<div class="icone_action icone_vide">

</div>
</td>
<!-- END BLOCK : td_valider_non -->


<!-- START BLOCK : td_consulter_oui -->
<td class="icone_action">
<div class="icone_consulter">
	<a href="{global_jsvoid}"  title="{alt_consulter}"
         onclick="openPopup('{url_consulter}','','{lp}','{hp}')">
	<img src='{chemin_images}/i_blank.gif'
         alt="{alt_consulter}" />
	</a>
</div>
</td>
<!-- END BLOCK : td_consulter_oui -->




<!-- START BLOCK : td_consulter_non -->
<td class="icone_action icone_vide_c"></td>
<!-- END BLOCK : td_consulter_non -->


<!-- START BLOCK : td_dupliquer_oui -->
<td class="icone_action">
<div class="icone_dupliquer">
	<a href="{global_jsvoid}"  title="{alt_dupliquer}"
		onclick="openPopup('{url_dupliquer}','','{lp}','{hp}')">
	<img src='{chemin_images}/i_blank.gif'
        alt="{alt_dupliquer}" />
	</a>
</div>
</td>
<!-- END BLOCK : td_dupliquer_oui -->

<!-- START BLOCK : td_dupliquer_non -->
<td class="icone_action icone_vide_d"></td>
<!-- END BLOCK : td_dupliquer_non -->



<!-- START BLOCK : td_modifier_oui -->
<td class="icone_action">
<div class="icone_modifier">
	<a href="{global_jsvoid}"  title="{alt_modifier}"
		onclick="openPopup('{url_modifier}','','{lp}','{hp}')" >
	<img src='{chemin_images}/i_blank.gif'
        alt="{alt_modifier}" />
	</a>
</div>
</td>
<!-- END BLOCK : td_modifier_oui -->

<!-- START BLOCK : td_modifier_non -->
<td class="icone_action icone_vide_m"></td>
<!-- END BLOCK : td_modifier_non -->





<!-- START BLOCK : td_filtrer_oui -->
<td class="icone_action">
<div class="icone_filtrer">
	<a href="{url_filtrer}"
		title="{alt_filtrer}" >
		<img src="{chemin_images}/i_blank.gif"
			alt="{alt_filtrer}" />
		</a>
</div>
</td>
<!-- END BLOCK : td_filtrer_oui-->

<!-- START BLOCK : td_defiltrer_oui -->
<td class="icone_action">
<div class="icone_defiltrer">
	<a href="{url_filtrer}"
		title="{alt_non_filtrer}">
		<img src="{chemin_images}/i_blank.gif"
			alt="{alt_non_filtrer}" />
	</a>
</div>
</td>
<!-- END BLOCK : td_defiltrer_oui -->

<!-- START BLOCK : td_filtrer_non -->
<td>
<div class="icone_action icone_vide_f">
</div>
</td>
<!-- END BLOCK : td_filtrer_non -->




<!-- START BLOCK : td_invalider_oui -->
<td class="icone_action">
<div class="icone_invalider">
	<a href="{url_iv}"
	title="{alt_invalider}"
	onclick="return confirm('{js_iv}')">
	<img src="{chemin_images}/i_blank.gif"
	 alt="{alt_invalider}" />
	 </a>
</div>
</td>
<!-- END BLOCK : td_invalider_oui -->
<!-- START BLOCK : td_invalider_non -->
<td>
<div class="icone_action icone_vide_iv">
</div>
</td>
<!-- END BLOCK : td_invalider_non -->


<!-- START BLOCK : td_supprimer_oui  -->
<td class="icone_action">
<div class="icone_supprimer">
	<a href="{url_supprimer}"   title="{alt_supprimer}"
		onclick="return confirm('{js_supp}')">
	<img src='{chemin_images}/i_blank.gif'
        alt="{alt_supprimer}" />
	</a>
</div>
</td>
<!-- END BLOCK : td_supprimer_oui-->


<!-- START BLOCK : td_supprimer_non -->
<td>
<div class="icone_action icone_vide_s">
</div>
</td>
<!-- END BLOCK : td_supprimer_non -->


<!-- END BLOCK : icones_action_liste -->














