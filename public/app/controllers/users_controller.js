app.controller('UsersController', function ($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, 
	                                        toastr, UserService, RoleService) {

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
			role_id: '',
			_saving: false
		};
	}

	this.beforeViewLoaded = function () {
		$scope.fetchRoles();
	}


	$scope.roles = [];


	$scope.fetchRoles = function () {
		RoleService.read({
			//filters: [{ field: 'active', value: 1 }],
			//order: { field: 'name', type: 'asc' }
		}).success(function (response) {
			// do not add "SYS" role
			response.forEach(function (item) {
				if (item.code != 'SYS') {
					$scope.roles.push(item);
				}
			});
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}


	BaseController.call(this, $scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, UserService);

});