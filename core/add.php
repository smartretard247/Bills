<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    if($_SESSION['db'] == 'Bills') {
      include_once $_SESSION['rootDir'] . '../../database.php'; $db = new Database('bills');
    } else {
      include_once $_SESSION['rootDir'] . '../../database.php'; $db = new Database('property');
    }
          
    include_once $_SESSION['rootDir'] . 'company.php'; $bill = new Company();
    
    $_SESSION['error_message'] = '';

    $amount = filter_input(INPUT_POST, 'Amount');
    
    if($amount < 0) {
      $bill->SetName("#Transfer In");
      $bill->SetType("Transfer In");
    } else {
      $bill->SetName("#Transfer Out");
      $bill->SetType("Transfer Out");
    }
    
    $bill->SetAmountDue($amount);
    $bill->SetLink("http://www.chase.com");
    $bill->SetRecurring(0);
    $bill->SetFrequency(6);
    $bill->SetCurrency("Debit");

    $bill->AddToDB();
    
    header("location:../");
    exit();
