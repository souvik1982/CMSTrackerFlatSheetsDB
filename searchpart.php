<html>
<head>
  <title>CMS Tracker Flat Sheets Database</title>
  <link rel="StyleSheet" type="text/css" href="theme.css"/>
</head>
<body>

<script src="showHideElements.js"></script>

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
    echo " Search by <b>Sheet String</b>: <input type='text' name='searchstring' value='%'/> <br/> \n";
    echo "Wildcards allowed: \n";
    echo "<ul> \n";
    echo " <li> <b>%</b> Represents zero or more characters. For example, 'bl%' finds bl, black, blue, and blob. </li> \n";
    echo " <li> <b>_</b> Represents a single character. For example, h_t finds hot, hat, and hit. </li> \n";
    echo " <li> <b>[]</b> Represents any single character within the brackets. For example,	h[oa]t finds hot and hat, but not hit. </li> \n";
    echo " <li> <b>^</b> Represents any character not in the brackets. For example, h[^oa]t finds hit, but not hot and hat. </li> \n";
    echo " <li> <b>-</b> Represents a range of characters. For example,	c[a-b]t finds cat and cbt. </li> \n";
    echo "</ul> \n";
    echo " AND <b>Current Location</b>: \n";
    echo " <select name='location'> \n";
    echo "  <option value='Any'>Any</option> \n";
    echo "  <option value='Purdue University'>Purdue University</option> \n";
    echo "  <option value='Fermilab'>Fermilab</option> \n";
    echo "  <option value='CERN'>CERN</option> \n";
    echo "  <option value='ACP Composites'>ACP Composites</option> \n";
    echo "  <option value='INFN Perugia'>INFN Perugia</option> \n";
    echo "  <option value='INFN Pisa'>INFN Pisa</option> \n";
    echo " </select> <br/><br/> \n";
    echo " AND <b>Thickness Mean Range</b>: <input type='text' name='thicknessMean_lo' value='0'/> to <input type='text' name='thicknessMean_hi'  value='1000'/> μm <br/><br/> \n";
    echo " AND <b>Thickness Standard Deviation Range</b>: <input type='text' name='thicknessStdDev_lo' value='0'/> to <input type='text' name='thicknessStdDev_hi' value='1000'/> μm <br/><br/> \n";
    echo " <input type='submit' value='Search'> \n";
    echo "</form> \n";
    echo "</p> \n";
  }
?>

<?php
  $goodArguments = false;
  if (isset($_SESSION["searchstring"]))
  {
    if ($_SESSION["searchstring"] != "")
    {
      $searchstring       = $_SESSION["searchstring"];       unset($_SESSION["searchstring"]);
      $searchLocation     = $_SESSION["location"];           unset($_SESSION["location"]);
      $thicknessMean_lo   = $_SESSION["thicknessMean_lo"];   unset($_SESSION["thicknessMean_lo"]);
      $thicknessMean_hi   = $_SESSION["thicknessMean_hi"];   unset($_SESSION["thicknessMean_hi"]);
      $thicknessStdDev_lo = $_SESSION["thicknessStdDev_lo"]; unset($_SESSION["thicknessStdDev_lo"]);
      $thicknessStdDev_hi = $_SESSION["thicknessStdDev_hi"]; unset($_SESSION["thicknessStdDev_hi"]);
      $goodArguments = true;
    }
  }
  else if (isset($_POST["searchstring"]))
  {
    if ($_POST["searchstring"] != "")
    {
      $searchstring       = $_POST["searchstring"];
      $searchLocation     = $_POST["location"];
      $thicknessMean_lo   = $_POST["thicknessMean_lo"];
      $thicknessMean_hi   = $_POST["thicknessMean_hi"];
      $thicknessStdDev_lo = $_POST["thicknessStdDev_lo"];
      $thicknessStdDev_hi = $_POST["thicknessStdDev_hi"];
      $goodArguments = true;
    }
    else echo "You must enter a search string. <br/> \n";
  }

  if ($goodArguments)
  {
    if ($searchLocation == "Any") $searchLocation = "%";

    echo "<br/> \n";
    echo "<p> \n";
    echo "<h2> Matching Sheets </h2> \n";
    echo "<table> \n";
    echo " <tr> \n";
    echo "  <th> # </th> \n";
    echo "  <th> Sheet String </th> \n";
    echo "  <th> Datasheets Entered by </th> \n";
    echo "  <th> PDF Datasheet </th> \n";
    echo "  <th> CSV Datasheet </th> \n";
    echo "  <th> Thickness Mean <br/> (μm) </th> \n";
    echo "  <th> Thickness Std Dev <br/> (μm) </th> \n";
    echo "  <th> Location </th> \n";
    echo "  <th> Location <br/> Modified by </th> \n";
    echo "  <th> Location <br/> Modified on </th> \n";
    echo " </tr> \n";

    include("dbconnect.php");
    $connection = openConnection();
    $sqlQuery = "SELECT * FROM sheets WHERE sheetstring LIKE '".$searchstring."' AND
                                            location LIKE '".$searchLocation."' AND
                                            thickness_mean >= '".$thicknessMean_lo."' AND
                                            thickness_mean <= '".$thicknessMean_hi."' AND
                                            thickness_stddev >= '".$thicknessStdDev_lo."' AND
                                            thickness_stddev <= '".$thicknessStdDev_hi."'";
    $queryResult = mysqli_query($connection, $sqlQuery);

    $itemNumber = 0;
    while ($map_output = mysqli_fetch_assoc($queryResult))
    {
      ++$itemNumber;

      $sheetstring = $map_output["sheetstring"];
      $file = $map_output["folder"].$sheetstring;
      $thickness_mean = $map_output["thickness_mean"];
      $thickness_stddev = $map_output["thickness_stddev"];
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
      echo " <td> ".$itemNumber." </td> \n";
      echo " <td> ".$sheetstring." </td> \n";
      echo " <td> ".$userInformation." </td> \n";
      echo " <td> <a href='".$file.".pdf' target='_blank'>".$sheetstring.".PDF</a> </td> \n";
      echo " <td> <a href='".$file.".csv' target='_blank'>".$sheetstring.".CSV</a> </td> \n";
      echo " <td> ".$thickness_mean." </td> \n";
      echo " <td> ".$thickness_stddev." </td> \n";

      echo " <td > \n";
      echo $location." \n";
      // The location can be changed if the privilege is Editor
      if ($_SESSION["privilege"] == "Editor")
      {
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
        echo "  <input type='hidden' name='searchLocation' value='".$searchLocation."'/>";
        echo "  <input type='hidden' name='thicknessMean_lo' value='".$thicknessMean_lo."'/>";
        echo "  <input type='hidden' name='thicknessMean_hi' value='".$thicknessMean_hi."'/>";
        echo "  <input type='hidden' name='thicknessStdDev_lo' value='".$thicknessStdDev_lo."'/>";
        echo "  <input type='hidden' name='thicknessStdDev_hi' value='".$thicknessStdDev_hi."'/>";
        echo "  <input type='hidden' name='sheetstring' value='".$sheetstring."'/>";
        echo "  <input id='submit_".$sheetstring."' style='display:none' type='submit' value='Submit'/> \n";
        echo "</form> \n";
        echo "<button type='button' id='cancel_".$sheetstring."' style='display:none' onclick=hideMovingElements('".$sheetstring."')>Cancel</button> \n";
      }
      echo " </td> \n";

      echo " <td> ".$moverInformation." </td> \n";
      echo " <td> ".$movingTime." </td> \n";
      echo "</tr> \n";
    }

    echo "</table> \n";
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
