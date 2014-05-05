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
	
	// ########################## INCLUDE BACK-END ###########################
	require_once (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');
	require_once (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . AC_FOLDER_INCLUDES . DIRECTORY_SEPARATOR . 'init.php');

	// ###################### START MAIN UPLOAD SCRIPT #######################
	if (!empty($_FILES)) 
	{
		$tempFile = $_FILES['Filedata']['tmp_name'];
		$targetPath = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . AC_FOLDER_UPLOADS . DIRECTORY_SEPARATOR;
		$fileParts  = pathinfo($_FILES['Filedata']['name']);
		$targetFile =  $targetPath . mysql_real_escape_string($_POST['unixtime']) . "." . $fileParts['extension'];
		
		/*
		 * Uncomment the lines below if you would like to restrict
		 * the upload to certain file types.
		 */
		/* 
		$fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
		$fileTypes  = str_replace(';','|',$fileTypes);
		$typesArray = split('\|',$fileTypes);
		
		if (in_array($fileParts['extension'],$typesArray)) 
		{
		*/
			move_uploaded_file($tempFile, $targetFile);
			echo str_replace(dirname(dirname(dirname(dirname(__FILE__)))), '', $targetFile);
		// }
	}
	
?>