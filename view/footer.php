    </div><!-- end main -->
        <div id="footer">
            <a href="../bills/core/logout.php">Logout</a><br/><br/>
            <?php if($_SESSION['admin_enabled']) : ?>
            <b>
                <a href="../">Go Back To Server</a>
            </b>
            <br/>
            <?php endif;
                include 'core/debug.php'; 
            ?>
            <p class="copyright">
		&copy; <?php echo date("Y"); ?> Jesse Young
            </p>
        </div>
        <?php $_SESSION['error_message'] = ''; $_SESSION['message'] = ''; ?>
    </div><!-- end page -->
    </body>
</html>