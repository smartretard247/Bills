<?php

// Create connection
$con=mysqli_connect("192.168.1.100:3307","Jeezy","BLiss20106=","bills");
 
// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$monthsOut = filter_input(INPUT_GET, "preview");
if(isset($monthsOut)) {
  $query = "SELECT * FROM company WHERE Currency = 'Cash'";
  $query .= " AND MONTH(Due) = MONTH(DATE_ADD(NOW(), INTERVAL $monthsOut MONTH))";
  $query .= " AND YEAR(Due) = YEAR(DATE_ADD(NOW(), INTERVAL $monthsOut MONTH))";
  $query .= " UNION ";
  $query .= "SELECT * FROM company WHERE Currency = 'Cash'";
  $query .= " AND MONTH(DATE_ADD(Due, INTERVAL $monthsOut MONTH)) = MONTH(DATE_ADD(NOW(), INTERVAL $monthsOut MONTH))";
  $query .= " AND YEAR(DATE_ADD(Due, INTERVAL $monthsOut MONTH)) = YEAR(DATE_ADD(NOW(), INTERVAL $monthsOut MONTH)) ORDER BY NAME";
} else {
  $query = "SELECT * FROM company WHERE Currency = 'Cash' AND MONTH(DATE(Due)) = MONTH(NOW()) AND YEAR(DATE(Due)) = YEAR(NOW()) ORDER BY NAME";
}
 
// Check if there are results
$result = mysqli_query($con, $query);
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
 
// Close connections
mysqli_close($con);
