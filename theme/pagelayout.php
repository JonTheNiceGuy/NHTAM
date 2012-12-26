<?php
function doSendPageHeader($title="Network Hardware Tracking and Maintainance") {
  echo "<html>\n";
  echo "<head>\n";
  echo "<title>$title</title>\n";
  echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"theme/css.css\" />\n";
  echo "</head>\n";
  echo "<body>\n";
}

function doSendPageFooter($footertext="") {
  echo $footertext;
  echo "</body>\n";
  echo "</html>\n";
}
?>
