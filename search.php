<?php
session_start();
include "db.php";
include "user.php";

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

$position    = $request->position;
$level       = $request->level;
$degree      = $request->degree;
$leader      = $request->leadership;
$skills      = $request->skills;

$candidates = $users->Search( $position, $level, $degree, $leader, $skills );

$out = "[";
$count = count( $candidates );
for ( $i = 0; $i < $count; $i++ ) {
	$entry    = $candidates[ $i ];
	$id       = $entry['id'];
	$summary  = str_replace( "\"", "'", $entry['summary'] );
	$summary  = str_replace( "\n", "", $summary );
	$position = $entry['position'];
	$level    = $entry['level'];
	$degree   = $entry['degree'];
	$leader   = $entry['leadership'];
	$out .= "{ \"id\": \"$id\", \"summary\": \"$summary\", \"position\": \"$position\", \"level\": \"$level\", \"degree\": \"$degree\", \"leadership\": \"$leader\" }";
	
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