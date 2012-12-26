<?php

function doPlugin_NTP_Add_Context($strIP) {
  // This function should add the UID of $strIP to it's check table
  $getContext=doPlugin_NTP_Show_Context($strIP);
  if($getContext['on']==FALSE) {
    $strEiUid=doGetEIUID($strIP);
    $sqlPushCheck="INSERT INTO plugin_NTP_checklist (ei_UID) VALUES ('$strEiUid')";
    $qryPushCheck=mysql_query($sqlPushCheck);
    $sqlCheckPush="SELECT ei_UID FROM plugin_NTP_checklist WHERE ei_UID like '$strEiUid'";
    $qryCheckPush=mysql_query($sqlCheckPush);
    $numCheckPush=mysql_num_rows($qryCheckPush);
    if($numCheckPush==0) {
      $result['state']=FALSE;
      $result['reason']="The inserted entry does not exist";
    } elseif ($numCheckPush==1) {
      $result['state']=TRUE;
      $result['reason']="Record inserted correctly";
    } elseif ($numCheckPush>=2) {
      $result['state']=FALSE;
      $result['reason']="Duplicate entries located";
    }
  } else {
    $result['state']=FALSE;
    $result['reason']="This entry already is marked for checking";
  }
}

function doPlugin_NTP_Del_Context($strIP) {
  // This function should remove the UID of $strIP from it's check table
  $getContext=doPlugin_NTP_Show_Context($strIP);
  if($getContext['on']==TRUE) {
    $strEiUid=doGetEIUID($strIP);
    $sqlPushCheck="DELETE FROM plugin_NTP_checklist WHERE ei_UID LIKE '$strEiUid'";
    $qryPushCheck=mysql_query($sqlPushCheck);
    $sqlCheckPush="SELECT ei_UID FROM plugin_NTP_checklist WHERE ei_UID like '$strEiUid'";
    $qryCheckPush=mysql_query($sqlCheckPush);
    $numCheckPush=mysql_num_rows($qryCheckPush);
    if($numCheckPush==0) {
      $result['state']=TRUE;
      $result['reason']="The entry has been removed successfully";
    } elseif ($numCheckPush>=1) {
      $result['state']=FALSE;
      $result['reason']="Entries stll exist with that ID";
    }
  } else {
    $result['state']=FALSE;
    $result['reason']="This entry was not marked for checking";
  }
}

function doPlugin_NTP_Show_Context($strIP) {
  // This function should return either TRUE or FALSE indicating whether the server
  // is listed as being checked.
  $strEiUid=doGetEIUID($strIP);
  $sqlCheckContext="SELECT ei_UID FROM plugin_NTP_checklist WHERE ei_UID LIKE '$strEiUid'";
  $qryCheckContext=mysql_query($sqlCheckContext);
  $numCheckContext=mysql_num_rows($qryCheckContext);
  $result['alt']="Ping.";
  if ($numCheckContext>0) {
    $result['on']=TRUE;
  } else {
    $result['on']=FALSE;
  }
  return $result;
}

function doPlugin_NTP_Mod_Context($strIP) {
  // This function should display any other fields to update it's check table with what to be checked.
  $result="";
  return $result;
}

function doPlugin_NTP_Check() {
  // This function runs all the checks for this function. It will be called by a scheduled task,
  // and should derive what servers it should be checking by the use of the plugin_NTP_checklist table and
  // write it's output to plugin_NTP_result.

  $sqlDoCheck="SELECT ei.strIP, ei.uid FROM equipment_inventory AS ei, plugin_NTP_checklist AS pPc WHERE pPc.ei_UID=ei.uid";
  $qryDoCheck=mysql_query($sqlDoCheck);
  
  while(list($strIP, $intUID)=mysql_fetch_array($qryDoCheck)) {
   	$NTP = new NTP_Core;
	$Result = $NTP->lookup($strIP);

	if ($Result==TRUE) {
      if ($NTP->Format()) {
        $timestamp = date('j/M/Y G:i:s T', $NTP->Timestamp); // 31/Oct/2005 18:10:05 GMT
        $sqlDoneCheck="INSERT INTO plugin_NTP_history (ei_UID, datTimeStamp, strResult) VALUES ('$intUID', '" . date("Y-m-d H:i") ."', '$timestamp')";
        if (mysql_num_rows(mysql_query("SELECT ei_UID FROM plugin_NTP_last_result WHERE ei_UID = '$intUID'"))==0) {
          $sqlLastCheck="INSERT INTO plugin_NTP_last_result (strResult, ei_UID) VALUES ('$timestamp', '$intUID')";
        } else {
          $sqlLastCheck="UPDATE plugin_NTP_last_result SET strResult = '$timestamp' WHERE ei_UID = '$intUID'";
        }
      } else {
        $sqlDoneCheck="INSERT INTO plugin_NTP_history (ei_UID, datTimeStamp, strResult) VALUES ('$intUID', '" . date("Y-m-d H:i") ."', 'Result Corrupt')";
        if (mysql_num_rows(mysql_query("SELECT ei_UID FROM plugin_NTP_last_result WHERE ei_UID = '$intUID'"))==0) {
          $sqlLastCheck="INSERT INTO plugin_NTP_last_result (strResult, ei_UID) VALUES ('Result Corrupt', '$intUID')";
        } else {
          $sqlLastCheck="UPDATE plugin_NTP_last_result SET strResult = 'Result Corrupt' WHERE ei_UID = '$intUID'";
        }
      }
    } else {
      $sqlDoneCheck="INSERT INTO plugin_NTP_history (ei_UID, datTimeStamp, strResult) VALUES ('$intUID', '" . date("Y-m-d H:i") ."', 'No Response')";
      if (mysql_num_rows(mysql_query("SELECT ei_UID FROM plugin_NTP_last_result WHERE ei_UID = '$intUID'"))==0) {
        $sqlLastCheck="INSERT INTO plugin_NTP_last_result (strResult, ei_UID) VALUES ('No Response', '$intUID')";
      } else {
        $sqlLastCheck="UPDATE plugin_NTP_last_result SET strResult = 'No Response' WHERE ei_UID = '$intUID'";
      }
    }
    $qryDoneCheck=mysql_query($sqlDoneCheck);
    $qryLastCheck=mysql_query($sqlLastCheck);
  }
}

function doPlugin_NTP_Display($strIP) {
  // This function should return a positive, warning or negative result from the plugin_NTP_result table. This
  // will set the relevant cells (0 = not requested), (1 = good), (2 = warning) or (3 = error).
  // It should also return the last recorded value.

  $strEiUid=doGetEIUID($strIP);

  $sqlDoReturn="SELECT strResult FROM plugin_NTP_last_result WHERE ei_UID='$strEiUid'";
  $qryDoReturn=mysql_query($sqlDoReturn);
  $numDoReturn=mysql_num_rows($qryDoReturn);
  
  if ($numDoReturn==0) {
    $result['trafficlight']=0;
    $result['value']="Not Requested";
  } else {
    list($strResult)=mysql_fetch_array($qryDoReturn);
    switch($strResult) {
      case "No Response":
        $result['trafficlight']=3;
        $result['value']="Timed Out or No Response";
        break;
      case "Result Corrupt":
        $result['trafficlight']=2;
        $result['value']="Response corrupted";
        break;
      default:
      $result['trafficlight']=1;
      $result['value']=$strResult;
    }
  }
  return $result;
}

function doPlugin_NTP_DrillDown($strIP, $strDisplayPeriod) {
  // This function should also return a positive, warning or negative result from the plugin_NTP_result table
  // above. It should also return the history for that check.
  
  $strEiUid=doGetEIUID($strIP);

  $sqlDoReturn="SELECT datTimeStamp, strResult FROM plugin_NTP_history WHERE ei_UID='$strEiUid' AND datTimeStamp>'$strDisplayPeriod'";
  $qryDoReturn=mysql_query($sqlDoReturn);
  $numDoReturn=mysql_num_rows($qryDoReturn);

  if ($numDoReturn==0) {
    $result['1']['trafficlight']=0;
    $result['1']['value']="Not Requested";
    $result['1']['timestamp']="0000-00-00 00:00:00";
    $result['general']['size']=0;
  } else {
    $intNumRows=0;
    while(list($datTimeStamp, $strResult)=mysql_fetch_array($qryDoReturn)) {
      $intNumRows++;
      switch($strResult) {
        case "No Response":
          $result["$intNumRows"]['trafficlight']=3;
          $result["$intNumRows"]['timestamp']=$datTimeStamp;
          $result["$intNumRows"]['value']="Timed Out or No Response";
          break;
        case "Result Corrupt":
          $result["$intNumRows"]['trafficlight']=2;
          $result["$intNumRows"]['timestamp']=$datTimeStamp;
          $result["$intNumRows"]['value']="Response corrupted";
          break;
        default:
          $result["$intNumRows"]['trafficlight']=1;
          $result["$intNumRows"]['timestamp']=$datTimeStamp;
          $result["$intNumRows"]['value']=$strResult;
      }
    }
    $result['general']['size']=$intNumRows;
  }
  return $result;
}

function doPlugin_NTP_ShowInfo() {
  // This is what will be called when previewing the use of each function in the admin pages.
  // Try not to make it too marketing!
  $result['Version']="0.1";
  $result['Author']="Jon Spriggs (Core functions by Brian Haase, obtained from PEAR.PHP.NET)";
  $result['Contact']="jontheniceguy@users.sourceforge.net";
  $result['Info']="This plugin retrieves the NTP time from the host.";
  $result['Title']="NTP";
  return $result;
}

// ******************************************************
// * NTP Interface Class for PHP                        *
// *  (Written by Brian Haase on June 22, 2004          *
// ******************************************************
class NTP_Core {
  var $Server;
  var $Port = 13;
  var $Timeout = 10;

  var $Time = "";
  var $Timestamp = 0;

  var $Error = "";

  function lookup($Server) {
    $this->Error = "";
    $this->Server = $Server;

    $_Success = FALSE;
    $_Time = "";

    $_Timeout = time();

    $Fp = @fsockopen( $this->Server, $this->Port, $errno, $errstr, $this->Timeout );

    if (!$Fp) {
      $this->Error = $errno . " : " . $errstr;
      return FALSE;
    }

    for (;time() <= ($_Timeout + $this->Timeout);) {
      $_Time .= fgets( $Fp, 2096 );
      if (feof($Fp)) {break;}
    }

    if ($Fp) {fclose($Fp);}

    if ($_Time<>"") {
      $this->Time = trim($_Time);
      return TRUE;
    }

    $this->Error = "N/A : NTP PHP Class Socket Timeout";
    return FALSE;
  }

  function format() {
    // Tue Jun 22 07:19:36 UTC 2004
    // Tue Jun 22 07:19:51 2004

    $_Fields = explode( ' ', $this->Time );
    $_Subfields = explode( ':', $_Fields[3] );

    // Discard the day of week - $_Fields[0];
    $Month = $_Fields[1];
    $Day = $_Fields[2];
    $Hour = $_Subfields[0];
    $Min = $_Subfields[1];
    $Sec = $_Subfields[2];

    if ( count( $_Fields ) == 6 ) {
      $Zone = $_Fields[4];
      $Year = $_Fields[5];
    } else {
      $Zone = "";
      $Year = $_Fields[4];
    }

    $MTable = array( "", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" );
    $Month = array_search( $Month, $MTable );

    $this->Timestamp = mktime( $Hour, $Min, $Sec, $Month, $Day, $Year );

    return TRUE;
  }
}
?>
