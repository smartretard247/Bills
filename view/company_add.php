<table>
		<form name="add" action="" method="post">
		<tr>
			<th colspan="2">Company Information</th>
		</tr>
		<tr>
			<td>Name:</td><td><input type="input" name="Name" maxlength="25"/></td>
		</tr>
                <tr>
                    <td>Initial Amount:</td><td><input type="input" name="Amount" size="8"/>
                    <select name="Currency">
                        <option value="Debit" selected>Debit</option>
                        <option value="Credit">Credit</option>
                        <option value="Cash">Cash</option>
                        <option value="Yen">Yen</option>
                    </select>
                    </td>
		</tr>
		<tr>
			<td>Type:</td>
			<td>
				<select name="Type" size="5">
					<?php foreach($types as $t) {
                                            echo '<option value="' . $t . '">' . $t . '</option>';
                                        } ?>
				</select>
			</td>
		</tr>
    <tr>
			<td>Link:</td><td><input type="input" name="Link"/></td>
		</tr>
		<tr>
			<td>Recurring:</td><td>
				<select name="Recur" size="2">
					<option value="0" selected>No</option>
					<option value="1">Yes</option>
				</select></td>
		</tr>
                <tr>
			<td>Frequency:</td>
                        <td>
                            <select name="Frequency" size="5">
                                <option value="1" selected>Monthly</option>
                                    <option value="2">Bimonthly</option>
                                    <option value="3">Trimonthly</option>
                                    <option value="4">Bimestrial</option>
                                    <option value="5">Biennial</option>
                                    <option value="6">Once</option>
                                    <option value="7">Biannual</option>
                                    
                            </select>
                        </td>
		</tr>
		<tr>
			<td colspan="2">
				<input name="action" type="hidden" value="company_add"/>
				<input name="pending_add" type="submit" value="Add"/>
			</td>
		</tr>
    </table>
    </form>
<br/>
<?php insertGoBackURL();