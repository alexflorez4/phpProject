<?php
include 'functions.php';

class inv_class extends project
{
    public function getOrders($seller, $startDate, $endDate)
    {
        $conn = $this->inv_conn();
        $qry = "SELECT `ord_id`,`ord_ref_item`,`ord_quant`,`seller`,`ord_total`
                FROM `order`
                INNER JOIN `inventory` ON `order`.`ref_fk` = `inventory`.`id`
                WHERE `seller` = '$seller'
                AND `ord_date` BETWEEN '$startDate' AND '$endDate'
                ORDER BY `ord_id` ASC";

        $result = mysqli_query($conn, $qry);

        return $result;
    }

    public function getOrdersRefund($seller, $startDate, $endDate)
    {
        $conn = $this->inv_conn();
        $qry = "SELECT * FROM `trans_adj`
                INNER JOIN `users` ON `trans_adj`.`seller_id` = `users`.`user_id`
                WHERE `user_nm` = '$seller'
                AND `trans_date` BETWEEN '$startDate' AND '$endDate'";

        $result = mysqli_query($conn, $qry);

        return $result;
    }

    public function getTotalvalue($seller, $startDate, $endDate)
    {
        $conn = $this->inv_conn();
        $qry = "SELECT SUM(`ord_total`)
                FROM `order`
                INNER JOIN `inventory` ON `order`.`ref_fk` = `inventory`.`id`
                WHERE `seller` = '$seller'
                AND `ord_date` BETWEEN '$startDate' AND '$endDate'";

        $result = mysqli_query($conn, $qry);

        while($res = mysqli_fetch_assoc($result))
        {
            $val = $res['SUM(`ord_total`)'];
        }
        return $val;

    }

    public function getTotalRefundsValue($seller, $startDate, $endDate)
    {
        $conn = $this->inv_conn();
        $qry = "SELECT SUM(`trans_value`)
                FROM `trans_adj`
                INNER JOIN `users` ON `trans_adj`.`seller_id` = `users`.`user_id`
                WHERE `user_nm` = '$seller'
                AND `trans_date` BETWEEN '$startDate' AND '$endDate'";

        $result = mysqli_query($conn, $qry);

        while($res = mysqli_fetch_assoc($result))
        {
            $val = $res['SUM(`trans_value`)'];
        }
        return $val;
    }

    public function updateTrigger()
    {
        $conn = $this->inv_conn();
        $qry1 = "SELECT `id`,`price` FROM `inventory`";
        $result = mysqli_query($conn, $qry1);

        while($res = mysqli_fetch_assoc($result))
        {
            $id = $res['id'];
            $ref = $res['price'];

            $qry = "UPDATE `order` SET `ord_total` = '$id' where `ord_ref_item` = '$ref'";
            $conn->query($qry);
        }

    }

}

?>
