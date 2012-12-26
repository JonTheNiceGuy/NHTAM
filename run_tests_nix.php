#!/usr/bin/php -q
<?php

/***************************************************
 * This code derived from PHP manual               *
 * http://uk.php.net/manual/en/ref.pcntl.php#44937 *
 * This code is designed to fork off and launch    *
 * the checks against the Equipment Inventory in   *
 * order to update the SQL server with all the ups *
 * and downs!                                      *
 ***************************************************/

declare(ticks = 1);

$max=10;
$child=0;

// function for signal handler
function sig_handler($signo) {
  global $child;
  switch ($signo) {
   case SIGCHLD:
     echo "SIGCHLD received\n";
     $child -= 1;
  }
}

// install signal handler for dead kids
pcntl_signal(SIGCHLD, "sig_handler");

for ($i=1;$i<=20;$i++) {
       while ($child >= $max) {
               sleep(5); echo "\t Maximum children allowed\n";
               }
       $child++;
       $pid=pcntl_fork();
       if ($pid == -1) {
               die("could not fork");
       } else if ($pid) {
               // we are the parent
       } else {
               // we are the child
               echo "child number $i\n";
               // presumably doing something interesting
               sleep(15);
               exit;
       }
}

?>
