function BaseController($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, ModelService) {
	var me = this;

	// grid data
	$scope.table = {
		data: [],
		page: 1,
		total: 0,
		items: 5,
		search: '',
		selected: null,
		order: me.list.order,
		filters: me.list.filters
	};

	// initialize model
	$scope.model = {};

	// templates / partials
	$scope.tpls = {
		new_button       : 'partials/_tpls/new_button.html',
		search           : 'partials/_tpls/index_search.html',
		actions          : 'partials/_tpls/index_actions.html',
		filter_status    : 'partials/_tpls/index_filter_status.html',
		change_status    : 'partials/_tpls/index_change_status.html',
	};


	$scope.read = function (page) {
		$scope.loading = ModelService.read({
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
		// ---
		$scope.openForm(false);
	}

	$scope.view = function (id) {
		me.clearModel();

		$scope.loading = ModelService.get({
			id : id
		}).success(function(response) {
			$scope.openForm(response);
			$scope.model = response;
		}).error(function(response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	$scope.save = function () {
		var data = me.validation();

		if (data) {
			$scope.model._saving = true;

			$scope.loading = ModelService.save(data)
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
		
		ModelService.activate({
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

		ModelService.deactivate({
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
						$scope.loading = ModelService.delete({
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
		var title = (record) ? me.form.titleEdit : me.form.titleNew;
		me.clearModel();

		$scope.modalForm = $uibModal.open({
			ariaLabelledBy: 'modal-title',
			ariaDescribedBy: 'modal-body',
			templateUrl: me.form.templateUrl,
			size: me.form.size,
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
		if (me.beforeViewLoaded) me.beforeViewLoaded();
		
		me.clearModel();
		$scope.read();

		if (me.afterViewLoaded) me.afterViewLoaded();
	});
}
