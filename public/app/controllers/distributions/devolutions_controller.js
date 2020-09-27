app.controller('DevolutionsController', function ($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, 
	                                              AllocationService, SalespersonService, BrandService, BrandTypeService) {

	this.modelType = 'D';

	DistributionsController.call(this, $scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, AllocationService, SalespersonService, BrandService, BrandTypeService);
});