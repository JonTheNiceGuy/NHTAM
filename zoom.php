<?php

$boolDebug=TRUE;

require_once("functions/dbConnect.php");
require_once("functions/functions.php");

require_once("theme/pagelayout.php");

doDebug("Page header sent");

doSendPageHeader();

doDebug("Checking Plugins (Passed: " . $_REQUEST['plugins'] . ")");

if ($_REQUEST['plugins']=="") {
  $plugins=doGetPlugins("plugins"); // This returns an array of the plugins
} else {
  $plugins[1]=$_REQUEST['plugins'];
  $plugins['size']=1;
}

$debugstring="";
for ($debug=1; $debug<=$plugins['size']; $debug++) {
  if($debugstring!="") {$debugstring.=", ";}
  $debugstring.=$plugins[$debug];
}
doDebug("Plugins to use: $debugstring");

doDebug("Checking IPs (Passed: " . $_REQUEST['ip'] . ")");

if ($_REQUEST['ip']=="") {
  $ips=doGetEquipment();
} else {
  $ips[1]=$_REQUEST['ip'];
  $ips['size']=1;
}

$debugstring="";
for ($debug=1; $debug<=$ips['size']; $debug++) {
  if($debugstring!="") {$debugstring.=", ";}
  $debugstring.=$ips[$debug];
}
doDebug("IPs to use: $debugstring");

doDebug("Checking IDP (Passed: " . $_REQUEST['idp'] . ")");

if ($_REQUEST['idp']=="") {
  $idp=date("Y-m-d", time()) . " 00:00:00";
} else {
  $idp=$_REQUEST['idp'];
}

doDebug("IDP is: " . $idp);

echo "<table width=100%>\n";

if ($_REQUEST['ip']!="") {
  $resort.="&ip=" . $_REQUEST['ip'];
}
if ($_REQUEST['plugins']!="") {
  $resort.="&plugins=" . $_REQUEST['plugins'];
}
if ($_REQUEST['idp']!="") {
  $resort.="&idp=" . $_REQUEST['idp'];
}

switch($_REQUEST['sort']) {
  case "plugin":
    $item1=$plugins;
    $item2=$ips;
    echo "<tr><th><a href='zoom.php?sort=plugin" . $resort . "'><b>Plugin</b></a></th><th><a href='zoom.php?sort=ip" . $resort . "'>IP</a></th><th>Results</th></tr>";
    break;
  default:
    $item1=$ips;
    $item2=$plugins;
    echo "<tr><th><a href='zoom.php?sort=ip" . $resort . "'><b>IP</b></a></th><th><a href='zoom.php?sort=plugin" . $resort . "'>Plugin</a></th><th>Results</th></tr>";
}

for ($item1inc=1; $item1inc<=$item1['size']; $item1inc++) {
  for ($item2inc=1; $item2inc<=$item2['size']; $item2inc++){
    switch($_REQUEST['sort']) {
      case "plugin":
        $plugininc=$item1inc;
        $ipsinc=$item2inc;
        break;
      default:
        $ipsinc=$item1inc;
        $plugininc=$item2inc;
        break;
    }
    $strIP=$ips[$ipsinc];
    doDebug("Running process for plugin " . $plugins[$plugininc]);
    require_once("plugins/" . $plugins[$plugininc] . "/function.php");
    $WhatCalledMe="info";
    include("plugins/" . $plugins[$plugininc] . "/switch.php");
    switch($_REQUEST['sort']) {
      case "plugin":
        echo "<tr><th>" . $return['Title'] . "</th><th>$strIP</th><td><table width=100%>";
        break;
      default:
        echo "<tr><th>$strIP</th><th>" . $return['Title'] . "</th><td><table width=100%>";
        break;
    }
    echo "<tr><th>\n";
    $WhatCalledMe="drilldown";
    include("plugins/" . $plugins[$plugininc] . "/switch.php");
    // 0 = not requested
    // 1 = good
    // 2 = warning
    // 3 = error
    $cellcount=0;
    for ($res=1; $res<=$return['general']['size']; $res++) {
      switch($return["$res"]['trafficlight']) {
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
      $cellcount++;
      $datesplit=$return["$res"]['timestamp'];
      $datestring=substr($datesplit,0,4) . "-";
      $datestring.=substr($datesplit,4,2) . "-";
      $datestring.=substr($datesplit,6,2) . " ";
      $datestring.=substr($datesplit,8,2) . ":";
      $datestring.=substr($datesplit,10,2);
      echo "<td width=2% $col><img src='theme/icon/$img' alt='" . $return["$res"]['value'] . " @ " . $datestring . "'></td>\n";
      if($cellcount>=50) {
        echo "</tr><tr>";
        $cellcount=0;
      }
    }
    for(; $cellcount<=49; $cellcount++) {
      echo "<td width=2% id='noexist'>&nbsp;</td>\n";
    }
    echo "</tr>\n";
    echo "</table>\n";
  }
}

doSendPageFooter();
?>
