<html>
<head>
  <title>CMS Tracker Flat Sheets Database</title>
  <link rel="StyleSheet" type="text/css" href="theme.css"/>
</head>
<body>

<h1 align="center"> CMS Tracker Flat Sheets Database </h1>

<?php
  session_start();
  if ($_SESSION["username"] != "")
  {
    echo "Current User: ".$_SESSION["firstname"]." ".$_SESSION["lastname"]."<br/> \n";
    echo "Affiliation: ".$_SESSION["affiliation"]."<br/> \n";
    echo "Privilege: ".$_SESSION["privilege"]."<br/> \n";

    echo "<br/> \n";
    echo "<p> \n";
    echo "<h2> Users of the database </h2> \n";
    echo "<table> \n";
    echo " <tr> \n";
    echo "  <th> Name </th> \n";
    echo "  <th> Affiliation </th> \n";
    echo "  <th> Privilege </th> \n";
    echo "  <th> Email </th> \n";
    echo " </tr> \n";

    include("dbconnect.php");
    $connection = openConnection();
    $sqlQuery = "SELECT firstname, lastname, affiliation, privilege, email FROM users";
    $queryResult = mysqli_query($connection, $sqlQuery);
    while ($map_output = mysqli_fetch_assoc($queryResult))
    {
      echo "<tr> \n";
      echo " <td> ".$map_output["firstname"]." ".$map_output["lastname"]." </td> \n";
      echo " <td> ".$map_output["affiliation"]." </td> \n";
      echo " <td> ".$map_output["privilege"]." </td> \n";
      echo " <td> ".$map_output["email"]." </td> \n";
      echo "</tr> \n";
    }

    echo "</table> \n";
  }
?>

<br/>
<a href="main.php">Back</a>
<br/>

<p align="right">
Author: Souvik Das <br/>
Purdue University, 2021 <br/>
souvik@purdue.edu
</p>

</body>
</html>
