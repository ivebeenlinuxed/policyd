<?php
# Policy group member change
# Copyright (C) 2008, LinuxRulz
# 
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.



include_once("includes/header.php");
include_once("includes/footer.php");
include_once("includes/db.php");



$db = connect_db();



printHeader(array(
		"Tabs" => array(
			"Back to groups" => "policy-group-main.php",
			"Back to members" => "policy-group-member-main.php?policy_group_id=".$_POST['policy_group_id'],
		),
));



# Display change screen
if ($_POST['action'] == "change") {

	# Check a policy was selected
	if (isset($_POST['policy_group_member_id'])) {
		# Prepare statement
		$stmt = $db->prepare('SELECT ID, Member, Disabled FROM policy_group_members WHERE ID = ?');
?>
		<h1>Update Policy Group Member</h1>

		<form action="policy-group-member-change.php" method="post">
			<div>
				<input type="hidden" name="action" value="change2" />
				<input type="hidden" name="policy_group_id" value="<?php echo $_POST['policy_group_id']; ?>" />
				<input type="hidden" name="policy_group_member_id" value="<?php echo $_POST['policy_group_member_id']; ?>" />
			</div>
<?php

			$res = $stmt->execute(array($_POST['policy_group_member_id']));

			$row = $stmt->fetchObject();
?>
			<table class="entry" style="width: 75%;">
				<tr>
					<td></td>
					<td class="entrytitle textcenter">Old Value</td>
					<td class="entrytitle textcenter">New Value</td>
				</tr>
				<tr>
					<td class="entrytitle">Member</td>
					<td class="oldval"><?php echo $row->member ?></td>
					<td><input type="text" name="policy_group_member_member" /></td>
				</tr>
				<tr>
					<td class="entrytitle">Disabled</td>
					<td class="oldval"><?php echo $row->disabled ? 'yes' : 'no' ?></td>
					<td>
						<select name="policy_group_member_disabled">
							<option value="">--</option>
							<option value="0">No</option>
							<option value="1">Yes</option>
						</select>		
					</td>
				</tr>
			</table>
	
			<p />
			<div class="textcenter">
				<input type="submit" />
			</div>
		</form>
<?php
	} else {
?>
		<div class="warning">No policy selected</div>
<?php
	}
	
	
	
# SQL Updates
} elseif ($_POST['action'] == "change2") {
?>
	<h1>Policy Group Member Update Results</h1>
<?
	$updates = array();

	if (!empty($_POST['policy_group_member_member'])) {
		array_push($updates,"Member = ".$db->quote($_POST['policy_group_member_member']));
	}
	if (isset($_POST['policy_group_member_disabled']) && $_POST['policy_group_member_disabled'] != "") {
		array_push($updates ,"Disabled = ".$db->quote($_POST['policy_group_member_disabled']));
	}

	# Check if we have updates
	if (sizeof($updates) > 0) {
		$updateStr = implode(', ',$updates);

		$res = $db->exec("UPDATE policy_group_members SET $updateStr WHERE ID = ".$db->quote($_POST['policy_group_member_id']));
		if ($res) {
?>
			<div class="notice">Policy group member updated</div>
<?php
		} else {
?>
			<div class="warning">Error updating policy group member!</div>
<?php
		}

	} else {
?>
		<div class="warning">No changes made to policy group member</div>
<?php
	}

} else {
?>
	<div class="warning">Invalid invocation</div>
<?php
}


printFooter();


# vim: ts=4
?>
