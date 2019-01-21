<?php
    if(filter_input(INPUT_GET, 'db')) { $_SESSION['db'] = filter_input(INPUT_GET, 'db'); }
    if(empty($_SESSION['db'])) { $_SESSION['db'] = 'Bills'; }

    include_once $_SESSION['rootDir'] . '../database.php'; $db = new Database(strtolower($_SESSION['db']));
    include_once $_SESSION['rootDir'] . 'core/company.php'; $bill = new Company;
    
    if($_SESSION['db'] == 'Bills') {
        $buffer = 100.00;
        $types = array('Auto', 'Credit', 'Extras', 'Insurance', 'Necessities', 'Other', 'Payment', 'Pets', 'Services', 'Transfer In', 'Transfer Out', 'Utilities'); //for type dropdown boxes
    } else {
        $buffer = 100.00;
        $types = array('Auto and travel', 'Insurance', 'Management fees', 'Other', 'Payment', 'Repairs', 'Supplies', 'Tax', 'Transfer In', 'Transfer Out', 'Utilities');
    }
    
    function StartTable() {
        echo '<table><tr>';
    }

    function EndTable() {
        echo "</tr></table>";
    }

    function TH($header, $span = '1') {
        echo '<th colspan="' . $span . '">' . $header . '</th>';
    }

    function TH2($header, $style = '') {
        echo '<th style="' . $style . '">' . $header . '</th>';
    }

    function TR($data, $span = '1') {
        echo '<tr colspan="' . $span . '">"' . $data . '</tr>';
    }

    function NoDataRow($array, $colspan) {
        if($array == 0) {
            echo '<tr><td colspan="' . $colspan . '"><b>No data exists in the table.</b></td></tr>';
        }
    }
    
    function DisplayMessage($message, $error = false) {
        $error ? $id = 'error' : $id = 'success'; ?>
        <table style="position: absolute; top: 15px; left: 15px; background-color: whitesmoke;">
            <tr>
                <td><b id=<?php echo $id; ?>><?php echo $message; ?></b></td>
            </tr>
        </table><?php
    }
    
    function insertGoBackURL() {
      echo '<a href="?action=default">Go Back</a>';
    }
