<?php

// The plug-in should pick up where it was called from by this.

doDebug("Switching for NTP Plugin: $WhatCalledMe");

switch ($WhatCalledMe) {
case "check":
    $return=doPlugin_NTP_Check();
    break;
case "display":
    $return=doPlugin_NTP_Display($strIP);
    break;
case "drilldown":
    $return=doPlugin_NTP_DrillDown($strIP,$idp);
    break;
case "showcontext":
    $return=doPlugin_NTP_Show_Context($strIP);
    break;
case "addcontext":
    $return=doPlugin_NTP_Add_Context($strIP);
    break;
case "delcontext":
    $return=doPlugin_NTP_Del_Context($strIP);
    break;
case "modcontext":
    $return=doPlugin_NTP_Mod_Context($strIP);
    break;
default:
    $return=doPlugin_NTP_ShowInfo();
}

?>
