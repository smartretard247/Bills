<?php

// Create connection
$con=mysqli_connect("192.168.1.100:3307","Jeezy","BLiss20106=","property");
 
// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$meth = $_POST['meth'];
$id = $_POST['id'];
$paid = $_POST['paid'];
$gid = $_POST['gid'];

if (!empty($meth)) {
  if($meth == "up") {
    if($gid > 0) {
      $sql = "UPDATE company SET Processed = '$paid' WHERE GroupID = $gid";
    } else {
      $sql = "UPDATE company SET Processed = '$paid' WHERE ID = $id";
    }
    $result = mysqli_query($con, $sql);
    if ($result) {
      echo "Successful.";
    }
  }  
}

// Close connections
mysqli_close($con);