<?php

if($_SESSION['debug']) {
    echo '<ul>'; //display debugging information as an unordered list

    //output POST, GET, and SESSION array variables
    if($_POST) {
        foreach($_POST as $key => $value) {
            echo '<li>$_POST[' . $key . '] => ' . $value . "</li>";
        }
        echo '<br/>';
    }
    if($_GET) {
        foreach($_GET as $key => $value) {
            echo '<li>$_GET[' . $key . '] => ' . $value . "</li>";
        }
        echo '<br/>';
    }
    if($_SESSION) {
        foreach($_SESSION as $key => $value) {
            echo '<li>$_SESSION[' . $key . '] => ' . $value . "</li>";
        }
        echo '<br/>';
    }
    ///////////////////////////////////////////////
    
    //output any additional variables here...
    if($lockedDate) { echo '<li>$lockedDate = ' . $bill->GetDateLocked() . "</li>"; }
    if($todaysDate) { echo '<li>$todaysDate = ' . $todaysDate . "</li>"; }
    if($tempUnlockDate) { echo '<li>$tempUnlockDate = ' . $tempUnlockDate . "</li>"; }
    if($unlockDate) { echo '<li>$unlockDate = ' . $unlockDate . "</li>"; }
    ///////////////////////////////////////////////
    echo '</ul>';
}