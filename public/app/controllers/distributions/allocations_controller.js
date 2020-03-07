app.controller('AllocationsController', function ($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, 
	                                              AllocationService, SalespersonService, BrandService) {

	this.modelType = 'E';

	DistributionsController.call(this, $scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, AllocationService, SalespersonService, BrandService);	
});