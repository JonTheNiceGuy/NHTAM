<?php

$boolDebug=FALSE;

require_once("functions/dbConnect.php");
require_once("functions/functions.php");
require_once("theme/pagelayout.php");

if(isset($_REQUEST['logout'])) {
  doAuthenticate(TRUE);
  echo "<p>You have now logged out</p>\n";
  $LogInOrOut=" <a href='edit.php'>Log in</a>";
} else {
  doAuthenticate();
  $LogInOrOut=" <a href='edit.php?logout'>Click here to log out</a>";
}

echo "<!-- \n";
var_dump($_REQUEST);
echo "\n -->";

echo "<form method='post' action='edit.php'>\n";
echo "<input type=hidden name=type value='" . $_REQUEST['type'] . "'>\n";

if($_SESSION['admin_approved']) {
  switch($_REQUEST['type']) {
    case "location":
      if(doCheckLevel(1,3,5,6,8,9)) {
        switch($_REQUEST['query']) {
          case "edit":
            echo "<h1 align=center>Edit Equipment Locations</h1>\n";
            $return=getLocationList("el.intUID=" . $_REQUEST['uid'],FALSE);
            echo "<table width=100%>\n";
            echo "<tr><th>Site</th><th>Location</th><th>Edit</th></tr>\n";
            if($return['size']==0) {
              echo "<tr><td colspan=3>WARNING: This is not a valid location!</td></tr>\n";
            } else {
              while(list($intUID, $strLocation, $intSite)=mysql_fetch_array($return['query'])) {
                echo "<tr><td>";
                $returnSite=getSiteList();
                if($returnSite['size']>0) {
                  echo "<select name=site>\n";
                  while(list($intUID, $strListSite)=mysql_fetch_array($returnSite['query'])) {
                    if($intUID==$intSite) {$selected="SELECTED";} else {$selected="";}
                    echo "<option value='$intUID' $selected>$strListSite</option>\n";
                  }
                  echo "</select>";
                }
                echo "</td>
                      <td><input type=text name=location value='$strLocation'></td>
                      <td><input type=submit name=Save value=Save>
                          <input type=submit name=Save value=Del></td></tr>\n";
              }
              echo "</table>";
              echo "<input type=hidden name=query value=save><input type=hidden name=uid value=" . $_REQUEST['uid'] . ">";
            }
          break;
          case "save":
            if($_REQUEST['uid']=="") {
              $sqlInsert="INSERT INTO equipment_locations (intSite, strLocation) VALUES ('" . $_REQUEST['site'] . "', '" . $_REQUEST['location'] . "')";
              $qryInsert=mysql_query($sqlInsert);
              $numInsert=mysql_affected_rows();
            } else {
              if($_REQUEST['Save']=="Save") {
                $sqlInsert="UPDATE equipment_locations SET intSite = '" . $_REQUEST['site'] . "', strLocation = '" . $_REQUEST['location'] . "' WHERE intUID = '" . $_REQUEST['uid'] . "'";
              } else {
                $sqlInsert="DELETE FROM equipment_locations WHERE intUID = '" . $_REQUEST['uid'] . "'";
              }
              $qryInsert=mysql_query($sqlInsert);
              $numInsert=mysql_affected_rows();
            }
          break;
          default:
            echo "<h1 align=center>Edit Equipment Locations</h1>\n";
            $return=getLocationList();
            echo "<table width=100%>\n";
            echo "<tr><th>Site</th><th>Location</th><th>Edit</th></tr>\n";
            if($return['size']==0) {
              echo "<tr><td colspan=3>WARNING: You have no locations defined!</td></tr>\n";
            } else {
              while(list($intUID, $strLocation, $strSite)=mysql_fetch_array($return['query'])) {
                echo "<tr><td>$strSite</td><td>$strLocation</td><td><a href='edit.php?query=edit&type=location&uid=$intUID'>Edit</a></td></tr>\n";
              }
              echo "<tr><td>";
              $return=getSiteList();
              if($return['size']>0) {
                echo "<select name=site>\n";
                while(list($intUID, $strSite)=mysql_fetch_array($return['query'])) {
                  echo "<option value='$intUID'>$strSite</option>\n";
                }
                echo "</select>";
              }
              echo "</td><td><input type=text name=location></td><td><input type=submit value=Add></td></tr>\n";
              echo "</table>";
              echo "<input type=hidden name=query value=save>";
            }
          break;
        }
      } else {
        echo "<p>You do not have sufficient admin priviliges to perform these tasks.</p>";
      }
    break;
    case "network":
      if(doCheckLevel(4,5,6,7,8,9)) {
        switch($_REQUEST['query']) {
          case "edit":
            echo "<h1 align=center>Edit Network Details</h1>\n";
            $return=getNetworkList("intUID=" . $_REQUEST['uid'],FALSE);
            echo "<table width=100%>\n";
            echo "<tr><th>Network</th><th>Edit</th></tr>\n";
            if($return['size']==0) {
              echo "<tr><td colspan=2>WARNING: You have no networks defined!</td></tr>\n";
            } else {
              while(list($intUID, $strName)=mysql_fetch_array($return['query'])) {
                echo "<tr><td><input type=text name=strName value='$strName'></td>";
                echo "<td><a href='edit.php?query=edit&type=site&uid=" . $aryReturn['intUID'] . "'>Edit</a></td></tr>\n";
              }
              echo "<tr><td><td><input type=submit name=Save value=Save>
                          <input type=submit name=Save value=Del></td></tr>\n";
            }
            echo "</table>";
            echo "<input type=hidden name=query value=save><input type=hidden name=uid value=" . $_REQUEST['uid'] . ">";
          break;
          case "save":
            if($_REQUEST['uid']=="") {
              $sqlInsert="INSERT INTO equipment_networks (strName) VALUES ('" . $_REQUEST['strName'] . "')";
              $qryInsert=mysql_query($sqlInsert);
              $numInsert=mysql_affected_rows();
            } else {
              if($_REQUEST['Save']=="Save") {
                $sqlInsert="UPDATE equipment_networks SET strName = '" . $_REQUEST['strName'] . "' WHERE intUID = '" . $_REQUEST['uid'] . "'";
              } else {
                $sqlInsert="DELETE FROM equipment_networks WHERE intUID = '" . $_REQUEST['uid'] . "'";
              }
              $qryInsert=mysql_query($sqlInsert);
              $numInsert=mysql_affected_rows();
            }
          break;
          default:
            echo "<h1 align=center>Edit Network Details</h1>\n";
            $return=getNetworkList();
            echo "<table width=100%>\n";
            echo "<tr><th>Network</th><th>Edit</th></tr>\n";
            if($return['size']==0) {
              echo "<tr><td colspan=2>WARNING: You have no networks defined!</td></tr>\n";
            } else {
              while(list($intUID, $strName)=mysql_fetch_array($return['query'])) {
                echo "<tr><td>$strName</td>";
                echo "<td><a href='edit.php?query=edit&type=site&uid=" . $aryReturn['intUID'] . "'>Edit</a></td></tr>\n";
              }
              echo "<tr><td><input type=text name=strName></td>";
              echo "<td><input type=submit value=Add></td></tr>\n";
              echo "</table>";
              echo "<input type=hidden name=query value=save>";
            }
          break;
        }
      } else {
        echo "<p>You do not have sufficient admin priviliges to perform these tasks.</p>";
      }
    break;
    case "server":
      if(doCheckLevel(8,9)) {
        switch($_REQUEST['query']) {
          case "edit":
            echo "<h1 align=center>Edit Servers</h1>\n";
            $return=getServerList();
            echo "<table width=100%>\n";
            echo "<tr><th>IP Address</th><th>Display Name</th><th>Site</th><th>Location</th><th>Network</th><th>Function</th><th>Hardware Type</th><th>Supported By</th><th>Edit</th></tr>\n";
            if($return['size']==0) {
              echo "<tr><td colspan=3>WARNING: You have no servers defined!</td></tr>\n";
            } else {
              while($aryGetServers=mysql_fetch_array($return['query'])) {
                echo "<tr>" .
                     "<td>" . $aryGetServers['strIP'] . "</td>" .
                     "<td>" . $aryGetServers['strDisplayName'] . "</td>" .
                     "<td>" . $aryGetServers['strSite'] . "</td>" .
                     "<td>" . $aryGetServers['strLocation'] . "</td>" .
                     "<td>" . $aryGetServers['strName'] . "</td>" .
                     "<td>" . $aryGetServers['strFunction'] . "</td>" .
                     "<td>" . $aryGetServers['strHardwareType'] . "</td>" .
                     "<td>" . $aryGetServers['strCompany'] . "</td>" .
                     "<td><a href='edit.php?query=edit&type=server&uid=" . $aryGetServers['uid'] . "'>Edit</a> | " .
                     "<a href='edit.php?query=del&type=server&uid=" . $aryGetServers['uid'] . "'>Del</a></td>" .
                     "</tr>\n";
              }
            }
            echo "<tr>";
            echo "<td><input type=text size=10 name=strIP></td>\n";
            echo "<td><input type=text size=10 name=strDisplayName></td>\n";
            echo "<td colspan=2>" . doDisplayLocationList() . "</td>\n";
            echo "<td>" . doDisplayNetworkList() . "</td>\n";
            echo "<td><input type=text size=10 name=strFunction></td>\n";
            echo "<td>" . doDisplayHardwareTypeList() . "</td>\n";
            echo "<td>" . doDisplaySupportList() . "</td>\n";
            echo "<td><input type=submit value=Add></td></tr>\n";
            echo "</table>";
            echo "<input type=hidden name=query value=save>";
          break;
          case "save":
            $sqlInsert="INSERT INTO equipment_inventory (strIP, strDisplayName, intLocation, intNetwork, strFunction, intEquipmentType, intSupport) VALUES ('". $_REQUEST['strIP'] . "', '". $_REQUEST['strDisplayName'] . "', '". $_REQUEST['intLocation'] . "', '". $_REQUEST['intNetwork'] . "', '". $_REQUEST['strFunction'] . "', '". $_REQUEST['intHardware'] . "', '". $_REQUEST['intSupport'] . "')";
echo "<!-- $sqlInsert -->";
            $qryInsert=mysql_query($sqlInsert);
            $numInsert=mysql_affected_rows();
          break;
          default:
            echo "<h1 align=center>Edit Servers</h1>\n";
            $return=getServerList();
            echo "<table width=100%>\n";
            echo "<tr><th>IP Address</th><th>Display Name</th><th>Site</th><th>Location</th><th>Network</th><th>Function</th><th>Hardware Type</th><th>Supported By</th><th>Edit</th></tr>\n";
            if($return['size']==0) {
              echo "<tr><td colspan=3>WARNING: You have no servers defined!</td></tr>\n";
            } else {
              while($aryGetServers=mysql_fetch_array($return['query'])) {
                echo "<tr>" .
                     "<td>" . $aryGetServers['strIP'] . "</td>" .
                     "<td>" . $aryGetServers['strDisplayName'] . "</td>" .
                     "<td>" . $aryGetServers['strSite'] . "</td>" .
                     "<td>" . $aryGetServers['strLocation'] . "</td>" .
                     "<td>" . $aryGetServers['strName'] . "</td>" .
                     "<td>" . $aryGetServers['strFunction'] . "</td>" .
                     "<td>" . $aryGetServers['strHardwareType'] . "</td>" .
                     "<td>" . $aryGetServers['strCompany'] . "</td>" .
                     "<td><a href='edit.php?query=edit&type=server&uid=" . $aryGetServers['uid'] . "'>Edit</a> | " .
                     "<a href='edit.php?query=del&type=server&uid=" . $aryGetServers['uid'] . "'>Del</a></td>" .
                     "</tr>\n";
              }
            }
            echo "<tr>";
            echo "<td><input type=text size=10 name=strIP></td>\n";
            echo "<td><input type=text size=10 name=strDisplayName></td>\n";
            echo "<td colspan=2>" . doDisplayLocationList() . "</td>\n";
            echo "<td>" . doDisplayNetworkList() . "</td>\n";
            echo "<td><input type=text size=10 name=strFunction></td>\n";
            echo "<td>" . doDisplayHardwareTypeList() . "</td>\n";
            echo "<td>" . doDisplaySupportList() . "</td>\n";
            echo "<td><input type=submit value=Add></td></tr>\n";
            echo "</table>";
            echo "<input type=hidden name=query value=save>";
          break;
        }
      } else {
        echo "<p>You do not have sufficient admin priviliges to perform these tasks.</p>";
      }
    break;
    case "site":
      if(doCheckLevel(1,3,5,6,8,9)) {
        switch($_REQUEST['query']) {
          case "del":

          break;
          case "edit":

          break;
          case "save":
            $sqlInsert="INSERT INTO equipment_sites (strSite, strSiteAddr1, strSiteAddr2, strSiteAddr3, strSiteAddr4, strSiteAddr5, strSitePostcode, strSiteCountry, strContactNum, strContactName) VALUES ('" . $_REQUEST['strSite'] . "', '" . $_REQUEST['strSiteAddr1'] . "', '" . $_REQUEST['strSiteAddr2'] . "', '" . $_REQUEST['strSiteAddr3'] . "', '" . $_REQUEST['strSiteAddr4'] . "', '" . $_REQUEST['strSiteAddr5'] . "', '" . $_REQUEST['strSitePostcode'] . "', '" . $_REQUEST['strSiteCountry'] . "', '" . $_REQUEST['strContactNum'] . "', '" . $_REQUEST['strContactName'] . "')";
            $qryInsert=mysql_query($sqlInsert);
            $numInsert=mysql_affected_rows();
          break;
          default:
            echo "<h1 align=center>Edit Sites</h1>\n";
            $return=getSiteList();
            echo "<table width=100%>\n";
            echo "<tr><th>Site</th><th>Details</th><th>Edit</th></tr>\n";
            if($return['size']==0) {
              echo "<tr><td colspan=3>WARNING: You have no sites defined!</td></tr>\n";
            } else {
              while($aryReturn=mysql_fetch_array($return['query'])) {
                echo "<tr><td>" . $aryReturn['strSite'] . "</td>";
                echo "<td><table width=100%>";
                echo "<tr><td>Address 1</td><td>" . $aryReturn['strSiteAddr1'] . "</td></tr>";
                echo "<tr><td>Address 2</td><td>" . $aryReturn['strSiteAddr2'] . "</td></tr>";
                echo "<tr><td>Address 3</td><td>" . $aryReturn['strSiteAddr3'] . "</td></tr>";
                echo "<tr><td>Address 4</td><td>" . $aryReturn['strSiteAddr4'] . "</td></tr>";
                echo "<tr><td>Address 5</td><td>" . $aryReturn['strSiteAddr5'] . "</td></tr>";
                echo "<tr><td>Postcode</td><td>" . $aryReturn['strSitePostcode'] . "</td></tr>";
                echo "<tr><td>Country</td><td>" . $aryReturn['strSiteCountry'] . "</td></tr>";
                echo "<tr><td>Contact Number</td><td>" . $aryReturn['strContactNum'] . "</td></tr>";
                echo "<tr><td>Contact Name</td><td>" . $aryReturn['strContactName'] . "</td></tr>";
                echo "</table></td>";
                echo "<td><a href='edit.php?query=edit&type=site&uid=" . $aryReturn['intUID'] . "'>Edit</a></td></tr>\n";
              }
              echo "<tr><td><input type=text name=strSite></td>";
              echo "<td><table width=100%>";
              echo "<tr><td>Address 1</td><td><input type=text name=strSiteAddr1></td></tr>";
              echo "<tr><td>Address 2</td><td><input type=text name=strSiteAddr2></td></tr>";
              echo "<tr><td>Address 3</td><td><input type=text name=strSiteAddr3></td></tr>";
              echo "<tr><td>Address 4</td><td><input type=text name=strSiteAddr4></td></tr>";
              echo "<tr><td>Address 5</td><td><input type=text name=strSiteAddr5></td></tr>";
              echo "<tr><td>Postcode</td><td><input type=text name=strSitePostcode></td></tr>";
              echo "<tr><td>Country</td><td><input type=text name=strSiteCountry></td></tr>";
              echo "<tr><td>Contact Number</td><td><input type=text name=strContactNum></td></tr>";
              echo "<tr><td>Contact Name</td><td><input type=text name=strContactName></td></tr>";
              echo "</table></td>";
              echo "<td><input type=submit value=Add></td></tr>\n";
              echo "</table>";
              echo "<input type=hidden name=query value=save>";
            }
          break;
        }
      } else {
        echo "<p>You do not have sufficient admin priviliges to perform these tasks.</p>";
      }
      break;
      case "support":
      if(doCheckLevel(3,5,7,8,9)) {
        switch($_REQUEST['query']) {
          case "del":

          break;
          case "edit":

          break;
          case "save":
            $sqlInsert="INSERT INTO equipment_support (strCompany , strAddr1 , strAddr2 , strAddr3 , strAddr4 , strAddr5 , strPostcode , strCountry , strContactName , strContactNum ) VALUES ('" . $_REQUEST['strCompany'] . "', '" . $_REQUEST['strAddr1'] . "', '" . $_REQUEST['strAddr2'] . "', '" . $_REQUEST['strAddr3'] . "', '" . $_REQUEST['strAddr4'] . "', '" . $_REQUEST['strAddr5'] . "', '" . $_REQUEST['strPostcode'] . "', '" . $_REQUEST['strCountry'] . "', '" . $_REQUEST['strContactName'] . "', '" . $_REQUEST['strContactNum'] . "')";
            $qryInsert=mysql_query($sqlInsert);
            $numInsert=mysql_affected_rows();
          break;
          default:
            echo "<h1 align=center>Edit Support Teams</h1>\n";
            $return=getSupportList();
            echo "<table width=100%>\n";
            echo "<tr><th>Support Team</th><th>Details</th><th>Edit</th></tr>\n";
            if($return['size']==0) {
              echo "<tr><td colspan=3>WARNING: You have no support teams defined!</td></tr>\n";
            } else {
              while($aryReturn=mysql_fetch_array($return['query'])) {
                echo "<tr><td>" . $aryReturn['strCompany'] . "</td>";
                echo "<td><table width=100%>";
                echo "<tr><td>Address 1</td><td>" . $aryReturn['strAddr1'] . "</td></tr>";
                echo "<tr><td>Address 2</td><td>" . $aryReturn['strAddr2'] . "</td></tr>";
                echo "<tr><td>Address 3</td><td>" . $aryReturn['strAddr3'] . "</td></tr>";
                echo "<tr><td>Address 4</td><td>" . $aryReturn['strAddr4'] . "</td></tr>";
                echo "<tr><td>Address 5</td><td>" . $aryReturn['strAddr5'] . "</td></tr>";
                echo "<tr><td>Postcode</td><td>" . $aryReturn['strPostcode'] . "</td></tr>";
                echo "<tr><td>Country</td><td>" . $aryReturn['strCountry'] . "</td></tr>";
                echo "<tr><td>Contact Number</td><td>" . $aryReturn['strContactNum'] . "</td></tr>";
                echo "<tr><td>Contact Name</td><td>" . $aryReturn['strContactName'] . "</td></tr>";
                echo "</table></td>";
                echo "<td><a href='edit.php?query=edit&type=site&uid=" . $aryReturn['intUID'] . "'>Edit</a></td></tr>\n";
              }
              echo "<tr><td><input type=text name=strCompany></td>";
              echo "<td><table width=100%>";
              echo "<tr><td>Address 1</td><td><input type=text name=strAddr1></td></tr>";
              echo "<tr><td>Address 2</td><td><input type=text name=strAddr2></td></tr>";
              echo "<tr><td>Address 3</td><td><input type=text name=strAddr3></td></tr>";
              echo "<tr><td>Address 4</td><td><input type=text name=strAddr4></td></tr>";
              echo "<tr><td>Address 5</td><td><input type=text name=strAddr5></td></tr>";
              echo "<tr><td>Postcode</td><td><input type=text name=strPostcode></td></tr>";
              echo "<tr><td>Country</td><td><input type=text name=strCountry></td></tr>";
              echo "<tr><td>Contact Number</td><td><input type=text name=strContactNum></td></tr>";
              echo "<tr><td>Contact Name</td><td><input type=text name=strContactName></td></tr>";
              echo "</table></td>";
              echo "<td><input type=submit value=Add></td></tr>\n";
              echo "</table>";
              echo "<input type=hidden name=query value=save>";
            }
          break;
        }
      } else {
        echo "<p>You do not have sufficient admin priviliges to perform these tasks.</p>";
      }
      break;
      case "type":
      if(doCheckLevel(8,9)) {
        switch($_REQUEST['query']) {
          case "del":

          break;
          case "edit":

          break;
          case "save":
            $sqlInsert="INSERT INTO equipment_type (strHardwareType, strGraphicFilename)VALUES ('" . $_REQUEST['strHardwareType'] . "', '" . $_REQUEST['strGraphicFilename'] . "')";
            $qryInsert=mysql_query($sqlInsert);
            $numInsert=mysql_affected_rows();
          break;
          default:
            echo "<h1 align=center>Edit Hardware Details</h1>\n";
            $return=getHardwareTypeList();
            echo "<table width=100%>\n";
            echo "<tr><th>Hardware Type</th><th>Graphic</th><th>Edit</th></tr>\n";
            if($return['size']==0) {
              echo "<tr><td colspan=3>WARNING: You have no hardware types defined!</td></tr>\n";
            } else {
              while($aryReturns=mysql_fetch_array($return['query'])) {
                echo "<tr><td>" . $aryReturns['strHardwareType'] . "</td>";
                echo "<td><img src='theme/equipment/" . $aryReturns['strGraphicFilename'] . "'></td>";
                echo "<td><a href='edit.php?query=edit&type=site&uid=" . $aryReturns['intUID'] . "'>Edit</a></td></tr>\n";
              }
              echo "<tr><td><input type=text name=strHardwareType></td>";
              echo "<td><select name=strGraphicFilename>";
              $handle=opendir("theme/equipment");
              while ($file = readdir($handle)) {
                if ($file != "." && $file != ".." && $file != "CVS") {
                  echo "<option name='$file'>$file</option>";
                }
              }
              //close the directory handle
              closedir($handle);
              echo "</select></td>";
              echo "<td><input type=submit value=Add></td></tr>\n";
              echo "</table>";
              echo "<input type=hidden name=query value=save>";
            }
          break;
        }
      } else {
        echo "<p>You do not have sufficient admin priviliges to perform these tasks.</p>";
      }
      break;
      case "user":
      if(doCheckLevel(8,9)) {
        switch($_REQUEST['query']) {
          case "del":

          break;
          case "edit":

          break;
          case "save":
            $sqlInsert="INSERT INTO nhtam_administrators (strUsername, strPassword, intNTAllowed, intLevel) VALUES ('" . $_RESULT['strUsername']. "', MD5('" . $_RESULT['strPassword']. "'), '" . $_RESULT['intNTAllowed']. "', '" . $_RESULT['intLevel']. "');";
            $qryInsert=mysql_query($sqlInsert);
            $numInsert=mysql_affected_rows();
          break;
          default:
            echo "<h1 align=center>Edit User</h1>";
            echo "<table width=100%>\n";
            echo "<tr><td>Username</td><td>Password</td><td>NT Login Allowed</td><td>Admin Level</td></tr>";
            $sqlGetAdmins="SELECT na.intUID, na.strUsername, na.intNTAllowed, nal.strAdmin FROM nhtam_administrators AS na, nhtam_admin_levels AS nal WHERE na.intLevel=nal.intLevel";
            $qryGetAdmins=mysql_query($sqlGetAdmins);
            $numGetAdmins=mysql_num_rows($qryGetAdmins);
            if($numGetAdmins==0) {
              echo "<tr><td colspan=4>WARNING: You have no users defined! (How the hell are you logged in?!)</td></tr>\n";
            } else {
              while($aryReturns=mysql_fetch_array($qryGetAdmins)) {
                echo "<tr><td>" . $aryReturns['strUsername'] . "</td>\n";
                echo "<td>Not Listed</td>\n";
                echo "<td>";
                if($aryReturns['intNTAllowed']==1) {echo "Yes";} else {echo "No";}
                echo "</td>\n";
                echo "<td>" . $aryReturns['strAdmin'] . "</td>\n";
                echo "<td><a href='edit.php?query=edit&type=user&uid=" . $aryReturns['intUID'] . "'>Edit</a></td></tr>\n";
              }
              echo "<tr><td><input type=text name=strUsername></td>";
              echo "<td><input type=text name=strPassword></td>";
              echo "<td><select name=intNTAllowed><option value=0 Selected>No</option><option value=1>Yes</option></select></td>";
              $sqlAdminLevels="SELECT intLevel, strAdmin FROM nhtam_admin_levels";
              $qryAdminLevels=mysql_query($sqlAdminLevels);
              echo "<td><select name=intLevel>";
              while(list($intUID, $strAdmin)=mysql_fetch_array($qryAdminLevels)) {
                echo "<option value=$intUID>$strAdmin</option>";
              }
              echo "</select></td>";
              echo "<td><input type=submit value=Add></td></tr>\n";
            }
            echo "</table>";
            echo "<input type=hidden name=query value=save>";
          break;
        }
      } else {
        echo "<p>You do not have sufficient admin priviliges to perform these tasks.</p>";
      }
      break;
    default:
      echo "<h1 align=center>Edit</h1>\n";
      echo "<table width=100%>\n";
      echo "<tr>";
      echo "<td width=25%><a href='edit.php?type=server'>Equipment Inventory</a></td>";
      echo "<td width=25%><a href='edit.php?type=location'>Locations</a></td>";
      echo "<td width=25%><a href='edit.php?type=site'>Sites</a></td>";
      echo "<td width=25%><a href='edit.php?type=support'>Support Contracts and Teams</a></td>";
      echo "</tr>\n<tr>";
      echo "<td width=25%><a href='edit.php?type=network'>Network Scope Names</a></td>";
      echo "<td width=25%><a href='edit.php?type=type'>Equipment Types</a></td>";
      echo "<td width=25%><a href='edit.php?type=user'>Administrative Users</a></td>";
      echo "<td width=25%>&nbsp;</td>";
      echo "</tr>\n";
      echo "</table>\n";
      echo "<input type=hidden name=query value=save>";
    break;
  }
}
if(isset($numInsert)) {
//  header("Location: edit.php?type=" . $_REQUEST['type']);
}

function getSiteList($strWhere="") {
  if($strWhere!="") {$strWhereStatement="WHERE " . $strWhere;}
  $strSQL="SELECT * FROM equipment_sites " . $strWhereStatement . " ORDER BY strSite";
  echo "\n\n<!-- $strSQL -->\n";
  $result['query']=mysql_query($strSQL);
  $result['size']=mysql_num_rows($result['query']);
  return($result);
}
function getLocationList($strWhere="",$bolReturnStrings=TRUE) {
  if($bolReturnStrings==TRUE) {
    $strSelect="el.intUID, el.strLocation, es.strSite";
    $strTables="equipment_locations AS el, equipment_sites AS es";
    $strOrderBy="ORDER BY es.strSite, el.strLocation";
    if($strWhere!="") {
      $strWhereStatement="WHERE el.intSite=es.intUID AND " . $strWhere;
    } else {
      $strWhereStatement="WHERE el.intSite=es.intUID";
    }
  } else {
    $strSelect="el.intUID, el.strLocation, el.intSite";
    $strTables="equipment_locations AS el";
    $strOrderBy="";
    if($strWhere!="") {$strWhereStatement="WHERE " . $strWhere;}
  }

  $strSQL="SELECT $strSelect FROM $strTables $strWhereStatement $strOrderBy";
  echo "\n\n<!-- $strSQL -->\n";
  $result['query']=mysql_query($strSQL);
  $result['size']=mysql_num_rows($result['query']);
  return($result);
}
function getNetworkList($strWhere="",$bolReturnStrings=TRUE) {
  if($strWhere!="") {$strWhereStatement="WHERE " . $strWhere;}
  $strSQL="SELECT intUID, strName FROM equipment_networks " . $strWhereStatement . " ORDER BY strName";
  echo "\n\n<!-- $strSQL -->\n";
  $result['query']=mysql_query($strSQL);
  $result['size']=mysql_num_rows($result['query']);
  return($result);
}
function getSupportList($strWhere="",$bolReturnStrings=TRUE) {
  if($strWhere!="") {$strWhereStatement="WHERE " . $strWhere;}
  $strSQL="SELECT * FROM equipment_support ". $strWhereStatement . " ORDER BY strCompany";
  echo "\n\n<!-- $strSQL -->\n";
  $result['query']=mysql_query($strSQL);
  $result['size']=mysql_num_rows($result['query']);
  return($result);
}
function getHardwareTypeList($strWhere="",$bolReturnStrings=TRUE) {
  if($strWhere!="") {$strWhereStatement="WHERE " . $strWhere;}
  $strSQL="SELECT * FROM equipment_type ". $strWhereStatement . " ORDER BY strHardwareType";
  echo "\n\n<!-- $strSQL -->\n";
  $result['query']=mysql_query($strSQL);
  $result['size']=mysql_num_rows($result['query']);
  return($result);
}
function getServerList($strWhere="",$bolReturnStrings=TRUE) {
  if($strWhere!="") {$strWhereStatement="AND " . $strWhere;}
  if($bolReturnStrings==TRUE) {
    $strSelect="ei.uid, ei.strIP, ei.strDisplayName, els.strSite, el.strLocation, en.strName, ei.strFunction, et.strHardwareType, es.strCompany";
    $strTables="equipment_inventory AS ei, equipment_sites AS els, equipment_locations AS el, equipment_networks AS en, equipment_type AS et, equipment_support AS es";
    $strWhereStatement="WHERE ei.intLocation=el.intUID AND el.intSite=els.intUID AND ei.intNetwork=en.intUID AND ei.intEquipmentType=et.intHardwareType AND ei.intSupport=es.intUID";
    $strOrderBy="ORDER BY els.strSite, el.strLocation, en.strName, et.strHardwareType";
  } else {
    $strSelect="ei.uid, ei.strIP, ei.strDisplayName, ei.intLocation, ei.intNetwork, ei.strFunction, ei.intEquipmentType, ei.intSupport";
    $strTables="equipment_inventory AS ei";
    $strWhereStatement="";
    $strOrderBy="";
  }
  
  $strSQL="SELECT $strSelect FROM $strTables $strWhereStatement $strOrderBy";
  echo "\n\n<!-- $strSQL -->\n";
  $result['query']=mysql_query($strSQL);
  $result['size']=mysql_num_rows($result['query']);
  return($result);
}

function doDisplaySiteList($strWhere="") {
  $return=getSiteList($strWhere);
  if($return['size']>0) {
    $result="<select name=intSite>\n";
    while($aryResult=mysql_fetch_array($return['query'])) {
      $result.="<option value='" . $aryResult['intUID'] . "'>" . $aryResult['strSite'] . "</option>\n";
    }
    $result.="</select>\n";
  }
  return($result);
}
function doDisplayLocationList($strWhere="") {
  $return=getLocationList($strWhere);
  if($return['size']>0) {
    $result="<select name=intLocation>\n";
    while(list($intUID, $strLocation, $strSite)=mysql_fetch_array($return['query'])) {
      $result.="<option value='$intUID'>$strSite - $strLocation</option>\n";
    }
    $result.="</select>\n";
  }
  return($result);
}
function doDisplayNetworkList($strWhere="") {
  $return=getNetworkList($strWhere);
  if($return['size']>0) {
    $result="<select name=intNetwork>\n";
    while(list($intUID, $strNetwork)=mysql_fetch_array($return['query'])) {
      $result.="<option value='$intUID'>$strNetwork</option>\n";
    }
    $result.="</select>\n";
  }
  return($result);
}
function doDisplaySupportList($strWhere="") {
  $return=getSupportList($strWhere);
  if($return['size']>0) {
    $result="<select name=intSupport>\n";
    while($aryResult=mysql_fetch_array($return['query'])) {
      $result.="<option value='" . $aryResult['intUID'] . "'>" . $aryResult['strCompany'] . "</option>\n";
    }
    $result.="</select>\n";
  }
  return($result);
}
function doDisplayHardwareTypeList($strWhere="") {
  $return=getHardwareTypeList($strWhere);
  if($return['size']>0) {
    $result="<select name=intHardware>\n";
    while($aryResult=mysql_fetch_array($return['query'])) {
      $result.="<option value='" . $aryResult['intUID'] . "'>" . $aryResult['strHardwareType'] . "</option>\n";
    }
    $result.="</select>\n";
  }
  return($result);
}

if ($_REQUEST['query']=="edit") {
  $EditPage="<a href='edit.php'>List editable categories</a> | <a href='edit.php?type=" . $_REQUEST['type'] . "'>Edit " . $_REQUEST['type'] . " items |";
} elseif ($_REQUEST['type']!="") {
  $EditPage="<a href='edit.php'>List editable categories</a> |";
} else {
  $EditPage="";
}

doSendPageFooter("<p align=center><a href='index.php'>Return to main page</a> | $EditPage $LogInOrOut</p>\n");

function doCheckLevel($lvl0="",$lvl1="",$lvl2="",$lvl3="",$lvl4="",$lvl5="",$lvl6="",$lvl7="",$lvl8="",$lvl9="") {
  switch($_SESSION['admin_level']) {
    case $lvl0:
    case $lvl1:
    case $lvl2:
    case $lvl3:
    case $lvl4:
    case $lvl5:
    case $lvl6:
    case $lvl7:
    case $lvl8:
    case $lvl9:
      return(TRUE);
    break;
    default:
      return(FALSE);
    break;
  }
}
?>

