<?php if(!Company::IsLocked()) : ?>
    <?php $displayAmount = ($bill->InYen()) ? $bill->GetYenDue() : $bill->GetDollarDue(); ?>
    <?php if($bill->IsRecurring()) : ?>
      <input readonly style="background-color: #cccccc;" size="5" name="<?php echo $bill->GetID() . '@'; ?>Amount" type="input" value="<?php echo number_format($displayAmount,$decimalPlaces,'.',''); ?>"/>
    <?php else : ?>
      <input size="5" name="<?php echo $bill->GetID() . '@'; ?>Amount" type="input" value="<?php echo number_format($displayAmount,$decimalPlaces,'.',''); ?>"/>
    <?php endif; ?>
  
<?php endif;