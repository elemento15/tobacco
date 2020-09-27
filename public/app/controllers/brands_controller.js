app.controller('BrandsController', function ($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, 
	                                         toastr, BrandService, BrandTypeService) {

	this.list = {
		order: { field: 'name', type: 'asc' },
		filters: { active: '1' }
	}

	this.form = {
		titleNew: 'Nueva',
		titleEdit: 'Editar', 
		templateUrl: '/partials/brands/form.html',
		size: 'md'
	};

	// form validations
	this.validation = function () {
		var data = $scope.model;
		var invalid = false;
		
		if (! data.name) {
			invalid = toastr.warning('Nombre requerido', 'Validaciones');
		}

		if (! data.brand_type_id) {
			invalid = toastr.warning('Tipo de Marca requerido', 'Validaciones');
		}

		if (! data.packs_per_box || data.packs_per_box < 0) {
			invalid = toastr.warning('Paquetes por caja inválido', 'Validaciones');
		}

		if (! data.cost || data.cost < 0) {
			invalid = toastr.warning('Costo inválido', 'Validaciones');
		}

		if (! data.price || data.price < 0) {
			invalid = toastr.warning('Precio inválido', 'Validaciones');
		}

		if (data.price <= data.cost) {
			invalid = toastr.warning('Precio debe ser mayor que Costo', 'Validaciones');
		}

		return (invalid) ? false : data;
	}

	// model reseting
	this.clearModel = function () {
		$scope.model = {
			id: 0,
			name: '',
			packs_per_box: 0,
			cost: 0,
			price: 0,
			brand_type_id: '',
			_saving: false
		};
	}

	this.beforeViewLoaded = function () {
		$scope.fetchBrandTypes();
	}


	$scope.types = [];


	$scope.fetchBrandTypes = function () {
		BrandTypeService.read({
			//filters: [{ field: 'active', value: 1 }],
			order: { field: 'name', type: 'asc' }
		}).success(function (response) {
			$scope.types = response;
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}


	BaseController.call(this, $scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, BrandService);

});