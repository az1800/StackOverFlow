<?php
// MySQL server configuration

function conTosql(){
    $host = "localhost"; // Hostname
    $username = "root"; // Username
    $password = ""; // Password (leave empty for root user)
$try = mysqli_connect($host,$username,$password,"stack",3390);
if(!$try){
die("not connected");
}
return true;

}
//conTosql();
?>
