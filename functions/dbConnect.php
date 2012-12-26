<?php

if ($host=="") {$host="localhost";}
if ($user=="") {$user="root";}
if ($pass=="") {$pass="";}
if ($data=="") {$data="nhtam";}

mysql_connect($host,$user,$pass);
mysql_select_db($data);

ini_set('max_execution_time', 86400); // This is here because the development box is SLOW

?>
