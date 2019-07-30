app.controller('MovementsController', function ($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, 
	                                            toastr, MovementService, WarehouseService, BrandService) {
	var self = this;

	this.list = {
		order: { field: 'mov_date', type: 'desc' },
		filters: { active: '1', warehouse_id: '' }
	}

	this.form = {
		titleNew: 'Nuevo',
		titleEdit: 'Editar', 
		templateUrl: '/partials/movements/form.html',
		size: 'md'
	};

	// form validations
	this.validation = function () {
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

	// model reseting
	this.clearModel = function () {
		$scope.model = {
			id: 0,
			mov_date: '',
			//warehouse_id: '',
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

		$scope.boxesUnityDetail = 0;
	}

	this.beforeViewLoaded = function () {
		$scope.fetchWarehouses();
		$scope.fetchBrands();
	}



	$scope.show_form = false;

	$scope.warehouses = [];
	$scope.warehousesTarget = [];
	$scope.selWarehouse = {};

	$scope.brands = [];
	$scope.selBrandId = '';
	$scope.selBrand = {};

	$scope.quantityDetail = 0;
	$scope.boxesUnityDetail = 0; // 0: packages | 1: boxes

	$scope.selectedMovement = {};


	// ========================================================
	// - Specific methods -
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
		}).error(function (response) {
			$scope.modalCancel.dismiss();
			toastr.error(response.msg || 'Error en el servidor');
		});
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

		if (_.find($scope.model.details, { brandID: $scope.selBrand.id })) {
			toastr.warning('Marca ya agregada a los detalles', 'Validaciones');
			return false;
		}


		// if boxes selected, calculate total of packages
		let quantity = $scope.quantityDetail;
		if ($scope.boxesUnityDetail) {
			quantity = $scope.quantityDetail * $scope.selBrand.packs_per_box;
		}

		$scope.model.details.push({
			brandID: $scope.selBrand.id,
			brand: {
				id: $scope.selBrand.id,
				name: $scope.selBrand.name
			},
			quantity: quantity
		});

		$scope.clearDetail();
		$('[ng-model="selBrandId"]').focus();
	}

	$scope.deleteDetail = function (index) {
		$scope.model.details.splice(index, 1);
	}
	// ========================================================


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


	BaseController.call(this, $scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, MovementService);

	// ========================================================
	// - Base controller replaced methods -
	$scope.new = function () {
		if (! $scope.table.filters.warehouse_id) {
			toastr.warning('Seleccione un almacén', 'Validaciones');
			return false;
		}

		self.clearModel();
		$scope.openForm(false);
	}

	$scope.save = function () {
		var data = self.validation();

		if (data) {
			$scope.model._saving = true;

			$scope.loading = MovementService.save(data)
				.success(function(response) {
					toastr.success('Registro guardado');

					self.clearModel();
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

	$scope.openForm = function (record) {
		$scope.show_form = true;
	}
	// ========================================================
});