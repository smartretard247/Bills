<?php
$dbget = filter_input(INPUT_GET, "db");
$db = empty($dbget) ? "bills" : "property";

// Create connection
$con=mysqli_connect("192.168.1.100:3307","Jeezy","BLiss20106=",$db);
 
// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$cid = filter_input(INPUT_GET, "cid");
if(!$cid) {
  $cid = 245; //food
}
$gid = filter_input(INPUT_GET, "gid");
$total = 0;
$tally = 0;
$estimate = 0;

if (!empty($gid)) {
  $sql = "SELECT SUM(Amount) AS Total FROM company WHERE ID IN (SELECT ID FROM company WHERE GroupID = '$gid')";
  $sql2 = "SELECT Sum(Amount) AS Tally FROM transactions WHERE CompanyID In (SELECT ID FROM company WHERE GroupID = '$gid')";
  $sql3 = "SELECT SUM(IF(ID = '$cid', Tally, IF(Tally > Amount, Tally, Amount))) AS Estimate FROM company WHERE ID IN (SELECT ID FROM company WHERE GroupID = '$gid')";
  
  $result = mysqli_query($con, $sql); // Check if there are results
  $result2 = mysqli_query($con, $sql2);
  $result3 = mysqli_query($con, $sql3);
  if ($result && $result2 && $result3) {
    $total = $result->fetch_object()->Total;
    $tally = $result2->fetch_object()->Tally;
    $estimate = $result3->fetch_object()->Estimate;
  }
}

$resultArray = array();
array_push($resultArray, $total);
array_push($resultArray, $tally);
array_push($resultArray, $estimate);
echo json_encode($resultArray);

mysqli_close($con);
