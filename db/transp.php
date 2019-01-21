<?php

// Create connection
$con=mysqli_connect("192.168.1.100:3307","Jeezy","BLiss20106=","property");
 
// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$cid = filter_input(INPUT_GET, "cid");

if (!empty($cid)) {
  $sql = "SELECT * FROM transactions WHERE CompanyID = '$cid' ORDER BY ID DESC";
  $result = mysqli_query($con, $sql); // Check if there are results
  if ($result) {
    // If so, then create a results array and a temporary one
    // to hold the data
    $resultArray = array();
    $tempArray = array();

    // Loop through each row in the result set
    while($row = $result->fetch_object()) {
        // Add each row into our results array
        $tempArray = $row;
        array_push($resultArray, $tempArray);
    }

    // Finally, encode the array to JSON and output the results
    echo json_encode($resultArray);
  }
}
 
// Close connections
mysqli_close($con);
