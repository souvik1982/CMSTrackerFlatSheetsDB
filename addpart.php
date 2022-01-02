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
    if ($_SESSION["privilege"] == "Editor" || $_SESSION["privilege"] == "Administrator")
    {
      echo " This form allows you to upload datasheets of multiple flat sheets. <br/> \n";
      echo " Please ensure there is a CSV file corresponding to every PDF file. <br/> \n";
      echo " The unique sheet string will be inferred from the filename. <br/> \n";
      echo "<form action='addpart.php' method='post' enctype='multipart/form-data'> \n";
      echo " <b>PDF Datasheets to upload</b> <input type='file' name='PDFToUpload[]' multiple='multiple'> <br/> \n";
      echo " <b>CSV Datasheets to upload</b> <input type='file' name='CSVToUpload[]' multiple='multiple'> <br/> \n";
      echo " <input type='submit' value='Upload Datasheets' name='submit'> \n";
      echo "</form>";
    }
    else echo "<b>WARNING</b>: You are not an Editor and therefore do not have the privilege level needed to add sheets. Please contact database management if you think this is an error.<br/> \n";
    echo "</p> \n";

  }
?>

<?php

  if (isset($_POST["submit"]))
  {
    $N_PDF = count($_FILES["PDFToUpload"]["name"]);
    $N_CSV = count($_FILES["CSVToUpload"]["name"]);

    include("dbconnect.php");
    $connection = openConnection();

    // Iterate over the PDF and CSV lists to find files that correspond to each other
    // This is an expensive N^2/2 operation
    for ($i_PDF = 0; $i_PDF < $N_PDF; $i_PDF++)
    {
      $filename_PDF = $_FILES["PDFToUpload"]["name"][$i_PDF];
      $sheetstring = substr($filename_PDF, 0, strlen($filename_PDF)-4);

      // There ought to be checks on the sheetstring


      // Check if this sheetstring already exists in the database
      // If it does, throw an ERROR and continue to next sheet
      $sqlQuery = "SELECT * from sheets where sheetstring ='".$sheetstring."'";
      $map_output = mysqli_fetch_assoc(mysqli_query($connection, $sqlQuery));
      if ($map_output["sheetstring"] == $sheetstring)
      {
        echo "<b>ERROR</b>: A sheet with identifier ".$sheetstring." had been added on ".$map_output["created_at"]." and therefore cannot be added again. Please contact database management.<br/> \n";
        continue;
      }

      // Check for CSV partner file
      $foundCSV = false;
      for ($i_CSV = 0; $i_CSV < $N_CSV && $foundCSV == false; $i_CSV++)
      {
        $filename_CSV = $_FILES["CSVToUpload"]["name"][$i_CSV];
        if ($sheetstring == substr($filename_CSV, 0, strlen($filename_CSV)-4)) // The filenames match
        {
          $foundCSV = true;
          $storageDirectory = "../CMSTrackerFlatSheetsDB_Data/".$sheetstring."/";

          // Check if storage directory already exists
          // If it does, throw an ERROR and continue to the next sheet
          if (is_dir($storageDirectory))
          {
            echo "<b>ERROR</b>: A folder with name ".$storageDirectory." found but database entry for ".$sheetstring." does not exist. Will not add this sheet to the database. Please contact database management.<br/> \n";
            continue;
          }
          // If you cannot make the storage directory, this could be a disk issue.
          // Throw an ERROR and continue to the next sheet
          if (!mkdir($storageDirectory))
          {
            echo "<b>ERROR</b>: Could not create the folder ".$storageDirectory.". Will not add this sheet to the database. Please contact database management.<br/> \n";
            continue;
          }

          $storageFilename_PDF = $storageDirectory.basename($filename_PDF);
          $storageFilename_CSV = $storageDirectory.basename($filename_CSV);
          // Store both files in folder and add entry to database/
          if (move_uploaded_file($_FILES["PDFToUpload"]["tmp_name"][$i_PDF], $storageFilename_PDF) &&
              move_uploaded_file($_FILES["CSVToUpload"]["tmp_name"][$i_CSV], $storageFilename_CSV))
          {

            // Parse CSV file to extract thickness
            if ($handle_CSV = fopen($storageFilename_CSV, "r"))
            {
              $thickness_mean = 999;
              $thickness_stddev = 999;
              while ($line = fgetcsv($handle_CSV))
              {
                if ($line[0] == "Thickness_Mean") $thickness_mean = floatval($line[1]*1000);
                if ($line[0] == "Thickness_StdDev") {$thickness_stddev = floatval($line[1]*1000); break;}
              }

              if ($thickness_mean != 999 && $thickness_stddev != 999)
              {
                $userId = $_SESSION["userId"];
                $dateTime = date("Y-m-d H:i:s");
                $location = $_SESSION["affiliation"];
                $sqlQuery = "INSERT INTO sheets (sheetstring, folder, created_at, userId, thickness_mean, thickness_stddev, location)
                             VALUES ('".$sheetstring."',
                                     '".$storageDirectory."',
                                     '".$dateTime."',
                                     '".$userId."',
                                     '".$thickness_mean."',
                                     '".$thickness_stddev."',
                                     '".$location."')";
                $output = mysqli_query($connection, $sqlQuery);
                echo "<b>LOG</b>: Flat sheet ".$sheetstring." PDF and CSV uploaded. Database entry created.<br/> \n";
              }
              else
              {
                system("rm -rf ".$storageDirectory);
                echo "<b>ERROR</b>: Mean and standard deviation of sheet thickness of ".$sheetstring." could not be read from its CSV. Will not add this sheet to the database. <br/> \n";
                echo "Please ensure there is a 'Thickness_Mean' field followed by a 'Thickness_StdDev' field in the CSV. <br/> \n";
              }
            }
            else echo "<b>WARNING</b>: Could not open CSV file to extract thickness.</br/> \n";
          }
          else echo "<b>ERROR</b>: Could not store PDF and CSV files in ".$storageDirectory.". Please contact database management.<br/> \n";
        }
      } // For loop over the CSV files
      if ($foundCSV == false) echo "<b>WARNING</b>: Could not find CSV file for ".$sheetstring.". Neither the PDF nor the CSV file will be uploaded. The database will not be populated with this sheet. Please return with the CSV file.<br/> \n";
    } // For loop over the PDF files
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
