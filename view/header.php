<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta charset="utf-8"/>
  <title><?php echo $_SESSION['db']; ?></title>

  <link rel="stylesheet" type="text/css" href="<?php echo $SERVER['DOCUMENT_ROOT']; ?>/bills/bills.css" />
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
  <style type="text/css">@import "JS/jquery.datepick.css";</style>
  <script type="text/javascript" src="JS/jquery.datepick.js"></script>
  <script type="text/javascript" src="JS/calc.js"></script>
  <script type="text/javascript" src="JS/datePickers.js"></script>
</head>

<body OnLoad="document.getElementById('CurBalance').focus();window.scrollTo(0, 0);">
  <div id="page">
    <div id="header">
      <br/>
      <center>
        <h5><?php
          if($_SESSION['db'] == 'Property') {
            $swapTo = '<a href="?db=Bills"> <--</a>';
            echo '<a href="http://owner.vetrlty_141909.propertyboss.net/" target="_blank" style="font-size: larger;">';
          } else {
            $swapTo = '&nbsp;<a href="?db=Property">--></a>'; 
          }
          
          echo str_replace(" ", "", $_SESSION['db']);
          
          if($_SESSION['db'] == 'Property') {
            echo "</a>&nbsp;";
          }
          
          echo $swapTo; ?>
        </h5>
      </center>
    </div>
    <div id="main"><br/>