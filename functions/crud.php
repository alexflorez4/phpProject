<?php
include 'inventory.php';

class crudOps extends catalogue
{
	//This functions is used to add an item on admin/add.php
	public function add($item, $quantity, $price)
	{
		$qryIns = "INSERT INTO `inventory`(`id`, `ref_name`, `price`, `quantity`) VALUES ('', '$item', '$price', '$quantity')";
		$conn = $this->inv_conn();
		if($conn->query($qryIns) == TRUE)
		{
			return 1;
		}
		else
		{
			return 2;
		}
	}

	public function delete($item)
	{

		$conn = $this->inv_conn();
		$qry_count = "SELECT * from `inventory` WHERE `id` = $item";
		$result = mysqli_query($conn, $qry_count);
		$nrows = mysqli_num_rows($result);

		if($nrows == 0)
		{
			return 1;
		}
		elseif($nrows == 1)
		{
			$del = "DELETE FROM `inventory` WHERE `id` = '$item'";
			$conn->query($del);

			$qry_count2 = "SELECT * from `inventory` WHERE `id` = '$item'";
			$result2 = mysqli_query($conn, $qry_count2);
			if($row = (mysqli_num_rows($result2)) == 0)
			{
				return 2;
			}
		}
	}

	public function update($refName, $quantity)
	{
		$conn = $this->inv_conn();

		$qry_all = "SELECT DISTINCT `quantity` FROM `inventory` WHERE `ref_name` = '$refName'";
		$result = mysqli_query($conn, $qry_all);

		while($res = mysqli_fetch_assoc($result))
		{
			$quant = $res['quantity'];
		}

		$updQuantity = $quant + $quantity;

		$qry_upd = "UPDATE `inventory` SET `quantity` = $updQuantity, `last_update` = CURRENT_TIMESTAMP WHERE `ref_name` = '$refName'";
		if($conn->query($qry_upd) == TRUE)
		{
			return 1;
		}
		else
		{
			return 2;
		}
	}

}

?>
