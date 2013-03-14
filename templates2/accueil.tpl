<div id="accueil">

          <p>{texte_bienvenue} </p>
          <p class="commentaire1">{message_a}&nbsp;</p>
	      <p class="rouge2" id="message_version">&nbsp;</p>
          <p>  Dans cet espace vous pouvez g&eacute;rer la liste : </p>
                <ul class="commentaire2">
                  <li>des questions, </li>
                  <li>des examens </li>
                  <li>des inscrits.</li>
<!-- START BLOCK : notions_parcours -->
                  <li>des parcours</li>
                  <li>des notions  </li>
<!-- END BLOCK : notions_parcours -->


<!-- START BLOCK : qcm_profs -->
                 <li>passer des qcms pr&eacute;par&eacute;s pour vous, </li>
<!-- END BLOCK : qcm_profs -->
<!-- START BLOCK : parc_profs -->
                <li> suivre vos parcours de formation. </li>
<!-- END BLOCK : parc_profs -->
                </ul>

                <p>Bonne utilisation.</p>

</div>


<!-- START BLOCK : cherche_version -->
<script  type="text/javascript">

function cherche_version () {
        if ($("message_version")) {
                  url="{chemin_commun}/verifier_version.php";
                var xmlHttp= new Ajax.Updater ("message_version",url, {
                        method:"get",
                        parameters:"",
                        onComplete: function() {},
			onFailure : function () {
				$("message_version").innerHTML="Erreur de communication en recherche des mises &agrave; jours ";
			}
                });
        }
}
cherche_version();

</script>

<!-- END BLOCK : cherche_version -->


