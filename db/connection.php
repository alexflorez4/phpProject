<?php
class conn
{
    public function inv_conn()
    {
        $host_name  = "";
        $database   = "";
        $user_name  = "";
        $password   = "";

        $connect = mysqli_connect($host_name, $user_name, $password, $database);

        if(mysqli_connect_errno())
        {
            //echo '<p>Verbindung zum MySQL Server fehlgeschlagen: '.mysqli_connect_error().'</p>';
        }
        else
        {
            //echo '<p>DB connected successful.</p>';
            return $connect;
        }
    }

}
?>
