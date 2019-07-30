app.controller('SalespersonsController', function ($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, 
	                                               toastr, SalespersonService, BrandService) {

	this.list = {
		order: { field: 'name', type: 'asc' },
		filters: { active: '1' }
	}

	this.form = {
		titleNew: 'Nuevo',
		titleEdit: 'Editar', 
		templateUrl: '/partials/salespersons/form.html',
		size: 'md'
	};

	// form validations
	this.validation = function () {
		var data = $scope.model;
		var invalid = false;
		
		if (! data.name) {
			invalid = toastr.warning('Nombre requerido', 'Validaciones');
		}

		return (invalid) ? false : data;
	}

	// model reseting
	this.clearModel = function () {
		$scope.model = {
			id: 0,
			name: '',
			mobile: '',
			_saving: false
		};
	}

	this.afterViewLoaded = function () {
		$scope.readBrands();
	}


	// ========================================================
	// - Functions and attributes related to price management -

	$scope.showPrices = false;
	$scope.selectedSalesPerson = {};
	$scope.brandsPrices = [];

	$scope.showPricesScreen = function () {
		var table = $scope.table;
		$scope.showPrices = true;
		$scope.selectedSalesPerson = table.data[table.selected];
		$scope.readPrices();
	}

	$scope.readBrands = function () {
		BrandService.read({
			filters: [{ field: 'active', value: 1 }],
			order: { field: 'name', type: 'asc' },
		}).success(function (response) {
			var brands = response;
			$scope.brandsPrices = _.map(brands, function (item) {
				return { id: item.id, name: item.name, price: 0 };
			});
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	$scope.readPrices = function () {
		$scope.loading = SalespersonService.prices({
			id: $scope.selectedSalesPerson.id
		}).success(function (response) {
			var prices = response;
			var record;
			
			_.each($scope.brandsPrices, function(item, key) {
				if ((record = _.find(prices, { brand_id: item.id })) != undefined) {
					$scope.brandsPrices[key].price = parseFloat(record.price);
				} else {
					$scope.brandsPrices[key].price = 0;
				}
			});
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	$scope.savePrices = function () {
		$scope.loading = SalespersonService.savePrices({
			id: $scope.selectedSalesPerson.id,
			data: $scope.brandsPrices
		}).success(function (response) {
			toastr.success('Lista de precios guardada');
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	// ========================================================


	BaseController.call(this, $scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, SalespersonService);

});