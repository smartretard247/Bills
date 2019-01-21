<center>
<?php $monthlyDebitTotal += $bill->GetDebitTotal(); ?>
<?php $monthlyCashTotal = $bill->GetCashTotal(); ?>
<?php $removeCashTotal = ($_SESSION['RemoveCashTotal']); ?>
<table>
  <form name="bil" method="post" action="">
    <?php if(!Company::IsLocked()) {
      $tempColSpan = 1;
    } else {
      $tempColSpan = 2;
    }
    TH('Name', $tempColSpan);

    if(!Company::IsLocked()) {
      if($_SESSION['db'] == 'Bills') {
        TH2('Date-Due');
      } else {
        TH2('Due-By');
      }
    } else {
      TH2('Posted');
    }

    TH('Jan');
    TH('Feb');
    TH('Mar');
    TH('Apr');
    TH('May');
    TH('Jun');
    TH('Jul');
    TH('Aug');
    TH('Sep');
    TH('Oct');
    TH('Nov');
    TH('Dec');
    TH('Totals');

    $pop = 1;
    $remaining = array($bill->AllotmentWas(1),$bill->AllotmentWas(2),$bill->AllotmentWas(3),$bill->AllotmentWas(4),$bill->AllotmentWas(5),$bill->AllotmentWas(6),$bill->AllotmentWas(7),$bill->AllotmentWas(8),$bill->AllotmentWas(9),$bill->AllotmentWas(10),$bill->AllotmentWas(11),$bill->AllotmentWas(12));
    $id_list = $db->GetSingleColumn('company', 'ID', 'Name');
    $temp = $db->GetSingleColumnWhere('company', 'Amount, Currency, Frequency', 'Frequency <> 6', 'Name'); $ready = true;
    $left = ($removeCashTotal) ? $bill->GetAllotment() : $bill->GetAllotment() + $bill->GetCashAllotment();
    $leftC = ($removeCashTotal) ? $bill->GetCashAllotment() : 0;
    $allotted = $left;
    $allottedC = $leftC;
    $totalForMonth = 0;
    $totalForMonthC = 0;
    
    foreach($temp as $t) {
      if($t['Currency'] == 'Yen') {
        $tempAmount = Company::ConvertToDollar($t['Amount']);
      } else {
        $tempAmount = $t['Amount'];
      }

      $tempFreq = $t['Frequency'];
      if($tempAmount == 0) { $ready = false; }
      if($tempFreq == Company::GetFrequencyFromWord('Bimestrial')) { $tempAmount = round($tempAmount/2, 2); } //halve the Bimestrial payment

      if($removeCashTotal) {
        if($t['Currency'] != 'Cash' && $t['Currency'] != 'Yen') {
          $totalForMonth += $tempAmount;
          $left -= $tempAmount;
        } else {
          $totalForMonthC += $tempAmount;
          $leftC -= $tempAmount;
        }
      } else {
        $totalForMonth += $tempAmount;
        $left -= $tempAmount;
      }
    }
    
    $lastMonth = date('m') - 1;
    if($lastMonth == 0) { $lastMonth = 12; } //fix incorrect previous month
    $monthLocked = date_format(date_create(Company::GetDateLocked()), 'm');

    foreach($id_list as $tc) :
      $bill->SetFromDB($tc['ID']); 
      $decimalPlaces = $bill->GetDecimalPlaces(); 

      $rowIsHidden = ($_SESSION['HideRecurring'] == 1 && $bill->IsRecurring()) ? " hidden" : ""; ?>
      <tr <?php echo $rowIsHidden; ?>>
        <input type="hidden" name="ID<?php echo $bill->GetID(); ?>" value="<?php echo $bill->GetID(); ?>"/>
        <td colspan="<?php echo $tempColSpan; ?>">
          <?php echo '<a href="' . $bill->GetLink() . '" target="_blank" title="' . $bill->GetNotes() .  '">' . $bill->GetDisplayName() . ((($bill->InCash() || $bill->InYen()) ? '*' : '')) . '</a>'; ?>
        </td>

        <?php $postedColWidth = (Company::IsLocked()) ? "" : "width: 140px;"; ?>
        <td style="<?php echo $postedColWidth; ?>">
          <?php if(!Company::IsLocked()) : ?>
            <input class="datePickers" type="input" size="10" id="popupDatepicker<?php echo $pop; ?>" name="<?php echo $bill->GetID() . '@'; ?>Due" value="<?php echo $bill->GetDueDate(); ?>"/>
          <?php endif; ?>

          <input type="hidden" name="<?php echo $bill->GetID() . '@'; ?>Post" value="<?php echo $bill->GetPostDay(); ?>">

          <?php //create the link to use for the checkbox, and add checkbox to the view ?>
          <?php $freq = $bill->GetFrequency();
          if(!$bill->IsAnnual() && (!$bill->IsBimestrial())) : //then create a useable checkbox (freq 4 means semi-monthly)
            if($freq > 3) { $freq = 1; } //this is actually a 'once' payment, or 'Bimestrial' payment
            for($i = 1; $i <= $freq; $i++) :
              $proc = $bill->WasProcessed($i);
              $inv_proc = ($proc == 0) ? 1 : 0;
              $location = "window.location='?action=processed&ID=" . $bill->GetID() . "&PI=$i&P=$inv_proc';"; ?>
              <input type="checkbox" name="<?php echo $bill->GetID() . '@'; ?>Processed" value="<?php echo $proc; ?>" onclick="<?php echo $location; ?>"<?php if($proc) { echo " checked"; } else { echo ' class="inverted"'; } ?>/>
            <?php endfor;
          else : //the bill is annual so do not allow checkbox ?>
            <input type="checkbox" disabled/>
          <?php endif; ?>
        </td>

        <?php for($i = 1; $i <= 12; $i++) : ?>
          <td>
            <?php if(($bill->IsAnnual() || $bill->IsBimestrial()) && $i == $bill->GetMonthDue()) {
              if($bill->IsDueThisMonth()) :
                $billid = $bill->GetID(); ?>
                <input type="button" value="Pay" onclick="window.location='?action=paidAnnualBill&billid=<?php echo $billid; ?>'"/>
              <?php else :
                echo '<input type="button" value="Pay" disabled/>';
              endif;
            } else {
              if($bill->IsCheck()) {
                echo "--.--";
              } else {
                if(!Company::IsLocked() && $i == date('m')) {
                  include 'view/colAmount.php';
                } else {
                  echo number_format($bill->GetMonth($i), $decimalPlaces);
                }
              }
              $remaining[$i-1] -= $bill->GetMonth($i, true);
            } ?>
          </td>
        <?php endfor; ?>

        <td>
          <?php if($bill->IsBiannual()) {
            $totalBalance += $bill->GetTotal();
            $rowTotal = $bill->GetTotal();
          } else if($bill->IsAnnual()) { //annual bill, like AmazonPrime
            $totalBalance += $bill->GetTotal();
            $rowTotal = $bill->GetTotal();

            //temporarily remove one months payment if account was just paid off
            if($bill->WasPaidThisMonth()) {
              $rowTotal -= $bill->GetAmountDue();
              if($rowTotal < 0) { $rowTotal = 0; }
            }
          } else if($bill->IsBimestrial()) {
            if($bill->WasPaidThisMonth()) {
              $rowTotal = 0;
            } else if($bill->IsDueThisMonth()) {
              $rowTotal = $bill->GetAmountDue();
            } else {
              $rowTotal = $bill->GetAmountDue()/2;
            }

            $monthlyDebitTotal -= $bill->GetAmountDue()/2;
            $totalBalance += $rowTotal;
          } else { //regular monthly bill
            if(!$bill->WasProcessed(0) && !($bill->InCash() || $bill->InYen())) { //bill was not processed so...
              if($bill->GetAmountDue() == 0) {
                $estimated = ($bill->GetMonthDue() == date("m"));

                $lastMonthForEstimate = date("m")-1;
                if($lastMonthForEstimate == 0) { $lastMonthForEstimate = 12; }

                $rowTotal = $bill->GetMonth($lastMonthForEstimate); //"n" is month number, without leading zero

                $monthlyDebitTotal += $rowTotal;
              } else {
                $estimated = false;
                $rowTotal = $bill->GetAmountDue();
              }

              $totalBalance += $rowTotal; //store to display total balance in bottom row
            } else { //at least one bill was already processed, add nothing or deduct portion
              $freq = $bill->GetFrequency();
              $fullPayment = $bill->GetAmountDue();

              //step backwards to figure which portions have been processed
              for($i = $freq, $totalPaymentsProcessed = 0; $i > 0; $i--) {
                if($bill->WasProcessed($i)) { $totalPaymentsProcessed++; }
              }

              $rowTotal = ($totalPaymentsProcessed == $freq) ? 0 : $fullPayment - ($fullPayment * $totalPaymentsProcessed / $freq);

              $totalBalance += $rowTotal;
            }
          }

          if($estimated) { echo '<font color="#FF6600;">'; } //higlight estimated values

          $bill->OutputRowTotal($rowTotal);

          if($estimated) { echo '</font>'; $estimated = false; } //stop highlighting

          
          

          if($bill->InCash() || $bill->InYen()) {
            $cashTotal += $rowTotal;

            if($bill->IsDueThisMonth()) {
              if($bill->InYen()) {
                $thisMonthsCashTotalYen += $rowTotal * Company::GetExchangeRateDB();
              } else {
                $thisMonthsCashTotal += $rowTotal;
              }
            }
          } ?>
        </td>
      </tr>
    <?php $pop++; endforeach; ?>

    <?php NoDataRow($tc, 16); ?>
        
    <tr>
      <?php $col = (!Company::IsLocked()) ? 2 : 3; ?>
      <?php if(!$removeCashTotal) { $col--; } ?>
      <td colspan="<?php echo $col; ?>" id="topandbottomborder">
        <table>
          <tr>
            <td style="border-right: none;">
              <p align="right">
                <br/>Alloted<br/>
                <u>- Total</u><br/>
                <b>= Saved</b>
              </p>
            </td>
            <td <?php echo ($removeCashTotal) ? 'style="border-right: none;"' : ""; ?>>
              <p align="right">
                <?php echo "Bank&nbsp;&nbsp;&nbsp;<br/>$ " . number_format($allotted, 2); ?><br/>
                <u><?php echo "$ " . number_format($totalForMonth, 2);?></u><br/>
                <?php if($left < -0.01) {
                    echo '<b id="error">$ ' . number_format($left, 2) . '</b>';
                  } else {
                    echo '<b id="success">$ ' . number_format($left, 2) . '</b>';
                  } ?>
              </p>
            </td>
            <?php if($removeCashTotal) : ?>
              <td>
                <p align="right">
                  <?php echo "Cash&nbsp;&nbsp;&nbsp;<br/>$ " . number_format($allottedC, 2); ?><br/>
                  <?php echo "<u>$ " . number_format($totalForMonthC, 2) . "</u>"; ?><br/>
                  <?php if($leftC < -0.01) {
                    echo '<b id="error">$ ' . number_format($leftC, 2) . '</b>';
                  } else {
                    echo '<b id="success">$ ' . number_format($leftC, 2) . '</b>';
                  } ?>
                </p>
              </td>
            <?php endif; ?>
          </tr>
        </table>
      </td>
      <?php if(!$removeCashTotal) { echo '<td id="topandbottomborder"></td>'; } ?>
      <?php for($i = 1; $i <= 12; $i++) : ?>
        <td id="topandbottomborder">
          <?php echo number_format($bill->AllotmentWas($i), 2) . '<br/>'; ?>
          <?php echo '<u>' . number_format($bill->AllotmentWas($i) - $bill->SavedWas($i), 2) . '</u><br/>'; ?>

          <?php $savedWas = number_format($bill->SavedWas($i), 2);
          if($savedWas > 0) {
            echo '<b id=success>' . $savedWas . '</b>'; 
          } else {
            echo '<b id=error>(' . $savedWas . ')</b>';
          } ?>
        </td>
      <?php endfor;
      $cashTotalLessThisMonth = ceil($cashTotal - $thisMonthsCashTotal);
      $cashTotalLessThisMonthYen = round($cashTotalLessThisMonth * Company::GetExchangeRateDB(), -2); ?>
      <td colspan="1" id="topandbottomborder" style="text-align: right;" title="<?php echo "Minimum cash balance: $" . number_format($cashTotalLessThisMonth, 0) . " (&#165;" . number_format($cashTotalLessThisMonthYen, 0) . ")"; ?>&#013;(after removing due this month)">
        <?php for($i = 1; $i <= 12; $i++) { $avesaved += $remaining[$i-1]; } ?>
        <input type="hidden" id="MinBalance" name="MinBalance" value="<?php echo $totalBalance+$buffer-(($removeCashTotal) ? $cashTotal : 0); ?>">
        <?php $waitValue = ($bill->LockedThisMonth() && !$_SESSION['ManualUnlock']) ? "--.--" : number_format(round($totalBalance+$buffer-(($removeCashTotal) ? $cashTotal : 0)+0.5,0),2,'.',',');  ?>
        Min.&nbsp;<input type="text" size="6" value="<?php echo $waitValue; ?>" style="background-color: #cccccc;" readonly><br/>
        <u>-Cur.</u>&nbsp;<input type="text" id="CurBalance" name="CurBalance" size="6" <?php if($bill->LockedThisMonth()) { echo ' style="background-color: #cccccc;" readonly value="WAIT"'; } ?>><br/>
        <input type="button" value="=" OnClick="calc();">
        <input type="text" id="Remaining" name="Remaining" size="7" style="background-color: #cccccc;" readonly>         
      </td>
    </tr>
    <tr>
      <?php if(!Company::IsLocked()) : ?>
        <?php $fixColspan = 3; ?>
        <td colspan="2">
          <p align="center">
            <?php if(!Company::IsLocked()) :
              if($_SESSION['db'] == 'DEPRECATED') :
                if($ready) : ?>
                  <input name="store" type="submit" value="Store"/>
                <?php else : ?>
                  <input disabled type="submit" value="Store"/>
                <?php endif; 
              endif; ?>    

              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="action" value="savechanges"/>
              <input type="submit" value="Save"/>
            <?php endif; ?>
          </p>
        </td>
      <?php else :
        $fixColspan = 5;
      endif; ?>

      <td colspan="<?php echo $fixColspan; ?>">
        <p align="center">
          <a href="https://communitybankonline.bankofamerica.com/org/servlet/efs/jsp-ns/login.jsp" target="_blank"><img src="../images/boa.png"/></a>
        </p>
      </td>
      <td colspan="4">
        <p align="center">
          <a href="http://www.chase.com" target="_blank"><img src="../images/chase.gif"/></a>
        </p>
      </td>
      <td colspan="4">
        <p align="center">
          <a href="https://mypay.dfas.mil/mypay.aspx" target="_blank"><img src="../images/mypay.jpg"/></a>
        </p>
      </td>
      <td colspan="3">
        <p align="center">
          <a href="http://www.usaa.com" target="_blank"><img src="../images/usaa.png"/></a>
        </p>
      </td>
    </tr>
  </form>
</table>

<?php include 'view/menu.php'; ?>
<?php include 'view/breakdown.php'; ?>
  
</center>