app.controller('LiquidationsController', function ($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, 
	                                              AllocationService, SalespersonService, BrandService, BrandTypeService) {

	this.modelType = 'L';

	DistributionsController.call(this, $scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, AllocationService, SalespersonService, BrandService, BrandTypeService);
});