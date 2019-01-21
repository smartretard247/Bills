<?php

// Create connection
$con=mysqli_connect("192.168.1.100:3307","Jeezy","BLiss20106=","property");
 
// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$id = $_POST['id'];
$amount = $_POST['amount'];

if($id && $amount) {
  $sql = "UPDATE company SET Amount = '$amount' WHERE ID = $id";
  $result = mysqli_query($con, $sql);

  if ($result) {
    echo "Successful.";
  }
}

// Close connections
mysqli_close($con);