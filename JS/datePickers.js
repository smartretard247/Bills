/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(function() {
  var popups = document.getElementsByClassName('datePickers');

  for (var i = 1; i < popups.length+1; i++) {
    $('#popupDatepicker'+i).datepick({dateFormat: 'yyyy-mm-dd'});
  };
});

function showDate(date) {
  alert('The date chosen is ' + date);
}