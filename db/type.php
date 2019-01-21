<?php
$dbget = filter_input(INPUT_GET, "db");
$db = empty($dbget) ? "bills" : "property";

// Create connection
$con=mysqli_connect("192.168.1.100:3307","Jeezy","BLiss20106=",$db);
 
// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$type = filter_input(INPUT_GET, "type");
$total = 0;

if (!empty($type)) {
  $sql = "SELECT SUM(Amount) AS Total FROM company WHERE Type = '$type'";
  
  $result = mysqli_query($con, $sql); // Check if there are results
  if ($result) {
    $total = $result->fetch_object()->Total;
  }
}

$resultArray = array();
array_push($resultArray, $total);
echo json_encode($resultArray);

mysqli_close($con);
