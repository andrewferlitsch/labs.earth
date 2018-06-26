labs.controller( 'dataCtrl', function( $scope ) {
	$scope.vocabulary = false;
	$scope.specification = false;
})
.directive( "ontology", function() {
	return {
		restrict: 'A',
		templateUrl: 'data/ontology.html'
	}
})
.directive( "vocabulary", function() {
	return {
		restrict: 'A',
		templateUrl: 'data/vocabulary.html'
	}
})
.directive( "specification", function() {
	return {
		restrict: 'A',
		templateUrl: 'data/specification.html'
	}
});
