
<!-- START COMMENT -->
/**
 * @author Patrick Pollet
 * @version $Id: login.html 1224 2011-03-16 11:53:54Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

 /* partie commun des ecrans de login (cert, pos, ano ...)


 */

<!-- END COMMENT -->

<!-- INCLUDE BLOCK : C2Iheader -->

<body onload="{global_onload}" >



<!-- INCLUDE BLOCK : login  -->


<div id="contenu_principale">
        <div id="forme_login">
        <a href="index.php"><img src="{chemin_images}/logos/logo_{CFG:c2i}.png" alt="{logo_c2i}"/></a>
         <img src="{chemin_images}/logos/certificat_{CFG:c2i}.png" alt="Test"/>
        <div style="float:right;">
           <img src="{chemin_images}/test02{type_p}.png" width="385" height="54" alt="{alt_type_p}"/>
        </div>
        </div>

</div>

 <!-- INCLUDE BLOCK : C2Ifooter -->

<!-- START BLOCK : ALERT -->
<script type="text/JavaScript">
<!--
    alert('{err_msg}');
    document.fc.identifiant.focus();
-->
</script>
<!-- END BLOCK : ALERT -->

<!-- START BLOCK : FOCUS -->
<script  type="text/JavaScript">
<!--
    document.fc.identifiant.focus();
-->
</script>
<!-- END BLOCK : FOCUS -->


</body>
</html>

<!-- START BLOCK : DUMP -->
<!--
USER={USER}
CFG={CFG}
SESSION={SESSION}
POST={POST}
GET={GET}
SERVER={SERVER}
-->
<!-- END BLOCK : DUMP -->
