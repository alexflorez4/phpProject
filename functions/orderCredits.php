<?php
include '../db/connection.php';

class orderRefund extends conn
{
    public function orderDetails($orderId)
    {
        $conn = $this->inv_conn();
        //Select order id that is not 1001 refunded.
        $qry_all = "SELECT `ord_id`,`ord_ref_item`,`ord_quant`,`ord_date`,`seller`,`ord_total`
                    FROM `order` WHERE `ord_id` = '$orderId' AND `ord_stat` != 1001 AND `ord_track` <> '' ";
        $result = mysqli_query($conn, $qry_all);
        return $result;
    }

    public function processRefund($orderId)
    {
        $conn = $this->inv_conn();
        $qry_update = "UPDATE `order` SET `ord_stat` = 1001 WHERE `ord_id` = '$orderId'";
        if($conn->query($qry_update) == TRUE)
        {
            $result = $this->updateRefundTable($orderId);
            if($result == 1)
            {
                return 1;
            }
            return 3;
        }
        else
        {
            return 2;
        }
    }

    public function processReStock($orderId)
    {
        $conn = $this->inv_conn();
        $qry = "SELECT * FROM `order` WHERE `ord_id` = '$orderId'";
        $result = mysqli_query($conn, $qry);

        while($res = mysqli_fetch_assoc($result))
        {
            $ref = $res['ref_fk'];
            $quant = $res['ord_quant'];
            $seller = $res['seller'];
            $value = $res['ord_total'];
        }
        $result = $this->updateInventory($ref, $quant);
        return $result;

    }

    public function updateInventory($refId, $quantity)
    {
        $conn = $this->inv_conn();

        $qry_all = "SELECT DISTINCT `quantity` FROM `inventory` WHERE `id` = '$refId'";
        $result = mysqli_query($conn, $qry_all);

        while($res = mysqli_fetch_assoc($result))
        {
            $quant = $res['quantity'];
        }

        $updQuantity = $quant + $quantity;

        $qry_upd = "UPDATE `inventory` SET `quantity` = $updQuantity, `last_update` = CURRENT_TIMESTAMP WHERE `id` = '$refId'";
        if($conn->query($qry_upd) == TRUE)
        {
            return 1;
        }
        else
        {
            return 2;
        }
    }

    public function updateRefundTable($orderid)
    {
        $conn = $this->inv_conn();
        $qry = "SELECT * FROM `order` WHERE `ord_id` = '$orderid' AND `ord_stat` = 1001";
        $result = mysqli_query($conn, $qry);

        while($res = mysqli_fetch_assoc($result))
        {
            $orderid = $res['ord_id'];
            $sellerid = $res['seller_id'];
            $value = $res['ord_total'];
        }

        $qryIns = "INSERT INTO `trans_adj`(`trans_id`,`seller_id`,`trans_value`) VALUES('$orderid',$sellerid , '$value')";
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
}

?>
