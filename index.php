<?php #$root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    $lifetime = 60 * 60 * 24; //24 hours.
    ini_set('session.use_only_cookies', true);
    ini_set('session.gc_probability', 1);
    ini_set('session.gc_divisor', 100);
    session_set_cookie_params($lifetime, '/'); //all paths, must be called before session_start()
    session_save_path(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/sessions'); session_start();
    
    #date_default_timezone_set('America/New_York');
    date_default_timezone_set('Japan');
    
    #$_SESSION['rootDir'] = "/";
    $_SESSION['rootDir'] = "";
    require_once $_SESSION['rootDir'] . 'core/include.php';
        
    if(empty($_SESSION['admin_enabled'])) { $_SESSION['admin_enabled'] = false; }
    if(empty($_SESSION['debug'])) { $_SESSION['debug'] = false; }
    if(empty($_SESSION['error_message'])) { $_SESSION['error_message'] = ''; }
    if(empty($_SESSION['HideRecurring'])) { $_SESSION['HideRecurring'] = 0; }
    if(empty($_SESSION['TempUnlockDay'])) { $_SESSION['TempUnlockDay'] = 12; }
    if(empty($_SESSION['ManualUnlock'])) { $_SESSION['ManualUnlock'] = 0; }
    if(empty($_SESSION['RemoveCashTotal']) && empty($_POST)) { $_SESSION['RemoveCashTotal'] = 1; }

    $hideRecurring = filter_input(INPUT_POST, 'HideRecurring');
    if(isset($hideRecurring)) {
        if($hideRecurring == 0) { $_SESSION['HideRecurring'] = 0; }
        else { $_SESSION['HideRecurring'] = 1; }
    }
    $manualUnlock = filter_input(INPUT_POST, 'ManualUnlock');
    if(isset($manualUnlock)) {
        if($manualUnlock == 0) {
            $_SESSION['ManualUnlock'] = 0;
            $_SESSION['TempUnlockDay'] = 12;
        } else {
            $_SESSION['ManualUnlock'] = 1;
            $_SESSION['TempUnlockDay'] = date('j');
        }
    }
    $rct = filter_input(INPUT_POST, 'RemoveCashTotal');
    if(isset($rct)) {
        if($rct == 1) { $_SESSION['RemoveCashTotal'] = 1; }
        else { $_SESSION['RemoveCashTotal'] = 0; }
    }
    
    if($_SESSION['error_message'] != '') {
        DisplayMessage($_SESSION['error_message'], true); //display error message
    }

    $action = filter_input(INPUT_POST, 'action');
    if(!$action) { $action = filter_input(INPUT_GET, 'action'); }
    if(!$action) { $action = 'default'; }
    
    $todaysDate = date(Company::GetDateFormat());
    $lockedDate = date_parse_from_format(Company::GetDateFormat(), $bill->GetDateLocked());
    $billWillUnlockDate = $lockedDate;
    
    //if($_SESSION['db'] == 'Bills') {
        if($billWillUnlockDate['month'] == 12) {
            $billWillUnlockDate['month'] = 0;
            $billWillUnlockDate['year'] += 1;
        }
    
        $billWillUnlockDate['month'] += 1;
        $billWillUnlockDate['day'] = $_SESSION['TempUnlockDay']; //change back if falls on a weekend, was 16
    //} else {
        //do not lock property website
    //}

    //create date string for the 12th of the month, and set temporary unlock for the 1st (MUST be on the first because previous month has to be correct!)
    $formattedDateString = $billWillUnlockDate['year'] . "-" . $billWillUnlockDate['month'] . "-" . $billWillUnlockDate['day'];
    $unlockDate = date(Company::GetDateFormat(), strtotime($formattedDateString)); //convert $unlockDate into Date() object
    $firstPayday = date_parse_from_format(Company::GetDateFormat(), $unlockDate);
    $paydayFormat = $firstPayday['year'] . "-" . $firstPayday['month'] . "-" . 1;
    $tempUnlockDate = date(Company::GetDateFormat(), strtotime($paydayFormat));
    
    if($bill->IsLocked()) {
        /* now handled in ResetBills.ps1
        //on the first of the month reset all bills, for both db's
        if($todaysDate == $tempUnlockDate && empty($_SESSION['Reset'])) {
            $_SESSION['Reset'] = true;
            
            //redirect to reset.php
            header("Location: core/reset.php");
            die();
        }
        */
        //check current date and unlock if necessary
        if($todaysDate >= $unlockDate || $todaysDate == $tempUnlockDate) {
            $bill->SetLocked(0); //unlock
        }
    } else if($todaysDate < $unlockDate && $todaysDate != $tempUnlockDate) {
        $bill->SetLocked(1);
    }
    
    //create string to say when function will come back
    $strWillUnlockDate = "Full functionality will return temporarily on " . date('F jS', strtotime($paydayFormat));
    $strWillUnlockDate .= ", and again as of ";
    $strWillUnlockDate .= date('l', strtotime($formattedDateString)) . ", the " . $billWillUnlockDate['day'] . "th of " . 
            date('F', strtotime($formattedDateString)) . ", " . $billWillUnlockDate['year'] . ".";

    include_once 'view/header.php'; //begin HTML
    
    if($_SESSION['valid_user']) : //begin section requring login
    
    //perform necessary action, sent by forms
    switch($action) {
        case 'group':
          $id = filter_input(INPUT_POST, 'ID');
          $gid = filter_input(INPUT_POST, 'GroupID');
          if(isset($id) && isset($gid)) {
            $bill->SetFromDB($id);
            $bill->SetGroupId($gid);
            $name = $bill->GetName();
                
            if($bill->UpdateDB()) {
                DisplayMessage("Updated GroupID for $name.");
            } else {
                DisplayMessage("Could not update GroupID for $name.", true);
            }
          } else {
            DisplayMessage('ID and/or GroupID not set.', true);
          }
          include 'view/home.php';
          break;
        case 'paidAnnualBill':
            $billid = filter_input(INPUT_GET, 'billid');
            if(isset($billid)) {
                $bill->SetFromDB($billid);
                
                if($bill->IsDueThisMonth()) {
                    $bill->SetPostDay(date(Company::GetDateFormat()));
                    $due = date_create($bill->GetDueDate());
                    $timeToAdd = ($bill->IsBimestrial()) ? '2 months' : '1 year';
                    if($bill->IsBiennial()) { $timeToAdd = '2 years'; }
                    if($bill->IsBiannual()) { $timeToAdd = '6 months'; }
                    date_add($due, date_interval_create_from_date_string($timeToAdd));
                    $bill->SetDueDate(date_format($due, Company::GetDateFormat()));
                    if($bill->IsAnnual()) {
                        $bill->ResetAllMonths();
                    } else {
                        $bill->SetMonth(date("m"), $bill->GetAmountDue()); //set the amount that was due for current month
                    }

                    if($bill->UpdateDB()) {
                        DisplayMessage('Successfully applied payment to account.');
                    } else {
                        DisplayMessage('Failed to pay off.', true);
                    }
                } else {
                    DisplayMessage('Bill is not due till next year.', true);
                }
            }
            
            include 'view/home.php';
            break;
        case 'change_exch':
            $newExchangeRate = filter_input(INPUT_POST, 'new_exch');
            if(isset($newExchangeRate)) { Company::SetExchangeRateDB($newExchangeRate); }
            include 'view/home.php';
            break;
        case 'change_allt':
            $newCashAllt = 0;
            if(isset($_POST['new_cash_allt'])) { $newCashAllt = $_POST['new_cash_allt']; }
            if(isset($_POST['new_allt'])) { $bill->SetAllotment($_POST['new_allt'], $newCashAllt); }
            include 'view/home.php';
            break;
        case 'company_add':
            if(isset($_POST['pending_add'])) {
                //code for adding a company
                if(isset($_POST['Name'])) {
                    $name = filter_input(INPUT_POST, 'Name');
                    $bill->SetName($name);

                    if($bill->IsValidName()) {
                        $bill->SetAmountDue($_POST['Amount']);
                        
                        if(isset($_POST['Type'])) {
                          $bill->SetType($_POST['Type']);
                        } else {
                          $bill->SetType("Other");
                        }
                        
                        $link = filter_input(INPUT_POST, 'Link');
                        if($link == '') {
                            $name = str_replace('#', '', $name);
                            $link = "https://www.google.com/webhp?gws_rd=ssl#q=$name";
                        }
                        $bill->SetLink($link);
                        
                        $bill->SetRecurring($_POST['Recur']);
                        $bill->SetFrequency($_POST['Frequency']);
                        if($_POST['Frequency'] == 7) { //biannual
                          $bill->SetRecurring(1);
                          //$bill->SetAnnual(1);
                        }
                        $bill->SetCurrency($_POST['Currency']);

                        DisplayMessage($bill->AddToDB() . " Company added.");
                    } else {
                        DisplayMessage("Name entered was invalid.", true);
                    }

                    include 'view/home.php';
                }
            } else {
                include 'view/company_add.php';
            } break;
        case 'company_remove':
            if(isset($_POST['select_all'])) { $select_all = true; }
            if(isset($_POST['select_none'])) { $select_all = false; }

            if(isset($_POST['pending_removal'])) {
                $bill_amount = 0;
                $bill_success = 0;

                $removenext = false;
                foreach($_POST as $tpost) {
                    if($removenext) {
                        $bills = $db->GetTable('company', 'Name');
                        foreach($bills as $tbills) {
                            if($tpost == $tbills['ID']) {
                                $bill->SetFromDB($tbills['ID']);
                                $bill_success += $bill->AddToDB('company_hist', $tbills['ID']);
                                if($bill_success) {
                                    $bill_success += $bill->UpdateDB('company_hist');
                                    if($bill_success == 2) {
                                        $bill_success += $bill->RemoveFromDB();
                                    } else {
                                        $bill->RemoveFromDB('company_hist');
                                    }
                                }

                                switch($bill_success) {
                                    case 0: DisplayMessage("Error moving Company to history.", true); break;
                                    case 1: DisplayMessage("Could not update database fields in history, removed from historical table.", true); break;
                                    case 2: DisplayMessage("Error removing Company, manually remove from database.", true); break;
                                    case 3: ++$bill_amount; break;
                                }

                                $bill_success = 0;
                            }
                        }
                    }

                    if($tpost == 'on') { $removenext = true; } else { $removenext = false; }
                }

                DisplayMessage($bill_amount . " Company(ies) removed.");

                include 'view/home.php';
            } else {
            include 'view/company_remove.php';
            } break;
        case 'company_delete':
            $success = 0;
            if(isset($_POST['conf_delete'])) {
                if(isset($_POST['ID'])) {
                    $success = $bill->RemoveFromDB('company_hist', $_POST['ID']); //delete
                }

                if($success) {
                    DisplayMessage("1 company permanently deleted.");
                } else {
                   DisplayMessage("Error deleting company.", true);
                }

                include 'view/home.php';
            } else {
                include 'view/sure.php';
            }

            break;
        case 'company_edit':
            $id = filter_input(INPUT_POST, 'ID');
            if($id) {
                $bill->SetFromDB($id);
                
                $pending_update = filter_input(INPUT_POST, 'pending_update');
                if($pending_update) { //update company
                    $name = filter_input(INPUT_POST, 'Name');
                    $amount = str_replace(",", "", filter_input(INPUT_POST, 'Amount'));
                    $type = filter_input(INPUT_POST, 'Type');
                    $link = filter_input(INPUT_POST, 'Link');
                    $annual = filter_input(INPUT_POST, 'Annual');
                    $recur = filter_input(INPUT_POST, 'Recur');
                    $freq = filter_input(INPUT_POST, 'Frequency');
                    $due = filter_input(INPUT_POST, 'Due');
                    $currency = filter_input(INPUT_POST, 'Currency');
                    $phone = filter_input(INPUT_POST, 'Phone');
                    $notes = filter_input(INPUT_POST, 'Notes');
                    
                    if(isset($phone)) {
                      $bill->SetPhone($phone);
                    }
                    
                    if(isset($notes)) {
                      $bill->SetNotes($notes);
                    }
                    
                    $bill->SetName($name);
                    $bill->SetAmountDue($amount);
                    $bill->SetType($type);
                    $bill->SetLink($link);
                    $bill->SetFrequency($freq);
                    $bill->SetCurrency($currency);
                    
                    if($bill->IsAnnual()) { //bill was already annual
                        $bill->SetAnnual($annual);
                        $bill->SetDueDate($due);
                        $oneOrTwo = ($bill->IsBiennial()) ? 2 : 1;
                        
                        if($bill->IsBiannual()) {
                          $post = date_create($bill->GetPostDay());
                          date_add($post, date_interval_create_from_date_string('6 months'));
                          $bill->SetPostDay(date_format($post, Company::GetDateFormat()));
                        } else {
                          $dueMinusAYear = $bill->GetYearDue()-$oneOrTwo . "-" . $bill->GetMonthDue() . "-" . $bill->GetDayDue();
                          $bill->SetPostDay($dueMinusAYear);
                        }
                        
                        $dateLocked = date_create(Company::GetDateLocked());
                        $startDate = date_create($bill->GetYearMonthPaid());
                        date_add($startDate, date_interval_create_from_date_string('1 month'));
                        
                        for($i = 1; $i <= 12; $i++) {
                            if($startDate <= $dateLocked) {
                                $bill->SetMonth(date_format($startDate, 'n'), $bill->GetYenDue());
                                date_add($startDate, date_interval_create_from_date_string('1 month'));
                            }
                        }
                        
                    } else if($annual && !$bill->IsBimestrial()) { //bill wasn't annual, so set it now
                        $monthlyFee = number_format($bill->GetYenDue()/12, $decimalPlaces);
                        if($bill->IsBiennial()) {
                            //$monthlyFee -= 0.01;
                            $monthlyFee /= 2; //halve payment for semi annual
                        } else if($bill->IsBiannual()) {
                            $monthlyFee = number_format($bill->GetYenDue()/6, $decimalPlaces);
                        }
                        $bill->SetAmountDue($monthlyFee);
                        $bill->SetRecurring(1); //if annual, must be recurring
                        $bill->SetAnnual(1);

                        //get dates to check
                        $due = date_create($bill->GetDueDate());
                        
                        //save today as post day if not already set
                        if($due == '2000-01-01') {
                            $bill->SetPostDay(date(Company::GetDateFormat()));
                            
                            if($bill->IsBiannual()) {
                                date_add($due, date_interval_create_from_date_string('6 months'));
                            } else if($bill->IsBiennial()) {
                                date_add($due, date_interval_create_from_date_string('2 years'));
                            } else { //update year due
                                date_add($due, date_interval_create_from_date_string('1 year'));
                            }
                            
                            $bill->SetDueDate(date_format($due, Company::GetDateFormat()));
                        }
                        
                        //from start date until date locked, correct the amount 'saved' each month (same as above but problem fixed)
                        $dateLocked = date_create(Company::GetDateLocked());
                        $startDate = date_create($bill->GetYearMonthPaid());
                        date_add($startDate, date_interval_create_from_date_string('1 month'));
                        
                        for($i = 1; $i <= 12; $i++) {
                            if($startDate <= $dateLocked) {
                                $bill->SetMonth(date_format($startDate, 'n'), $bill->GetYenDue());
                                date_add($startDate, date_interval_create_from_date_string('1 month'));
                            }
                        }
                        
                    } else {
                        if(!$bill->IsBimestrial()) { $bill->SetAnnual($annual); }
                        $bill->SetRecurring($recur);
                    }

                    DisplayMessage($bill->UpdateDB() . " Company edited successfully.");

                    include 'view/company_show.php';
                } else { //goto company_edit
                    include 'view/company_edit.php';
                }
            } else {
                DisplayMessage("Error retrieving Company.", true);
            }
            break;
        case 'company_show': //code to view the bills roster
            include 'view/company_show.php';
            include 'view/menu.php';
            break;
        case 'company_restore': //code to restore a company
            $id = filter_input(INPUT_POST, 'ID');
            if($id) {
                $bill->SetFromDB($id, 'company_hist');
                $restoreSuccess = $bill->AddToDB('company', $id);
                if($restoreSuccess) {
                    $restoreSuccess += $bill->UpdateDB('company');
                    if($restoreSuccess == 2) {
                        $restoreSuccess += $bill->RemoveFromDB('company_hist', $id); //remove from hist
                    }
                }

                switch($restoreSuccess) {
                    case 0: DisplayMessage("Error adding Company.", true); break;
                    case 1: DisplayMessage("Error updating Company.", true); break;
                    case 2: DisplayMessage("Error removing Company from history.", true); break;
                    case 3: DisplayMessage("1 Company restored successfully."); break;
                }

                include 'view/home.php';
            } else {
                include 'view/company_restore.php';
            }
			
            break;
        case 'processed': //for checkbox next to date, if a check cleared delete, else toggle checkbox
            $id = filter_input(INPUT_GET, 'ID');
            $processed = filter_input(INPUT_GET, 'P');
            $pindex = filter_input(INPUT_GET, 'PI');
            if($id && $processed != null) {
                $bill->SetFromDB($id);
                if($bill->IsCheck()) { //then variable is a cleared check, delete entirely
                    if($bill->RemoveFromDB()) {
                        DisplayMessage("Processed check/transfer.");
                    } else {
                        DisplayMessage("Could not process check.", true);
                    }
                } else { //do standard toggle of checkboxes (and variables)
                  if($bill->IsGrouped()) {
                    Company::UpdateProcessedDBGroup($bill->GetGroupId(), $pindex, $processed);
                  } else {
                    Company::UpdateProcessedDB($id, $pindex, $processed);
                  }
                }
            }
            include 'view/home.php';
            break;
        case 'savechanges': //save changes
            $bills = $db->GetSingleColumn('company', 'ID');

            $numsuccessful = 0;
            foreach($bills as $tbills) {
                $bill->SetFromDB($tbills['ID']);
                
                if(!$bill->IsRecurring() && !$bill->IsOnce()) {
                  $bill->SetAmountDue($_POST[$tbills['ID'] . '@' . 'Amount']);
                }
                
                $bill->SetDueDate($_POST[$tbills['ID'] . '@' . 'Due']);
                $bill->SetPostDay($_POST[$tbills['ID'] . '@' . 'Post']);

                $numsuccessful += $bill->UpdateDB();
            }

            if(!isset($_POST['store'])) {
                    DisplayMessage($numsuccessful . " row(s) saved.");
            } else { //code to store values to month
                $ready = true;
                $temp = $db->GetSingleColumnWhere('company', 'Amount', 'Frequency <> 6', 'Name');
                
                //confirm no bill amount was left at 0
                foreach($temp as $t) {
                    if($t['Amount'] == 0) { $ready = false; }
                }

                if($ready) {
                    for($i = 0; $i < 2; $i++) {
                        if($i == 1) {
                            $db = new Database('Property'); // switch to property database
                        }
                        $spent = 0;
                        $allotmentWas = $bill->GetCombinedAllotment();
                        $bill->SetAllotmentWas(date("m"), $allotmentWas);
                        
                        $temp = $db->GetSingleColumnWhere('company', 'ID', 'Frequency <> 6', 'Name');
                        foreach($temp as $t) {
                            $bill->SetFromDB($t['ID']);
                            $amountDue = ($bill->IsBimestrial()) ? round($bill->GetAmountDue()/2,2) : $bill->GetAmountDue();
                            $spent += $amountDue; //tally amount for the month

                            if(!(date("m") == $bill->GetMonthDue() && $bill->IsAnnual())) {
                                $bill->SetMonth(date("m"), $amountDue); //set the amount that was due for current month
                            }
                            if(date('m') == $bill->GetMonthDue() && $bill->IsBiennial()) {
                                $bill->ResetAllMonths();
                            }

                            $bill->UpdateDB(); //update the database
                        }

                        $_SESSION['ManualUnlock'] = 0;
                        $bill->SetSavedWas(date("m"), $allotmentWas - $spent);
                        $bill->SetLocked(1);
                        $bill->SetDateLocked($todaysDate);
                        
                        if($i == 1) {
                           $db = new Database('Bills'); // switch back to bills database
                        }
                    }
                } else {
                    DisplayMessage("Not all amounts are set.", true);
                }
            }

            include 'view/home.php';
            break;
        case 'Clear':
            include 'view/sure.php';
            break;
        default: //do default action, load home page
            include 'view/home.php';
            break;	
    } //end of switch statement

    include 'view/footer.php';
?>

<?php else :
    DisplayMessage("You do not have permission to view this site.", true);
    
    include 'view/rightbar.php';
endif;