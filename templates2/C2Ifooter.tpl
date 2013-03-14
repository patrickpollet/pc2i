
<!-- START COMMENT -->
/**
 * @author Patrick Pollet
 * @version $Id: C2Ifooter.tpl 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

 /* contient la derniere ligne des pages
 * voir la feuille de style

 */

<!-- END COMMENT -->

<!-- START COMMENT -->
ne pas le mettre  dans le div footer qui est en position absolue et fausse le calcul de la position souris!

code js perimé en v3.0
<script src="{chemin_commun}/js/parseurl.js" type="text/JavaScript"></script>

<!-- END COMMENT -->

<script type="text/JavaScript">
var chemin_images="{CFG:chemin_images}";
var chemin_serveur="{CFG:wwwroot}";
</script>



<!-- START BLOCK : info_bulle -->
<script type="text/JavaScript">
//<![CDATA[
     InitBulle("#000000","#FBFFD9","red",1);
//]]>
</script>
<!-- END BLOCK : info_bulle -->



<!-- START COMMENT -->
revision 981 ajout automatique dans le pied de page par le g�n�rateur de template
donc inutile de mettre ce code dans chaque mod�le
NB: la forme DOIT avoir un id=monform
<!-- END COMMENT -->

<!-- START BLOCK : validation -->
 <script type="text/javascript">
//<![CDATA[
    validator  =new Validation ("monform",{useTitles:true});
//]]>
</script>

<!-- END BLOCK : validation -->

<!-- START COMMENT -->
revision 981 ajout automatique dans le pied de page par le g�n�rateur de template
donc inutile de mettre ce code dans chaque mod�le
NB: le div  DOIT avoir un id=tabs
<!-- END COMMENT -->

<!-- START BLOCK : fab_tabs -->
<script type="text/javascript">
            new Fabtabs('tabs');
</script>
<!-- END BLOCK : fab_tabs -->

<!-- START BLOCK : form_actions -->
{form_actions}	
<!-- END BLOCK : form_actions -->



<div id="footer">
    <div class= "taille1">
            <a href="http://c2i.education.fr/wikipfc2i-X/index.php/Svn_log_1.5" target="_blank">
            <span class="rouge1">version {CFG:version}</span>
            </a> - {copyright}
            &nbsp;&nbsp;
            <a href="#"
                onclick="openPopup('{chemin_commun}/legende.php?ou=aide&amp;quoi=http://c2i.education.fr/pf/licence.html','{licence}','{lp}','{hp}')">{licence}</a>
            &nbsp;&nbsp;
            <a href="#"
                onclick="openPopup('{chemin_commun}/legende.php?ou=aide&amp;quoi=http://c2i.education.fr/pf/credits_{CFG:c2i}.html','{credits}','{lp}','{hp}')">{credits}</a>
            &nbsp;&nbsp;
            <a href="#"
                onclick="openPopup('{chemin_commun}/legende.php?ou=aide&amp;quoi=http://c2i.education.fr/pf/prerequis.html','{pre_requis}','{lp}','{hp}')">{pre_requis}</a>
            &nbsp;&nbsp;
            <a href="{CFG:adresse_forum}" target="_blank">Forum</a>
            &nbsp;&nbsp;
            <a href="{CFG:adresse_wiki}" target="_blank">Wiki</a>
            &nbsp;&nbsp;
            <span class="commentaire1">{USER:fullname}</span>
       </div>
</div>

<!-- START BLOCK : validators -->

 <div class="centre">

            Ce site essaie de respecter les normes suivantes (merci de nous signaler les anomalies sur le forum) <br/>

<!--

            <a href="http://www.contentquality.com/mynewtester/cynthia.exe?rptmode=-1&amp;url1={me}" target="_blank">
            <img src='{chemin_images}/w3c/colophon_sec508.gif'
                 height="15"
                 width="80"
                 alt="Section 508"
                 title="Section 508"
                  />
          </a>

          <a href="http://www.contentquality.com/mynewtester/cynthia.exe?rptmode=0&amp;warnp2n3e=1&amp;url1={me}"
             title="Explanation of WCAG Conformance" target="_blank">
            <img src='{chemin_images}/w3c/colophon_wai-aa.gif'
                 height="15"
                 width="80"
                 alt="WCAG"
                 title="W3C-WAI Web Content Accessibility Guidelines."
                 />

          </a>
-->
          <a href="http://validator.w3.org/check?verbose=1&amp;ss=1&amp;uri={me}" target="_blank">
            <img src='{chemin_images}/w3c/colophon_xhtml.png'
                 height="15"
                 width="80"
                 alt="Valid XHTML"
                 title="valid XHTML."
                  />
          </a>

          <a href="http://jigsaw.w3.org/css-validator/validator?uri={me}" target="_blank">
            <img src='{chemin_images}/w3c//colophon_css.png'
                 height="15"
                 width="80"
                 alt="Valid CSS"
                 title="valid CSS."
                  />
          </a>
</div>




<!-- END BLOCK : validators -->


<!-- START BLOCK : table_sorter -->
<script type="text/JavaScript">
<!--
   initalizeTableSort();
-->
</script>
<!-- END BLOCK : table_sorter -->


<!-- START BLOCK : inline_mod -->
<script type="text/JavaScript">
<!--
  tds=getElementsByClassName('editable',null,'td');
  for (var i=0; i< tds.length;i++)
    tds[i].title="{double_click_toedit}";
-->
</script>
<!-- END BLOCK : inline_mod -->



<!-- START BLOCK : alert_js -->
<script type="text/JavaScript">
<!--
    alert('{message}');
 -->
</script>
<!-- END BLOCK : alert_js -->





<!-- START BLOCK : EXTRA_FOOTER -->
<!-- START BLOCK : element -->
<!-- END BLOCK : element -->
<!-- END BLOCK : EXTRA_FOOTER -->
