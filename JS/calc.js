/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function calc() {
  var minBalance = document.getElementById("MinBalance").value;
  var curBalance = document.getElementById("CurBalance").value;
  curBalance = curBalance.toString().replace(",","");
  curBalance = curBalance.toString().replace("$","");
  var remaining = document.getElementById("Remaining");
  remaining.value = '';

  var left = Math.floor(curBalance - minBalance);
  if(left < 0) {
    remaining.value = '(';
  }
  remaining.value += left.toString() + '.00';
  if(left < 0) {
    remaining.value += ')';
  }
};