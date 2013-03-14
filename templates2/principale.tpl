<!-- START COMMENT -->
/**
 * @author Patrick Pollet
 * @version $Id: principale.tpl 1051 2010-03-09 18:36:08Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
<!-- END COMMENT -->


<!-- INCLUDE BLOCK : C2Iheader -->

<body  onload="{global_onload}" >


<!-- INCLUDE BLOCK : C2Ilogo -->

<div id="contenu_principale">

  <div id="titre">
    <div id="menu">
<ul class="menu_niveau1">
<!-- START BLOCK : menu_item -->
<li class="menu_niveau1_item icone_menu_{item}">
  <a href="{url}" title="{alt}">
      <img src="{chemin_images}/t_{item}{_b}.gif" alt="{alt}"/>
  </a>
</li>
<!-- END BLOCK : menu_item -->
</ul>



<!-- START BLOCK : quitter -->
  <div {style_quitter} id="quitter">
<ul class="menu_niveau1">
<li class="menu_niveau1_item icone_menu_quitter">

     <a href="{url_quit}" title="{quitter}">
       <img src="{chemin_images}/t_quitter.gif" alt="{quitter}"/>
     </a>
</li>
</ul>
  </div>
<!-- END BLOCK : quitter -->

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
</div>
<div id="corps">
<!-- INCLUDE BLOCK : corps -->
</div>
</div>



<!-- INCLUDE BLOCK : C2Ifooter -->

</body>
</html>

<!-- START BLOCK : DUMP -->
<!--
USER={USER}
CFG={CFG}
SESSION={SESSION}
POST={POST}
GET={GET}
ENV={ENV}
REQUEST={REQUEST}
PU={PU}

SERVER={SERVER}
-->
<!-- END BLOCK : DUMP -->

