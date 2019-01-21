<?php
    $bills = $db->GetTable('company_hist', 'Name');
    StartTable();
    TH('Name');
    TH('Type');
	TH('Link');
	TH('Recurring');
        TH('Annual');
        TH('Notes');
        TH('Restore');
        TH('Delete');
    echo    "</tr>" ;

    if($bills) { foreach ($bills as $tbill) : ?>
        <?php echo "<tr><td>" . $tbill['Name'] . "</td>"; ?>
		<?php echo "<td>" . $tbill['Type'] . "</td>"; ?>
		<?php echo '<td><a href="' . $tbill['Link'] . '" target="_blank">Website</a></td>'; ?>
		<?php echo "<td>";
			if($tbill['Recurring']) echo "Yes";
			else echo "No";
			echo "</td>"; 
		?>
                <?php echo "<td>";
			if($tbill['Annual']) echo "Yes";
			else echo "No";
			echo "</td>"; 
		?>
      <?php echo "<td>" . $tbill['Notes'] . "</td>"; ?>
        <td><form action="" method="post">
			<input type="hidden" name="ID" value="<?php echo $tbill['ID']; ?>"/>
            <input type="hidden" name="action" value="company_restore"/>
            <input type="submit" value="Go"/>
        </form></td>
        <td><form action="" method="post">
            <input type="hidden" name="ID" value="<?php echo $tbill['ID']; ?>"/>
            <input type="hidden" name="action" value="company_delete"/>
            <input type="submit" value="Go"/>
        </form></td>
    <?php endforeach; NoDataRow($tbill, 7); } EndTable(); ?>

    <br/>
    <?php insertGoBackURL();
