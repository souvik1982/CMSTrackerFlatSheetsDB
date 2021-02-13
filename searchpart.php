<html>
<head>
  <title>CMS Tracker Flat Sheets Database</title>
  <link rel="StyleSheet" type="text/css" href="theme.css"/>
</head>
<body>

<script>
  function showMovingElements(id)
  {
    var id_edit = "edit_" + id;
    var id_dropDown = "dropDown_" + id;
    var id_submit = "submit_" + id;
    var id_cancel = "cancel_" + id;

    document.getElementById(id_edit).style.display = 'none';
    document.getElementById(id_dropDown).style.display = 'block';
    document.getElementById(id_submit).style.display = 'block';
    document.getElementById(id_cancel).style.display = 'block';
  }

  function hideMovingElements(id)
  {
    var id_edit = "edit_" + id;
    var id_dropDown = "dropDown_" + id;
    var id_submit = "submit_" + id;
    var id_cancel = "cancel_" + id;

    document.getElementById(id_edit).style.display = 'block';
    document.getElementById(id_dropDown).style.display = 'none';
    document.getElementById(id_submit).style.display = 'none';
    document.getElementById(id_cancel).style.display = 'none';
  }
</script>

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
    echo "<form action='searchpart.php' method='post' enctype='multipart/form-data'> \n";
    echo " Search by <b>Sheet String</b>: <input type='text' name='searchstring'/> <br/> \n";
    echo "Wildcards allowed: \n";
    echo "<ul> \n";
    echo " <li> <b>%</b> Represents zero or more characters. For example, 'bl%' finds bl, black, blue, and blob. </li> \n";
    echo " <li> <b>_</b> Represents a single character. For example, h_t finds hot, hat, and hit. </li> \n";
    echo " <li> <b>[]</b> Represents any single character within the brackets. For example,	h[oa]t finds hot and hat, but not hit. </li> \n";
    echo " <li> <b>^</b> Represents any character not in the brackets. For example, h[^oa]t finds hit, but not hot and hat. </li> \n";
    echo " <li> <b>-</b> Represents a range of characters. For example,	c[a-b]t finds cat and cbt. </li> \n";
    echo "</ul> \n";
    echo " <input type='submit' value='Search'> \n";
    echo "</form> \n";
    echo "</p> \n";
  }
?>

<?php
  if (isset($_POST["searchstring"]) || isset($_SESSION["searchstring"]))
  {
    if ($_POST["searchstring"] != "" || $_SESSION["searchstring"] != "")
    {
      $searchstring = "%";
      if ($_SESSION["searchstring"] != "") {$searchstring = $_SESSION["searchstring"]; unset($_SESSION["searchstring"]);}
      else $searchstring = $_POST["searchstring"];

      echo "<br/> \n";
      echo "<p> \n";
      echo "<h2> Matching Sheets </h2> \n";
      echo "<table> \n";
      echo " <tr> \n";
      echo "  <th> Sheet String </th> \n";
      echo "  <th> Datasheets Entered by </th> \n";
      echo "  <th> PDF Datasheet </th> \n";
      echo "  <th> CSV Datasheet </th> \n";
      echo "  <th> Thickness (μm) </th> \n";
      echo "  <th> Location </th> \n";
      echo "  <th> Location Modified by </th> \n";
      echo "  <th> Location Modified on </th> \n";
      echo " </tr> \n";

      include("dbconnect.php");
      $connection = openConnection();
      $sqlQuery = "SELECT * FROM sheets WHERE sheetstring LIKE '".$searchstring."'";
      $queryResult = mysqli_query($connection, $sqlQuery);

      while ($map_output = mysqli_fetch_assoc($queryResult))
      {
        $sheetstring = $map_output["sheetstring"];
        $file = $map_output["folder"].$sheetstring;
        $thickness = $map_output["thickness_mean"]." ± ".$map_output["thickness_stddev"];
        $location = $map_output["location"];
        $movingTime = $map_output["movingTime"];

        $userId = $map_output["userId"];
        $sqlQuery_user = "SELECT firstname, lastname, affiliation FROM users WHERE id=".$userId;
        $map_output_user = mysqli_fetch_assoc(mysqli_query($connection, $sqlQuery_user));
        $userInformation = $map_output_user["firstname"]." ".$map_output_user["lastname"].", ".$map_output_user["affiliation"];

        $moverId = $map_output["moverId"];
        $sqlQuery_mover = "SELECT firstname, lastname, affiliation FROM users WHERE id=".$moverId;
        $result_mover = mysqli_query($connection, $sqlQuery_mover);
        $moverInformation = "";
        if ($result_mover)
        {
          $map_output_mover = mysqli_fetch_assoc($result_mover);
          $moverInformation = $map_output_mover["firstname"]." ".$map_output_mover["lastname"].", ".$map_output_mover["affiliation"];
        }

        echo "<tr> \n";
        echo " <td> ".$sheetstring." </td> \n";
        echo " <td> ".$userInformation." </td> \n";
        echo " <td> <a href='".$file.".pdf' target='_blank'>".$sheetstring.".PDF</a> </td> \n";
        echo " <td> <a href='".$file.".csv' target='_blank'>".$sheetstring.".CSV</a> </td> \n";
        echo " <td> ".$thickness." </td> \n";

        // The location can be changed and needs a dropdown menu if it is to be edited
        echo " <td > \n";
        echo $location." \n";
        echo "<button type='button' id='edit_".$sheetstring."' onclick=showMovingElements('".$sheetstring."')>Change Location</button> \n";
        echo "<form action='move.php' method='post' enctype='multipart/form-data'> \n";
        echo "  <select id='dropDown_".$sheetstring."' style='display:none' name='newLocation'> \n";
        echo "   <option value='Purdue University'>Purdue University</option> \n";
        echo "   <option value='Fermilab'>Fermilab</option> \n";
        echo "   <option value='CERN'>CERN</option> \n";
        echo "   <option value='ACP Composites'>ACP Composites</option> \n";
        echo "   <option value='INFN Perugia'>INFN Perugia</option> \n";
        echo "   <option value='INFN Pisa'>INFN Pisa</option> \n";
        echo "  </select> \n";
        echo "  <input type='hidden' name='searchstring' value='".$searchstring."'/>";
        echo "  <input type='hidden' name='sheetstring' value='".$sheetstring."'/>";
        echo "  <input id='submit_".$sheetstring."' style='display:none' type='submit' value='Submit'/> \n";
        echo "</form> \n";
        echo "<button type='button' id='cancel_".$sheetstring."' style='display:none' onclick=hideMovingElements('".$sheetstring."')>Cancel</button> \n";
        echo " </td> \n";

        echo " <td> ".$moverInformation." </td> \n";
        echo " <td> ".$movingTime." </td> \n";
        echo "</tr> \n";
      }

      echo "</table> \n";
    }
    else echo "You must enter a search string. <br/> \n";
  }
?>

<br/><br/>
<a href="main.php">Back</a>
<br/>

<p align="right">
Author: Souvik Das <br/>
Purdue University, 2021 <br/>
souvik@purdue.edu
</p>

</body>
</html>
