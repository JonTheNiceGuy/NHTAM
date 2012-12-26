<?php

function doGetPlugins($dirName) {
  //Load Directory Into Array
  $handle=opendir($dirName);
  $i=1;
  doDebug("Searching $dirName for Plugin files");
  while ($file = readdir($handle)) {
    doDebug("Found file: $file");
    if ($file != "." && $file != ".." && $file != "CVS" && $file != "plugin_TEMPLATE")
    {
      doDebug("File matches criteria");
      $result[$i]=$file;
      $i++;
    }
  }
  //close the directory handle
  closedir($handle);
  $i--;
  $result['size']=$i;
  doDebug("Finished searching for Plugin Files. Found $i files.");
  return($result);
}

function doGetEquipment($intSite="", $intClass="", $intNetwork="", $intSupport="", $intLocation="") {
  doDebug("Searching for equipment. Variables: intSite($intSite) intClass($intClass) intNetwork($intNetwork) intSupport($intSupport) intLocation($intLocation)");
  $strSelectStatement="ei.strIP";
  $strTableList="equipment_inventory AS ei";
  $strWhere="";
  
  if($intSite!="") {
    if ($strTableList!="") {$strTableList.=", ";}
    $strTableList.="equipment_locations AS el";
    if ($strWhere!="") {$strWhere.=" AND ";}
    $strWhere.="el.intSite='$intSite' AND el.intUID=ei.intLocation";
  }
  if($intClass!="") {
    if ($strWhere!="") {$strWhere.=" AND ";}
    $strWhere.="ei.intEquipmentType='$intClass'";
  }
  if($intNetwork!="") {
    if ($strWhere!="") {$strWhere.=" AND ";}
    $strWhere.="ei.intNetwork='$intNetwork'";
  }
  if($intSupport!="") {
    if ($strWhere!="") {$strWhere.=" AND ";}
    $strWhere.="ei.intSupport='$intSupport'";
  }
  if($intLocation!="") {
    if ($strWhere!="") {$strWhere.=" AND ";}
    $strWhere.="ei.intLocation='$intLocation'";
  }

  $sqlReadEquipment="SELECT $strSelectStatement FROM $strTableList";
  if($strWhere!="") {$sqlReadEquipment.=" WHERE " . $strWhere;}
  $sqlReadEquipment.=" ORDER BY ei.intEquipmentType";

  doDebug("SQL Statement: $sqlReadEquipment");

  $qryReadEquipment=mysql_query($sqlReadEquipment);
  $numReadEquipment=mysql_num_rows($qryReadEquipment);

  doDebug("SQL Statement returns $numReadEquipment rows");

  $result['size']=$numReadEquipment;
  for($i=1; $i<=$numReadEquipment; $i++) {
    list($strIP)=mysql_fetch_array($qryReadEquipment);
    doDebug("Pushing $strIP into result array in position $i");
    $result[$i]=$strIP;
  }
  return($result);
}

function doGetServerInfo($strIP) {
  doDebug("Searching for Server Information from IP Address: $strIP");
  $sqlReadServer="SELECT strDisplayName, intLocation, intNetwork, strFunction, intEquipmentType, intSupport FROM equipment_inventory WHERE strIP='$strIP'";
  doDebug("SQL Statement: $sqlReadServer");
  $qryReadServer=mysql_query($sqlReadServer);
  list($strDisplayName, $intLocation, $intNetwork, $strFunction, $intEquipmentType, $intSupport)=mysql_fetch_array($qryReadServer);
  doDebug("SQL Returns: strDisplayName($strDisplayName), intLocation($intLocation), intNetwork($intNetwork), strFunction($strFunction), intEquipmentType($intEquipmentType), intSupport($intSupport)");
  
  $sqlReadLocation="SELECT intSite, strLocation FROM equipment_locations WHERE intUID='$intLocation'";
  doDebug("SQL Statement: $sqlReadLocation");
  $qryReadLocation=mysql_query($sqlReadLocation);
  list($intSite, $strLocation)=mysql_fetch_array($qryReadLocation);
  doDebug("SQL Returns: intSite($intSite), strLocation($strLocation)");
  
  $sqlReadSite="SELECT strSite, strSiteAddr1, strSiteAddr2, strSiteAddr3, strSiteAddr4, strSiteAddr5, strSitePostcode, strSiteCountry, strContactNum, strContactName FROM equipment_sites WHERE intUID='$intSite'";
  doDebug("SQL Statement: $sqlReadSite");
  $qryReadSite=mysql_query($sqlReadSite);
  list($strSite, $strSiteAddr1, $strSiteAddr2, $strSiteAddr3, $strSiteAddr4, $strSiteAddr5, $strSitePostcode, $strSiteCountry, $strSiteContactNum, $strSiteContactName)=mysql_fetch_array($qryReadSite);
  doDebug("SQL Returns: strSite($strSite), strSiteAddr1($strSiteAddr1), strSiteAddr2($strSiteAddr2), strSiteAddr3($strSiteAddr3), strSiteAddr4($strSiteAddr4), strSiteAddr5($strSiteAddr5), strSitePostcode($strSitePostcode), strSiteCountry($strSiteCountry), strSiteContactNum($strSiteContactNum), strSiteContactName($strSiteContactName)");
  
  $sqlReadNetwork="SELECT strName FROM equipment_networks WHERE intUID='$intNetwork'";
  doDebug("SQL Statement: $sqlReadNetwork");
  $qryReadNetwork=mysql_query($sqlReadNetwork);
  list($strNetwork)=mysql_fetch_array($qryReadNetwork);
  doDebug("SQL Returns: strNetwork($strNetwork)");
  
  $sqlReadFunction="SELECT strHardwareType, strGraphicFilename FROM equipment_type WHERE intHardwareType='$intEquipmentType'";
  doDebug("SQL Statement: $sqlReadFunction");
  $qryReadFunction=mysql_query($sqlReadFunction);
  list($strHardwareType, $strGraphicFilename)=mysql_fetch_array($qryReadFunction);
  doDebug("SQL Returns: strHardwareType($strHardwareType), strGraphicFilename($strGraphicFilename)");
  
  $sqlReadSupport="SELECT strCompany, strAddr1, strAddr2, strAddr3, strAddr4, strAddr5, strPostcode, strCountry, strContactName, strContactNum FROM equipment_support WHERE intUID='$intSupport'";
  doDebug("SQL Statement: $sqlReadSupport");
  $qryReadSupport=mysql_query($sqlReadSupport);
  list($strCompany, $strAddr1, $strAddr2, $strAddr3, $strAddr4, $strAddr5, $strPostcode, $strCountry, $strContactName, $strContactNum)=mysql_fetch_array($qryReadSupport);
  doDebug("SQL Returns: strCompany($strCompany), strAddr1($strAddr1), strAddr2($strAddr2), strAddr3($strAddr3), strAddr4($strAddr4), strAddr5($strAddr5), strPostcode($strPostcode), strCountry($strCountry), strContactName($strContactName), strContactNum($strContactNum)");
  
  $return['serverinfo']=$strHardwareType . " Name: " . $strDisplayName . " at " . $strSite . " on " . $strNetwork . " supported by " . $strCompany;
  $return['displayname']=$strDisplayName;
  $return['image']=$strGraphicFilename;
  
  doDebug("Function returns values: serverinfo(" . $return['serverinfo'] . "), displayname(" . $return['displayname'] . "), image(" . $return['image'] . ")");
  return($return);
}

function doGetEIUID($strIP) {
  doDebug("Searching for UID from IP: $strIP");
  $sqlGetUID="SELECT uid FROM equipment_inventory WHERE strIP LIKE '$strIP'";
  doDebug("SQL Statement: $sqlGetUID");
  $qryGetUID=mysql_query($sqlGetUID);
  $numGetUID=mysql_num_rows($qryGetUID);
  doDebug("SQL Returns $numGetUID rows");
  if($numGetUID>0) {
    list($result)=mysql_fetch_array($qryGetUID);
  } else {
    echo "ERROR";
  }
  doDebug("SQL Returns: $result");
  return($result);
}

function doGetSiteList($intSite="") {
  doDebug("Searching for site where intSite: $intSite");
  doDebug("Start drawing select statement");
  echo "\n<SELECT name='site' onChange=\"document.forms['selectkit'].submit();\">\n";
  if($intSite!="") {echo "<OPTION value=''>RESET</OPTION>\n";} else {echo "<OPTION value=''>ALL</OPTION>\n";}
  $sqlGetList="SELECT intUID, strSite FROM equipment_sites ORDER BY strSite";
  doDebug("SQL Statement: $sqlGetList");
  $qryGetList=mysql_query($sqlGetList);
  doDebug("Draw options");
  while(list($uid, $site)=mysql_fetch_array($qryGetList)) {
    doDebug("SQL Returns: uid($uid), site($site)");
    if ($uid==$intSite) {$selected="SELECTED";} else {$selected="";}
    echo "<OPTION value='$uid' $selected>$site</OPTION>\n";
  }
  doDebug("Finish drawing select statement");
  echo "</SELECT>\n";
}

function doGetLocationList($intLocation="", $intSite="") {
  doDebug("Searching for Location where intLocation: $intLocation, intSite: $intSite");
  doDebug("Start drawing select statement");
  echo "\n<SELECT name='location' onChange=\"document.forms['selectkit'].submit();\">\n";
  if($intLocation!="") {echo "<OPTION value=''>RESET</OPTION>\n";} else {echo "<OPTION value=''>ALL</OPTION>\n";}
  if($intSite!="") {$where="WHERE intSite='$intSite'";} else {$where="";}
  $sqlGetList="SELECT intUID, strLocation FROM equipment_locations $where ORDER BY strLocation";
  doDebug("SQL Statement: $sqlGetList");
  $qryGetList=mysql_query($sqlGetList);
  doDebug("Draw options");
  while(list($uid, $location)=mysql_fetch_array($qryGetList)) {
    doDebug("SQL Returns: uid($uid), location($location)");
    if ($uid==$intLocation) {$selected="SELECTED";} else {$selected="";}
    echo "<OPTION value='$uid' $selected>$location</OPTION>\n";
  }
  doDebug("Finish drawing select statement");
  echo "</SELECT>\n";
}

function doGetClassList($intClass) {
  doDebug("Searching for Class where intClass: $intClass");
  doDebug("Start drawing select statement");
  echo "\n<SELECT name='class' onChange=\"document.forms['selectkit'].submit();\">\n";
  if($intClass!="") {echo "<OPTION value=''>RESET</OPTION>\n";} else {echo "<OPTION value=''>ALL</OPTION>\n";}
  $sqlGetList="SELECT intHardwareType, strHardwareType FROM equipment_type ORDER BY strHardwareType";
  doDebug("SQL Statement: $sqlGetList");
  $qryGetList=mysql_query($sqlGetList);
  doDebug("Draw options");
  while(list($uid, $type)=mysql_fetch_array($qryGetList)) {
    doDebug("SQL Returns: uid($uid), type($type)");
    if ($uid==$intClass) {$selected="SELECTED";} else {$selected="";}
    echo "<OPTION value='$uid' $selected>$type</OPTION>\n";
  }
  doDebug("Finish drawing select statement");
  echo "</SELECT>\n";
}

function doGetNetworkList($intNetwork) {
  doDebug("Searching for Network where intNetwork: $intNetwork");
  doDebug("Start drawing select statement");
  echo "\n<SELECT name='network' onChange=\"document.forms['selectkit'].submit();\">\n";
  if($intNetwork!="") {echo "<OPTION value=''>RESET</OPTION>\n";} else {echo "<OPTION value=''>ALL</OPTION>\n";}
  $sqlGetList="SELECT intUID, strName FROM equipment_networks ORDER BY strName";
  doDebug("SQL Statement: $sqlGetList");
  $qryGetList=mysql_query($sqlGetList);
  doDebug("Draw options");
  while(list($uid, $name)=mysql_fetch_array($qryGetList)) {
    doDebug("SQL Returns: uid($uid), name($name)");
    if ($uid==$intNetwork) {$selected="SELECTED";} else {$selected="";}
    echo "<OPTION value='$uid' $selected>$name</OPTION>\n";
  }
  doDebug("Finish drawing select statement");
  echo "</SELECT>\n";
}

function doGetSupportList($intSupport) {
  doDebug("Searching for Support Contract where intSupport: $intSupport");
  doDebug("Start drawing select statement");
  echo "\n<SELECT name='support' onChange=\"document.forms['selectkit'].submit();\">\n";
  if($intSupport!="") {echo "<OPTION value=''>RESET</OPTION>\n";} else {echo "<OPTION value=''>ALL</OPTION>\n";}
  $sqlGetList="SELECT intUID, strCompany FROM equipment_support ORDER BY strCompany";
  doDebug("SQL Statement: $sqlGetList");
  $qryGetList=mysql_query($sqlGetList);
  doDebug("Draw options");
  while(list($uid, $name)=mysql_fetch_array($qryGetList)) {
    doDebug("SQL Returns: uid($uid), name($name)");
    if ($uid==$intSupport) {$selected="SELECTED";} else {$selected="";}
    echo "<OPTION value='$uid' $selected>$name</OPTION>\n";
  }
  doDebug("Finish drawing select statement");
  echo "</SELECT>\n";
}

function doAuthenticate($logout=FALSE) {
  session_start();
  if($logout==FALSE) {
    if (!isset($_SESSION['admin_approved'])) {
      if (!isset($_POST['username'])) {
        doSendPageHeader();
        echo "<form name='Authentication' action='" . $_SERVER["REQUEST_URI"] . "' method='post'>\n";
        echo "<table>\n";
        echo "<tr><th>Username</th><td><input type='text' name='username' size='20'></td></tr>\n";
        echo "<tr><th>Password</th><td><input type='password' name='password' size='20'></td></tr>\n";
        echo "<tr><td colspan=2 align=center><input type=submit name=submit value='Log In'><td></tr>\n";
        echo "</table>\n";
        echo "</form>\n";
        exit;
      } else {
        $sqlCheckAuth="SELECT intLevel FROM nhtam_administrators WHERE strUsername LIKE '" . $_REQUEST['username'] . "' AND strPassword=MD5('" . $_REQUEST['password'] . "')";
        $qryCheckAuth=mysql_query($sqlCheckAuth);
        $numCheckAuth=mysql_num_rows($qryCheckAuth);
        if($numCheckAuth>0) { echo "<!-- Authenticated with Form: " . $_REQUEST['username'] . " -->\n";
          doSendPageHeader();
          $_SESSION['admin_username']=$_REQUEST['username'];
          list($_SESSION['admin_level'])=mysql_fetch_array($qryCheckAuth);
          $_SESSION['admin_approved']=TRUE;
        } else {
          doSendPageHeader();
          echo "<h1 align=center>Login Failed</h1>\n";
          echo "<form name='Authentication' action='" . $_SERVER["REQUEST_URI"] . "' method='post'>\n";
          echo "<table>\n";
          echo "<tr><th>Username</th><td><input type='text' name='username' size='20'></td></tr>\n";
          echo "<tr><th>Password</th><td><input type='password' name='password' size='20'></td></tr>\n";
          echo "<tr><td colspan=2 align=center><input type=submit name=submit value='Log In'><td></tr>\n";
          echo "</table>\n";
          echo "</form>\n";
          exit;
        }
      }
    } else {
      $sqlCheckAuth="SELECT intLevel FROM nhtam_administrators WHERE strUsername LIKE '" . $_SESSION['admin_username'] . "'";
      $qryCheckAuth=mysql_query($sqlCheckAuth);
      $numCheckAuth=mysql_num_rows($qryCheckAuth);
      if($numCheckAuth>0) { echo "<!-- Authenticated with Session: " . $_SESSION['admin_username'] . " -->\n";
        doSendPageHeader();
        list($_SESSION['admin_level'])=mysql_fetch_array($qryCheckAuth);
        $_SESSION['admin_approved']=TRUE;
      }
    }
  } else {
    doSendPageHeader();
    echo "<!-- Authentication Removed -->\n";
    unset($_SESSION['admin_approved']);
    unset($_SESSION['admin_level']);
    unset($_SESSION['admin_username']);
  }
}

Function doDebug($strDebug) {
  global $boolDebug;
  if($boolDebug==TRUE) {echo "<!-- " . $strDebug . " -->\n";}
}
?>
