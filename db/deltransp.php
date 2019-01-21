<?php

// Create connection
$con=mysqli_connect("192.168.1.100:3307","Jeezy","BLiss20106=","property");
 
// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$tid = filter_input(INPUT_GET, "tid");
$cid = filter_input(INPUT_GET, "cid");
$amt = filter_input(INPUT_GET, "amt");

if (!empty($tid)) {
  $sql = "DELETE FROM transactions WHERE ID = '$tid'";
  $result = mysqli_query($con, $sql); // Check if there are results
  
  if(!empty($cid) && !empty($amt)) {
    $sql2 = "UPDATE company SET Tally = Tally - '$amt' WHERE ID = '$cid'";
    $result2 = mysqli_query($con, $sql2); // Check if there are results
  }

  if ($result && $result2) {
    echo "Successful.";
  }
}

mysqli_close($con); // Close connections
