app.controller('StocksController', function ($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout,
	                                         toastr, StockService, WarehouseService, BrandTypeService) {
	var self = this;

	this.list = {
		//order: { field: 'brand', type: 'asc' },
		filters: { active: '1', warehouse_id: '' }
	}

	this.form = {};

	this.clearModel = function () {
		//
	}

	this.beforeViewLoaded = function () {
		$scope.fetchWarehouses();
		$scope.fetchBrandTypes();
	}

	$scope.warehouses = [];
	$scope.brand_types = [];

	$scope.boxesUnityDetail = 1; // 0: packages | 1: boxes


	// ========================================================
	$scope.fetchWarehouses = function () {
		WarehouseService.read({
			filters: [{ field: 'active', value: 1 }],
			order: { field: 'name', type: 'asc' }
		}).success(function (response) {
			$scope.warehouses = response;
			$scope.table.filters.warehouse_id = window.defaultWarehouseId;
			$scope.read(1);
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


	BaseController.call(this, $scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, StockService);
});