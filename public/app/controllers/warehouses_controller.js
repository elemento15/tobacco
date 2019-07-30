app.controller('WarehousesController', function ($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, 
	                                             toastr, WarehouseService) {

	this.list = {
		order: { field: 'name', type: 'asc' },
		filters: { active: '1' }
	}

	this.form = {
		titleNew: 'Nuevo',
		titleEdit: 'Editar', 
		templateUrl: '/partials/warehouses/form.html',
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
			_saving: false
		};
	}


	BaseController.call(this, $scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, WarehouseService);

});