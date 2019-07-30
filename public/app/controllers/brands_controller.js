app.controller('BrandsController', function ($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, 
	                                         toastr, BrandService) {

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

		if (! data.packs_per_box) {
			invalid = toastr.warning('Paquetes por caja requerido', 'Validaciones');
		}

		if (! data.cost) {
			invalid = toastr.warning('Costo requerido', 'Validaciones');
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
			_saving: false
		};
	}


	BaseController.call(this, $scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, BrandService);

});