<?php
session_start();
include "db.php";
include "user.php";

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

$id    = $request->id;

$skills = $users->GetSkills( $id );

$out = "[";
$count = count( $skills );
for ( $i = 0; $i < $count; $i++ ) {
	$entry    = $skills[ $i ];
	$skill    = $entry['skill'];
	$years    = $entry['years'];
	$rate     = $entry['rate'];
	$out .= "{ \"skill\": \"$skill\", \"years\": \"$years\", \"rate\": \"$rate\" }";
	
	// not the last entry
	if ( $i < $count - 1 )
		$out .= ",";
}
$out .= "]";

if ( $users->error ) {
		echo $users->errmsg;
		header("HTTP/1.1 501 " . $users->errmsg );
        die();
}

header("HTTP/1.1 200 OK" );
echo $out;
?>