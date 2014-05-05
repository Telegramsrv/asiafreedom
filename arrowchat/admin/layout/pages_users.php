			<div class="title_bg"> 
				<div class="title">Users</div> 
				<div class="module_content">
<?php
	if ($do == "logs") 
	{
?>
					<form method="post" action="users.php?do=manageusers" enctype="multipart/form-data">
					<div class="subtitle">Manage Users</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="username">Username</label>
							</dt>
							<dd>
								<input type="text" id="username" class="selectionText" name="username" value="" />
								<p class="explain">
									Enter the username or part of the username that you'd like to search for.  You may also enter the user ID if known.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms[0].submit(); return false">
									<span>Search</span>
								</a>
								<input type="hidden" name="user_search" value="1" />
							</div>
						</dd>
					</dl>
<?php
		if (!empty($_REQUEST['w'])) 
		{
?>
					</form>

				</div>
			</div>
			<div class="title_bg"> 
				<div class="title">Chat</div> 
				<div class="module_content">
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div style="height: 300px; padding: 10px; overflow: auto;" id="chatboxes">
<?php
			$result2 = $db->execute("
				SELECT " . DB_USERTABLE_NAME . ", " . DB_USERTABLE_USERID . " 
				FROM " . TABLE_PREFIX . DB_USERTABLE . " 
				WHERE " . DB_USERTABLE_USERID . " = '" . get_var('w') . "'
			");

			$result3 = $db->execute("
				SELECT " . DB_USERTABLE_NAME . ", " . DB_USERTABLE_USERID . " 
				FROM " . TABLE_PREFIX . DB_USERTABLE . " 
				WHERE " . DB_USERTABLE_USERID . " = '" . get_var('id') . "'
			");
			
			$result = $db->execute("
				SELECT * 
				FROM arrowchat 
				WHERE (arrowchat.to = '" . $db->escape_string(get_var('w')) . "' 
						AND arrowchat.from = '" . $db->escape_string(get_var('id')) . "') 
					OR (arrowchat.to = '" . $db->escape_string(get_var('id')) . "' 
						AND arrowchat.from = '" . $db->escape_string(get_var('w')) . "') 
				ORDER BY id ASC
			");

			if ($result AND $db->count_select() > 0) 
			{
				$row2 = $db->fetch_array($result2);
				$row3 = $db->fetch_array($result3);
				
				while ($row = $db->fetch_array($result)) 
				{
					if ($row['from'] == get_var('w')) 
					{
						if (check_if_guest(get_var('w')))
						{
							$msg_username = $language[83] . " " . substr(get_var('w'), 1);
						}
						else
						{
							$msg_username = $row2[DB_USERTABLE_NAME];
						}
?>					
						<div style="padding:0px 10px 5px 0px; float: left; background-color: #fff; width: 470px;"><a href="users.php?do=logs&id=<?php echo get_var('w'); ?>"><b><?php echo $msg_username; ?></b></a>: <?php echo $row['message']; ?></div><div style="padding:0px 10px 5px; float: right; background-color: #fff; width: 150px;"><?php echo date('M j, Y g:i a', $row['sent']); ?></div><div class="clear"></div>
<?php
					} 
					else 
					{
						if (check_if_guest(get_var('id')))
						{
							$msg_username = $language[83] . " " . substr(get_var('id'), 1);
						}
						else
						{
							$msg_username = $row3[DB_USERTABLE_NAME];
						}
?>
						<div style="padding:0px 10px 5px 0px; float: left; width: 470px;"><a href="users.php?do=logs&id=<?php echo get_var('id'); ?>"><b><?php echo $msg_username; ?></b></a>: <?php echo $row['message']; ?></div><div style="padding: 0px 10px 5px; float: right; width: 150px;"><?php echo date('M j, Y g:i a', $row['sent']); ?></div><div class="clear"></div>
<?php
					}
?>
<?php
				}
			} 
			else 
			{
?>
				Oops, we found no chats to or from these users.
<?php
			}
?>
					</div>
			<script type="text/javascript">
				var objDiv = document.getElementById("chatboxes");
				objDiv.scrollTop = objDiv.scrollHeight;
			</script>
<?php
		}
?>
<?php
		if (!empty($_REQUEST['id'])) 
		{
			if (check_if_guest(get_var('id')))
			{
				$username = $language[83] . " " . substr(get_var('id'), 1);
			}
			else
			{
				$result = $db->execute("
					SELECT " . DB_USERTABLE_NAME . ", " . DB_USERTABLE_USERID . " 
					FROM " . TABLE_PREFIX . DB_USERTABLE . " 
					WHERE " . DB_USERTABLE_USERID . " = '" . get_var('id') . "'
				");
				
				$username = $row[DB_USERTABLE_NAME];
			}
?>
					</form>

				</div>
			</div>
			<div class="title_bg"> 
				<div class="title">Logs</div> 
				<div class="module_content">
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
<?php
			if ($result AND $db->count_select() > 0) 
			{
				$row = $db->fetch_array($result);
?>
					<div class="subtitle"><a href="users.php?do=view&id=<?php echo get_var('id'); ?>"><?php echo $username; ?></a> Chat Logs</div>
					<table cellspacing="0" cellpadding="0" style="margin-bottom: 15px;" class="module_table">
						<tr>
<?php
				$result2 = $db->execute("
					SELECT arrowchat.to 
					FROM arrowchat 
					WHERE arrowchat.from = '" . get_var('id') . "' 
					GROUP BY arrowchat.to
				");

				$i = 1;
				
				if ($result2 AND $db->count_select() > 0) 
				{
					while ($row2 = $db->fetch_array($result2)) 
					{
						if (check_if_guest($row2['to']))
						{
							$username = $language[83] . " " . substr($row2['to'], 1);
						}
						else
						{
							$result3 = $db->execute("
								SELECT " . DB_USERTABLE_NAME . " 
								FROM " . TABLE_PREFIX . DB_USERTABLE . " 
								WHERE " . DB_USERTABLE_USERID . " = '" . $row2['to'] . "'
							");
							
							$row3 = $db->fetch_array($result3);
							
							$username = $row3[DB_USERTABLE_NAME];
						}
?>
							<td style="width:175px; text-align: center;"><a href="users.php?do=logs&id=<?php echo get_var('id'); ?>&w=<?php echo $row2['to']; ?>"><?php echo $username; ?></a></td>
<?php
						if ($i%4 == 0) 
						{
?>
						</tr>
						<tr>
<?php
						}
?>
<?php
						$i++;
					}
				} 
				else 
				{
?>
						<td>This user has never sent any chats.</td>
<?php
				}
			} 
			else 
			{
?>
						There is no user with that ID.
<?php
			}
?>
						</tr>
					</table>
					</form>
				</div>
			</div>
			<div class="title_bg"> 
				<div class="title">Last 20 Messages</div> 
				<div class="module_content">
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<table cellspacing="0" cellpadding="0" class="module_table">
<?php
			$result = $db->execute("
				SELECT * 
				FROM arrowchat 
				WHERE arrowchat.from = '" . $db->escape_string(get_var('id')) . "' 
				ORDER BY arrowchat.id DESC 
				LIMIT 20
			");

			if ($result AND $db->count_select() > 0) 
			{
?>
						<tr>
							<td style="width: 175px;" class="row2">Sent To</td>
							<td style="width: 525px;" class="row2">Message</td>
						</tr>
<?php
				while ($row = $db->fetch_array($result)) 
				{
					if (check_if_guest($row['to']))
					{
						$usertable_name = $language[83] . " " . substr($row['to'], 1);
					}
					else
					{
						$result3 = $db->execute("
							SELECT " . DB_USERTABLE_NAME . " 
							FROM " . TABLE_PREFIX . DB_USERTABLE . " 
							WHERE " . DB_USERTABLE_USERID . " = '" . $row['to'] . "'
						");
						
						$row3 = $db->fetch_array($result3);
						$usertable_name = $row3[DB_USERTABLE_NAME];
					}
?>
						<tr>
							<td class="row1" style="padding-top: 3px; padding-bottom: 3px;"><a href="users.php?do=logs&id=<?php echo $row['to']; ?>"><?php echo $usertable_name; ?></a></td>
							<td class="row1" style="padding-top: 3px; padding-bottom: 3px;"><?php echo $row['message']; ?></td>
						</tr>
<?php
				}
			} 
			else 
			{
?>
						This user has no messages in the database.
<?php
			}
?>
					</table>
<?php
		}
	}
?>

<?php
	if ($do == "view") 
	{
?>
					<form method="post" action="users.php?do=manageusers" enctype="multipart/form-data">
					<div class="subtitle">Manage Users</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="username">Username</label>
							</dt>
							<dd>
								<input type="text" id="username" class="selectionText" name="username" value="" />
								<p class="explain">
									Enter the username or part of the username that you'd like to search for.  You may also enter the user ID if known.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms[0].submit(); return false">
									<span>Search</span>
								</a>
								<input type="hidden" name="user_search" value="1" />
							</div>
						</dd>
					</dl>
<?php
		if (!empty($_REQUEST['id'])) 
		{
			$request_id = get_var('id');
			
			if (check_if_guest(get_var('id')))
			{
				$username = $language[83] . " " . substr(get_var('id'), 1);
			}
			else
			{
				$result = $db->execute("
					SELECT " . DB_USERTABLE_NAME . ", " . DB_USERTABLE_USERID . " 
					FROM " . TABLE_PREFIX . DB_USERTABLE . " 
					WHERE " . DB_USERTABLE_USERID . " = '" . $db->escape_string($request_id) . "'
				");

				$row = $db->fetch_array($result);
				$username = $row[DB_USERTABLE_NAME];
			}
			
			$result = $db->execute("
				SELECT * 
				FROM arrowchat_status 
				WHERE userid = '" . $db->escape_string($request_id) . "'
			");

			$row = $db->fetch_array($result);
			
			$status = $row['status'];
			$is_admin = $row['is_admin'];
			$hide_bar = $row['hide_bar'];
			$play_sound = $row['play_sound'];
			$window_open = $row['window_open'];
			$only_names = $row['only_names'];
			$announcement = $row['announcement'];
			
			if ($status == "available" OR empty($status)) 
			{
				if ((time() - $row['session_time']) < $online_timeout)
				{
					$status = "Available";
				}
				else
				{
					$status = "Offline";
				}
			}
?>
					</form>

				</div>
			</div>
			<div class="title_bg"> 
				<div class="title">Edit <?php echo $username; ?> ( <?php echo $status; ?> )</div> 
				<div class="module_content">
					<form method="post" id="edit-user" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>&id=<?php echo get_var('id'); ?>" enctype="multipart/form-data">
					<div class="subtitle">Edit <?php echo $username; ?></div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="is_admin">
											<input type="checkbox" id="is_admin" name="is_admin" <?php if($row['is_admin'] == 1) echo 'checked="checked"';  ?> value="1" />
											Make Administrator
										</label>
									</li>
								</ul>
								<p class="explain">
									Makes this user have administrator privileges.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="hide_bar">
											<input type="checkbox" id="hide_bar" name="hide_bar" <?php if($row['hide_bar'] == 1) echo 'checked="checked"';  ?> value="1" />
											Hide Bar
										</label>
									</li>
								</ul>
								<p class="explain">
									Hide or show the ArrowChat bar for this user.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="play_sound">
											<input type="checkbox" id="play_sound" name="play_sound" <?php if($row['play_sound'] == 1) echo 'checked="checked"';  ?> value="1" />
											Play Chat Sounds
										</label>
									</li>
								</ul>
								<p class="explain">
									Choose whether this user has sounds enabled or disabled.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="window_open">
											<input type="checkbox" id="window_open" name="window_open" <?php if($row['window_open'] == 1) echo 'checked="checked"';  ?> value="1" />
											Buddy List Window Opened
										</label>
									</li>
								</ul>
								<p class="explain">
									If checked, the user's buddy list will stay open.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="only_names">
											<input type="checkbox" id="only_names" name="only_names" <?php if($row['only_names'] == 1) echo 'checked="checked"';  ?> value="1" />
											Show Only Names
										</label>
									</li>
								</ul>
								<p class="explain">
									If checked, the user's buddy list, chat, and chat rooms will not show avatars.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label for="announcement">
											<input type="checkbox" id="announcement" name="announcement" <?php if($row['announcement'] == 1) echo 'checked="checked"';  ?> value="1" />
											Hide Announcement Message
										</label>
									</li>
								</ul>
								<p class="explain">
									If checked, this will mark the user's announcement as read and it will not display.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="username">View Logs</label>
							</dt>
							<dd style="margin-top: 7px;">
								<a href="users.php?do=logs&id=<?php echo get_var('id'); ?>">Click Here</a>
								<p class="explain">
									
								</p>
							</dd>
						</dl>
					<?php
						if (check_if_guest(get_var('id'))) {
					?>
						<dl class="selectionBox">
							<dt>
								<label for="username">Guest Name</label>
							</dt>
							<dd style="margin-top: 7px;">
								<a href="users.php?do=view&id=<?php echo get_var('id'); ?>&guest_name=1">Delete User's Guest Name</a>
								<p class="explain">
									Click the link above will remove the user's guest name and they will have the option to change it again.
								</p>
							</dd>
						</dl>
					<?php
						}
					?>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms['edit-user'].submit(); return false">
									<span>Save Changes</span>
								</a>
								<input type="hidden" name="user_id" value="<?php echo get_var('id'); ?>" />
								<input type="hidden" name="user_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
		}
	}
?>

<?php
	if ($do == "manageusers") 
	{
?>
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div class="subtitle">Manage Users</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="username">Username</label>
							</dt>
							<dd>
								<input type="text" id="username" class="selectionText" name="username" value="" />
								<p class="explain">
									Enter the username or part of the username that you'd like to search for.  You may also enter the user ID if known.
								</p>
							</dd>
						</dl>
						<dl class="selectionBox">
							<dt>
								<label for="guest_id">Guest ID/Name</label>
							</dt>
							<dd>
								<input type="text" id="guest_id" class="selectionText" name="guest_id" value="" />
								<p class="explain">
									Enter the guest ID or guest name that you would like to search for.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms[0].submit(); return false">
									<span>Search</span>
								</a>
								<input type="hidden" name="user_search" value="1" />
							</div>
						</dd>
					</dl>
<?php
		if (!empty($_POST['user_search'])) 
		{
			$result = $db->execute("
				SELECT " . DB_USERTABLE_NAME . ", " . DB_USERTABLE_USERID . " 
				FROM " . TABLE_PREFIX . DB_USERTABLE . " 
				WHERE LOWER(" . DB_USERTABLE_NAME . ") 
				LIKE '%" . $db->escape_string(strtolower(get_var('username'))) . "%'
					OR " . DB_USERTABLE_USERID . " = '" . $db->escape_string(strtolower(get_var('username'))) . "'
				ORDER BY " . DB_USERTABLE_NAME . " ASC 
				LIMIT 50
			");
?>
					</form>

				</div>
			</div>
			<div class="title_bg"> 
				<div class="title">Results</div> 
				<div class="module_content">
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div class="subtitle">Search Results</div>
					<h2 class="subHeading">Results</h2>
					<ol class="scrollable">
<?php
			if ($result AND $db->count_select() > 0 AND empty($_POST['guest_id'])) 
			{
				while ($row = $db->fetch_array($result)) 
				{
?>
						<li class="listItem">
							<a href="users.php?do=logs&id=<?php echo $row[DB_USERTABLE_USERID]; ?>" class="secondaryContent">Logs</a>
							<a href="users.php?do=view&id=<?php echo $row[DB_USERTABLE_USERID]; ?>" class="secondaryContent">Edit</a>
							<h4>
								<a href="users.php?do=view&id=<?php echo $row[DB_USERTABLE_USERID]; ?>">
									<?php echo $row[DB_USERTABLE_USERID]; ?>&nbsp;&nbsp;&nbsp;<?php echo $row[DB_USERTABLE_NAME]; ?>
								</a>
							</h4>
						</li>
<?php
				}
			}
			else if (!empty($_POST['guest_id']))
			{
				$result = $db->execute("
					SELECT userid, guest_name
					FROM arrowchat_status 
					WHERE userid 
							LIKE 'g" . $db->escape_string(strtolower(get_var('guest_id'))) . "%'
						OR LOWER(guest_name)
							LIKE '%" . $db->escape_string(strtolower(get_var('guest_id'))) . "%'
					ORDER BY userid ASC 
					LIMIT 50
				");
				
				if ($result AND $db->count_select() > 0) 
				{
					while ($row = $db->fetch_array($result)) 
					{
?>
						<li class="listItem">
							<a href="users.php?do=logs&id=<?php echo $row['userid']; ?>" class="secondaryContent">Logs</a>
							<a href="users.php?do=view&id=<?php echo $row['userid']; ?>" class="secondaryContent">Edit</a>
							<h4>
								<a href="users.php?do=view&id=<?php echo $row['userid']; ?>">
									<?php echo $row['userid']; ?>&nbsp;&nbsp;&nbsp;<?php if (!empty($row['guest_name'])) echo $row['guest_name']; else echo "Guest " . substr($row['userid'], 1); ?>
								</a>
							</h4>
						</li>
<?php
					}
				}
				else
				{
?>
						<li class="listItem">
							<h4>
								<a href="#">
									We didn't find any guests with that ID.
								</a>
							</h4>
						</li>
<?php
				}
?>
<?php
			}
			else
			{
?>
						<li class="listItem">
							<h4>
								<a href="#">
									We didn't find any usernames matching that search.
								</a>
							</h4>
						</li>
<?php
			}
?>
					</ol>
					</form>
<?php
		}
	}
?>
<?php
	if ($do == "banusernames") 
	{
?>
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div class="subtitle">Ban Usernames</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="ban_username">Username(s)</label>
							</dt>
							<dd>
								<textarea id="ban_username" class="selectionArea" name="ban_username"></textarea>
								<p class="explain">
									Enter each username you would like to ban <b>separated by a new line</b>.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms[0].submit(); return false">
									<span>Ban Usernames</span>
								</a>
								<input type="hidden" name="ban_username_submit" value="1" />
							</div>
						</dd>
					</dl>
					</form>
				</div>
			</div>
			<div class="title_bg"> 
				<div class="title">Remove Bans</div> 
				<div class="module_content">
					<form method="post" id="remove-ban" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div class="subtitle">Remove Bans</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="unban_username">Username(s)</label>
							</dt>
							<dd>
								<select multiple="multiple" id="unban_username" name="unban_username[]" style="width: 440px; height: 125px;">
<?php
		$result = $db->execute("
			SELECT * 
			FROM arrowchat_banlist 
			WHERE ban_userid IS NOT NULL
		");

		if ($result AND $db->count_select() > 0) 
		{
			while ($row = $db->fetch_array($result)) 
			{
				$result2 = $db->execute("
					SELECT " . DB_USERTABLE_NAME . " 
					FROM " . TABLE_PREFIX . DB_USERTABLE . " 
					WHERE " . DB_USERTABLE_USERID . " = '" . $row['ban_userid'] . "'
				");

				$row2 = $db->fetch_array($result2);
?>
									<option value="<?php echo $row['ban_id']; ?>"><?php echo $row2[DB_USERTABLE_NAME]; ?></option>
<?php
			}
		} 
		else 
		{
?>
									<option value="0">There are no banned usernames</option>
<?php
		}
?>
								</select>
								<p class="explain">
									Select the usernames that you would like to unban. Hold Ctrl to select multiple names.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms['remove-ban'].submit(); return false">
									<span>Remove Bans</span>
								</a>
								<input type="hidden" name="unban_username_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
	}
?>
<?php
	if ($do == "banip") 
	{
?>
					<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div class="subtitle">Ban IP Addresses</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="ban_ip">IP Address(es)</label>
							</dt>
							<dd>
								<textarea id="ban_ip" class="selectionArea" name="ban_ip"></textarea>
								<p class="explain">
									Enter each IP address you would like to ban <b>separated by a new line</b>.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms[0].submit(); return false">
									<span>Ban IP Addresses</span>
								</a>
								<input type="hidden" name="ban_ip_submit" value="1" />
							</div>
						</dd>
					</dl>
					</form>
				</div>
			</div>
			<div class="title_bg"> 
				<div class="title">Remove Bans</div> 
				<div class="module_content">
					<form method="post" id="remove-ban" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?do=<?php echo $do; ?>" enctype="multipart/form-data">
					<div class="subtitle">Remove Bans</div>
					<fieldset class="firstFieldset">
						<dl class="selectionBox">
							<dt>
								<label for="unban_ip">IP Address(es)</label>
							</dt>
							<dd>
								<select multiple="multiple" id="unban_ip" name="unban_ip[]" style="width: 440px; height: 125px;">
<?php
		$result = $db->execute("
			SELECT * 
			FROM arrowchat_banlist 
			WHERE ban_ip IS NOT NULL
		");

		if ($result AND $db->count_select() > 0) 
		{
			while ($row = $db->fetch_array($result)) 
			{
?>
									<option value="<?php echo $row['ban_id']; ?>"><?php echo $row['ban_ip']; ?></option>
<?php
			}
		} 
		else 
		{
?>
									<option value="0">There are no banned IP addresses</option>
<?php
		}
?>
								</select>
								<p class="explain">
									Select the usernames that you would like to unban. Hold Ctrl to select multiple names.
								</p>
							</dd>
						</dl>
					</fieldset>
					<dl class="selectionBox submitBox">
						<dt></dt>
						<dd>
							<div class="floatr">
								<a class="fwdbutton" onclick="document.forms['remove-ban'].submit(); return false">
									<span>Remove Bans</span>
								</a>
								<input type="hidden" name="unban_ip_submit" value="1" />
							</div>
						</dd>
					</dl>
<?php
	}
?>
					
					</form>

				</div>
			</div>