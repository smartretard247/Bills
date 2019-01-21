<?php
$dbget = filter_input(INPUT_GET, "db");
$db = empty($dbget) ? "bills" : "property";

// Create connection
$con=mysqli_connect("192.168.1.100:3307","Jeezy","BLiss20106=",$db);
 
// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$src = $_POST['src'];
if($src == "site") {
  $cid = filter_input(INPUT_POST, "cid");
  $options = explode("|", filter_input(INPUT_POST, "amt"));
  $tid = $options[1];
  $amt = $options[0];
} else {
  $cid = filter_input(INPUT_GET, "cid");
  $tid = filter_input(INPUT_GET, "tid");
  $amt = filter_input(INPUT_GET, "amt");
}

if (!empty($tid)) {
  $sql = "DELETE FROM transactions WHERE ID = '$tid'";
  $result = mysqli_query($con, $sql); // Check if there are results
  
  if(!empty($cid) && !empty($amt)) {
    $sql2 = "UPDATE company SET Tally = Tally - '$amt' WHERE ID = '$cid'";
    $result2 = mysqli_query($con, $sql2); // Check if there are results
  }
  
  mysqli_close($con); // Close connections
  
  if($src == "site") {
    header("location:../");
  }

  if ($result && $result2) {
    echo "Successful.";
  }
}
