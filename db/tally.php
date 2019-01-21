<?php
$dbget = filter_input(INPUT_GET, "db");
$db = empty($dbget) ? "bills" : "property";

// Create connection
$con=mysqli_connect("192.168.1.100:3307","Jeezy","BLiss20106=",$db);
 
// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$meth = $_POST['meth'];
$id = $_POST['id'];
$amount = $_POST['amount'];

if (!empty($meth)) {
  if($meth == "up") {
    if(!empty($amount)) {
      $dateOf = date('Y-m-d');
      $sql = "UPDATE company SET Tally = Tally + '$amount' WHERE ID = $id";
      $sql2 = "INSERT INTO transactions (CompanyID, Amount,TransDate) VALUES ('$id', '$amount', '$dateOf')";
    }
  } else if($meth == "rst") {
    $sql = "UPDATE company SET Tally = '0' WHERE ID = $id";
    $sql2 = "DELETE FROM transactions WHERE CompanyID = '$id'";
  } else if($meth == "rsta") {
    $sql = "UPDATE company SET Tally = '0' WHERE ID > 0";
    $sql2 = "DELETE FROM transactions WHERE ID > '0'";
  }
  
  $result = mysqli_query($con, $sql);
  $result2 = mysqli_query($con, $sql2);
  mysqli_close($con); // Close connections
  
  $src = $_POST['src'];
  if($src == "site") {
    header("location:../");
  }

  if ($result && $result2) {
    echo "Successful.";
  }
}


