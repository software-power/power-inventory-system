<?php
	$data['layout'] = 'layout_changepassword.tpl.php';

	if ( $action == 'index' ) {

		$data['content'] = loadTemplate('changepassword.tpl.php');
	}

	if ( $action == 'changeDefault' ) {
			$option = ['cost'=>12];
			$nData['password'] = password_hash($_POST['newpassword'],PASSWORD_BCRYPT,$option);
			$nData['changepass'] = '0';
			$nData['modifiedby'] = $_SESSION['member']['id'];

			$Users->update($_SESSION['member']['id'],$nData);
			$_SESSION['message'] = "Password changed, please re-login";
			redirect('authenticate','login');

	}
