<?php $dbWithAccounts = new Database('ssa');

class Account {
    private $username;
    private $thePassword;
    private $accountType;
    private $lastname;
    private $firstname;
    private $mi;

    private static $AccountCount = 0;
    
    public function GetUsername() { return $this->username; }
    public function SetUsername($to) { $this->username = $to; }
    
    public function GetPassword() { return $this->thePassword; }
    public function SetPassword($to) { $this->thePassword = md5($to); }

    public function GetAccountType() { return $this->accountType; }
    public function SetAccountType($to) { $this->accountType = $to; }
    
    public function GetLastName() { return $this->lastname; }
    public function SetLastName($to) { $this->lastname = $to; }
    
    public function GetFirstName() { return $this->firstname; }
    public function SetFirstName($to) { $this->firstname = $to; }
    
    public function GetMI() { return $this->mi; }
    public function SetMI($to) { $this->mi = $to; }

    public static function GetAccountCount() { return self::$AccountCount; }

    public function __construct() {
        $this->username = '';
        $this->thePassword = '';
        $this->accountType = '';
        $this->lastname = '';
        $this->firstname = '';
        $this->mi = '';
    
        self::$AccountCount++;
    }
    public function __destruct() {
        self::$AccountCount--;
    }
    
    public static function IsAvailable($user) {
        require_once $_SESSION['rootDir'] . 'database.php';
        global $dbWithAccounts;
        
        $query = "SELECT AccountType FROM accounts WHERE `ID` = '$user'";
        $dbWithAccountsItem = $dbWithAccounts->query($query)->fetch();
        
        if($dbWithAccountsItem['AccountType']) { return false; } else { return true; }
    }

    public function HasValidCombo() {
        require_once $_SESSION['rootDir'] . 'database.php';
        global $dbWithAccounts;
        
        $query = "SELECT AccountType FROM accounts WHERE `ID` = '$this->username' AND ThePassword = '$this->thePassword'";
        $dbWithAccountsItem = $dbWithAccounts->query($query)->fetch();
        
        return $dbWithAccountsItem['AccountType'];
    }
    
    public function SetFromDB($username) {
        require_once $_SESSION['rootDir'] . 'database.php';
        global $dbWithAccounts;

        $query = "SELECT * FROM accounts WHERE `ID` = '$username'";
        $dbWithAccountsItem = $dbWithAccounts->query($query)->fetch();

        //set all member variables here...
        $this->SetUsername($dbWithAccountsItem['ID']);
        $this->SetLastName($dbWithAccountsItem['LastName']);
        $this->SetFirstName($dbWithAccountsItem['FirstName']);
        $this->SetMI($dbWithAccountsItem['MI']);
    }
}
?>
