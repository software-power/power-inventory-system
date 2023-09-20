<?php

if ( $action == 'index' ) {

	$tData['users'] = $Users->getAll();
	$tData['userDa'] = $Users->get($_SESSION['member']['id']);

	$data['content'] = loadTemplate('profile.tpl.php', $tData);
}

if ( $action == 'password' ) {

	$tData['users'] = $Users->getAll();
	$tData['userDa'] = $Users->get($_SESSION['member']['id']);

	$data['content'] = loadTemplate('password.tpl.php', $tData);
}

if ( $action == 'profile_image' ) {

	$tData['users'] = $Users->getAll();
	$tData['userDa'] = $Users->get($_SESSION['member']['id']);

	if (empty($_SESSION['member']['image'])) {
			$default_character = str_split($_SESSION['member']['name']);
			$tData['name_character'] = $default_character[0];
	}

	$data['content'] = loadTemplate('profileimage.tpl.php', $tData);
}

if ( $action == 'image_save' ) {

	$user = $_SESSION['member'];
	$image_data = $_FILES['image'];
	$dpPath = 'images/dp/';
	$dp = resizeUploadImage($image_data,$user['username'],220,220,$dpPath,$format='jpg');
	$dpData = array('image' => $dp);

	if (empty($user['image'])) {
			//new image
			$Users->update($user['id'],$dpData);
			$_SESSION['message'] = 'Profile image saved Successfully';
			//resent new DP in session
			$_SESSION['member']['image'] = $dp;

		}else if(!empty($user['image'])){
			//update the image
			$Users->update($user['id'],$dpData);
			$_SESSION['message'] = 'Profile image updated Successfully';
			//resent new DP in the session
			$_SESSION['member']['image'] = $dp;
	}
	//die();
	redirect('profile','profile_image');
}

if ( $action == 'password_update' ) {

	$id = $_POST['username'];
	//
	$oldPassword = $_POST['oldpword'];
	$helpStat = $_POST['helpStat'];


	if ($helpStat!=1) $helpStat=0;
	$tData['password'] = $_POST['newpword'];
	$tData['help'] = $helpStat;

	$user = $Users->get($id);

	$verifypassword = password_verify($oldPassword,$user['password']);

	if ($verifypassword != 1) {
		$_SESSION['error'] = 'Old password Mismatch';
		redirect('profile','password');
	}
	else {
		//print_r($verifypassword);
		$option = ['cost'=>12];
		$newPassword = password_hash($tData['password'],PASSWORD_BCRYPT,$option);
		$tData['password'] = $newPassword;
		$Users->update($id,$tData);
		$_SESSION['message'] = 'Password changed successfully';
		redirect('profile','password');
	}

}


if ( $action == 'profile_index' ) {

	$userid['userid']=$_SESSION['member']['id'];
	$supp = $Suppliers->find($userid);
	$tData['supplier'] = $supp[0];
	$data['content'] = loadTemplate('profile.tpl.php', $tData);
}

?>
