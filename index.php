<html>
<head>
  <title>CMS Tracker Flat Sheets Database</title>
  <link rel="StyleSheet" type="text/css" href="theme.css"/>
</head>
<body>

<h1 align="center"> CMS Tracker Flat Sheets Database </h1>

<div class="login">
<h2> User Login </h2>
<form action="index.php" method="post" enctype='multipart/form-data'>
  <b>Username</b><br/>     <input type="text" name="username"> <br/><br/>
  <b>Password</b><br/>     <input type="password" name="userpass"> <br/><br/>
  <input type="submit" value="Sign In">
</form>
Don't have an account? <a href="signup.php">Sign up now.</a><br/>
</div>
<br/>

<?php

  if (isset($_POST["username"]) && isset($_POST["userpass"]))
  {
    $username = $_POST["username"];
    $userpass = $_POST["userpass"];

    $allOkay = true;
    if ($username == "") {echo "<b><center>You need to enter a username.</center></b><br/> \n"; $allOkay = false;}
    if ($userpass == "") {echo "<b><center>You need to enter a password.</center></b><br/> \n"; $allOkay = false;}

    if ($allOkay)
    {
      include("dbconnect.php");
      if ($connection = openConnection();)
      {
        $sqlQuery = "SELECT * FROM users WHERE username='".$username."'";
        $map_output = mysqli_fetch_assoc(mysqli_query($connection, $sqlQuery));

        if (password_verify($userpass, $map_output["userpass"]))
        {
          session_start();
          $_SESSION["loggedin"]    = true;
          $_SESSION["username"]    = $username;
          $_SESSION["userId"]      = $map_output["id"];
          $_SESSION["firstname"]   = $map_output["firstname"];
          $_SESSION["lastname"]    = $map_output["lastname"];
          $_SESSION["affiliation"] = $map_output["affiliation"];
          $_SESSION["privilege"]   = $map_output["privilege"];

          header("location: main.php");
        }
        else echo "<b><center>The password you entered is not valid.</center></b><br/> \n";
      }
      else echo "<b>ERROR</b>: Cannot connect to the mySQL database. Please contact database management. \n";
    }
  }

?>

<p align="right">
Author: Souvik Das <br/>
Purdue University, 2021 <br/>
souvik@purdue.edu
</p>

</body>
</html>
