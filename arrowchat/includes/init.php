<?php

	/*
	|| #################################################################### ||
	|| #                             ArrowChat                            # ||
	|| # ---------------------------------------------------------------- # ||
	|| #    Copyright ©2010-2012 ArrowSuites LLC. All Rights Reserved.    # ||
	|| # This file may not be redistributed in whole or significant part. # ||
	|| # ---------------- ARROWCHAT IS NOT FREE SOFTWARE ---------------- # ||
	|| #   http://www.arrowchat.com | http://www.arrowchat.com/license/   # ||
	|| #################################################################### ||
	*/
	
	// Deny hacking attempt
	if (!IN_ARROWCHAT)
	{
		exit;
	}
	
	// ############################ INITILIZATION ############################
	$userid 				= get_user_id();
	$status					= '';
	$settings 				= '';
	$popout					= '99';
	$hide_bar				= '0';
	$play_sound				= '1';
	$window_open			= '0';
	$chatroom_window		= '-1';
	$chatroom_stay			= '-1';
	$chatroom_block_chats	= '0';
	$only_names				= '0';
	$unfocus_chat			= '0';
	$focus_chat				= '0';
	$is_admin				= '0';
	$apps_bookmarks			= '';
	$apps_other				= '';
	$hash_id				= '';
	$block_chats 			= NULL;
	$chatroom_sound 		= '1';
	$apps_open 				= NULL;
	$guest_name 			= '';
	
	// Create a guest user ID if enabled
	if (!logged_in($userid) AND $guests_can_chat == 1)
	{
		$userid = check_guest_hash();
	}

	// ############################ GET USER DATA ############################
	if (logged_in($userid)) 
	{
		$result = $db->execute("
			SELECT theme, status, popout, hash_id, hide_bar, is_admin, play_sound, window_open, chatroom_window, chatroom_stay, chatroom_block_chats, only_names, unfocus_chat, focus_chat, apps_bookmarks, apps_other, block_chats, chatroom_sound, apps_open, guest_name
			FROM arrowchat_status 
			WHERE userid = '" . $db->escape_string($userid) . "'
		");
		
		if ($result AND $db->count_select() > 0)
		{
			$row = $db->fetch_array($result);
			
			if (isset($row['popout'])) 
			{
				$popout = $row['popout'];
				$popout_time = $popout;
				
				if ((time() - $popout < ($buddy_list_heart_beat +7)) AND $popout != "99") 
				{
					$popout = 1;
				}
			}
			
			if (isset($row['hide_bar']))
			{
				$hide_bar = $row['hide_bar'];
			}
			
			if (isset($row['play_sound']))
			{
				$play_sound = $row['play_sound'];
			}
			
			if (isset($row['guest_name']))
			{
				$guest_name = $row['guest_name'];
			}
			
			if (isset($row['window_open']))
			{
				$window_open = $row['window_open'];
			}
			
			if (isset($row['chatroom_window']))
			{
				$chatroom_window = $row['chatroom_window'];
			}
			
			if (isset($row['chatroom_stay']))
			{
				$chatroom_stay = $row['chatroom_stay'];
			}
			
			if (isset($row['apps_open']))
			{
				$apps_open = $row['apps_open'];
			}
			
			if (isset($row['chatroom_block_chats']))
			{
				$chatroom_block_chats = $row['chatroom_block_chats'];
			}
			
			if (isset($row['chatroom_sound']))
			{
				$chatroom_sound = $row['chatroom_sound'];
			}
			
			if (isset($row['only_names']))
			{
				$only_names = $row['only_names'];
			}
			
			if (isset($row['is_admin']))
			{
				$is_admin = $row['is_admin'];
			}
			
			if (isset($row['theme']) AND $theme_change_on == 1 AND ($chat_maintenance != 1 OR ($admin_view_maintenance == 1 AND $is_admin == 1)))
			{
				$theme 	= $row['theme'];
			}	
			
			if (!empty($row['unfocus_chat'])) 
			{
				$unfocus_chat = $row['unfocus_chat'];
				$unfocus_chat = explode(":", $unfocus_chat);
			}
			
			if (!empty($row['hash_id'])) 
			{
				$hash_id = $row['hash_id'];
			} 
			else 
			{
				$hash_id = random_string();
				
				$db->execute("
					UPDATE arrowchat_status 
					SET hash_id='" . $hash_id . "' 
					WHERE userid = '" . $db->escape_string($userid) . "'
				");
			}
			
			if (!empty($row['focus_chat']))
			{
				$focus_chat = $row['focus_chat'];
			}
			
			if (!empty($row['apps_bookmarks']))
			{
				$apps_bookmarks = $row['apps_bookmarks'];
			}
			
			if (!empty($row['apps_other']))
			{
				$apps_other = $row['apps_other'];
			}
			
			if (!empty($row['status']))
			{
				$status = $row['status'];
			}
			
			if (!empty($row['block_chats']))
			{
				$block_chats = $row['block_chats'];
			}
		} 
		else 
		{
			$hash_id = random_string();
			
			$db->execute("
				INSERT INTO arrowchat_status (userid, chatroom_window, chatroom_stay, hash_id, session_time) 
				VALUES ('" . $db->escape_string($userid) . "', '-1', '-1', '" . $hash_id . "', '" . time() . "')
			");
		}
		
	}

?>