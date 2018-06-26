labs.controller( 'welcomeCtrl', function( $scope ) {
})
.directive( "welcome", function() {
	return {
		restrict: 'A',
		templateUrl: 'welcome.html'
	}
});
