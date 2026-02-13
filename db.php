<?php
#$con=mysqli_connect("localhost","root","","studentrecorddb") #connect to db parameter have 3, localhost, root(give the user as root), db name
$serverName="localhost";
$userName="root";
$userPass="";
$dbName="nyvarstore";
$con=mysqli_connect($serverName, $userName, $userPass, $dbName);
#if we want the 3 parameter then 
#$con= mysqli_connect(servername, username, userpass);
#mysqli_select_db(con, dbname);

if(mysqli_connect_error()){
    echo "connection Fail" .mysqli_connect_error();
}
?>