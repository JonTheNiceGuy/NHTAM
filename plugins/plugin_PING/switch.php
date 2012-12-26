<?php

// The plug-in should pick up where it was called from by this.

doDebug("Switching for PING Plugin: $WhatCalledMe");

switch ($WhatCalledMe) {
case "check":
    $return=doPlugin_PING_Check();
    break;
case "display":
    $return=doPlugin_PING_Display($strIP);
    break;
case "drilldown":
    $return=doPlugin_PING_DrillDown($strIP,$idp);
    break;
case "showcontext":
    $return=doPlugin_PING_Show_Context($strIP);
    break;
case "addcontext":
    $return=doPlugin_PING_Add_Context($strIP);
    break;
case "delcontext":
    $return=doPlugin_PING_Del_Context($strIP);
    break;
case "modcontext":
    $return=doPlugin_PING_Mod_Context($strIP);
    break;
default:
    $return=doPlugin_PING_ShowInfo();
}

?>
