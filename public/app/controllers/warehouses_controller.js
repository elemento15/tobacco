app.controller('WarehousesController', function ($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, 
	                                             toastr, WarehouseService) {

	$scope.table = {
		data: [],
		page: 1,
		total: 0,
		items: 5,
		search: '',
		selected: null,
		order: { field: 'name', type: 'asc' },
		filters: { active: '1' }
	};

	$scope.model = {
		id: 0,
		name: '',
		_saving: false
	};

	$scope.tpls = {
		new_button       : 'partials/_tpls/new_button.html',
		search           : 'partials/_tpls/index_search.html',
		actions          : 'partials/_tpls/index_actions.html',
		filter_status    : 'partials/_tpls/index_filter_status.html',
		change_status    : 'partials/_tpls/index_change_status.html',
	};

	$scope.validation = function () {
		var data = $scope.model;
		var invalid = false;
		
		if (! data.name) {
			invalid = toastr.warning('Nombre requerido', 'Validaciones');
		}

		return (invalid) ? false : data;
	}

	$scope.read = function (page) {
		$scope.loading = WarehouseService.read({
			page: page || $scope.table.page,
			filters: $scope.mapFiltersBase(),
			search: $scope.table.search,
			order: $scope.table.order
		}).success(function (response) {
			$scope.table.data = response.data;
			$scope.table.page = response.current_page;
			$scope.table.total = response.total;
			$scope.table.items = response.per_page;
			$scope.table.selected = null;
			
			if ($scope.afterRead) $scope.afterRead();
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	$scope.new = function () {
		$scope.openForm(false);
	}

	$scope.view = function (id) {
		$scope.clearModel();

		$scope.loading = WarehouseService.get({
			id : id
		}).success(function(response) {
			$scope.openForm(response);
			$scope.model = response;
		}).error(function(response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	$scope.save = function () {
		var data = $scope.validation();

		if (data) {
			$scope.model._saving = true;

			$scope.loading = WarehouseService.save(data)
				.success(function(response) {
					toastr.success('Registro guardado');
					$scope.modalForm.dismiss();

					$scope.model._saving = false;

					$scope.read();
				})
				.error(function(response) {
					if (response.errors) {
						response.errors.forEach(function (item) {
							toastr.error(item);
						});
					} else {
						toastr.error(response.msg || 'Error en el servidor');
					}
					$scope.model._saving = false;
				});
		}
	}

	$scope.clearSearch = function () {
		$scope.table.search = '';
		$scope.read(1);
	}

	$scope.clearModel = function () {
		$scope.model = {
			id: 0,
			name: '',
			packs_per_box: 0,
			cost: 0
		};
	}

	$scope.setOrder = function (field) {
		var order = $scope.table.order;
		
		if (field != order.field) {
			order.field = field;
			order.type = 'asc';
		} else {
			order.type = (order.type == 'asc') ? 'desc' : 'asc'; 
		}

		$scope.read();
	}

	$scope.showOrderIcon = function (field, type) {
		var order = $scope.table.order;
		return (field == order.field && type == order.type) ? true : false;
	}

	$scope.setActive = function (record) {
		record.status_loading = true;
		
		WarehouseService.activate({
			id: record.id
		}).success(function (response) {
			record.active = response.active;
			record.status_loading = false;
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
			record.status_loading = false;
		});
	}

	$scope.setInactive = function (record) {
		record.status_loading = true;

		WarehouseService.deactivate({
			id: record.id
		}).success(function (response) {
			record.active = response.active;
			record.status_loading = false;
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
			record.status_loading = false;
		});
	}

	$scope.delete = function (id) {
		$ngConfirm({
			title: 'Eliminar',
			content: '¿Desea eliminar el registro seleccionado?',
			type: 'red',
			buttons: {
				ok: {
					text: 'Aceptar',
					btnClass: 'btn-red',
					action: function () {
						$scope.loading = WarehouseService.delete({
							id: id
						}).success(function (response) {
							toastr.warning('Registro eliminado');
							$scope.read(1);
						}).error(function (response) {
							toastr.error(response.msg || 'Error en el servidor');
						});
					}
				},
				close: {
					text: 'Omitir',
					btnClass: 'btn-default'
				}
			}
		});
	}

	$scope.openForm = function (record) {
		var title = (record) ? 'Editar' : 'Nueva';
		$scope.clearModel();

		$scope.modalForm = $uibModal.open({
			ariaLabelledBy: 'modal-title',
			ariaDescribedBy: 'modal-body',
			templateUrl: '/partials/warehouses/form.html',
			size: 'md',
			backdrop: 'static',
			controller: function ($scope) {
				$scope.title = title;
			},
			controllerAs: '$ctrl',
			scope: $scope
		});
	}

	$scope.mapFiltersBase = function () {
		if ($scope.mapFilters) {
			return $scope.mapFilters();
		} else {
			var filters = [];

			$.map($scope.table.filters, function (value, index) {
				if (value) {
					filters.push({
						field: index,
						value: value
					});
				}
			});

			return filters;
		}
	}

	$scope.$on('$viewContentLoaded', function (view) {
		$scope.read();
	});

});