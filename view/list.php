<p>Welcome to list!</p>

 <?php
    $items = GetTableAndGroup('company', 'Name', 'Name', $_SESSION['default_store']);
    StartTable();
    TH('Name');
    TH('Add');
    echo    "</tr>" ; ?>
    <form action="" method="post">
    <?php if($items) { foreach ($items as $titem) : ?>
        <?php echo "<tr><td>" . $titem['Name'] . "</td><td>"; ?>
            <?php if($select_all) : ?>
                    <input name="add<?php echo $titem['Barcode']; ?>" type="checkbox" checked="checked"/>
            <?php else : ?>
                    <input name="add<?php echo $titem['Barcode']; ?>" type="checkbox"/>
            <?php endif; ?>
            <input name="<?php echo $titem['Barcode']; ?>" value="<?php echo $titem['Barcode']; ?>" type="hidden"/>
        </td>
    <?php endforeach; } ?>
    </tr>
    <?php NoDataRow($titem, 2) ?>
    <tr>
        <td colspan="2">
            <p align="right">
                    <input type="hidden" name="action" value="SL_add"/>
                    <input name="select_all" type="submit" value="Select All"/>
                    <input name="select_none" type="submit" value="Select None"/>
                    <input name="pending_add" type="submit" value="Add Selected"/>
            </p>
        </td>
    </tr>
    </form>

    <?php EndTable(); ?>

<br/>
<?php insertGoBackURL();