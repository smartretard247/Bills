<?php
    $bills = $db->GetTable('company', 'Name');
    StartTable();
    TH('Name');
    TH('Type');
    TH('Link');
    TH('Notes');
    TH('Remove');
    echo    "</tr>" ; ?>
    <form action="" method="post">
    <?php if($bills) { foreach ($bills as $tbill) : ?>
		<?php echo '<tr><td style="border-bottom: thin dotted black;">' . $tbill['Name'] . '</td>'; ?>
        <?php echo '<td style="border-bottom: thin dotted black;">' . $tbill['Type'] . '</td>'; ?>
        <?php echo '<td style="border-bottom: thin dotted black;"><a href="' . $tbill['Link'] . '" target="_blank">Website</a></td>'; ?>
        <?php echo '<td style="border-bottom: thin dotted black;">' . $tbill['Notes'] . '</td>'; ?>
        <?php echo '<td style="border-bottom: thin dotted black;">'; ?>
	<?php if($select_all) : ?>
            <input name="remove<?php echo $tbill['ID']; ?>" type="checkbox" checked="checked"/>
	<?php else : ?>
            <input name="remove<?php echo $tbill['ID']; ?>" type="checkbox"/>
	<?php endif; ?>
            <input name="<?php echo $tbill['ID']; ?>" value="<?php echo $tbill['ID']; ?>" type="hidden"/>
        </td>
    <?php endforeach; } ?>
    </tr>
    <?php NoDataRow($tbill, 5) ?>
    <tr>
        <td colspan="5">
			<p align="right">
				<input type="hidden" name="action" value="company_remove"/>
				<input name="select_all" type="submit" value="Select All"/>
				<input name="select_none" type="submit" value="Select None"/>
				<input name="pending_removal" type="submit" value="Remove Selected"/>
			</p>
        </td>
    </tr>
    </form>

    <?php EndTable(); ?>

<br/>
<?php insertGoBackURL();
