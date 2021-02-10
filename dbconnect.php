<?php

function openConnection()
{
  $dbhost = "avogadro.physics.purdue.edu";
  $dbuser = "cmstrackeruser";
  $dbpass = "DB for the CMS tracker.";
  $db = "CMSTrackerFlatSheetsDB";
  $conn = new mysqli($dbhost, $dbuser, $dbpass, $db);
  return $conn;
}

?>
