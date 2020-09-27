app.controller('SalespersonStocksController', function ($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, 
	                                                    toastr, SalespersonStockService, SalespersonService, BrandTypeService) {
	var self = this;

	this.list = {
		//order: { field: 'brand', type: 'asc' },
		filters: { active: '1', salesperson_id: '' }
	}

	this.form = {};

	this.clearModel = function () {
		//
	}

	this.beforeViewLoaded = function () {
		$scope.fetchSalespersons();
		$scope.fetchBrandTypes();
	}

	$scope.salespersons = [];
	$scope.brand_types = [];

	$scope.boxesUnityDetail = 1; // 0: packages | 1: boxes


	// ========================================================
	$scope.fetchSalespersons = function () {
		SalespersonService.read({
			filters: [{ field: 'active', value: 1 }],
			order: { field: 'name', type: 'asc' }
		}).success(function (response) {
			$scope.salespersons = response;
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	$scope.fetchBrandTypes = function () {
		BrandTypeService.read({
			//filters: [{ field: 'active', value: 1 }],
			order: { field: 'name', type: 'asc' }
		}).success(function (response) {
			$scope.brand_types = response;
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}
	// ========================================================


	BaseController.call(this, $scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, SalespersonStockService);
});