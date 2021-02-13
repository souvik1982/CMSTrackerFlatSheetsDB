<?php

  session_start();

  if (isset($_POST["newLocation"]) && isset($_POST[sheetstring]))
  {
    $searchstring = $_POST["searchstring"];
    $sheetstring = $_POST["sheetstring"];
    $newLocation = $_POST["newLocation"];
    $moverId = $_SESSION["userId"];
    $movingTime = date("Y-m-d H:i:s");

    include("dbconnect.php");
    $connection = openConnection();
    $sqlQuery = "UPDATE sheets SET location='".$newLocation."', moverId='".$moverId."', movingTime='".$movingTime."' WHERE sheetstring='".$sheetstring."'";
    if (mysqli_query($connection, $sqlQuery))
    {
      $_SESSION["searchstring"] = $searchstring;
      header("location: searchpart.php");
    }
  }

?>
