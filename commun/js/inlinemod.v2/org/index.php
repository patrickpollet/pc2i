<?php
	require('./common-top.php');

	$sql = 'SELECT * FROM `'.DB_TABLE_NAME.'`';
	$result = mysql_query($sql) or die(__LINE__.mysql_error().$sql);

	/// Listing des classes disponibles ////////////////////////////////////////////////////////////////////////////////////////
	$scripts = array();


    /******************
	avec kl'héritage c'est impossible l'ordre est capital !!!!
    $i = 0;

	foreach(glob('../inlinemod.class.*.js') as $fichier)
	{
		$scripts[$i] = $fichier;
		$i++;
	}
    *****************/
    $scripts=array( "../inlinemod.class.texte.js",
                    "../inlinemod.class.texteNV.js",
                    "../inlinemod.class.nombre.js",
                    "../inlinemod.class.texteMulti.js",
                    "../inlinemod.class.texteMultiNV.js",
                    "../inlinemod.class.email.js",
                    "../inlinemod.class.entier.js",
                    "../inlinemod.class.url.js",


    );

$monTableauPHP = array(array("toutou", "toto"), array("titi", "tata",
array("tonton", "tutu", array("quatrieme etage du tableau", 54))));


function construisTableauJS($tableauPHP, $nomTableauJS){
   echo $nomTableauJS." = new Array();";
   for($i = 0; $i < count($tableauPHP); $i++){
      if(!is_array($tableauPHP[$i])){
         echo $nomTableauJS."[".$i."] = '".$tableauPHP[$i]."';";
      }
      else{
         construisTableauJS($tableauPHP[$i], $nomTableauJS."[".$i."]");
      }
   }
   return;
}




	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
		<meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1" />

		<title>Modification "Inline" d'éléments dans une page web</title>

		<link rel="StyleSheet" type="text/css" href="index.css"/>

		<script type="text/javascript" src="../utils.js"></script>

	<?php
		//Inclusion des fichiers javascript de classes
		foreach($scripts as $script)
		{
			print '<script type="text/javascript" src="' . $script . '"></script>';
		}
	?>

		<script type="text/javascript" src="../inlinemod.js"></script>


<?
echo "<script type='text/javascript'>";
construisTableauJS($monTableauPHP, "monTableauJS");
//echo ("document.write(monTableauJS.toSource());");
echo "</script> ";
?>

  </head>

  <body>
	<h1>Liste d'utilisateurs</h1>

	<div id="erreurMsg"></div>

	<br/>

	<table id="table-utilisateurs">
		<tr>
			<th>Nom</th>
			<th>Prénom</th>
			<th>Adresse</th>
			<th>Code Postal</th>
			<th>Ville</th>
			<th>Enfants</th>
			<th>Email</th>
            <th>URL</th>
		</tr>

	<?php
	while ($user = mysql_fetch_assoc($result))
	{
	?>
		<tr>
			<td id="nom-<?php echo $user['id']; ?>"  class="cellule" ondblclick="inlineMod(<?php echo $user['id']; ?>, this, 'nom', 'TexteNV','sauverMod.php')"><?php echo $user['nom']; ?></td>

			<td id="prenom-<?php echo $user['id']; ?>"  class="cellule" ondblclick="inlineMod(<?php echo $user['id']; ?>, this, 'prenom', 'Texte','sauverMod.php')"><?php echo $user['prenom']; ?></td>

			<td id="adresse-<?php echo $user['id']; ?>"  class="cellule" ondblclick="inlineMod(<?php echo $user['id']; ?>, this, 'adresse', 'TexteMultiNV','sauverMod.php')"><?php echo $user['adresse']; ?></td>

			<td id="cp-<?php echo $user['id']; ?>"  class="cellule" ondblclick="inlineMod(<?php echo $user['id']; ?>, this, 'code_postal', 'Entier','sauverMod.php')"><?php echo $user['code_postal']; ?></td>

			<td id="ville-<?php echo $user['id']; ?>"  class="cellule" ondblclick="inlineMod(<?php echo $user['id']; ?>, this, 'ville', 'TexteMulti','sauverMod.php')"><?php echo $user['ville']; ?></td>

			<td id="enfants-<?php echo $user['id']; ?>"  class="cellule" ondblclick="inlineMod(<?php echo $user['id']; ?>, this, 'enfants', 'Entier','sauverMod.php')"><?php echo $user['enfants']; ?></td>

			<td id="email-<?php echo $user['id']; ?>"  class="cellule" ondblclick="inlineMod(<?php echo $user['id']; ?>, this, 'email', 'Email','sauverMod.php')"><?php echo $user['email']; ?></td>

                <td id="blog-<?php echo $user['id']; ?>"  class="cellule" ondblclick="inlineMod(<?php echo $user['id']; ?>, this, 'blog', 'URL','sauverMod.php')"><?php echo $user['blog']; ?></td>
		</tr>
	<?php
	}
	?>
	</table>

	<div id="info">(les données de ce tableau sont fictives)</div>

  </body>
  </html>

<?php

require('./common-bottom.php');

?>