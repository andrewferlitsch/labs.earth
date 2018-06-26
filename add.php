<?php
session_start();
include "db.php";
include "user.php";

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

$email    = $request->email;
$password = $request->password;
$name     = addslashes( $request->name );
$tel      = $request->tel;
$summary  = addslashes( $request->summary );
$position = $request->position;
$level    = $request->level;
$degree   = $request->degree;
$leadership = $request->leadership;
$skills   = $request->skills;

if ( $request->action == "add" )
	$rc = $users->NewUser( $email, $password, $name, $tel, $summary, $position, $level, $degree, $leadership, $skills );
else {
	$rc = $users->UpdateUser( $request->userid, $email, $password, $name, $tel, $summary, $position, $level, $degree, $leadership, $skills, 1 );
}

if ( $users->error ) {
		echo $users->errmsg;
		header("HTTP/1.1 501 " . $users->errmsg );
        die();
}

header("HTTP/1.1 200 OK" );
echo $rc;
?>