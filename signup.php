<html>
<head>
  <title>CMS Tracker Flat Sheets Database</title>
  <link rel="StyleSheet" type="text/css" href="theme.css"/>
</head>
<body>

<h1 align="center"> CMS Tracker Flat Sheets Database </h1>

<div class="login">
<h2> User Registration </h2>
<form action="signup.php" method="post" enctype='multipart/form-data'>
  <b>First Name</b><br/>        <input type="text" name="firstname"> <br/><br/>
  <b>Last Name</b><br/>         <input type="text" name="lastname"> <br/><br/>
  <b>Privilege</b><br/>
    <select name="privilege">
      <option value="Viewer">Viewer</option>
      <option value="Editor">Editor</option>
    </select><br/><br/>
  <b>Affiliation</b><br/>
    <select name="affiliation">
      <option value="Purdue University">Purdue University</option>
      <option value="Fermilab">Fermilab</option>
      <option value="CERN">CERN</option>
      <option value="ACP Composites">ACP Composites</option>
      <option value="INFN Perugia">INFN Perugia</option>
      <option value="INFN Pisa">INFN Pisa</option>
    </select><br/><br/>
  <b>Email</b><br/>             <input type="text" name="email"> <br/><br/>
  <b>Username</b><br/>          <input type="text" name="username"> <br/><br/>
  <b>Password</b><br/>          <input type="password" name="userpass"> <br/><br/>
  <b>Confirm Password</b><br/>  <input type="password" name="confirmpass"> <br/><br/>
  <input type="submit" value="Sign Up"/>
</form>
Already have an account? <a href="index.php"> Login here.</a>
</div>
<br/>

<?php
  if (isset($_POST["firstname"]) &&
      isset($_POST["lastname"]) &&
      isset($_POST["affiliation"]) &&
      isset($_POST["privilege"]) &&
      isset($_POST["email"]) &&
      isset($_POST["username"]) &&
      isset($_POST["userpass"]) &&
      isset($_POST["confirmpass"]))
  {
    $firstname   = $_POST["firstname"];
    $lastname    = $_POST["lastname"];
    $affiliation = $_POST["affiliation"];
    $privilege   = $_POST["privilege"];
    $email       = $_POST["email"];
    $username    = $_POST["username"];
    $userpass    = $_POST["userpass"];
    $confirmpass = $_POST["confirmpass"];

    $allOkay = true;
    if ($firstname == "")          {echo "<b><center>You need to enter a first name.</center></b><br/>"; $allOkay = false;}
    if ($lastname == "")           {echo "<b><center>You need to enter a last name.</center></b><br/>"; $allOkay = false;}
    if ($affiliation == "")        {echo "<b><center>You need to enter an affiliation.</center></b><br/>"; $allOkay = false;}
    if ($privilege == "")          {echo "<b><center>You need to enter a privilege level.</center></b><br/>"; $allOkay = false;}
    if ($email == "")              {echo "<b><center>You need to enter an email.</center></b><br/>"; $allOkay = false;}
    if ($username == "")           {echo "<b><center>You need to enter a username.</center></b><br/>"; $allOkay = false;}
    if ($userpass == "")           {echo "<b><center>You need to enter a password.</center></b><br/>"; $allOkay = false;}
    if ($confirmpass != $userpass) {echo "<b><center>Passwords do not match.</center></b><br/>"; $allOkay = false;}

    if ($allOkay)
    {
      include("dbconnect.php");
      $connection = openConnection();
      $sqlQuery = "SELECT username FROM users WHERE username='".$username."'";
      $output = mysqli_query($connection, $sqlQuery);
      if (mysqli_num_rows($output) == 0)
      {
        $userpass_hashed = password_hash($userpass, PASSWORD_DEFAULT);
        $sqlQuery = "INSERT INTO users (firstname, lastname, affiliation, privilege, email, username, userpass)
                     VALUES (\"".$firstname."\",
                             \"".$lastname."\",
                             \"".$affiliation."\",
                             \"".$privilege."\",
                             \"".$email."\",
                             \"".$username."\",
                             \"".$userpass_hashed."\")";
        if (mysqli_query($connection, $sqlQuery)) header("location: index.php");
        else echo "<center><b>ERROR</b>: There has been an error communicating with the mySQL database.</center><br/> \n";
      }
      else echo "<b><center>The username is taken. Please try something else.</center></b><br/> \n";
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
