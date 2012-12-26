<?php

$boolDebug=TRUE;

require_once("functions/dbConnect.php");
require_once("functions/functions.php");
require_once("theme/pagelayout.php");

doSendPageHeader();

doDebug("Page header sent");

$plugins=doGetPlugins("plugins"); // This returns an array of the plugins

doDebug("Plugins retrieved");

echo "<form method='get' action='index.php' name='selectkit'>\n";
echo "<table width=100%>\n";
echo "<tr>\n";

echo "<th>Site</th><th>Device Type</th><th>Network</th><th>Support</th><td><a href='edit.php'>Edit Entries</a></td></tr><tr>\n";

echo "<td>\n";
doGetSiteList($_REQUEST['site']);
if ($_REQUEST['site']!="") {doGetLocationList($_REQUEST['location'],$_REQUEST['site']);}
echo "</td>\n";

echo "<td>\n";
doGetClassList($_REQUEST['class']);
echo "</td>\n";

echo "<td>\n";
doGetNetworkList($_REQUEST['network']);
echo "</td>\n";

echo "<td>\n";
doGetSupportList($_REQUEST['support']);
echo "</td>\n";

echo "<td><input type='Submit' name='Submit' value='Submit'></td>";
echo "</tr>\n";
echo "</table>\n";
echo "<br>";

echo "<table width=100%>\n";
echo "<tr><th>Server</th><th>Detail</th><th>Last Overall Report</th></tr>\n";

$aryEI=doGetEquipment($_REQUEST['site'], $_REQUEST['class'], $_REQUEST['network'], $_REQUEST['location'], $_REQUEST['support']);

for($ip=1; $ip<=$aryEI['size']; $ip++) {
  doDebug("Reading entry for $ip");
  $strIP=$aryEI[$ip];
  doDebug("Working with $strIP");
  $result=doGetServerInfo($strIP);
  
  $trafficlight=0;
  $display="| ";
  doDebug("Drawing row");
  echo "<tr><th>" . $result['displayname'] . "</th><td><img src='theme/equipment/" . $result['image'] . "' alt='" . $result['serverinfo'] . "'></td>\n";

  for ($i=1; $i<=$plugins['size']; $i++) {
    doDebug("Working with plugin " . $plugins[$i]);
    require_once("plugins/" . $plugins[$i] . "/function.php");
    $WhatCalledMe="info";
    include("plugins/" . $plugins[$i] . "/switch.php");
    $display.=$return['Title'] . " (";
    $WhatCalledMe="display";
    include("plugins/" . $plugins[$i] . "/switch.php");
    // 0 = not requested
    // 1 = good
    // 2 = warning
    // 3 = error
    doDebug("Got values from plugin. Processing");
    switch($return['trafficlight']) {
      case 0:
        $display.="Not Requested";
        break;
      case 1:
        $display.="Green";
        if ($trafficlight<1) {$trafficlight=1;}
        break;
      case 2:
        $display.="Amber";
        if ($trafficlight<2) {$trafficlight=2;}
        break;
      case 3:
        $display.="Red";
        if ($trafficlight<3) {$trafficlight=3;}
        break;
    }
    $display.=") " . $return['value'] . " | ";
  }
  switch($trafficlight) {
    case 0:
      $img="emblem-unreadable.png";
      $col="id='noexist'";
      break;
    case 1:
      $img="emblem-favorite.png";
      $col="id='good'";
      break;
    case 2:
      $img="emblem-important.png";
      $col="";
      break;
    case 3:
      $img="emblem-important.png";
      $col="id='warning'";
      break;
  }
  echo "<td $col><a href='zoom.php?ip=$strIP'><img src='theme/icon/$img' alt='$display' border=0></a></td>\n";
  echo "</tr>\n";
  $trafficlight=0;
}

echo "</table>\n";
doSendPageFooter();
?>
