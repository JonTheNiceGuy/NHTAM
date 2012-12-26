<HTML>
<HEAD>
<TITLE>Review Switch Topology</TITLE>
<link rel="stylesheet" type="text/css" href="theme/css.css">
</HEAD>
<BODY>
<?php

require_once("functions/dbConnect.php");


echo "<table width=100%>\n";

if ($_GET['strSite']!="") {
	$strSelectSite="&strSite=" . $_GET['strSite'];
}

if ($_GET['strNetwork']!="") {
	$strSelectNetwork="&strNetwork=" . $_GET['strNetwork'];
}


$qrySite=mysql_query("SELECT strSite FROM equipment_inventory WHERE intEquipmentType=7 GROUP BY strSite");
$qryNetwork=mysql_query("SELECT strNetwork FROM equipment_inventory WHERE intEquipmentType=7 GROUP BY strNetwork");

$rowSite=mysql_num_rows($qrySite);
$rowNetwork=mysql_num_rows($qryNetwork);

$maxRow=$rowSite;
If ($rowNetwork>$maxRow) {$maxRow=$rowNetwork;}

echo "<tr><th>Select Site</th>\n";

$rowCounter=0;

while (list($strGetSite)=mysql_fetch_array($qrySite)) {
	$rowCounter++;
	if ($strGetSite==$_GET['strSite']) {$live="id='selected'";} else {$live="";}
	echo "<td><a href='?strSite=$strGetSite$strSelectEqType$strSelectNetwork' $live>$strGetSite</a></td>\n";
}

$rowBlank=$maxRow-$rowCounter;

echo "<td><a href='?strSite=%$strSelectEqType$strSelectNetwork'>All</a></td>\n";

if ($rowBlank>0) {echo "<td colspan=" . $rowBlank . ">&nbsp;</td>\n";}

echo "</tr>\n";

echo "<tr><th>Select Network</th>\n";

$rowCounter=0;

while (list($strGetNetwork)=mysql_fetch_array($qryNetwork)) {
	$rowCounter++;
	if ($strGetNetwork==$_GET['strNetwork']) {$live="id='selected'";} else {$live="";}
	echo "<td><a href='?strNetwork=$strGetNetwork$strSelectEqType$strSelectSite' $live>$strGetNetwork</a></td>\n";
}

$rowBlank=$maxRow-$rowCounter;

echo "<td><a href='?strNetwork=%$strSelectEqType$strSelectSite'>All</a></td>\n";

if ($rowBlank>0) {echo "<td colspan=" . $rowBlank . ">&nbsp;</td>\n";}

echo "</tr>\n";

echo "</table><br>";

if ($_GET['strNetwork']!="" OR $_GET['strEqType']!="" OR $_GET['strSite']!="") {

	echo "<TABLE width=100%>";

	if ($_GET['strNetwork']!="") {
		$GetNetwork=" AND hardware.strNetwork LIKE '" . $_GET['strNetwork'] . "' ";
	}
	
	if ($_GET['strSite']!="") {
		$GetSite=" AND hardware.strSite LIKE '" . $_GET['strSite'] . "' ";
	}


	$sqlGetSwitch="SELECT switch.Src_Switch, hardware.strSite, hardware.strNetwork FROM switch_map AS switch, equipment_inventory AS hardware WHERE switch.Src_Switch=hardware.strDisplayName $GetNetwork $GetSite GROUP BY switch.Src_Switch ORDER BY hardware.strNetwork, hardware.strSite, switch.Src_Switch";

	$qryGetSwitch=mysql_query($sqlGetSwitch);

	if (mysql_num_rows($qryGetSwitch)>0) {
	
		echo "<table width=100%>";
	
		echo "<tr><th>Switch</th><th>Site</th><th>Network</th>";
	
		for ($port=1; $port<=48; $port++) {
			echo "<th>Port $port</th>";
		}
	
		echo "<th>Uplink/GB Ethernet 1</th><th>Uplink/GB Ethernet 2</th><th>GB Fibre 1</th><th>GB Fibre 2</th>";
	
		while (list($strSrcSwitch, $strSite, $strNetwork)=mysql_fetch_array($qryGetSwitch)) {
	
			echo "<tr><th>$strSrcSwitch</th><td>$strSite</td><td>$strNetwork</td>";
	
			for ($port=1; $port<=48; $port++) {
				checkport($strSrcSwitch,$port);
			}
	
			$port="U1";
			checkport($strSrcSwitch,$port);
			$port="U2";
			checkport($strSrcSwitch,$port);
			$port="F1";
			checkport($strSrcSwitch,$port);
			$port="F2";
			checkport($strSrcSwitch,$port);
	
			echo "</tr>";
		}
	}

	echo "</table>";

	echo "</body>";
	echo "</html>";
}

function checkport($strSrcSwitch,$port) {

	$sqlReadSwitch="SELECT Dst_Switch, Dst_Port FROM switch_map WHERE Src_Switch = '$strSrcSwitch' AND Src_Port LIKE '$port'";
		
	$qryReadSwitch=mysql_query($sqlReadSwitch);
		
	if (mysql_num_rows($qryReadSwitch)>0) {

		list($strDstSwitch, $strDstPort)=mysql_fetch_array($qryReadSwitch);
				
		if ($strDstSwitch!="") {
				
			if ($strDstPort!="") {
					
				$sqlConfirmDst="SELECT Dst_Switch, Dst_Port FROM switch_map WHERE Src_Switch = '$strDstSwitch' AND Src_Port LIKE '$strDstPort'";
					
				$qryConfirmDst=mysql_query($sqlConfirmDst);
					
				if (mysql_num_rows($qryConfirmDst)>0) {
						
					$aryConfirmDst=mysql_fetch_array($qryConfirmDst);
						
					if ($aryConfirmDst['Dst_Switch']==$strSrcSwitch AND $aryConfirmDst['Dst_Port']==$port) {
							
						// You'll get here if the Source Switch Port matches the Destination Switch Port
						
						$Spacer="/";
							
						$ConfirmDst=True;
						$Server=False;
						
					} else {
						
						// You'll get here if the Source Switch Port doesn't match the Destination Switch Port
						
						$Spacer="/";
						
						$ConfirmDst=False;
						$Server=False;
				
					}
					
				} else {
					
					// You'll get here if the Destination Switch Port doesn't exist
					
					$Spacer="/";
					
					$ConfirmDst=False;
					$Server=False;
					
				}
				
			} else {
				
				// You'll get here if the Source Switch Port is connected to a non-switch device
				
				$Spacer="";
			
				$ConfirmDst=True;
				$Server=True;
				
			}
				
		} else {
				
			// You'll get here if the Destination Switch Port is empty
			
			$Spacer="";
					
			$strDstSwitch="&nbsp;";
			$strDstPort="&nbsp;";
					
			$ConfirmDst=True;
			$Server=False;
				
		}
			
	} else {
			
		// You'll get here if there's no source port.
			
		$Spacer="";
			
		$strDstSwitch="&nbsp;";
		$strDstPort="&nbsp;";

		$ConfirmDst=True;
		$Server=False;
			
	}

	if ($ConfirmDst==True AND $Server==True) {
		$id="";
	} elseif ($ConfirmDst==True AND $Server==False) {
		$id="id='Connected'";
	} elseif ($ConfirmDst==False) {
		$id="id='NoConnection'";
	} else {
		$id="id='NoConnection'";
	}
		
	echo "<td $id>" . $strDstSwitch . $Spacer . $strDstPort . "</td>";
}

?>
