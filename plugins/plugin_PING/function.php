<?php

function doPlugin_PING_Add_Context($strIP) {
  // This function should add the UID of $strIP to it's check table
  $getContext=doPlugin_PING_Show_Context($strIP);
  if($getContext['on']==FALSE) {
    $strEiUid=doGetEIUID($strIP);
    $sqlPushCheck="INSERT INTO plugin_PING_checklist (ei_UID) VALUES ('$strEiUid')";
    $qryPushCheck=mysql_query($sqlPushCheck);
    $sqlCheckPush="SELECT ei_UID FROM plugin_PING_checklist WHERE ei_UID like '$strEiUid'";
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

function doPlugin_PING_Del_Context($strIP) {
  // This function should remove the UID of $strIP from it's check table
  $getContext=doPlugin_PING_Show_Context($strIP);
  if($getContext['on']==TRUE) {
    $strEiUid=doGetEIUID($strIP);
    $sqlPushCheck="DELETE FROM plugin_PING_checklist WHERE ei_UID LIKE '$strEiUid'";
    $qryPushCheck=mysql_query($sqlPushCheck);
    $sqlCheckPush="SELECT ei_UID FROM plugin_PING_checklist WHERE ei_UID like '$strEiUid'";
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

function doPlugin_PING_Show_Context($strIP) {
  // This function should return either TRUE or FALSE indicating whether the server
  // is listed as being checked.
  $strEiUid=doGetEIUID($strIP);
  $sqlCheckContext="SELECT ei_UID FROM plugin_PING_checklist WHERE ei_UID LIKE '$strEiUid'";
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

function doPlugin_PING_Mod_Context($strIP) {
  // This function should display any other fields to update it's check table with what to be checked.
  $result="";
  return $result;
}

function doPlugin_PING_Check() {
  // This function runs all the checks for this function. It will be called by a scheduled task,
  // and should derive what servers it should be checking by the use of the plugin_PING_checklist table and
  // write it's output to plugin_PING_result.

  $sqlDoCheck="SELECT ei.strIP, ei.uid FROM equipment_inventory AS ei, plugin_PING_checklist AS pPc WHERE pPc.ei_UID=ei.uid";
  $qryDoCheck=mysql_query($sqlDoCheck);
  
  while(list($strIP, $intUID)=mysql_fetch_array($qryDoCheck)) {
   	$ping = new Net_Ping;
	$ping->ping($strIP);

	if ($ping->time) {
      $sqlDoneCheck="INSERT INTO plugin_PING_history (ei_UID, datTimeStamp, strResult) VALUES ('$intUID', '" . date("Y-m-d H:i") ."', '" . $ping->time . "')";
      if (mysql_num_rows(mysql_query("SELECT ei_UID FROM plugin_PING_last_result WHERE ei_UID = '$intUID'"))==0) {
        $sqlLastCheck="INSERT INTO plugin_PING_last_result (strResult, ei_UID) VALUES ('" . $ping->time . "', '$intUID')";
      } else {
        $sqlLastCheck="UPDATE plugin_PING_last_result SET strResult = '" . $ping->time . "' WHERE ei_UID = '$intUID'";
      }
    } else {
      $sqlDoneCheck="INSERT INTO plugin_PING_history (ei_UID, datTimeStamp, strResult) VALUES ('$intUID', '" . date("Y-m-d H:i") ."', '9.99999')";
      if (mysql_num_rows(mysql_query("SELECT ei_UID FROM plugin_PING_last_result WHERE ei_UID = '$intUID'"))==0) {
        $sqlLastCheck="INSERT INTO plugin_PING_last_result (strResult, ei_UID) VALUES ('9.99999', '$intUID')";
      } else {
        $sqlLastCheck="UPDATE plugin_PING_last_result SET strResult = '9.99999' WHERE ei_UID = '$intUID'";
      }
    }
    $qryDoneCheck=mysql_query($sqlDoneCheck);
    $qryLastCheck=mysql_query($sqlLastCheck);
  }
}

function doPlugin_PING_Display($strIP) {
  // This function should return a positive, warning or negative result from the plugin_PING_result table. This
  // will set the relevant cells (0 = not requested), (1 = good), (2 = warning) or (3 = error).
  // It should also return the last recorded value.

  $strEiUid=doGetEIUID($strIP);

  $sqlDoReturn="SELECT strResult FROM plugin_PING_last_result WHERE ei_UID='$strEiUid'";
  $qryDoReturn=mysql_query($sqlDoReturn);
  $numDoReturn=mysql_num_rows($qryDoReturn);
  
  if ($numDoReturn==0) {
    $result['trafficlight']=0;
    $result['value']="Not Requested";
  } else {
    list($strResult)=mysql_fetch_array($qryDoReturn);
    if ($strResult=="9.9999") {
      $result['trafficlight']=3;
      $result['value']="Timed Out or No Response";
    } else {
      $result['trafficlight']=1;
      $result['value']=$strResult;
    }
  }

  return $result;
}

function doPlugin_PING_DrillDown($strIP, $strDisplayPeriod) {
  // This function should also return a positive, warning or negative result from the plugin_PING_result table
  // above. It should also return the history for that check.
  
  $strEiUid=doGetEIUID($strIP);

  $sqlDoReturn="SELECT datTimeStamp, strResult FROM plugin_PING_history WHERE ei_UID='$strEiUid' AND datTimeStamp>'$strDisplayPeriod'";
  echo "<!-- $sqlDoReturn -->\n";
  $qryDoReturn=mysql_query($sqlDoReturn);
  $numDoReturn=mysql_num_rows($qryDoReturn);

  if ($numDoReturn==0) {
    $result['1']['trafficlight']=0;
    $result['1']['value']="Not Requested";
    $result['1']['timestamp']="0000-00-00 00:00:00";
    $result['general']['size']=1;
  } else {
    $intNumRows=0;
    while(list($datTimeStamp, $strResult)=mysql_fetch_array($qryDoReturn)) {
      $intNumRows++;
      if ($strResult=="9.9999") {
        $result["$intNumRows"]['trafficlight']=3;
        $result["$intNumRows"]['timestamp']=$datTimeStamp;
        $result["$intNumRows"]['value']="Timed Out or No Response";
      } else {
        $result["$intNumRows"]['trafficlight']=1;
        $result["$intNumRows"]['timestamp']=$datTimeStamp;
        $result["$intNumRows"]['value']=$strResult;
      }
    }
    $result['general']['size']=$intNumRows;
  }
  return $result;
}

function doPlugin_PING_ShowInfo() {
  // This is what will be called when previewing the use of each function in the admin pages.
  // Try not to make it too marketing!
  $result['Version']="0.1";
  $result['Author']="Jon Spriggs";
  $result['Contact']="jontheniceguy@users.sourceforge.net";
  $result['Info']="This plugin pings servers using ICMP.";
  $result['Title']="Ping";
  return $result;
}

/******************************************************
 * In order to run this check, I need to be           *
 * able to send an ICMP packet to the target          *
 * machine, and read it's response. This              *
 * code derived from comments in the PHP              *
 * manual. See comments                               *
 * http://uk2.php.net/manual/en/ref.sockets.php#56061 *
 * http://uk2.php.net/manual/en/ref.sockets.php#42466 *
 * http://uk2.php.net/manual/en/ref.sockets.php#39151 *
 ******************************************************/

class Net_Ping {
	var $icmp_socket;
	var $request;
	var $request_len;
	var $reply;
	var $errstr;
	var $time;
	var $timer_start_time;

	function Net_Ping() {
		$this->icmp_socket = socket_create(AF_INET, SOCK_RAW, 1);
		socket_set_block($this->icmp_socket);
	}

	function ip_Checksum($data) {
		// Add a 0 to the end of the data, if it's an "odd length"
		if (strlen($data)%2) $data .= "\x00";

		// Let PHP do all the dirty work
		$bit = unpack('n*', $data);
		$sum = array_sum($bit);

		// Stolen from: Khaless [at] bigpond [dot] com
		// The code from the original ping program:
		// sum = (sum >> 16) + (sum & 0xffff); /* add hi 16 to low 16 */
		// sum += (sum >> 16); /* add carry */
		// which also works fine, but it seems to me that
		// Khaless will work on large data.
		while ($sum>>16) $sum = ($sum >> 16) + ($sum & 0xffff);
		return pack('n*', ~$sum);
	}


	function start_time() {
		$this->timer_start_time = microtime();
	}

	function get_time($acc=2) {
		// format start time
		$start_time = explode (" ", $this->timer_start_time);
		$start_time = $start_time[1] + $start_time[0];
		// get and format end time
		$end_time = explode (" ", microtime());
		$end_time = $end_time[1] + $end_time[0];
		return number_format ($end_time - $start_time, $acc);
	}

	function Build_Packet() {
		$data = "abcdefghijklmnopqrstuvwabcdefghi"; // the actual test data
		$type = "\x08"; // 8 echo message; 0 echo reply message
		$code = "\x00"; // always 0 for this program
		$chksm = "\x00\x00"; // generate checksum for icmp request
		$id = "\x00\x00"; // we will have to work with this later
		$sqn = "\x00\x00"; // we will have to work with this later

		// now we need to change the checksum to the real checksum
		$chksm = $this->ip_checksum($type.$code.$chksm.$id.$sqn.$data);

		// now lets build the actual icmp packet
		$this->request = $type.$code.$chksm.$id.$sqn.$data;
		$this->request_len = strlen($this->request);
	}

	function Ping($dst_addr,$timeout=100,$percision=5) {
		// lets catch dumb people
		if ((int)$timeout <= 0) $timeout=100;
		if ((int)$percision <= 0) $percision=5;

		// set the timeout
		socket_set_option($this->icmp_socket,
			SOL_SOCKET, // socket level
			SO_RCVTIMEO, // timeout option
			array(
				"sec"=>$timeout, // Timeout in seconds
				"usec"=>0 // I assume timeout in microseconds
			)
		);

		if ($dst_addr) {
			if (@socket_connect($this->icmp_socket, $dst_addr, NULL)) {
			} else {
				$this->errstr = "Cannot connect to $dst_addr";
				return FALSE;
			}
			$this->Build_Packet();
			$this->start_time();
			socket_write($this->icmp_socket, $this->request, $this->request_len);
			if (@socket_recv($this->icmp_socket,
					$this->reply,
					256,
					0)) {
				$this->time = $this->get_time($percision);
				return $this->time;
			} else {
				$this->errstr = "Timed out";
				return FALSE;
			}
		} else {
			$this->errstr = "Destination address not specified";
			return FALSE;
		}
	}
}

?>
