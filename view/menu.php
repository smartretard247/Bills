<?php if($_SESSION['admin_enabled']) : ?>
<br/>
<table>
    <tr>
        <td>
            <form name="add" action="core/add.php" method="post">
              <input type="input" name="Amount" size="8" title="Quick add"/><input type="submit" value="+"/>
            </form>
        </td>

        <td>
            <input type="button" value="Add a Company" onclick="window.location='?action=company_add'"/>
        </td>

        <td>
            <input type="button" value="Remove a Company" onclick="window.location='?action=company_remove'"/>
        </td>

        <td>
            <input type="button" value="Restore/Delete a Company" onclick="window.location='?action=company_restore'"/>
        </td>

        <td>
            <input type="button" value="View Companies" onclick="window.location='?action=company_show'"/>
        </td>
    </tr>
    <tr>
      <?php if($_SESSION['db'] == 'Bills') { $verbage = ' allotment '; } else { $verbage = ' rental income '; } ?>
      <?php if(!Company::IsLocked()) : ?>
        <td colspan="2">
          <form action="" method="post">Exchange Rate:
            <input type="hidden" name="action" value="change_exch"/>
            <input type="text" size="7" value="<?php echo Company::GetExchangeRateDB(); ?>" name="new_exch" />
            <input value="Set" type="submit"/>
          </form>
        </td>
        <td colspan="3">
          <form action="" method="post">Change monthly<?php echo $verbage; ?>to (bank/cash):
            <input type="hidden" name="action" value="change_allt"/>
            <input type="text" size="7" value="<?php echo $bill->GetAllotment(); ?>" name="new_allt" />
            <input type="text" size="7" value="<?php echo $bill->GetCashAllotment(); ?>" name="new_cash_allt" />
            <input value="Set" type="submit"/>
          </form>
        </td>
      <?php else : ?>
        <td colspan="5">
          <b><?php echo $strWillUnlockDate; ?></b>
        </td>
      <?php endif; ?>
    </tr>

</table><br/>
<?php endif; ?>