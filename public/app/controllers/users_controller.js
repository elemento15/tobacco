app.controller('UsersController', function ($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, 
	                                        toastr, UserService) {

	this.list = {
		order: { field: 'name', type: 'asc' },
		filters: { active: '1' }
	}

	this.form = {
		titleNew: 'Nuevo',
		titleEdit: 'Editar', 
		templateUrl: '/partials/users/form.html',
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
			email: '',
			role: '',
			_saving: false
		};
	}


	BaseController.call(this, $scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, UserService);

});