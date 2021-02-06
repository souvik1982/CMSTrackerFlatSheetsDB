<?php

function openConnection()
{
  $dbhost = "localhost";
  $dbuser = "souvik";
  $dbpass = "MuayThai23";
  $db = "practice";
  if (!$conn = new mysqli($dbhost, $dbuser, $dbpass, $db)) echo "<b>ERROR</b>: Could not connect to the mySQL database. Please contact database management. </br> \n";
  return $conn;
}

?>
