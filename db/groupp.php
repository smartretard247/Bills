<?php

// Create connection
$con=mysqli_connect("192.168.1.100:3307","Jeezy","BLiss20106=","property");
 
// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$gid = filter_input(INPUT_GET, "gid");
$total = 0;
$tally = 0;

if (!empty($gid)) {
  $sql = "SELECT SUM(Amount) AS Total FROM company WHERE ID IN (SELECT ID FROM company WHERE GroupID = '$gid')";
  $sql2 = "SELECT Sum(Amount) AS Tally FROM transactions WHERE CompanyID In (SELECT ID FROM company WHERE GroupID = '$gid')";
  
  $result = mysqli_query($con, $sql); // Check if there are results
  $result2 = mysqli_query($con, $sql2);
  if ($result && $result2) {
    $total = $result->fetch_object()->Total;
    $tally = $result2->fetch_object()->Tally;
  }
}

$resultArray = array();
array_push($resultArray, $total);
array_push($resultArray, $tally);
echo json_encode($resultArray);

mysqli_close($con);
