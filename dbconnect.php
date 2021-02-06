<?php

function openConnection()
{
  $dbhost = "localhost";
  $dbuser = "souvik";
  $dbpass = "MuayThai23";
  $db = "practice";
  $conn = new mysqli($dbhost, $dbuser, $dbpass, $db);
  return $conn;
}

?>
