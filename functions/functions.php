<?php
include '../db/connection.php';

class project extends conn
{
	public function refDropDown()
	{
		$conn = $this->inv_conn();
		$qry_all = "SELECT `ref_name` FROM `inventory`";
		$result = mysqli_query($conn, $qry_all);
		return $result;
	}

	public function get_orders_all()
	{
		$conn = $this->inv_conn();
		$qry_all = "SELECT `ord_id`,`ord_ref_item`,`ord_quant`,`buyer_nm`,`buyer_add`,`buyer_apt`, `ord_city`,`ord_state`,`ord_zip`,`ord_country`,`ord_date`,`ord_observ`,`seller`,`ord_track`
					FROM `order` WHERE `ord_track` != ''  ORDER BY `ord_id` ASC";
		$result = mysqli_query($conn, $qry_all);
		return $result;
	}

	public function get_orders_pending()
	{
		$conn = $this->inv_conn();
		$qry_all = "SELECT `ord_id`,`ord_ref_item`,`ord_quant`,`buyer_nm`,`buyer_add`, `buyer_apt`, `ord_city`,`ord_state`,`ord_zip`, `ord_country`,`ord_date`,`ord_observ`,`seller`,`ord_track`
					FROM `order` WHERE `ord_track` = '' order by `ord_id` desc";
		$result = mysqli_query($conn, $qry_all);
		return $result;
	}

	public function ordersCount()
	{
		$conn = $this->inv_conn();
		$qry = "SELECT * FROM `order`";
		$result = mysqli_query($conn, $qry);
		$rows = mysqli_num_rows($result);
		return $rows;
	}

	public function updateTrack($id, $trackid, $seller)
	{
		$conn = $this->inv_conn();
		$qry = "UPDATE `order` SET `ord_track` =  '$trackid' WHERE `ord_id` = '$id'";
		if($conn->query($qry) == TRUE)
		{
			$sellerEmail = $this->getEmail($seller);
			$this->sendEmailOrderTrack($sellerEmail, $seller, $id, $trackid);
			return 1;
		}
	}

	public function getSellersName()
	{
		$conn = $this->inv_conn();
		$qry = "SELECT `user_nm`, `user_email`  FROM `users` WHERE `type` = 'seller'";
		$result = mysqli_query($conn, $qry);
		return $result;
	}

	public function getEmail($seller)
	{
		$conn = $this->inv_conn();
		$qry = "SELECT `user_email` FROM `users` WHERE `user_nm` = '$seller'";
		$result = mysqli_query($conn, $qry);

		while($res = mysqli_fetch_assoc($result))
		{
			$email = $res['user_email'];
		}
		return $email;
	}

	public function sendEmailConfirmation($sellers)
	{

		$subject = 'New Track id has been issue.';
		$to = $sellers;
		$headers = "MIME-Version: 1.1";
		$headers .= "Content-type: text/html; charset=iso-8859-1";
		$headers .= 'To: ' . $sellers . "\r\n";
		$headers .= 'From: EA Group <admin@thugcode.com>' . "\r\n";
		$message = 'Attention: A new tracker id has been issue.  Please check your account'. "\n";

		if (@mail($to, $subject, $message, $headers))
		{
			return 'sent';
		}
		else
		{
			return 'failed';
		}
	}

	public function sendEmailOrderTrack($seller, $sellerNm, $orderid, $track)
	{

		$subject = 'Tracking number updated.  Order # ' . $orderid ;
		$to = $seller;

		$headers = "MIME-Version: 1.1";
		$headers .= "Content-type: text/html; charset=iso-8859-1";

		$headers .= 'To: ' . $seller . "\r\n";
		$headers .= 'From: EA Group <admin@thugcode.com>' . "\r\n";

		$message = 'Hi '. $sellerNm . "\n";
		$message .= 'A tracking number has been issue for your order id #' . $orderid . "\n";
		$message .= 'Tracking number: ' . $track . "\n\n";
		$message .= 'Best Regards,' . "\n";
		$message .= 'EA Group VAC';


		if (@mail($to, $subject, $message, $headers))
		{
			return 'sent';
		}
		else
		{
			return 'failed';
		}
	}

	public function object_to_array($obj)
	{
		if(is_object($obj)) $obj = (array) $obj;
		if(is_array($obj)) {
			$new = array();
			foreach($obj as $key => $val) {
				$new[$key] = $this->object_to_array($val);
			}
		}
		else $new = $obj;
		return $new;
	}


	function cleanData(&$str)
	{
		$str = preg_replace("/\t/", "\\t", $str);
		$str = preg_replace("/\r?\n/", "\\n", $str);
		if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
	}

	public function export3()
	{
		// filename for download
		$filename = "OrdersPending" . date('Ymd') . ".xls";

		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		$conn = $this->inv_conn();
		$qry = "SELECT `ord_id`,`ord_ref_item`,`ord_quant`,`buyer_nm`,`buyer_add`, `buyer_apt`, `ord_city`,`ord_state`,`ord_zip`, `ord_country`,`ord_date`,`ord_observ`,`seller`,`ord_track`
					FROM `order` WHERE `ord_track` = '' order by `ord_id` ASC ";
		$result = mysqli_query($conn, $qry);
		$flag = false;

		while (false !== ($row = mysqli_fetch_assoc($result)))
		{
			if (!$flag) {
				echo implode("\t", array_keys($row)) . "\r\n";
				$flag = true;
			}
			array_walk($row, __NAMESPACE__ . '\cleanData');
			echo implode("\t", array_values($row)) . "\r\n";
		}
		exit;
	}

}

?>
