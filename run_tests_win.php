<?php

/****************************************
 * This code assumes Windows has the AT *
 * command installed which is (as far   *
 * as I know) default.                  *
 ****************************************/

$path_to_php_executable="D:\appserv\php\php.exe";

$dirName="D:/appserv/www/nhtam/plugins";
$winDirName="D:\\appserv\\www\\nhtam\\plugins\\";

//Load Directory Into Array
$handle=opendir($dirName);
$i=0;
while ($file = readdir($handle))
if ($file != "." && $file != ".." && $file != "CVS" && $file != "plugin_TEMPLATE")
{
  $exec_command="at " . date("G:i", time() + 60) . " " . $path_to_php_executable . " -q " . $winDirName . $file . "\\docheck.php\n";
  echo $exec_command;
  exec($exec_command);
}
//close the directory handle
closedir($handle);

?>
