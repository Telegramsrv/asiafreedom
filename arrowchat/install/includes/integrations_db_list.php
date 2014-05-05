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

	// The list of integrations to be displayed for installation
	// Keys:
	// 		0 - User Table
	// 		1 - Username field
	// 		2 - User ID field
	// 		3 - Avatar field
	// 		4 - Friend table
	//		5 - Friend user requester field
	//		6 - Friend user requestee field
	//		7 - Friend is confirmed field

	$installs = array(
				"buddypress" => array(0 => "users", 1 => "display_name", 2 => "ID", 3 => "user_email", 4 => "bp_friends", 5 => "initiator_user_id", 6 => "friend_user_id", 7 => "is_confirmed"),
				"burningboard" => array(0 => "user", 1 => "username", 2 => "userID", 3 => "userID", 4 => "user_follow", 5 => "userID", 6 => "followUserID", 7 => "Not Required"),
				"cbuilder" => array(0 => "users", 1 => "name", 2 => "id", 3 => "avatar", 4 => "comprofiler_members", 5 => "memberid", 6 => "referenceid", 7 => "accepted"),
				"concrete5" => array(0 => "Users", 1 => "uName", 2 => "uID", 3 => "uHasAvatar", 4 => "UsersFriends", 5 => "uID", 6 => "friendUID", 7 => "status"),
				"datalifeengine" => array(0 => "users", 1 => "name", 2 => "user_id", 3 => "foto", 4 => "Not Required", 5 => "Not Required", 6 => "Not Required", 7 => "Not Required"),
				"datingpro" => array(0 => "users", 1 => "nickname", 2 => "id", 3 => "user_logo", 4 => "users_lists", 5 => "id_user", 6 => "id_dest_user", 7 => "status"),
				"dolphin" => array(0 => "Profiles", 1 => "NickName", 2 => "ID", 3 => "Avatar", 4 => "friends", 5 => "profile", 6 => "ID", 7 => "Not Required"),
				"drupal" => array(0 => "users", 1 => "name", 2 => "uid", 3 => "uid", 4 => "Not Required", 5 => "Not Required", 6 => "Not Required", 7 => "Not Required"),
				"dzoic" => array(0 => "members", 1 => "username", 2 => "mem_id", 3 => "photo_small", 4 => "Not Required", 5 => "Not Required", 6 => "Not Required", 7 => "Not Required"),
				"e107" => array(0 => "user", 1 => "user_name", 2 => "user_id", 3 => "user_image", 4 => "Not Required", 5 => "Not Required", 6 => "Not Required", 7 => "Not Required"),
				"easysocial" => array(0 => "users", 1 => "name", 2 => "id", 3 => "id", 4 => "social_friends", 5 => "actor_id", 6 => "target_id", 7 => "state"),
				"elgg" => array(0 => "users_entity", 1 => "username", 2 => "guid", 3 => "time_created", 4 => "Not Required", 5 => "Not Required", 6 => "Not Required", 7 => "Not Required"),
				"expressionengine" => array(0 => "members", 1 => "screen_name", 2 => "member_id", 3 => "avatar_filename", 4 => "Not Required", 5 => "Not Required", 6 => "Not Required", 7 => "Not Required"),
				"ilias" => array(0 => "usr_data", 1 => "firstname", 2 => "usr_id", 3 => "usr_id", 4 => "Not Required", 5 => "Not Required", 6 => "Not Required", 7 => "Not Required"),
				"ipboard" => array(0 => "members", 1 => "members_display_name", 2 => "member_id", 3 => "pp_thumb_photo", 4 => "profile_friends", 5 => "friends_member_id", 6 => "friends_friend_id", 7 => "friends_approved"),
				"jamroom" => array(0 => "user", 1 => "user_nickname", 2 => "user_id", 3 => "user_band_id", 4 => "band_fans", 5 => "band_id", 6 => "fan_id", 7 => "fan_status"),
				"jcow" => array(0 => "accounts", 1 => "fullname", 2 => "id", 3 => "avatar", 4 => "friends", 5 => "uid", 6 => "fid", 7 => "Not Required"),
				"jomsocial" => array(0 => "users", 1 => "name", 2 => "id", 3 => "thumb", 4 => "community_connection", 5 => "connect_from", 6 => "connect_to", 7 => "status"),
				"jomwall" => array(0 => "users", 1 => "name", 2 => "id", 3 => "id", 4 => "awd_connection", 5 => "connect_from", 6 => "connect_to", 7 => "status"),
				"joomla" => array(0 => "users", 1 => "name", 2 => "id", 3 => "email", 4 => "Not Required", 5 => "Not Required", 6 => "Not Required", 7 => "Not Required"),
				"kunena" => array(0 => "users", 1 => "name", 2 => "id", 3 => "id", 4 => "Not Required", 5 => "Not Required", 6 => "Not Required", 7 => "Not Required"),
				"mediawiki" => array(0 => "user", 1 => "user_name", 2 => "user_id", 3 => "user_id", 4 => "Not Required", 5 => "Not Required", 6 => "Not Required", 7 => "Not Required"),
				"moosocial" => array(0 => "users", 1 => "name", 2 => "id", 3 => "avatar", 4 => "friends", 5 => "user_id", 6 => "friend_id", 7 => "Not Required"),
				"mybb" => array(0 => "users", 1 => "username", 2 => "uid", 3 => "avatar", 4 => "Not Required", 5 => "Not Required", 6 => "Not Required", 7 => "Not Required"),
				"offiria" => array(0 => "users", 1 => "username", 2 => "id", 3 => "params", 4 => "Not Required", 5 => "Not Required", 6 => "Not Required", 7 => "Not Required"),
				"osdate" => array(0 => "user", 1 => "username", 2 => "id", 3 => "id", 4 => "Not Required", 5 => "Not Required", 6 => "Not Required", 7 => "Not Required"),
				"oxwall" => array(0 => "base_user", 1 => "username", 2 => "id", 3 => "hash", 4 => "friends_friendship", 5 => "userId", 6 => "friendId", 7 => "status"),
				"phpbb" => array(0 => "users", 1 => "username", 2 => "user_id", 3 => "user_avatar", 4 => "zebra", 5 => "user_id", 6 => "zebra_id", 7 => "friend"),
				"phpfox" => array(0 => "user", 1 => "full_name", 2 => "user_id", 3 => "user_image", 4 => "friend", 5 => "user_id", 6 => "friend_user_id", 7 => "Not Required"),
				"phpnuke" => array(0 => "users", 1 => "username", 2 => "user_id", 3 => "user_avatar", 4 => "Not Required", 5 => "Not Required", 6 => "Not Required", 7 => "Not Required"),
				"sharetronix" => array(0 => "users", 1 => "username", 2 => "id", 3 => "avatar", 4 => "users_followed", 5 => "who", 6 => "whom", 7 => "Not Required"),
				"skadate" => array(0 => "profile", 1 => "username", 2 => "profile_id", 3 => "profile_photo", 4 => "profile_friend_list", 5 => "profile_id", 6 => "friend_id", 7 => "Not Required"),
				"smf" => array(0 => "members", 1 => "memberName", 2 => "ID_MEMBER", 3 => "avatar", 4 => "Not Required", 5 => "Not Required", 6 => "Not Required", 7 => "Not Required"),
				"smf2" => array(0 => "members", 1 => "member_name", 2 => "id_member", 3 => "avatar", 4 => "Not Required", 5 => "Not Required", 6 => "Not Required", 7 => "Not Required"),
				"socialengine" => array(0 => "users", 1 => "user_displayname", 2 => "user_id", 3 => "user_photo", 4 => "friends", 5 => "friend_user_id1", 6 => "friend_user_id2", 7 => "friend_status"),
				"socialengine4" => array(0 => "users", 1 => "displayname", 2 => "user_id", 3 => "photo_id", 4 => "user_membership", 5 => "user_id", 6 => "resource_id", 7 => "active"),
				"standalone" => array(0 => "", 1 => "", 2 => "", 3 => "", 4 => "", 5 => "", 6 => "", 7 => "",),
				"vanilla" => array(0 => "User", 1 => "Name", 2 => "UserID", 3 => "Photo", 4 => "Not Required", 5 => "Not Required", 6 => "Not Required", 7 => "Not Required"),
				"vbulletin" => array(0 => "user", 1 => "username", 2 => "userid", 3 => "userid", 4 => "userlist", 5 => "userid", 6 => "relationid", 7 => "friend"),
				"vbulletin5" => array(0 => "user", 1 => "username", 2 => "userid", 3 => "userid", 4 => "userlist", 5 => "userid", 6 => "relationid", 7 => "friend"),
				"vldpersonals" => array(0 => "members", 1 => "username", 2 => "member_id", 3 => "picture", 4 => "friends", 5 => "member_id", 6 => "friend_id", 7 => "pending"),
				"wordpress" => array(0 => "users", 1 => "display_name", 2 => "ID", 3 => "user_email", 4 => "useronline", 5 => "userid", 6 => "relationid", 7 => "friend"),
				"xenforo" => array(0 => "user", 1 => "username", 2 => "user_id", 3 => "gravatar", 4 => "user_follow", 5 => "user_id", 6 => "follow_user_id", 7 => "Not Required"),
				"xoops" => array(0 => "users", 1 => "uname", 2 => "uid", 3 => "user_avatar", 4 => "Not Required", 5 => "Not Required", 6 => "Not Required", 7 => "Not Required")
			);
			
?>