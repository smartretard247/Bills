<table><tr><td>

<table>
    <tr>
        <th colspan="2">Company Information</th>
    </tr>
    <tr>
        <form action="" method="post">
        <td>Name: </td>
        <td><input name="Name" type="input" value="<?php echo $bill->GetName(); ?>"/></td>
    </tr>
            <tr>
              <?php $decimalPlaces = ($bill->GetCurrency() == "Yen") ? 0 : 2; ?>
                        <td>Initial Amount:</td><td><input type="input" name="Amount" size="8" value="<?php echo number_format($bill->GetYenDue(), $decimalPlaces); ?>"/>
                        <?php $currency = $bill->GetCurrency(); ?>
                        <select name="Currency">
                            <option value="Debit" <?php if($currency == "Debit") { echo "selected"; } ?>>Debit</option>
                            <option value="Credit" <?php if($currency == "Credit") { echo "selected"; } ?>>Credit</option>
                            <option value="Cash" <?php if($currency == "Cash") { echo "selected"; } ?>>Cash</option>
                            <option value="Yen" <?php if($currency == "Yen") { echo "selected"; } ?>>Yen</option>
                        </select>
                        </td>
            </tr>
            <tr>
                <td>Type: </td>
                <td>
                    <select size="5" name="Type" value="<?php echo $type = $bill->GetType(); ?>">
                        <?php foreach($types as $t) {
                            if($t == $type) {
                                echo '<option value="' . $t . '" selected="selected">' . $t . '</option>';
                            } else {
                                echo '<option value="' . $t . '">' . $t . '</option>';
                            }
                        } ?>
                    </select>
                </td>
            </tr>
            <tr>
              <td>Phone:</td><td><input type="input" name="Phone" value="<?php echo $bill->GetPhone(); ?>"/></td>
            </tr>
            <tr><td>Link: </td><td><input name="Link" type="input" value="<?php echo $bill->GetLink(); ?>"/></td></tr>
                    <tr>
                        <td>Recurring: </td>
                        <td>
                            <select name="Recur" value="<?php echo $recur = $bill->IsRecurring(); ?>">
                                    <option value="<?php echo $recur; ?>"><?php echo $recurword = Company::GetRecurringWord($recur); ?></option>
                                    <?php if($recur) : ?><option value="0">No</option>
                                    <?php else : ?><option value="1">Yes</option><?php endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                      <?php $annual = $bill->IsAnnual(); ?>
                        <td>Annual<?php if($annual) { echo "/Month paid"; } ?>: </td>
                        <td>
                            <select name="Annual" value="<?php echo $annual; ?>">
                                    <option value="<?php echo $annual; ?>"><?php echo $annualword = Company::GetAnnualWord($annual); ?></option>
                                    <?php if($annual) : ?>
                                        <option value="0">No</option>
                                    <?php else : ?>
                                        <option value="1">Yes</option>
                                    <?php endif; ?>
                            </select>
                          <?php if($annual) : ?>
                          <?php
                            $year = $bill->GetYearDue();
                            $month = $bill->GetMonthDue();
                          ?>
                          <select name="Due">
                            <?php for($i = 1; $i <= 12; $i++) : ?>
                              <option value="<?php echo $year . "-" . $i . + "-01"; ?>" <?php if($month == $i) { echo "selected"; } ?>><?php echo Company::GetMonthWord($i); ?></option>
                            <?php endfor; ?>
                          </select>
                          <?php endif; ?>
                        </td>
                    </tr>

                    <tr>
                        <td>Frequency: </td>
                        <td>
                            <?php $freq = $bill->GetFrequency(); ?>
                            <select size="1" name="Frequency">
                                <?php for($i = 1; $i < 8; $i++) {
                                    echo '<option value="' . $i . '"';
                                    if($i == $freq) { echo ' selected'; }
                                    echo '>' . Company::GetFreqWord($i) . '</option>';
                                } ?>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                      <td>Notes:</td><td><input type="input" name="Notes" value="<?php echo $bill->GetNotes(); ?>"/></td>
                    </tr>
            <tr>
                <td colspan="2">
                    <p align="right">
                        <input type="hidden" name="ID" value="<?php echo $_POST['ID']; ?>"/>
                        <input type="hidden" name="action" value="company_edit"/>
                        <input type="hidden" name="pending_update" value="1"/>
                        <input type="submit" value="Save"/>
                    </p>
                </td>
        </form>
    </tr>
</table>
      
    </td>
    <td></td>
    <td style="vertical-align: top;">

<table>
  <tr>
    <th>Transactions</th>
    <th>Historical</th>
  </tr>
  <tr>
    <td>
      <?php $dbactionrem = ($_SESSION['db'] == "Bills") ? "db/deltrans.php" : "db/deltrans.php?db=prop"; ?>
      <form action="<?php echo $dbactionrem; ?>" method="post">
        <select name="amt" size="21" style="width: 100px;">
          <?php $transactions = $bill->GetTransactions(); $totalTransactions = 0;
            foreach($transactions as $row) : ?>
              <option style="text-align: right;" value="<?php echo $row['Amount'] . "|" . $row['ID']; ?>">
                <?php $totalTransactions += $row['Amount'];
                  echo "$ " . number_format($row['Amount'],2); ?>
              </option>
          <?php endforeach; ?>
          <?php if($totalTransactions) : ?>
            <option style="text-align: right;">----------</option>
            <option style="text-align: right;"><?php echo "$ " . number_format($totalTransactions,2); ?></option>
          <?php endif; ?>
        </select><br/>
        <input type="hidden" name="cid" value="<?php echo $_POST['ID']; ?>"/>
        <input type="hidden" name="src" value="site"/>
        <input type="submit" value="-"/>
      </form>
      <?php $dbactionadd = ($_SESSION['db'] == "Bills") ? "db/tally.php" : "db/tally.php?db=prop"; ?>
      <form action="<?php echo $dbactionadd; ?>" method="post">
        <input type="hidden" name="id" value="<?php echo $_POST['ID']; ?>"/>
        <input type="hidden" name="meth" value="up"/>
        <input type="hidden" name="src" value="site"/>
        <input name="amount" type="text" style="width: 94px;"/><br/>
        <input type="submit" value="Add"/>
      </form>
    </td>
    <td>
      <select size="24" style="width: 200px;">
        <?php $transactionsHist = $bill->GetTransactionsHistory();
          foreach($transactionsHist as $row) : ?>
            <option style="text-align: right;">
              <?php echo "$ " . number_format($row['Amount'],2); ?>
              <?php echo "\t| " . $row['TransDate']; ?>
            </option>
        <?php endforeach; ?>
      </select>
    </td>
  </tr>
</table>
      
    </td></tr></table>

<br/><a href="?action=company_show">Go Back To Companies</a>
<br/><a href="?">Home</a>
