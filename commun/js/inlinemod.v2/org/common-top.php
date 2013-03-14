<?php

define('DB_HOST',       'localhost');
define('DB_USER',       'c2iadmin');
define('DB_PASSWORD',   'c2i$2005');
define('DB_NAME',       'test');
define('DB_TABLE_NAME', 'inlinemod');

// Connexion à la base de données
mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(mysql_error());
mysql_select_db(DB_NAME) or die(mysql_error());
?>