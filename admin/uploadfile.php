<?php
include '../functions/zfunctTest.php';

if(isset($_POST['submit']))
{
    $obj = new zfuncTest();

    $obj->import($_POST['fileToUpload']);
}



?>


<form action=”uploadfile.php” enctype=”multipart/form-data” method=”post”>

    <input id=”fileToUpload” name=”fileToUpload” type=”file” />

    <input name=”submit” type=”submit” value=”Upload” />

</form>
