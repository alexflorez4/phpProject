<?php
include '../functions/sellerOrders.php';

session_start();
if(!$_SESSION['userlogin'])
{
    header("location:login.php");
    die;
}

$sellerId = $_SESSION['sellerId'];
$sellerNm = $_SESSION['sellerNm'];


$obj = new ordering();
$results = $obj->refDropDown();
$countries = $obj->contryList();

if(isset($_POST['submit']))
{
    $orderRef = $_POST['reference'];
    $orderQuant = trim($_POST['itemQuantity']);
    $orderBuyerNm = trim($_POST['bName']);
    $orderBuyerAdd = trim($_POST['bAddress']);
    $orderBuyerApt = trim($_POST['bApt']);
    $orderBuyerCiti = trim($_POST['bCiti']);
    $orderBuyerState = trim($_POST['bState']);
    $orderBuyerZip = trim($_POST['bZip']);
    $orderBuyerCountry = trim($_POST['bCountry']);
    $orderBuyerNotes = trim($_POST['bNotes']);

    $check = false;

    if(empty($orderRef))
    {
        echo '<p class="alert alert-info">Select an item reference.</p>';
        $check = true;
    }
    if(empty($orderQuant))
    {
        echo '<p class="alert alert-info">Enter quantity.</p>';
        $check = true;
    }
        if(empty($orderBuyerNm))
    {
        echo '<p class="alert alert-info">Enter buyer name.</p>';
        $check = true;
    }
        if(empty($orderBuyerAdd))
    {
        echo '<p class="alert alert-info">Enter buyer address.</p>';
        $check = true;
    }
    if(empty($orderBuyerCiti))
    {
        echo '<p class="alert alert-info">Enter buyer Citi, State, and zipcode.</p>';
        $check = true;
    }
    if(empty($orderBuyerCountry))
    {
        echo '<p class="alert alert-info">Enter buyer country.</p>';
        $check = true;
    }

    if($check == false)
    {
        $orderIns = $obj->placeOrder($orderRef, $orderQuant, $orderBuyerNm, $orderBuyerAdd, $orderBuyerApt, $orderBuyerCiti, $orderBuyerState, $orderBuyerZip, $orderBuyerCountry, $orderBuyerNotes, $sellerId, $sellerNm);

        if($orderIns == 1)
        {
            echo '<p class="alert alert-success" >Order has been placed successful.</p>';
        }
        if($orderIns == 2)
        {
            echo '<p class="alert alert-warning" >Sorry, there was a problem updating new quantity</p>';
        }
        if($orderIns == 3)
        {
            echo '<p class="alert alert-warning" >Sorry, there was a problem placing the order</p>';
        }
        if($orderIns == 4)
        {
            echo '<p class="alert alert-warning" >Sorry, there is not enough inventory for this order.</p>';
        }
    }

}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Orders</title>

    <!-- Bootstrap Core CSS -->
    <link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="../bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="../bower_components/datatables-responsive/css/dataTables.responsive.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <div id="page-wrapper">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="main_users.html">EA</a>
                    </div>
                     <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

                        <ul class="nav navbar-nav">
                            <li><a href="inventory.php">Inventory</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Orders <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#"><i class="fa fa-fighter-jet"></i> New Order</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="orderstrack.php"><i class="fa fa-thumbs-up"></i>  My Orders</a></li>
                                </ul>
                            </li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Log out <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                                </ul>
                            </li>
                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
            </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Orders</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Place an Order
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <form role="form" method="post" action="orders.php">

                                        <div class="form-group">
                                        <label>Item</label>
                                        <select class="form-control" name="reference" >
                                            <option selected></option>
                                            <?php while($res = mysqli_fetch_assoc($results)) { ?>
                                            <option><?echo $res["ref_name"]; ?></option>                                            
                                            <?}?>
                                        </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Quantity</label>
                                            <select class="form-control" name="itemQuantity">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                            </select>
                                        </div>
                                        <div class="">
                                        Buyer Shipping Information:
                                        </div>
                                        </br>

                                        <div class="form-group">
                                            <label>Full Name</label>
                                            <input class="form-control" name="bName">
                                        </div>
                                        <div class="form-group">
                                            <label>Address</label>
                                            <input class="form-control" name="bAddress">
                                        </div>
                                        <div class="form-group">
                                            <label>Apt.</label>
                                            <input class="form-control" name="bApt">
                                        </div>
                                        <div class="form-group">
                                            <label>City</label>
                                            <input class="form-control" name="bCiti">
                                        </div>
                                        <div class="form-group">
                                            <label>State</label>
                                            <input class="form-control" name="bState">
                                        </div>
                                        <div class="form-group">
                                            <label>Zip code</label>
                                            <input class="form-control" name="bZip">
                                        </div>
                                        <div class="form-group">
                                            <label>Country</label>
                                            <select class="form-control" name="bCountry" >
                                                <option selected></option>
                                                <?php while($res = mysqli_fetch_assoc($countries)) { ?>
                                                    <option><?echo $res["country_name"]; ?></option>
                                                <?}?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Observations</label>
                                            <textarea class="form-control" name="bNotes">  </textarea>
                                        </div>

                                        <button type="submit" class="btn btn-default" name="submit">Submit</button>
                                        <button type="reset" class="btn btn-default">Reset</button>

                                    </form>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                                <div class="col-lg-6">

                                </div>
                                <!-- /.col-lg-6 (nested) -->
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->


        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="../bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="../bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="../bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

    <!-- Page-Level Demo Scripts - Tables - Use for reference -->
    <script>
    $(document).ready(function() {
        $('#dataTables-example').DataTable({
                responsive: true
        });
    });
    </script>

</body>

</html>
