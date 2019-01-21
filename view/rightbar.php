<div id="rightbar">
<div class="barcrumb">
<form name="login" action="../../core/login.php?return=bills" method="post">
    <h3>Log In&nbsp;&nbsp;</h3><br/>
    Username: <input size="16" name="Username" type="text"/><br/>
    Password: <input size="16" name="ThePassword" type="password"/><br/>
    <p align="right"><input type="submit" value="Login"/></p>
</form>
</div>
    <?php if($_SESSION['valid_user']) : ?>
	<div class="barcrumb">
            <a href="../core/logout.php">Logout</a>
	</div>
    <?php endif; ?>
</div>