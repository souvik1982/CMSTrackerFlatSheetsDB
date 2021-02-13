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
    echo "Your options today are: <br/> \n";
    echo "<ul> \n";
    if ($_SESSION["privilege"] == "Editor") echo "<li><a href='addpart.php'> <b>Add</b> flat sheets to the database </a></li> \n";
    echo "<li><a href='searchpart.php'> <b>Search</b> for and <b>change geographical location of</b> sheets in the database </a></li> \n";
    echo "<li><a href='users.php'> View <b>users</b> of the database </a></li> \n";
    echo "<li><a href='logout.php'> <b>Logout</b> </a></li> \n";
    echo "</ul> \n";
  }
?>

<p align="right">
Author: Souvik Das <br/>
Purdue University, 2021 <br/>
souvik@purdue.edu
</p>

</body>
</html>
