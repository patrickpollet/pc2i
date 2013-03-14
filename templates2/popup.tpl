<!-- START COMMENT -->
/**
 * @author Patrick Pollet
 * @version $Id: popup.html 1051 2010-03-09 18:36:08Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
<!-- END COMMENT -->

<!-- INCLUDE BLOCK : C2Iheader -->
<body   onload="{global_onload}" >

<!-- INCLUDE BLOCK : C2Ilogo -->

<div id="contenu_popup">
		     <div id="titre">{titre_popup}


                <!-- START BLOCK : fermer -->
  <div {style_quitter} id="quitter">
<ul class="menu_niveau1">
<li class="menu_niveau1_item icone_menu_fermer">
     <a href="#" title="{fermer}" onclick="{global_windowclose}">
       <img src="{chemin_images}/t_fermer.gif" alt="{fermer}"/>
     </a>
</li>
</ul>
  </div>
<!-- END BLOCK : fermer -->

<!-- START BLOCK : retour -->
  <div {style_retour} id="retour">
<ul class="menu_niveau1">
<li class="menu_niveau1_item icone_menu_retour">
   <a href="{url_retour}" title="{retour}">
     <img src="{chemin_images}/t_retour.gif" alt="{retour}"/>
   </a>
</li>
</ul>
  </div>
<!-- END BLOCK : retour -->



              </div>

<div id="corps">
<!-- INCLUDE BLOCK : corps -->

<!-- START BLOCK : retour_bas -->
<div class="centre">
       {bouton_retour}
</div>
    <!-- END BLOCK : retour_bas -->

</div>

</div>


 <!-- INCLUDE BLOCK : C2Ifooter -->


<!-- START BLOCK : DUMP -->
<!--
USER={USER}
CFG={CFG}
SESSION={SESSION}
POST={POST}
GET={GET}
ENV={ENV}
REQUEST={REQUEST}
SERVER={SERVER}
FILES={FILES}
PU={PU}
-->
<!-- END BLOCK : DUMP -->

</body>
</html>