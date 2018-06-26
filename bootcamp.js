labs.controller( 'bootcampCtrl', function( $scope ) {
	$scope.name = ""
	$scope.email = ""
	$scope.bootcamp1 = function() {
		alert( $scope.name + " " + $scope.email );
	}
})
.directive( "bootcamp", function() {
	return {
		restrict: 'A',
		templateUrl: 'bootcamp.html'
	}
});
