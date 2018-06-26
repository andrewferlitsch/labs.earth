labs.controller( 'calendarCtrl', function( $scope ) {
})
.directive( "sept2017", function() {
	return {
		restrict: 'A',
		templateUrl: 'calendar/sept2017.html'
	}
})
.directive( "august2017", function() {
	return {
		restrict: 'A',
		templateUrl: 'calendar/august2017.html'
	}
})
.directive( "july2017", function() {
	return {
		restrict: 'A',
		templateUrl: 'calendar/july2017.html'
	}
});
