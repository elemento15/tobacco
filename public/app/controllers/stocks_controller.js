app.controller('StocksController', function ($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, 
	                                         toastr, StockService, WarehouseService) {
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
	}

	$scope.warehouses = [];


	// ========================================================
	$scope.fetchWarehouses = function () {
		WarehouseService.read({
			filters: [{ field: 'active', value: 1 }],
			order: { field: 'name', type: 'asc' }
		}).success(function (response) {
			$scope.warehouses = response;
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}
	// ========================================================


	BaseController.call(this, $scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, StockService);
});