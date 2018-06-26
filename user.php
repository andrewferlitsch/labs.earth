<?php
session_start();
include_once "db.php";

define("TBL_USERS",  "users" );
define("TBL_SKILLS", "skills" );

class Users {
	var $error  = false;
	var $errmsg = "";
	var $userid = -1;
	
	// Add (Register) a new user
	function NewUser( $email, $password, $name, $tel, $summary, $position, $level, $degree, $leadership, $skills ) {
		global $db;
	
		// Check format of password
		$password = $this->Strip( $password );
		if ( !( $password = $this->ValidPassword( $password ) ) ) {
			return !( $this->error = true );
		}
		
		// Check format of email address
		$email = $this->Strip( $email );
		if ( !( $email = $this->ValidEmail( $email, 1) ) ) {
			return !( $this->error = true );
		}
		
		// Check format of name
		$name = $this->Strip( $name );
		if ( !( $name = $this->ValidName( $name ) ) ) {
			return !( $this->error = true );
		}
		
		// Check format of telephone
		$tel = $this->Strip( $tel );
		if ( !( $tel = $this->ValidTele( $tel ) ) ) {
			return !( $this->error = true );
		}
				
		$q = "INSERT INTO " . TBL_USERS . " (email,password,name,tel,summary,position,level,degree,leadership,created,lastlogin) VALUES " .
			 "('$email','$password','$name','$tel','$summary','$position','$level','$degree','$leadership',SYSDATE(),SYSDATE() )";
		
		$result = mysqli_query( $db->connection, $q );	
		if ( $db->debug == true ) {
			echo "Q $q". PHP_EOL;
			echo mysqli_error( $db->connection ) . PHP_EOL;
		}

		$userid = mysqli_insert_id( $db->connection );
		for ( $i = 0; $i < count( $skills ); $i++ ) {
			$x = $skills[ $i ];
			$name  = $x->skill;
			$years = $x->years;
			$rate  = $x->rate;
			
			$q = "INSERT INTO " . TBL_SKILLS . " (userid,skill,years,rate) VALUES ('$userid','$name','$years','$rate')";
			$result = mysqli_query( $db->connection, $q );	
			if ( $db->debug == true ) {
				echo "Q $q". PHP_EOL;
				echo mysqli_error( $db->connection ) . PHP_EOL;
			}
		}
		
		return $userid;
	}
	
	// Update (Existing) User
	function UpdateUser( $userid, $email, $password, $name, $tel, $summary, $position, $level, $degree, $leadership, $skills, $active ) {
		global $db;
		
		$email    = $this->Strip( $email );
		if ( !( $email = $this->ValidEmail( $email, 0 ) ) ) {
			return !( $this->error = true );
		}
		
		// Check format of name
		$name = $this->Strip( $name );
		if ( !( $name = $this->ValidName( $name ) ) ) {
			return !( $this->error = true );
		}
		
		// Check format of telephone
		$tel = $this->Strip( $tel );
		if ( !( $tel = $this->ValidTele( $tel ) ) ) {
			return !( $this->error = true );
		}
		
		$q = "UPDATE " . TBL_USERS . " SET email='$email',name='$name',tel='$tel',summary='$summary'," .
		     "position='$position',level='$level',degree='$degree',leadership='$leadership',active='$active'";
		
		$password = $this->Strip( $password );
		if ( $password != "" ) {
			if ( !( $password = $this->ValidPassword( $password ) ) ) {
				return !( $this->error = true );
			}
			
			$q .= ",password='$password'";
		}
		
		$q .= " WHERE id=$userid";
		
		$result = mysqli_query( $db->connection, $q );
		if ( $db->debug == true ) {
			echo "Q $q". PHP_EOL;
			echo mysqli_error( $db->connection ) . PHP_EOL;
		}
		
		if ( count( $skills ) > 0 ) {
			$q = "DELETE FROM " . TBL_SKILLS . " WHERE userid='$userid'";
			$result = mysqli_query( $db->connection, $q );	
			if ( $db->debug == true ) {
				echo "Q $q". PHP_EOL;
				echo mysqli_error( $db->connection ) . PHP_EOL;
			}
			for ( $i = 0; $i < count( $skills ); $i++ ) {
				$x = $skills[ $i ];
				$name  = $x->skill;
				$years = $x->years;
				$rate  = $x->rate;
				
				$q = "INSERT INTO " . TBL_SKILLS . " (userid,skill,years,rate) VALUES ('$userid','$name','$years','$rate')";
				$result = mysqli_query( $db->connection, $q );	
				if ( $db->debug == true ) {
					echo "Q $q". PHP_EOL;
					echo mysqli_error( $db->connection ) . PHP_EOL;
				}
			}
		}

		return $result;
	}
	
	// Check if password is valid syntax
	function ValidPassword( $password ) {
		// Password Required
		if ( $password == "" ) {
			$this->errmsg = "No password specified.";
			return false;
		}
		
		return $password;
	}
	
	// Check if email is valid syntax
	function ValidEmail( $email, $registering ) {
		// Email Required
		if ( $email == "" ) {
			$this->errmsg = "No email specified.";
			return false;
		}
		
		// Check if valid format
		if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			$this->errmsg = "Not a valid email address.";
			return false;
		}
		
		// Check if email is already registered
		if ( $registering == 1 && $this->IsRegisteredEmail( $email ) ) {
			$this->errmsg = "Email already registered.";
			return !( $this->error = true );
		}
		
		return $email;
	}
	
	// Check if email already exists
	function IsRegisteredEmail( $email ) {
		global $db;
		
		$q = "Select email FROM " . TBL_USERS . " WHERE email='$email'";
		$result = mysqli_query( $db->connection, $q );
		if ( $db->debug == true ) {
			echo "Q $q". PHP_EOL;
			echo mysqli_error( $db->connection ) . PHP_EOL;
		}
		
		if ( mysqli_num_rows( $result ) > 0 )
			return true;
		
		return false;
	}
	
	// Check if name is valid syntax
	function ValidName( $name ) {
		// Name Required
		if ( $name == "" ) {
			$this->errmsg = "No name specified.";
			return false;
		}
		
		return $name;
	}
	
	// Check if telephone is valid syntax
	function ValidTele( $tel ) {
		$tel = str_replace( "-", "", $tel );
		$tel = str_replace( "(", "", $tel );
		$tel = str_replace( ")", "", $tel );
		$tel = str_replace( " ", "", $tel );
		
		return $tel;
	}
	
	// User Login
	function Login( $email, $password ) {
		global $db;
		
		$email    = $this->Strip( $email );
		$password = $this->Strip( $password );
		
		// login by email
		if ( !$this->IsRegisteredEmail( $email ) ) {
			$this->errmsg = "Email not valid.";
			return !( $this->error = true );
		}
		$where = "email='$email'";
		
		$q = "SELECT * FROM " . TBL_USERS . " WHERE " . $where;
		$result = mysqli_query( $db->connection, $q );
		if ( $db->debug == true ) {
			echo "Q $q". PHP_EOL;
			echo mysqli_error( $db->connection ) . PHP_EOL;
		}
		
		$data = mysqli_fetch_array( $result );
		$storedpassword = $data[ 'password' ];
		if ( $storedpassword != $password ) {
			$this->errmsg = "Password not valid.";
			return !( $this->error = true );
		}
		
		$this->userid = $data[ 'id' ];
		
		$_SESSION["email"]    = $email;
		$_SESSION["userid"]   = $this->userid;
		
		return $data;
	}
	
	// Facebook User Login
	function FBLogin( $email, $name ) {
		global $db;
		
		$email  = $this->Strip( $email );
		$name	= $this->Strip( $name  );
		
		// login by email
		if ( $email != "" ) {
			if ( !$this->IsRegisteredEmail( $email ) ) {
				$password = uniqid();
				$this->NewUser( $name, $email, $password, $password );
			}
			$where = "email='$email'";
		}
		
		$q = "SELECT password,username,id FROM " . TBL_USERS . " WHERE " . $where;
		$result = mysqli_query( $db->connection, $q );
		if ( $db->debug == true ) {
			echo "Q $q". PHP_EOL;
			echo mysqli_error( $db->connection ) . PHP_EOL;
		}
		
		$data = mysqli_fetch_array( $result );
		$this->userid = $data[ 'id' ];
		
		$_SESSION["email"]    = $email;
		$_SESSION["userid"]   = $this->userid;
		
		return $this->userid;
	}
	
	// Logout
	function Logout() {
		unset( $_SESSION );
	}
	
	// Get information for a user
	function GetUser( $userid ) {
		global $db;
		
		$q = "SELECT * FROM " . TBL_USERS . " WHERE id=$userid";
		$result = mysqli_query( $db->connection, $q );
		if ( $db->debug == true ) {
			echo "Q $q". PHP_EOL;
			echo mysqli_error( $db->connection ) . PHP_EOL;
		}
		
		$data = mysqli_fetch_array( $result );
		return $data;
	}
	
	// Return the number of users
	function Count() {
		global $db;
		
		$q = "SELECT count(id) FROM " . TBL_USERS;
		$result = mysqli_query( $db->connection, $q );
		if ( $db->debug == true ) {
			echo "Q $q". PHP_EOL;
			echo mysqli_error( $db->connection ) . PHP_EOL;
		}
		
		$data = mysqli_fetch_array( $result );
		return $data[ 0 ];
	}
	
	function ResetPassword() {
		
	}
	
	// Strip input of whitespace, tags and special characters
	function Strip( $name ) {
		$name = trim( $name );
		$name = strip_tags( $name );
		$name = htmlspecialchars( $name );
		return $name;
	}
	
	// Get a uers skills
	function GetSkills( $userid ) {
		global $db;
		
		$q = "SELECT * FROM " . TBL_SKILLS . " WHERE userid=" . $userid;
		$result = mysqli_query( $db->connection, $q );
		if ( $db->debug == true ) {
			echo "Q $q". PHP_EOL;
			echo mysqli_error( $db->connection ) . PHP_EOL;
		}
		
		$skills = array();
		while ( $data = mysqli_fetch_array( $result ) ) {
			array_push( $skills, $data );
		}
		
		return $skills;
	}
	
	// Search for Candidates
	function Search( $position, $level, $degree, $leadership, $skills ) {
		global $db;
		
		$q = "SELECT * FROM " . TBL_USERS . " WHERE 1";
		if ( $position != "" )
			$q .= " AND position='$position'";
		
		switch ($level ) {
		case "Junior"   : $where = " AND level IN ('Junior')"; break;
		case "Mid"      : $where = " AND level IN ('Junior','Mid')"; break;
		case "Senior"   : $where = " AND level IN ('Junior','Mid','Senior')"; break;
		case "Architect": $where = " AND level IN ('Junior','Mid','Senior','Architect')"; break;
		case "Principal": 
		default			: $where = ""; break;
		}
		
		$q .= $where;
		
		switch ($degree) {
		case "Self-Taught": $where = " AND degree IN ('Self-Taught')"; break;
		case "Certificate": $where = " AND degree IN ('Certificate','Self-Taught')"; break;
		case "Code School": $where = " AND degree IN ('Code School','Certificate','Self-Taught')"; break;
		case "Bachelors"  : $where = " AND degree IN ('Bachelors','Code School','Certificate','Self-Taught')"; break;
		case "Masters"    : $where = " AND degree IN ('Masters','Bachelors','Code School','Certificate','Self-Taught')"; break;
		case "PhD"		  :
		default			  : $where = ""; break;
		}
		
		$q .= $where;
		
		if ( $leadership != "" )
			$q .= " AND leadership='$leadership'";
			
		if ( count($skills) > 0 ) {
		}
		
		$result = mysqli_query( $db->connection, $q );
		if ( $db->debug == true ) {
			echo "Q $q". PHP_EOL;
			echo mysqli_error( $db->connection ) . PHP_EOL;
		}
		
		$candidates = array();
		while ( $data = mysqli_fetch_array( $result ) ) {
			array_push( $candidates, $data );
		}

		return $candidates;
	}
}

$users = new Users();
?>