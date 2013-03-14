<?php
/**
Map a filesystem with HTML TreeMenu
@author Tomas V.V.Cox <cox@idecnet.com>
*/
require_once '../TreeMenu.php';
$map_dir = '/var/www/c2i/V1.5';
$menu  = new HTML_TreeMenu('menuLayer', '../images', '_self');
$menu->addItem(recurseDir($map_dir));

function &recurseDir($path) {
    if (!$dir = opendir($path)) {
        return false;
    }
    $files = array();
//print "$path<br/>";
    $node = &new HTML_TreeNode(array('text'=>basename($path),'icon'=> 'folder.gif'));
    while (($file = readdir($dir)) !== false) {
        if ($file != '.' && $file != '..') {
            if (@is_dir("$path/$file")) {
                $addnode = &recurseDir("$path/$file");
            } else {
                $addnode = &new HTML_TreeNode(array('text'=>$file,'icon'=>'document2.png'));
            }
            $node->addItem($addnode);
        }
    }
    closedir($dir);
    return $node;
}
?>
<html>
<head>
    <script src="./css/sniffer.js" language="JavaScript" type="text/javascript"></script>
    <script src="../TreeMenu.js" language="JavaScript" type="text/javascript"></script>
</head>
<body>

<div id="menuLayer">
<?
  $treeMenu = &new HTML_TreeMenu_DHTML($menu, array('images' => '../images', 'defaultClass' => 'treeMenuDefault'));


$treeMenu->printMenu()?>
</div>
</body>
</html>