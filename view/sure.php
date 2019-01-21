<p>Are you sure you want to delete this company permanently? </p>
    
    <form action="" method="post">
        <input name="action" type="hidden" value="company_delete"/>
        <input type="hidden" name="ID" value="<?php echo $_POST['ID']; ?>"/>
        <input name="conf_delete" type="hidden" value="1"/>
	<input type="submit" value="Yes"/>
    </form><form action="" method="post">
	<input type="submit" value="Cancel"/>
    </form>
<br/>
<?php insertGoBackURL();