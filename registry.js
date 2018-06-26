
labs.controller( 'registryCtrl', function( $scope, $http ) {
	$scope.debug       = true;	// send debugging info to console
	
	$scope.AddLabel    = "Add";	// Label for add button
	$scope.userid	   = -1;	// userid of logged in user
	
	$scope.showLogin   = true;
	$scope.showAdd     = false;	// Add me to Registry
	$scope.showSearch  = true;	// Search for Candidates
	$scope.showResult  = false;	// Show Search Results
	$scope.showDetails = false;	// Show Search Details
	
	$scope.skill       = [];  	// technical skill
	$scope.years       = [];	// years of experience
	$scope.rate        = [];	// self-rating
	$scope.position    = "";
	$scope.positions   = [ "Not Specified", "Programmer", "Data Scientist", "Data Analyst", "Data Developer", "Data Wrangler", "Database Engineer", 
						   "Big Data Developer", "Robotics Engineer", "NLP Engineer", "Autonomous Engineer", "Manager"];
	$scope.level       = "";
	$scope.levels      = [ "Not Specified", "Junior", "Mid", "Senior", "Architect", "Principal" ];
	$scope.degree      = "";
	$scope.degrees     = [ "Not Specified", "Bachelors", "Masters", "PhD", "Self-Taught", "Certificate", "Code School"  ];
	$scope.leadership  = "";
	$scope.leaderships = [ "Not Specified", "Scrum Master", "Product Owner", "Project Manager", "Product Manager", "Technical Lead", "Manager" ];
	$scope.name   	   = "";
	$scope.email       = "";
	$scope.tel		   = "";
	$scope.password    = "";
	$scope.confirm     = "";
	
	$scope.results     = [];
	$scope.details	   = [];
	
	// Submit Registration
	$scope.Add = function() {
		// password optional on update
		if ( $scope.AddLabel == "Add" || $scope.password != "" )
			hpassword = md5($scope.password);
		else
			hpassword = "";
			
		json = { 'userid'    : $scope.userid,
				 'action'    : "add",
				 'summary'   : $scope.summary,
				 'position'  : $scope.position,
				 'level'     : $scope.level,
				 'degree'    : $scope.degree,
				 'leadership': $scope.leadership,
				 'skills'	 : [],
				 'name'		 : $scope.name,
				 'email'	 : $scope.email,
				 'tel'		 : $scope.tel,
				 'password'  : hpassword
			   };
		
		// get the user selected skills
		for ( skill in $scope.skill ) {
			check = $scope.skill[ skill ];
			if ( check ) {
				years = $scope.years[ skill ];
				rate  = $scope.rate [ skill ];
				json.skills.push( { 'skill': skill, 'years': years, 'rate': rate } );
			}
		}
			
		if ( $scope.AddLabel == "Update" ) {
			json.action = "update";
			json.userid = $scope.userid;
		}
		
		// Register the User
		if ( $scope.debug ) {
			if ( $scope.AddLabel == "Add" )
				console.log("Registered");
			else
				console.log("Update");
			console.log( JSON.stringify( json ) );
		}
		var res = $http.post( "/add.php", json );
		res.success(function(data, status, headers, config) {
			$scope.showAdd    = false;
			$scope.showSearch = true;
			$scope.showResult = false;
			$scope.showLogin  = false;
			$scope.AddLabel   = "Update";
			$scope.userid = data;
		});
		res.error(function(data, status, headers, config) {
			if ( $scope.debug ) console.log( "STATUS: " + status );
			$scope.addmessage = data;
		});		
	}
	
	// Search for candidates
	$scope.Search = function() {
		json = { 'position'  : $scope.position,
				 'level'     : $scope.level,
				 'degree'    : $scope.degree,
				 'leadership': $scope.leadership,
				 'skills'	 : []
			   };
		
		// get the selected skills
		for ( skill in $scope.skill ) {
			check = $scope.skill[ skill ];
			years = $scope.years[ skill ];
			rate  = $scope.rate [ skill ];
			json.skills.push( { 'skill': skill, 'years': years, 'rate': rate } );
		}
		
		if ( $scope.debug ) {
			console.log("Search");
			console.log( JSON.stringify( json ) );
		}
		var res = $http.post( "/search.php", json );
		res.success(function(data, status, headers, config) {
			$scope.showAdd     = false;
			$scope.showSearch  = false;
			$scope.showResult  = true;
			$scope.showDetails = false;
			
			if ( $scope.debug ) console.log("CANDIDATES " + data );
			if ( $scope.debug ) console.log("CANDIDATES " + data.length );
			$scope.results = [];
			for ( var i = 0; i < data.length; i++ ) {
				var entry    = data[i];
				var id       = entry.id;
				var summary  = entry.summary;
				var position = entry.position;
				var level    = entry.level;
				var degree   = entry.degree;
				var leader   = entry.leadership;
				
				$scope.results.push( { 'id': id, 'summary': summary, 'position': position, 'level': level, 'degree': degree, 'leadership': leader } );
			}
		});
		res.error(function(data, status, headers, config) {
			if ( $scope.debug ) console.log( "STATUS: " + status );
			$scope.searchmessage = JSON.stringify({data: data});
		});		
	}
	
	$scope.View = function( candidate ) {
		if ( $scope.debug ) {
			console.log("View");
			console.log( JSON.stringify( candidate ) );
		}
		
		json = { 'id' : candidate.id };
		var get = $http.post( "/get.php", json );
		get.success(function(data, status, headers, config) {
			$scope.details = [];
			for ( var i = 0; i < data.length; i++ ) {
				var entry = data[i];
				var skill = entry.skill; 
				var years = entry.years; if ( years == 0 ) entry.years = "";
				var rate  = entry.rate;  if ( rate  == 0 ) entry.rate  = "";
				if ( $scope.debug ) console.log( "SKILL " + skill + ", YEARS " + years + ", RATE " + rate );
				
				$scope.details.push( entry );
			}
			$scope.showDetails = true;

		});
		get.error(function(data, status, headers, config) {
			if ( $scope.debug ) console.log( "STATUS: " + status );
			$scope.resultmessage = JSON.stringify({data: data});
		});
	}
	
	// Log user in
	$scope.Login = function() {
		json = { 'email'     : $scope.login_email,
				 'password'  : md5($scope.login_password)
			   };
		if ( $scope.debug ) {
			console.log("Login");
			console.log( JSON.stringify( json ) );
		}
		$scope.loginmessage = ""
		
		var res = $http.post( "/login.php", json );
		res.success(function(data, status, headers, config) {
			$scope.showLogin = false;
			
			$scope.email = $scope.login_email;
			
			$scope.userid   = data.id;
			$scope.name     = data.name;
			$scope.tel      = data.tel;
			$scope.summary  = data.summary;
			$scope.position = data.position;
			$scope.level    = data.level;
			$scope.degree   = data.degree;
			$scope.leadership = data.leadership;
			
			$scope.AddLabel = "Update";
			$scope.password = "";
			$scope.confirm  = "";
			
			json = { 'id' : $scope.userid };
			var get = $http.post( "/get.php", json );
			get.success(function(data, status, headers, config) {
				for ( var i = 0; i < data.length; i++ ) {
					var entry = data[i];
					var skill = entry.skill;
					var years = entry.years;
					var rate  = entry.rate;
					if ( $scope.debug ) console.log( "SKILL " + skill + ", YEARS " + years + ", RATE " + rate );
				
					$scope.skill[ skill ] = true;
					if ( years != "0" ) $scope.years[ skill ] = parseInt(years);
					if ( rate  != "0" ) $scope.rate [ skill ] = parseInt(rate);
				}
			});
			get.error(function(data, status, headers, config) {
				if ( $scope.debug ) console.log( "STATUS: " + status );
				$scope.loginmessage = data;
			});
			
			$scope.showAdd     = true;
			$scope.showSearch  = false;
			$scope.showResults = false;
		});
		res.error(function(data, status, headers, config) {
			if ( $scope.debug ) console.log( "STATUS: " + status );
			$scope.loginmessage = data;
		});		
	}
	
	// Log user out
	$scope.Logout = function() {
		var res = $http.post( "/logout.php", json );
		$scope.showLogin = true;
		$scope.userid    = -1;
		$scope.login_email = "";
		$scope.login_password = "";	
		$scope.AddLabel = "Add";
		
		$scope.Clear();
	}
	
	// Clear the Add/Search Form
	$scope.Clear = function() {
		$scope.skill       = [];  	// technical skill
		$scope.years       = [];	// years of experience
		$scope.rate        = [];	// self-rating
		$scope.summary	   = "";
		$scope.position    = "";	// position
		$scope.level       = "";
		$scope.degree	   = "";
		$scope.leadership  = "";
		$scope.name   	   = "";
		$scope.email       = "";
		$scope.tel		   = "";
		$scope.password    = "";
		$scope.confirm     = "";
	}
})
.directive( "registry", function() {
	return {
		restrict: 'A',
		templateUrl: 'registry.html'
	}
});

var compareTo = function() {
    return {
        require: "ngModel",
        scope: {
            otherModelValue: "=compareTo"
        },
        link: function(scope, element, attributes, ngModel) {
             
            ngModel.$validators.compareTo = function(modelValue) {
                return modelValue == scope.otherModelValue;
            };
 
            scope.$watch("otherModelValue", function() {
                ngModel.$validate();
            });
        }
    };
};

labs.directive("compareTo", compareTo);
 
