<?php
//include '../db/connection.php';
include 'functions.php';

//class ordering extends conn
class ordering extends project
{
	//This function return the names of the products for the dropdown menu on orders.php
	public function refDropDown()
	{
		$conn = $this->inv_conn();
		$qry_all = "SELECT `ref_name` FROM `inventory`";
		$result = mysqli_query($conn, $qry_all);
		mysqli_close($conn);
		return $result;
	}

	public function contryList()
	{
		$conn = $this->inv_conn();
		$qry_all = "SELECT `country_name` FROM `countries`";
		$result = mysqli_query($conn, $qry_all);
		mysqli_close($conn);
		return $result;
	}

	//This method is used to place an order in orders.php
	public function placeOrder($refName, $quantityOrder, $buyerNm, $buyerAdd, $buyerApt, $buyerCiti, $buyerState, $buyerZip, $buyerCountry, $buyerNotes, $sellerId, $sellerNm)
	{
		$conn = $this->inv_conn();
		$qry_all = "SELECT DISTINCT `id`, `quantity`,`price`  FROM `inventory` WHERE `ref_name` = '$refName'";
		$result = mysqli_query($conn, $qry_all);

		while($res = mysqli_fetch_assoc($result))
		{
			$refid = $res['id'];
			$quant = $res['quantity'];
			$val = $res['price'];
		}

		if(($quant - $quantityOrder) < 0)
		{
			return 4;
		}
		else
		{
			$orderTotal = $val * $quantityOrder;

			//International orders
			if($buyerCountry != "United States")
			{
				$orderTotal += 10;
			}

			$qryIns = "INSERT INTO `order`(`ord_id`, `ord_ref_item`, `ref_fk`, `ord_quant`, `buyer_nm`, `buyer_add`, `buyer_apt`, `ord_city`, `ord_state`, `ord_zip`, `ord_country`, `ord_observ`, `seller_id`, `seller`, `ord_total`)
					   VALUES ('', '$refName','$refid','$quantityOrder', '$buyerNm', '$buyerAdd', '$buyerApt', '$buyerCiti', ' $buyerState', '$buyerZip' ,'$buyerCountry', '$buyerNotes', '$sellerId','$sellerNm', $orderTotal)";

			$conn = $this->inv_conn();
			if($conn->query($qryIns) == TRUE)
			{
				$updQuantity = $quant - $quantityOrder;
				$qry_upd = "UPDATE `inventory` SET `quantity` = $updQuantity, `last_update` = CURRENT_TIMESTAMP WHERE `ref_name` = '$refName'";
				if($conn->query($qry_upd) == TRUE)
				{
					if($updQuantity == 0)
					{
						$obj = new ordering();
						$obj->outOfStockEmail($refName);
					}
					//Order has been placed successful.  Send email to system admin.
					$this->sendEmailAdmin($sellerNm, $refName, $quantityOrder, $updQuantity );
					return 1;
				}
				else
				{
					return 2;
				}

			}
			else
			{
				return 3;
			}
		}
	}

	public function updateOrder($orderid, $orderRefNew, $orderQuant, $orderBuyerNm, $orderBuyerAdd, $orderBuyerApt, $orderBuyerCiti, $orderBuyerState, $orderBuyerZip, $orderBuyerCountry, $orderBuyerNotes, $tracking)
	{

		$conn = $this->inv_conn();

		$qry1 = "SELECT DISTINCT `ref_fk`, `ord_quant` FROM `order` WHERE `ord_id` = '$orderid'";
		$results1 = mysqli_query($conn, $qry1);

		while ($resultsA = mysqli_fetch_Assoc($results1)) {
			$oldRefId = $resultsA['ref_fk'];
			$oldQuantity = $resultsA['ord_quant'];
		}

		$qry2 = "SELECT `quantity` FROM `inventory` WHERE `id` = '$oldRefId'";
		$results2 = mysqli_query($conn, $qry2);
		while ($resultsB = mysqli_fetch_assoc($results2)) {
			$currQuantity = $resultsB['quantity'];
		}

		$newQuant = $oldQuantity + $currQuantity;

		$qry3 = "UPDATE `inventory` SET `quantity` = '$newQuant' WHERE `id` = '$oldRefId'";
		if ($conn->query($qry3) == TRUE)
		{

			$qry_all = "SELECT DISTINCT `id`, `quantity`,`price`  FROM `inventory` WHERE `ref_name` = '$orderRefNew'";
			$result = mysqli_query($conn, $qry_all);

			while ($res = mysqli_fetch_assoc($result)) {
				$refid = $res['id'];
				$quant = $res['quantity'];
				$val = $res['price'];
			}

			if (($quant - $orderQuant) < 0) {
				return 4;
			} else {
				$orderTotal = $val * $orderQuant;

				//International orders
				if ($orderBuyerCountry != "United States") {
					$orderTotal += 10;
				}

				$qryIns = "UPDATE `order` SET `ord_ref_item` = '$orderRefNew',`ref_fk` = '$refid', `ord_quant` = '$orderQuant', `buyer_nm` = '$orderBuyerNm',
												 `buyer_add` = '$orderBuyerAdd', `buyer_apt` = '$orderBuyerApt', `ord_city` =  '$orderBuyerCiti',
												 `ord_state` = '$orderBuyerState', `ord_zip` = '$orderBuyerZip', `ord_country` = '$orderBuyerCountry',
												 `ord_date` = CURRENT_TIMESTAMP,`ord_observ` = '$orderBuyerNotes', `ord_track` = '$tracking', `ord_total` = '$orderTotal'
							WHERE `ord_id` = '$orderid'";

				//$conn = $this->inv_conn();
				if ($conn->query($qryIns) == TRUE) {
					$updQuantity = $quant - $orderQuant;
					//update new order item.
					$qry_upd = "UPDATE `inventory` SET `quantity` = $updQuantity, `last_update` = CURRENT_TIMESTAMP WHERE `ref_name` = '$orderRefNew'";
					if ($conn->query($qry_upd) == TRUE) {
						if ($updQuantity == 0) {
							$obj = new ordering();
							$obj->outOfStockEmail($refName);
						}
						//Order has been placed successful.  Send email to system admin.
						//$this->sendEmailAdmin($sellerNm, $refName, $quantityOrder, $updQuantity );
						return 1;
					} else {
						return 2;
					}

				} else {
					return 3;
				}
			}
		}
		else
		{
			return 5;
		}
	}

	//This function return the seller orders to orderstrack.php
	public function get_orders_user($seller)
	{
		$conn = $this->inv_conn();
		$qry_all = "SELECT `ord_id`,`ord_ref_item`,`ord_quant`,`buyer_nm`,`buyer_add`, `buyer_apt`, `ord_city`, `ord_zip`, `ord_state`,`ord_country`,`ord_date`,`ord_observ`,`seller`,`ord_track`
					FROM `order` WHERE `seller` = '$seller'";
		$result = mysqli_query($conn, $qry_all);
		mysqli_close($conn);
		return $result;

	}

	public function get_order($orderid)
	{
		$conn = $this->inv_conn();
		$qry_all = "SELECT `ord_id`,`ord_ref_item`,`ord_quant`,`buyer_nm`,`buyer_add`,`buyer_apt`, `ord_city`,`ord_state`,`ord_zip`,`ord_country`,`ord_date`,`ord_observ`,`seller`,`ord_track`
					FROM `order` WHERE `ord_id` = '$orderid'";
		$result = mysqli_query($conn, $qry_all);
		return $result;
	}

	public function outOfStockEmail($reference)
	{
		$sellers = $this->getSellers();
		$subject = 'Reference out of stock: ' . $reference;

		$headers = "MIME-Version: 1.1";
		$headers .= "Content-type: text/html; charset=iso-8859-1";

		$headers .= 'To: ' . "\r\n";
		$headers .= 'Bcc: admin@thugcode.com' . ', ' . $sellers . "\r\n";
		$headers .= 'From: EA Group <admin@thugcode.com>' . "\r\n";

		$message .= 'Attention: This reference is out of Stock.  Please check your listings and update accordingly.'. "\n\n\n";
		$message .= 'EA Group.';

		if (@mail($to, $subject, $message, $headers))
		{
			// Transfer the value 'sent' to ajax function for showing success message.
			return 'sent';
		}
		else
		{
			// Transfer the value 'failed' to ajax function for showing error message.
			return 'failed';
		}
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


	public function deleteOrder($orderid)
	{
		$conn = $this->inv_conn();
		$qry_count = "SELECT * from `order` WHERE `ord_id` = $orderid";
		$result = mysqli_query($conn, $qry_count);
		$nrows = mysqli_num_rows($result);

		if($nrows == 0)
		{
			return 1;
		}
		elseif($nrows == 1)
		{
			$del = "DELETE FROM `order` WHERE `ord_id` = '$orderid'";
			$conn->query($del);

			$qry_count2 = "SELECT * from `order` WHERE `ord_id` = '$orderid'";
			$result2 = mysqli_query($conn, $qry_count2);
			if($row = (mysqli_num_rows($result2)) == 0)
			{
				return 2;
			}
		}
	}

	public function  sendEmailAdmin($seller, $refName, $quantityOrder, $updQuantity )
	{
		//$sellers = $this->getSellers();
		$subject = 'New Order from  ' . $seller;

		$headers = "MIME-Version: 1.1";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		$headers .= 'Bcc: admin@thugcode.com' . "\r\n";
		$headers .= 'From: ' . 'EA Group <admin@thugcode.com>' . "\r\n";

		$message .= $seller . ' has a new order.  '. "\n";
		$message .= 'Reference: ' . $refName . "\n";
		$message .= 'Quantity: ' . $quantityOrder . "\n ";
		$message .= 'Items left: ' . $updQuantity . "\n\n\n";
		$message .= 'EA Group.';

		@mail($to, $subject, $message, $headers);
	}
}

?>
