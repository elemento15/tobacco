function DistributionsController($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, 
	                             AllocationService, SalespersonService, BrandService) {

	var self = this;

	this.list = {
		order: { field: 'rec_date', type: 'desc' },
		filters: { active: '1', type: self.modelType }
	};

	this.form = {
		titleNew: 'Nuevo',
		titleEdit: 'Editar', 
		//templateUrl: '/partials/distributions/allocations/form.html',
		size: 'md'
	};

	// form validations
	this.validation = function () {
		var data = $scope.model;
		var invalid = false;

		return (invalid) ? false : data;
	}

	// model reseting
	this.clearModel = function () {
		$scope.model = {
			id: 0,
			rec_date: '',
			type: self.modelType,
			salesperson_id: '',
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
		$scope.fetchSalespeople();
		$scope.fetchBrands();
	}


	$scope.modelType = self.modelType;
	$scope.show_form = false;

	$scope.brands = [];
	$scope.selBrandId = '';
	$scope.selBrand = {};

	$scope.salespeople = [];

	$scope.quantityDetail = 0;
	$scope.boxesUnityDetail = 1; // 0: packages | 1: boxes

	$scope.selectedAllocation = {};

	$scope.is_cancelling = false;
	$scope.cancel_comment = '';

	// ========================================================
	// - Specific methods -
	$scope.openCancel = function () {
		$scope.selectedAllocation = $scope.table.data[$scope.table.selected];

		$scope.modalCancel = $uibModal.open({
			ariaLabelledBy: 'modal-title',
			ariaDescribedBy: 'modal-body',
			templateUrl: '/partials/distributions/cancel.html',
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
		$scope.loading = AllocationService.cancel({
			id: $scope.selectedAllocation.id,
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

	$scope.fetchSalespeople = function () {
		SalespersonService.read({
			filters: [{ field: 'active', value: 1 }],
			order: { field: 'name', type: 'asc' }
		}).success(function (response) {
			$scope.salespeople = response;
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
	// ========================================================

	
	$scope.$watch('selBrandId', function(newVal, oldVal) {
		// assign selected brand
		$scope.selBrand = _.find($scope.brands, function(o) { 
			return o.id == newVal;
		});
	});
	

	BaseController.call(this, $scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, toastr, AllocationService);

	// ========================================================
	// - Base controller replaced methods -
	$scope.new = function () {
		self.clearModel();
		$scope.openForm(false);
		//$scope.filterConcepts('E');
	}

	$scope.save = function () {
		var data = self.validation();

		if (data) {
			$scope.model._saving = true;

			$scope.loading = AllocationService.save(data)
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
}