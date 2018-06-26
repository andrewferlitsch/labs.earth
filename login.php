<?php
session_start();
include "db.php";
include "user.php";
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
	
$email    = $request->email;
$password = $request->password;

$rc = $users->Login( $email, $password );
if ( $users->error ) {
		echo $users->errmsg;
		header("HTTP/1.1 501 " . $users->errmsg );
        die();
}
header("HTTP/1.1 200 OK" );
header('Content-Type: application/json');
$resp = json_encode($rc);
echo $resp;
?>