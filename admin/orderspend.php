<?php
include '../functions/functions.php';

session_start();
if(!$_SESSION['login'])
{
    header("location:login.php");
    die;
}

$obj = new project();
$results = $obj->get_orders_pending();


if(isset($_POST['submit']))
{

    $count = $obj->ordersCount();

    $id = $_POST['id'];
    $track = $_POST['track'];
    $seller = $_POST['seller'];

    for($i=0;$i<$count;$i++)
    {
        if (strlen($track[$i]) > 0)
        {
            $returnvalue = $obj->updateTrack($id[$i], $track[$i], $seller[$i]);
        }
    }

    header("location: orderspend.php");
}

if(isset($_POST['export']))
{
    $results = $obj->export3();
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

    <title>EA Group Dashboard</title>

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
        <div id="page-wrapper">
            <!-- Navigation -->
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
                        <a class="navbar-brand" href="main.html">EA</a>
                    </div>
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

                        <ul class="nav navbar-nav">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> Actions <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="invpriv.php"><i class="fa fa-star fa-fw"></i> Inventory </a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="add.php"><i class="fa fa-android fa-fw"></i> Add Item</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="delete.php"><i class="fa fa-flash fa-fw"></i> Delete Item</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="update.php"><i class="fa fa-rocket fa-fw"></i> Update</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> Orders <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="orderspend.php"><i class="fa fa-shopping-cart"></i> Pending Orders </a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="orderspriv.php"><i class="fa fa-flag-checkered"></i> Orders </a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="editorder.php"><i class="fa fa-edit"></i> Edit an order </a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="deleteorder.php"><i class="fa fa-bomb "></i> Delete an order </a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="credits.php"><i class="fa fa-arrow-circle-left"></i> Refund an order</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> Others <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="confirmation.php"><i class="fa fa-truck"></i> Confirmation </a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="invoices.php"><i class="fa fa-linux "></i> Invoices </a></li>
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
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Pending Orders</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            Pending Orders
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="dataTable_wrapper">
                               <form method="post" action="orderspend.php">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Reference</th>
                                            <th>Qty</th>
                                            <th>Name</th>
                                            <th>Address</th>
                                            <th>Apt</th>
                                            <th>City</th>
                                            <th>State</th>
                                            <th>Zip</th>
                                            <th>Country</th>
                                            <th>Order Date</th>
                                            <th>Observations</th>
                                            <th>Seller</th>
                                            <th>Track #</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php while($rows = mysqli_fetch_array($results))
                                    {
                                    ?>

                                        <tr class="odd gradeX">
                                    <input type="hidden" name="id[]" value="<? echo $ordid = $rows['ord_id']; ?>" size="3">
                                    <input type="hidden" name="seller[]" value="<? echo $rows['seller']; ?>">
                                           <td><? echo $ordid; ?></td>
                                           <td><? echo $rows['ord_ref_item']; ?></td>
                                           <td><? echo $rows["ord_quant"];?></td>
                                           <td><? echo $rows["buyer_nm"];?></td>
                                           <td><? echo $rows["buyer_add"];?></td>
                                           <td><? echo $rows["buyer_apt"];?></td>
                                           <td><? echo $rows["ord_city"];?></td>
                                           <td><? echo $rows["ord_state"];?></td>
                                           <td><? echo $rows["ord_zip"];?></td>
                                           <td><? echo $rows["ord_country"];?></td>
                                           <td><? echo $rows["ord_date"];?></td>
                                           <td><? echo $rows["ord_observ"];?></td>
                                           <td><? echo $rows["seller"];?></td>
                                           <td><input name="track[]" type="text" value="<? echo $rows['ord_track']; ?>"></td>
                                        </tr>
                                        <?
                                    }
                                    ?>

                                    </tbody>
                                </table>
                                    <button type="submit" class="btn btn-default" name="submit">Update</button>
                                    <button type="submit" class="btn btn-default" name="export">Export to Excel</button>
                                </form>
                            </div>
                            <!-- /.table-responsive -->
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
