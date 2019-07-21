app.controller('MovementsController', function ($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, 
	                                            toastr, MovementService, WarehouseService, BrandService) {

	$scope.table = {
		data: [],
		page: 1,
		total: 0,
		items: 5,
		search: '',
		selected: null,
		order: { field: 'mov_date', type: 'desc' },
		filters: { active: '1', warehouse_id: '' }
	};

	$scope.model = {
		id: 0,
		mov_date: '',
		warehouse_id: '',
		type: 'E',
		warehouse_target: '',
		transfer_to: '',
		transfer_from: '',
		active: '',
		cancel_user_id: '',
		cancel_date: '',
		comments: '',
		details: [],
		_saving: false
	};

	$scope.show_form = false;

	$scope.warehouses = [];
	$scope.warehousesTarget = [];
	$scope.selWarehouse = {};

	$scope.brands = [];
	$scope.selBrandId = '';
	$scope.selBrand = {};

	$scope.quantityDetail = 0;

	$scope.selectedMovement = {};

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
		
		if (data.type == 'T' && !data.warehouse_target) {
			invalid = toastr.warning('Almacén destino requerido', 'Validaciones');
		}

		if (data.details.length < 1) {
			invalid = toastr.warning('Capture detalles al movimiento', 'Validaciones');
		}

		return (invalid) ? false : data;
	}

	$scope.read = function (page) {
		$scope.loading = MovementService.read({
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
		if (! $scope.table.filters.warehouse_id) {
			toastr.warning('Seleccione un almacén', 'Validaciones');
			return false;
		}

		$scope.clearModel();
		$scope.openForm(false);
	}

	$scope.view = function (id) {
		$scope.clearModel();

		$scope.loading = MovementService.get({
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

			$scope.loading = MovementService.save(data)
				.success(function(response) {
					toastr.success('Registro guardado');

					$scope.clearModel();
					$scope.read();
					$scope.show_form = false;
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
			mov_date: '',
			warehouse_id: $scope.table.filters.warehouse_id,
			type: 'E',
			transfer_to: '',
			transfer_from: '',
			active: '',
			cancel_user_id: '',
			cancel_date: '',
			comments: '',
			details: [],
			_saving: false
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
		
		MovementService.activate({
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

		MovementService.deactivate({
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
						$scope.loading = MovementService.delete({
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

	$scope.openCancel = function () {
		$scope.selectedMovement = $scope.table.data[$scope.table.selected];

		$scope.modalCancel = $uibModal.open({
			ariaLabelledBy: 'modal-title',
			ariaDescribedBy: 'modal-body',
			templateUrl: '/partials/movements/cancel.html',
			size: 'md',
			backdrop: 'static',
			controller: function ($scope) {
				$scope.is_canceling = false;
			},
			controllerAs: '$ctrl',
			scope: $scope
		});
	}

	$scope.cancel = function () {
		$scope.loading = MovementService.cancel({
			id: $scope.selectedMovement.id
		}).success(function (response) {
			$scope.modalCancel.dismiss();
			$scope.read(1);
			console.log(response);
		}).error(function (response) {
			$scope.modalCancel.dismiss();
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	$scope.openForm = function (record) {
		$scope.show_form = true;
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

	$scope.fetchBrands = function () {
		BrandService.read({
			filters: [{ field: 'active', value: 1 }],
			order: { field: 'name', type: 'asc' }
		}).success(function (response) {
			$scope.brands = response;
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	$scope.clearDetail = function () {
		$scope.selBrandId = '';
		$scope.quantityDetail = 0;
	}

	$scope.enterQuantity = function (evt) {
		if (evt.keyCode == 13) {
			$('[ng-click="addDetail()"]').focus();
		}
	}

	$scope.addDetail = function () {
		if ($scope.quantityDetail < 1) {
			toastr.warning('Cantidad inválida', 'Validaciones');
			return false;
		}

		if (! $scope.selBrandId) {
			toastr.warning('Marca inválida', 'Validaciones');
			return false;
		}

		//console.log( $scope.model.details );
		//console.log( $scope.selBrandId );

		if (_.find($scope.model.details, { brandID: $scope.selBrand.id })) {
			toastr.warning('Marca ya agregada a los detalles', 'Validaciones');
			return false;
		}


		$scope.model.details.push({
			brandID: $scope.selBrand.id,
			brand: {
				id: $scope.selBrand.id,
				name: $scope.selBrand.name
			},
			quantity: $scope.quantityDetail
		});

		$scope.clearDetail();
		$('[ng-model="selBrandId"]').focus();
	}

	$scope.deleteDetail = function (index) {
		$scope.model.details.splice(index, 1);
	}

	$scope.$on('$viewContentLoaded', function (view) {
		$scope.fetchWarehouses();
		$scope.fetchBrands();
	});

	$scope.$watch('table.filters.warehouse_id', function(newVal, oldVal) {
		$scope.model.warehouse_id = newVal;
		$scope.model.transfer_to = '';
		
		// assign selected warehouse
		$scope.selWarehouse = _.find($scope.warehouses || [], function(o) { 
			return o.id == newVal;
		});

		// populate list of warehouse that can receive a transfer
		$scope.warehousesTarget = _.filter($scope.warehouses || [], function (o) {
			return o.id != newVal;
		});
	});

	$scope.$watch('selBrandId', function(newVal, oldVal) {
		// assign selected brand
		$scope.selBrand = _.find($scope.brands, function(o) { 
			return o.id == newVal;
		});
	});

});