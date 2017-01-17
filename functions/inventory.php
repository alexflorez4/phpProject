<?php
include 'functions.php';

class catalogue extends project
{
	//This function returns all inventory.
	public function get_all_inventory()
	{
		$conn = $this->inv_conn();
		$qry_all = "SELECT `id`, `ref_name`, `price`, `quantity`, `last_update` FROM `inventory`";
		$result = mysqli_query($conn, $qry_all);
		mysqli_close($conn);
		return $result;
	}

	//This function returns the quantity status.
	public function getStatus($quantity)
	{
		if($quantity < 1)
		{
			return 'Out of Stock';
		}
		elseif($quantity <6)
		{
			return $quantity;
		}
		else
		{
			return 'OK';
		}
	}

	public function invCount()
	{
		$conn = $this->inv_conn();
		$qry = "SELECT * FROM `inventory`";
		$result = mysqli_query($conn, $qry);
		$rows = mysqli_num_rows($result);

		return $rows;
	}

	public function updateInvPriv($id, $quantity, $price)
	{
		$conn = $this->inv_conn();

		$qry = "SELECT `quantity` FROM `inventory` WHERE `id` = '$id'";
		$result = mysqli_query($conn, $qry);

		while($res = mysqli_fetch_assoc($result))
		{
			$count1 = $res['quantity'];
		}

		$qry = "UPDATE `inventory` SET `quantity` = '$quantity', `last_update` = CURRENT_TIMESTAMP, `price` = '$price' WHERE `id` = '$id'";
		if($conn->query($qry) == TRUE)
		{
			//return 1;
		}

		$qry = "SELECT `quantity`, `ref_name` FROM `inventory` WHERE `id` = '$id'";
		$result = mysqli_query($conn, $qry);

		while($res = mysqli_fetch_assoc($result))
		{
			$ref = $res['ref_name'];
			$count2 = $res['quantity'];
		}

		if($count1 == 0 && $count2>$count1)
		{
			$this ->sendNotificationAval($ref);
		}
		if($count1 > 0 && $count2 == 0)
		{
			$this -> sendNotificationOut($ref);
		}

		return;
	}

	public function sendNotificationAval($ref)
	{
		$sellers = $this->getSellers();
		$subject = 'Reference ' . $ref . ' is now available.';
		$headers = "MIME-Version: 1.1";
		$headers .= "Content-type: text/html; charset=iso-8859-1";

		$headers .= 'To: ' . "\r\n";
		$headers .= 'Bcc: admin@thugcode.com' . ', ' . $sellers . "\r\n";
		$headers .= 'From: ' . 'EA Group <admin@thugcode.com>' . "\r\n";

		$message .= 'Attention: This reference is now available.'. "\n\n\n";
		$message .= 'EA Group.';

		@mail($to, $subject, $message, $headers);
	}

	public function sendNotificationOut($ref)
	{
		$sellers = $this->getSellers();
		$subject = 'Reference out of stock: ' . $ref;

		$headers = "MIME-Version: 1.1";
		$headers .= "Content-type: text/html; charset=iso-8859-1";

		$headers .= 'To: ' . "\r\n";
		$headers .= 'Bcc: admin@thugcode.com' . ', ' . $sellers . "\r\n";
		$headers .= 'From: EA Group <admin@thugcode.com>' . "\r\n";

		$message .= 'Attention: This reference is out of Stock.  Please check your listings and update accordingly.'. "\n\n\n";
		$message .= 'EA Group.';

		@mail($to, $subject, $message, $headers);
	}

	public function getSellers()
	{
		$conn = $this->inv_conn();
		$qry = "SELECT `user_email` FROM `users` WHERE `type` = 'seller' AND `status` = 'active'";
		$result = mysqli_query($conn, $qry);

		while($res = mysqli_fetch_assoc($result))
		{
			$email[] = $res['user_email'];
		}

		$stg ="";
		for($i=0; $i<count($email); $i++)
		{
			$stg .= $email[$i] . ', ';
		}
		return $stg;
	}

}
?>
