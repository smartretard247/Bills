<div id="dragBreakdown">
<table id="breakdown">
  <tr>
    <td>
      <table id="noborders">
        <tr>
          <td>
            <label>
              <form name="hide" action="" method="post">
                <?php if($_SESSION['HideRecurring']) : ?>
                  <input type="checkbox" value="1" checked="checked" onclick="submit();"/>
                  <input name="HideRecurring" type="hidden" value="0"/>
                <?php else : ?>
                  <input type="checkbox" value="0" onclick="submit();"/>
                  <input name="HideRecurring" type="hidden" value="1"/>
                <?php endif; ?>
              Hide Recurring</form>
            </label>
          </td>
          <td>
            <label>
              <form name="unlock" action="" method="post">
                <?php if($_SESSION['ManualUnlock']) : ?>
                  <input type="checkbox" value="1" checked onclick="submit();"/>
                  <input name="ManualUnlock" type="hidden" value="0"/>
                <?php else : ?>
                  <input type="checkbox" value="0" onclick="submit();"/>
                  <input name="ManualUnlock" type="hidden" value="1"/>
                <?php endif; ?>
                Unlock
              </form>
            </label>
          </td>
          <td>
            <label>
              <form name="rct" action="" method="post">
                <?php if(!$removeCashTotal) : ?>
                  <input type="checkbox" value="1" checked onclick="submit();" title="Include Cash?"/>
                  <input name="RemoveCashTotal" type="hidden" value="1"/>
                <?php else : ?>
                  <input type="checkbox" value="0" onclick="submit();" title="Include Cash?"/>
                  <input name="RemoveCashTotal" type="hidden" value="0"/>
                <?php endif; ?>
              </form>
            </label>
          </td>
        </tr>
      </table>
      <br/>
      Monthly paid by Debit: $<?php echo number_format(ceil($monthlyDebitTotal), 0); ?><br/>
      Monthly paid by Cash: $<?php echo number_format(ceil($monthlyCashTotal), 0); ?><br/>
      Minimum Allotment: $<?php echo number_format(ceil($monthlyDebitTotal + $monthlyCashTotal), 0); ?>
    </td>
  </tr>
  <tr>
    <td>
      <label>
        Due this month in Cash: $<?php echo number_format(ceil($thisMonthsCashTotal), 0); ?>
      </label>
      <label>
        <br/>Due this month in Yen: &#165;<?php echo ($thisMonthsCashTotalYen) ? round($thisMonthsCashTotalYen+49, -2) : "0"; ?>
      </label>
    </td>
  </tr>
</table>
</div>

<script>
  var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth || window.screen.availWidth;
  var dragBreakdown = document.getElementById("dragBreakdown");
  var x = width - dragBreakdown.offsetWidth - 45;
  dragBreakdown.style.top = "15px";
  dragBreakdown.style.left = x + "px";

dragElement(document.getElementById("dragBreakdown"));

function dragElement(elmnt) {
  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
  if (document.getElementById(elmnt.id + "header")) {
    /* if present, the header is where you move the DIV from:*/
    document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
  } else {
    /* otherwise, move the DIV from anywhere inside the DIV:*/
    elmnt.onmousedown = dragMouseDown;
  }

  function dragMouseDown(e) {
    e = e || window.event;
    e.preventDefault();
    // get the mouse cursor position at startup:
    pos3 = e.clientX;
    pos4 = e.clientY;
    document.onmouseup = closeDragElement;
    // call a function whenever the cursor moves:
    document.onmousemove = elementDrag;
  }

  function elementDrag(e) {
    e = e || window.event;
    e.preventDefault();
    // calculate the new cursor position:
    pos1 = pos3 - e.clientX;
    pos2 = pos4 - e.clientY;
    pos3 = e.clientX;
    pos4 = e.clientY;
    // set the element's new position:
    elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
    elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
  }

  function closeDragElement() {
    /* stop moving when mouse button is released:*/
    document.onmouseup = null;
    document.onmousemove = null;
  }
}
</script>