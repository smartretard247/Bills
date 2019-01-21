<?php class Company {
  private $id;

  private $name;
  private $type;
  private $link;
  private $due;
  private $post;
  private $amount;
  private $january;
  private $february;
  private $march;
  private $april;
  private $may;
  private $june;
  private $july;
  private $august;
  private $september;
  private $october;
  private $november;
  private $december;
  private $recurring;
  private $annual;
  private $frequency;
  private $currency;
  private $processed = array('First' => 0, 'Second' => 0, 'Third' => 0);
  private $groupId;
  private $phone;
  private $notes;
  
  private $transactions;
    
  private static $dateLocked = '2000-01-01';
  private static $dateFormat = 'Y-m-d';
  public static function GetDateFormat() {
    return self::$dateFormat;
  }
  private static $exchangeRate = 100;
  public static function GetExchangeRate() {
    return self::$exchangeRate;
  }
  
	public function SetAllotment($amount, $cashAmount = 0) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

    global $db;
    $query = "UPDATE var SET Allotment = '" . $amount . "'";
    $query .= ", CashAllotment = '" . $cashAmount . "'";
    $db->exec($query);
  }
        
  public function GetAllotment() {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

    global $db;
    $query = "SELECT Allotment FROM var";
    $bill = $db->query($query);
    $bill = $bill->fetch();

    return $bill['Allotment'];
  }
  
  public function GetCombinedAllotment() {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

    global $db;
    $query = "SELECT Allotment + CashAllotment AS Allotment FROM var";
    $bill = $db->query($query);
    $bill = $bill->fetch();

    return $bill['Allotment'];
  }

  public function GetCashAllotment() {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

    global $db;
    $query = "SELECT CashAllotment FROM var";
    $bill = $db->query($query);
    $bill = $bill->fetch();

    return $bill['CashAllotment'];
  }

  public function GetCashTotal() {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

    global $db;
    $query = "SELECT SUM(Amount) AS CashTotal FROM (
      SELECT SUM(Amount) AS Amount FROM company WHERE (Currency = 'Cash') AND Frequency <> '6' AND Frequency <> '4'
      UNION ALL
      SELECT SUM(Amount)/2 AS Amount FROM company WHERE (Currency = 'Cash') AND Frequency = '4'
      ) AS Total";
    
    $query2 = "SELECT SUM(Amount) AS YenTotal FROM (
      SELECT SUM(Amount) AS Amount FROM company WHERE (Currency = 'Yen') AND Frequency <> '6' AND Frequency <> '4'
      UNION ALL
      SELECT SUM(Amount)/2 AS Amount FROM company WHERE (Currency = 'Yen') AND Frequency = '4'
      ) AS Total";
    $bill = $db->query($query);
    if($bill) { $bill = $bill->fetch(); }
    else { return 0; }
    $bill2 = $db->query($query2);
    if($bill2) { $bill2 = $bill2->fetch(); }
    else { return 0; }

    return $bill['CashTotal'] + $bill2['YenTotal'] / self::$exchangeRate;
  }
        
  public function GetDebitTotal() {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

    global $db;
    $query = "SELECT SUM(Amount) As Total FROM company WHERE Currency <> 'Yen' AND Currency <> 'Cash' AND Frequency <> '6'";
    $bill = $db->query($query);
    if($bill) { $bill = $bill->fetch(); }
    else { return 0; }

    return $bill['Total'];
  }

  public function GetPreview($monthsOut = 1, $cols = '*') {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

    global $db;
    $query = "SELECT $cols FROM company WHERE Currency = 'Cash'";
    $query .= " AND MONTH(Due) = MONTH(DATE_ADD(NOW(), INTERVAL $monthsOut MONTH))";
    $query .= " AND YEAR(Due) = YEAR(DATE_ADD(NOW(), INTERVAL $monthsOut MONTH))";
    $query .= " UNION ";
    $query .= "SELECT $cols FROM company WHERE Currency = 'Cash'";
    $query .= " AND MONTH(DATE_ADD(Due, INTERVAL $monthsOut MONTH)) = MONTH(DATE_ADD(NOW(), INTERVAL $monthsOut MONTH))";
    $query .= " AND YEAR(DATE_ADD(Due, INTERVAL $monthsOut MONTH)) = YEAR(DATE_ADD(NOW(), INTERVAL $monthsOut MONTH))";

    return $db->query($query);
  }
  
  public function GetTransactions() {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

    global $db;
    $query = "SELECT ID, Amount, TransDate FROM transactions WHERE CompanyID = '$this->id' ORDER BY TransDate DESC";
    $bill = $db->query($query);
    $bill = $bill->fetchAll();

    return $bill;
  }
  
  public function GetTransactionsHistory() {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

    global $db;
    $query = "SELECT Amount, TransDate FROM transactions_hist WHERE CompanyID = '$this->id' ORDER BY TransDate DESC";
    $bill = $db->query($query);
    $bill = $bill->fetchAll();

    return $bill;
  }
  
  public static function GetExchangeRateDB() {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

    global $db;
    $query = "SELECT ExchangeRate FROM var";
    $bill = $db->query($query)->fetch();
    //$bill = $bill;

    return $bill['ExchangeRate'];
  }
  public static function SetExchangeRateDB($to) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

    global $db;
    $query = "UPDATE var SET ExchangeRate = " . $to;
    $db->exec($query);
  }      
  
  public static function IsLocked() {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

    global $db;
    $query = "SELECT Locked FROM var";
    $bill = $db->query($query)->fetch();
    //$bill = $bill;

    return $bill['Locked'] && !$_SESSION['ManualUnlock'];
  }
  public function SetLocked($to) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

    global $db;
    $query = "UPDATE var SET Locked = " . $to;
    $db->exec($query);
  }
  public static function GetDateLocked() {
    if(self::$dateLocked == '2000-01-01') {
      require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';
      global $db;
      $query = "SELECT DateLocked FROM var";
      $bill = $db->query($query);
      $bill = $bill->fetch();

      self::$dateLocked = $bill['DateLocked'];
    }

    return self::$dateLocked;
  }
  public function SetDateLocked($to) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

    global $db;
    $query = "UPDATE var SET DateLocked = '" . $to . "'";
    $db->exec($query);
  }
  public function LockedThisMonth() {
    $lockedDate = date_parse_from_format(Company::GetDateFormat(), self::GetDateLocked());
    return (date('m') == $lockedDate['month']);
  }

  public function AllotmentWas($month) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

    global $db;
    $query = "SELECT AllotmentWas FROM saved WHERE Month =" . $month;
    $bill = $db->query($query);
    $bill2 = $bill->fetch();

    return $bill2['AllotmentWas'];
  }
		
	public function SetAllotmentWas($month, $amount) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

    global $db;
    $query = "UPDATE saved SET AllotmentWas = " . $amount;
    $query .= " WHERE Month = " . $month;
    $db->exec($query);
  }
        
  public function SavedWas($month) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

    global $db;
    $query = "SELECT SavedWas FROM saved WHERE Month =" . $month;
    $bill = $db->query($query);
    $bill2 = $bill->fetch();

    return $bill2['SavedWas'];
  }
        
  public function SetSavedWas($month, $amount) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

    global $db;
    $query = "UPDATE saved SET SavedWas = " . $amount;
    $query .= " WHERE Month = " . $month;
    $db->exec($query);
  }

  private static $CompanyCount = 0;
  public static function GetCompanyCount() { return self::$CompanyCount; }

  public function __construct($name = '', $type = 'default', $link = 'http://www.', $due = '2000-01-01', $post = '2000-01-01', $amount = 0.00, 
          $january = 0.00, $february = 0.00, $march = 0.00, $april = 0.00, $may = 0.00, $june = 0.00, $july = 0.00, $august = 0.00, 
          $september = 0.00, $october = 0.00, $november = 0.00, $december = 0.00, $recurring = 0, $annual = 0, $frequency = 1, $currency = "Debit", $groupId = 0) {
        
    $this->id = 0;
    $this->name = $name;
    $this->type = $type;
    $this->link = $link;
    $this->due = $due;
    $this->post = $post;
    $this->amount = $amount;
    $this->january = $january;
    $this->february = $february;
    $this->march = $march;
    $this->april = $april;
    $this->may = $may;
    $this->june = $june;
    $this->july = $july;
    $this->august = $august;
    $this->september = $september;
    $this->october = $october;
    $this->november = $november;
    $this->december = $december;
    $this->recurring = $recurring;
    $this->annual = $annual;
    $this->frequency = $frequency;
    $this->currency = $currency;
    $this->processed['First'] = 0;
    $this->processed['Second'] = 0;
    $this->processed['Third'] = 0;
    $this->groupId = $groupId;

    self::$CompanyCount++;
    self::$exchangeRate = Company::GetExchangeRateDB();
  }

  public function __destruct() {
    self::$CompanyCount--;
  }

  public function AddToDB($table = 'company', $id = '') {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';
    global $db;

    if(!$this->IsValidName()) { return 0; } //not valid name
    if($this->IsDuplicateName($table)) { return 0; } //double company
    if($id == '') {
      $query = "INSERT INTO $table (`Name`, Amount, Type, Link, Recurring, Frequency, Currency) VALUES ('";
    } else {
      $query = "INSERT INTO $table (ID, `Name`, Amount, Type, Link, Recurring, Frequency, Currency) VALUES ('$id', '";
    }

    $query .= $this->name . "'";
    $query .= ", '" . $this->amount . "'";
    $query .= ", '" . $this->type . "'";
    $query .= ", '" . $this->link . "'";
    $query .= ", '" . $this->recurring . "'";
    $query .= ", '" . $this->frequency . "'";
    $query .= ", '" . $this->currency . "'";
    $query .= ")";

    return $db->exec($query);
  }

  public function RemoveFromDB($table = 'company', $id = '') {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';
    global $db;

    if($id == '') {
      $id = $this->id;
    }

    $query = "DELETE FROM " . $table . " WHERE `ID` = '" . $id . "'";
    $num_rows_affected = $db->exec($query);

    return $num_rows_affected;
  }

  public function UpdateDB($table = 'company') {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';
    global $db;

    if($this->due == '2000-01-01') { $this->due = date($this->GetDateFormat()); }

    $query = "UPDATE " . $table . " SET Name = '" . $this->name;
    $query .= "', Type = '" . $this->type;
    $query .= "', Link = '" . $this->link;
    $query .= "', Due = '" . $this->due;
    $query .= "', Post = '" . $this->post;
    $query .= "', Amount = '" . $this->amount;
    $query .= "', January = '" . $this->january;
    $query .= "', February = '" . $this->february;
    $query .= "', March = '" . $this->march;
    $query .= "', April = '" . $this->april;
    $query .= "', May = '" . $this->may;
    $query .= "', June = '" . $this->june;
    $query .= "', July = '" . $this->july;
    $query .= "', August = '" . $this->august;
    $query .= "', September = '" . $this->september;
    $query .= "', October = '" . $this->october;
    $query .= "', November = '" . $this->november;
    $query .= "', December = '" . $this->december;
    $query .= "', Recurring = '" . $this->recurring;
    $query .= "', Annual = '" . $this->annual;
    $query .= "', Processed = '" . $this->processed['First'];
    $query .= "', Processed2 = '" . $this->processed['Second'];
    $query .= "', Processed3 = '" . $this->processed['Third'];
    $query .= "', Frequency = '" . $this->frequency;
    $query .= "', Currency = '" . $this->currency;
    $query .= "', GroupID = '" . $this->groupId;
    $query .= "', Phone = '" . $this->phone;
    $query .= "', Notes = '" . $this->notes;
    $query .= "' WHERE `ID` = '" . $this->id;
    $query .= "'";
    $numrowsaffected = $db->exec($query);

    return $numrowsaffected;
  }
    
  public static function UpdateProcessedDB($id, $index, $bool, $table = 'company') {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';
    global $db;

    $query = "UPDATE $table SET ";

    switch ($index) {
      case 1: $query .= "Processed"; break;
      case 2: $query .= "Processed2"; break;
      case 3: $query .= "Processed3"; break;
    }

    $query .= " = $bool WHERE `ID` = " . $id;
    $db->exec($query);
  }
    
  public static function UpdateProcessedDBGroup($gid, $index, $bool, $table = 'company') {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';
    global $db;

    $query = "UPDATE $table SET ";

    switch ($index) {
      case 1: $query .= "Processed"; break;
      case 2: $query .= "Processed2"; break;
      case 3: $query .= "Processed3"; break;
    }

    $query .= " = $bool WHERE GroupID = '$gid'";

    $db->exec($query);
  }

  public function SetFromDB($id, $table = 'company') {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';
    global $db;

    $query = "SELECT * FROM $table WHERE `ID` = '$id'";
    $bill_set = $db->query($query);
    $bill = $bill_set->fetch();

    $this->id = $id;
    $this->name = $bill['Name'];
    $this->type = $bill['Type'];
    $this->link = $bill['Link'];
    $this->due = $bill['Due'];
    $this->post = $bill['Post'];
    $this->amount = $bill['Amount'];
    $this->january = $bill['January'];
    $this->february = $bill['February'];
    $this->march = $bill['March'];
    $this->april = $bill['April'];
    $this->may = $bill['May'];
    $this->june = $bill['June'];
    $this->july = $bill['July'];
    $this->august = $bill['August'];
    $this->september = $bill['September'];
    $this->october = $bill['October'];
    $this->november = $bill['November'];
    $this->december = $bill['December'];
    $this->recurring = $bill['Recurring'];
    $this->annual = $bill['Annual'];
    $this->frequency = $bill['Frequency'];
    $this->currency = $bill['Currency'];
    $this->processed['First'] = $bill['Processed'];
    $this->processed['Second'] = $bill['Processed2'];
    $this->processed['Third'] = $bill['Processed3'];
    $this->groupId = $bill['GroupID'];
    $this->phone = $bill['Phone'];
    $this->notes = $bill['Notes'];
    return $bill;
  }

  public function InCash() { return $this->currency == 'Cash'; }
  public function InYen() { return $this->currency == 'Yen'; }
  
  public function GetCurrencySymbol() {
    return ($this->InYen()) ? "&#165;" : "$";
  }
  
  public function GetDisplayName() {
    if($this->InYen()) {
      return $this->GetName() . " (" . $this->GetCurrencySymbol() . ")";
    } else {
      return $this->GetName();
    }
  }
  public function GetName() { 
    return $this->name;
  }
  public function SetName($name) { $this->name = $name; }
  
  public function GetNotes() { return $this->notes; }
  public function SetNotes($notes) { $this->notes = $notes; }

  public function GetCurrency() { return $this->currency; }
  public function SetCurrency($currency) { $this->currency = $currency; }

  public function IsGrouped() {
    return $this->groupId > 0;
  }

  public function GetGroupId() {
    return $this->groupId;
  }
  public function SetGroupId($to) {
    $this->groupId = $to;
  }

  public function GetPhone() {
    return $this->phone;
  }
  public function SetPhone($to) {
    $this->phone = $to;
  }

  public function IsCheck() {
    if($this->IsOnce()) { return true; }
    if(strpos($this->name, "#") === false) { return false; } 
    return true;
  }

  public function IsValidName() {
    if($this->name != '') { return true; }
    else { return false; }
  }
  public function IsDuplicateName($inTable = 'company') {
    return false;
  }

  public function GetType() { return $this->type; }
  public function SetType($type) { $this->type = $type; }

  public function GetLink() { return $this->link; }
  public function SetLink($link) { $this->link = $link; }

  public function GetDueDate() { return $this->due; }
  public function SetDueDate($due) { $this->due = $due; }
    
  public function GetMonthDue() {
    return date("m", strtotime($this->GetDueDate()));
  }

  public function GetDayDue() {
    return date("d", strtotime($this->GetDueDate()));
  }

  public function GetYearDue() {
    return date("Y", strtotime($this->GetDueDate()));
  }

  public function GetMonthPaid() {
    return date("m", strtotime($this->GetPostDay()));
  }

  private function GetYearPaid() {
    return date("Y", strtotime($this->GetPostDay()));
  }

  public function GetYearMonthPaid() {
    return date("Y-m", strtotime($this->GetPostDay()));
  }
    
  public function WasPaidThisMonth() {
    return (date("m") == $this->GetMonthPaid()) && (date('Y') == $this->GetYearPaid()); //check if current month is same as month due
  }

  public function IsDueThisMonth() {
    if(!$this->IsAnnual() && $this->IsRecurring() && !$this->IsBimestrial()) { return true; }
    return (date("m") == $this->GetMonthDue()) && (date('Y') == $this->GetYearDue()); //check if current month is same as month due
  }

  private function IsDueThisYear() {
    return $this->GetYearDue() == date('Y');
  }
  
  public static function ConvertToYen($value) {
    return $value * self::$exchangeRate;
  }
  
  public static function ConvertToDollar($value) {
    return $value / self::$exchangeRate;
  }
  
  public function GetYenDue() {
    return str_replace(",", "", $this->amount);
  }

  public function GetPostDay() { return $this->post; }
  public function SetPostDay($day) { $this->post = $day; }
  
  public function GetDollarDue() {
    return ($this->InYen()) ? $this->amount / self::$exchangeRate : $this->amount;
  }

  public function GetAmountDue() { return $this->GetDollarDue(); }
  public function SetAmountDue($amount) { $this->amount = str_replace(",", "", $amount); }

  //gets the total of stored months for this bill
  public function GetRowTotal() {
    $tally = 0;
    
    for($i = 0; i < 12; $i++) {
      $tally += $this->GetMonth($i+1);
    }
    
    return $tally;
  }
  
  public function GetMonth($mon, $convert = false) {
    $value = 0;
    
    switch ($mon) {
    case '1':
    case 'Jan':
    case 'January': $value = $this->january; break;
    case '2':
    case 'Feb':
    case 'February': $value = $this->february; break;
    case '3':
    case 'Mar':
    case 'March': $value = $this->march; break;
    case '4':
    case 'Apr':
    case 'April': $value = $this->april; break;
    case '5':
    case 'May': $value = $this->may; break;
    case '6':
    case 'Jun':
    case 'June': $value = $this->june; break;
    case '7':
    case 'Jul':
    case 'July': $value = $this->july; break;
    case '8':
    case 'Aug':
    case 'August': $value = $this->august; break;
    case '9':
    case 'Sep':
    case 'September': $value = $this->september; break;
    case '10':
    case 'Oct':
    case 'October': $value = $this->october; break;
    case '11':
    case 'Nov':
    case 'November': $value = $this->november; break;
    case '12':
    case 'Dec':
    case 'December': $value = $this->december; break;
    case 'default': return 0;
    }
    
    return ($convert) ? self::ConvertToDollar($value) : $value;
	}
	public function SetMonth($mon, $amount) {
		switch ($mon) {
    case '1':
    case 'Jan':
    case 'January': $this->january = $amount; break;
    case '2':
    case 'Feb':
    case 'February': $this->february = $amount; break;
    case '3':
    case 'Mar':
    case 'March': $this->march = $amount; break;
    case '4':
    case 'Apr':
    case 'April': $this->april = $amount; break;
    case '5':
    case 'May': $this->may = $amount; break;
    case '6':
    case 'Jun':
    case 'June': $this->june = $amount; break;
    case '7':
    case 'Jul':
    case 'July': $this->july = $amount; break;
    case '8':
    case 'Aug':
    case 'August': $this->august = $amount; break;
    case '9':
    case 'Sep':
    case 'September': $this->september = $amount; break;
    case '10':
    case 'Oct':
    case 'October': $this->october = $amount; break;
    case '11':
    case 'Nov':
    case 'November': $this->november = $amount; break;
    case '12':
    case 'Dec':
    case 'December': $this->december = $amount; break;
    case 'default': return 0;
		}
	}

  public function ResetAllMonths() {
    for($i = 1; $i <= 12; $i++) {
      $this->SetMonth($i, 0);
    }
  }
        
	public function GetAverage() {
		$average = 0;
		for($i = 1; $i <= 12; $i++) {
			$average += $this->GetMonth($i);
		}
		return $average /= 12;
	}
  
  public function GetTotal() {
		$months = 0;
	
    if($this->IsBiannual()) {
      $months = 6;
    } else if($this->IsBiennial()) {
      $months = 24;
    } else if($this->IsAnnual()) {
      $months = 12;
    }
    
    //if($this->IsDueThisMonth() && $this->IsAnnual()) { $months += 1; }
    $total = $this->GetAmountDue() * ($months - $this->GetMonthsRemaining());
    return $total;
	}
  
  public static function GetTotalOf($amount, $frequency) {
		return $amount * $frequency;
	}
  
  public function GetMonthsRemaining() {
    $due = date_create_from_format('Y-m-d', $this->GetYearDue() . '-' . $this->GetMonthDue() . '-28');
    $today = new DateTime();
    //$today->setDate(date('Y'), date('m'), date('t'));
    $today->setDate(date('Y'), date('m'), 1);
    $interval = $today->diff($due);
    
    return $interval->format('%m') + ($interval->format('%Y') * 12);
  }

  public function IsRecurring() { return $this->recurring; }
  public function SetRecurring($recurring) { $this->recurring = $recurring; }
	public static function GetRecurringWord($for) { 
    switch($for) {
    case 0: return 'No';
    case 1: return 'Yes';
    default: return null;
    }
	}
        
  public function IsAnnual() { return $this->annual; }
  public function SetAnnual($annual) { $this->annual = $annual; }
	public static function GetAnnualWord($for) {
    switch($for) {
    case 0: return 'No';
    case 1: return 'Yes';
    default: return null;
    }
  }
  
  public static function GetMonthWord($for) {
    switch($for) {
    case 1: return 'Jan';
    case 2: return 'Feb';
    case 3: return 'Mar';
    case 4: return 'Apr';
    case 5: return 'May';
    case 6: return 'Jun';
    case 7: return 'Jul';
    case 8: return 'Aug';
    case 9: return 'Sep';
    case 10: return 'Oct';
    case 11: return 'Nov';
    case 12: return 'Dec';
    default: return null;
    }
  }
        
  public function WasProcessed($index = 1) {
    switch($index) {
    case 0: return $this->processed['First'] || $this->processed['Second'] || $this->processed['Third']; //special case for checking all at once
    case 1: return $this->processed['First'];
    case 2: return $this->processed['Second'];
    case 3: return $this->processed['Third'];
    case 4: return ($this->processed['First'] && $this->processed['Second']);
    }
  }
  public function SetProcessed($index, $to) {
    switch($index) {
    case 1: $this->processed['First'] = $to;
    case 2: $this->processed['Second'] = $to;
    case 3: $this->processed['Third'] = $to;
    }
  }
  public function ToggleProcessed($index) {
    ($this->processed[$index-1]) ? $this->processed[$index-1] = 0 : $this->processed[$index-1] = 1;
  }
        
  public static $frequencies = array('Monthly', 'Bimonthly', 'Trimonthly', 'Bimestrial', 'Biennial', 'Once', 'Biannual');
  public function GetFrequency() { return $this->frequency; }
  public function SetFrequency($freq) { $this->frequency = $freq; }
  public static function GetFreqWord($for) {
    if($for > 0 && $for < sizeof(self::$frequencies)+1) {
      return self::$frequencies[$for-1];
    } else { return null; }
  }
  public static function GetFrequencyFromWord($word) {
    for($i = 1; $i < sizeof(self::$frequencies)+1; $i++) {
      if(self::$frequencies[$i-1] == $word) {
        return $i;
      }
    }
  }
  public function IsOnce() {
    return ($this->frequency == 6);
  }
  public function IsBimestrial() {
    return ($this->frequency == 4);
  }
  public function IsBiennial() {
    return ($this->frequency == 5);
  }
  public function IsBiannual() {
    return ($this->frequency == 7);
  }

	public function GetID() { return $this->id; }
  
  public function OutputRowTotal($rowTotal) {
    if($this->InYen()) {
      echo $this->GetCurrencySymbol() . number_format($this->ConvertToYen($rowTotal), $this->GetDecimalPlaces());
    } else {
      echo $this->GetCurrencySymbol() . number_format($rowTotal, $this->GetDecimalPlaces());
    }
  }
  
  public function GetDecimalPlaces() {
    return ($this->InYen()) ? 0 : 2;
  }
}

