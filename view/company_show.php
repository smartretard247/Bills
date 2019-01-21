<?php
    $bills = $db->GetTable('company', 'Name');
    StartTable();
    TH('Name');
    TH('Monthly');
    TH('Total');
    TH('Phone');
    TH('Type');
    TH('Currency');
    TH('Link');
    TH('Recurring');
    TH('Annual');
    TH('Frequency');
    TH('GroupID');
    TH('Edit');
    echo    "</tr>" ;

    if($bills) { foreach ($bills as $tbill) : ?>
      <?php 
        $isYen = $tbill['Currency'] == "Yen";
        $currencySymbol = ($isYen) ? "&#165;" : "$";
        $currencyDecimal = ($isYen) ? 0 : 2;
        $name = ($isYen) ? $tbill['Name'] . " (&#165;)" : $tbill['Name'];
        
        switch(Company::GetFreqWord($tbill['Frequency'])) {
          case 'Monthly': $months = 12; break;
          case 'Biannual': $months = 6; break;
          case 'Bimestrial': $months = 1; break;
          case 'Biennial': $months = 24; break;
        }
        $amountTimesFrequency = Company::GetTotalOf($tbill['Amount'], $months);
        
        if(!$isYen) {
          $totalSpentYearly += $amountTimesFrequency;
        }
        
        echo '<tr>';
        echo "<td>$name</td>";
        echo "<td>$currencySymbol" . number_format($tbill['Amount'],$currencyDecimal) . "</td>";
        echo "<td>$currencySymbol" . number_format($amountTimesFrequency,$currencyDecimal) . "</td>";
        echo "<td>" . $tbill['Phone'] . "</td>";
        echo "<td>" . $tbill['Type'] . "</td>";
        echo "<td>" . $tbill['Currency'] . "</td>";
        echo '<td><a href="' . $tbill['Link'] . '" target="_blank">Website</a></td>';
        echo "<td>" . Company::GetRecurringWord($tbill['Recurring']) . "</td>";
        echo "<td>" . Company::GetAnnualWord($tbill['Annual']) . "</td>";
        echo "<td>" . Company::GetFreqWord($tbill['Frequency']) . "</td>";
      ?>

        <td>
          <form action="" method="post">
            <?php $gIdEnabled = ($tbill['Frequency'] == 1) ? "" : " disabled"; ?>
            <input type="text" size="2" name="GroupID" value="<?php echo $tbill['GroupID']; ?>" <?php echo $gIdEnabled; ?>/>
            <input type="hidden" name="ID" value="<?php echo $tbill['ID']; ?>"/>
            <input type="hidden" name="action" value="group"/>
            <input type="submit" value="Set" <?php echo $gIdEnabled; ?>/>
          </form>
        </td>
        <td>
          <form action="" method="post">
            <input type="hidden" name="ID" value="<?php echo $tbill['ID']; ?>"/>
            <input type="hidden" name="action" value="company_edit"/>
            <input type="submit" value="Edit"/>
          </form>  
        </td>
    <?php endforeach; NoDataRow($tbill, 11); } EndTable(); ?>
    
    <br>
    <table style="position: relative; left: 200px;">
      <tr>
        <th>Monthly Spending</th>
        <th>Annual Spending</th>
      </tr>
      <tr>
        <td><?php echo "$" . number_format($totalSpentYearly/12, 2); ?></td>
        <td><?php echo "$" . number_format($totalSpentYearly, 2); ?></td>
      </tr>
    </table>

    <br/>
    <?php insertGoBackURL(); ?>
    <br/>
