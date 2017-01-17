<?php
include 'functions.php';

class userAccess extends conn
{
	//function used in user/login.php to allow user access.
	public function userlogin($passcode)
	{
		$conn = $this->inv_conn();

		$qry_all = "SELECT `user_nm`, `user_pass` FROM `users` WHERE `user_pass` = '$passcode'";
		$result = mysqli_query($conn, $qry_all);
		$rows = mysqli_num_rows($result);
		if ($rows == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	//function used in user/login.php to display seller name.
	public function sellerNm($passcode)
	{
		$conn = $this->inv_conn();
		$qry = "SELECT `user_id`, `user_nm` FROM `users` WHERE `user_pass` = '$passcode' ";
		$result = mysqli_query($conn, $qry);

		return $result;
	}

	//This method is used for admin login
	public function login($username, $password)
	{
		$conn = $this->inv_conn();

		$qry_all = "SELECT `user_nm`, `user_pass` FROM `users` WHERE `user_nm` = '$username' AND `user_pass` = '$password'";
		$result = mysqli_query($conn, $qry_all);
		$rows = mysqli_num_rows($result);
		if ($rows == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

}
?>
