
<!-- START COMMENT -->
/**
 * @author Patrick Pollet
 * @version $Id: C2Ilogo.tpl 1227 2011-03-17 07:06:49Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/* logo C2I sur toute les pages de type "principale"
* mettre simplement un <!-- INCLUDE BLOCK : C2Ilogo --> aussitot apr�s body
 * pas de saut de ligne entre les images src , sinon une petite ligne blanche apparait !!!!
 */

<!-- END COMMENT -->

        <div id="logo">
           <div>
             <a href="{url_accueil}" title="{alt_accueil}">
                <img src="{chemin_images}/logos/logo_{CFG:c2i}.png" alt="{logo_c2i}"/>
             </a>
             <img src="{chemin_images}/logos/certificat_{CFG:c2i}.png" /><img src="{chemin_images}/test02{image_c}.png" alt="Test"/>
           </div>
           <div id="arc770">
           </div>
        </div>
