app.controller('MovementsController', function ($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, 
	                                            toastr, MovementService, WarehouseService, BrandService, ConceptService) {
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

		if (! data.concept_id) {
			invalid = toastr.warning('Concepto requerido', 'Validaciones');
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
			concept_id: '',
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

		$scope.boxesUnityDetail = 1;
		$scope.quantityDetail = 0;
		$scope.selBrandId = '';
	}

	this.beforeViewLoaded = function () {
		$scope.fetchWarehouses();
		$scope.fetchBrands();
		$scope.fetchConcepts();
	}


	$scope.show_form = false;

	$scope.warehouses = [];
	$scope.warehousesTarget = [];
	$scope.selWarehouse = {};

	$scope.concepts = [];
	$scope.filteredConcepts = [];

	$scope.brands = [];
	$scope.selBrandId = '';
	$scope.selBrand = {};

	$scope.quantityDetail = 0;
	$scope.boxesUnityDetail = 1; // 0: packages | 1: boxes

	$scope.selectedMovement = {};

	$scope.is_cancelling = false;
	$scope.cancel_comment = '';


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
				//
			},
			controllerAs: '$ctrl',
			scope: $scope
		});
	}

	$scope.cancel = function () {
		$scope.loading = MovementService.cancel({
			id: $scope.selectedMovement.id,
			comments: $scope.cancel_comment
		}).success(function (response) {
			$scope.modalCancel.dismiss();
			$scope.is_cancelling = false;
			$scope.cancel_comment = '';
			$scope.read(1);
		}).error(function (response) {
			$scope.is_cancelling = false;
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

	$scope.fetchConcepts = function () {
		ConceptService.read({
			filters: [{ field: 'active', value: 1 },{ field: 'is_automatic', value: 0 }],
			order: { field: 'name', type: 'asc' }
		}).success(function (response) {
			$scope.concepts = response;
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
		if ($scope.quantityDetail <= 0) {
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
			quantity: quantity,
			packs_per_box: $scope.selBrand.packs_per_box
		});

		$scope.clearDetail();
		$('[ng-model="selBrandId"]').focus();
	}

	$scope.deleteDetail = function (index) {
		$scope.model.details.splice(index, 1);
	}

	$scope.filterConcepts = function (option) {
		$scope.filteredConcepts = _.filter($scope.concepts || [], function (o) {
			return o.type == option;
		});
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

	$scope.$watch('model.type', function(newVal, oldVal) {
		$scope.model.concept_id = '';
		$scope.filterConcepts(newVal);
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
		$scope.filterConcepts('E');
	}

	$scope.save = function () {
		var data = self.validation();

		if (data) {
			$scope.model._saving = true;
			$scope.model.warehouse_id = $scope.table.filters.warehouse_id;

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