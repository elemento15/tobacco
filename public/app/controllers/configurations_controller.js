app.controller('ConfigurationsController', function ($scope, $http, $route, $location, toastr,
	                                                 WarehouseService, ConfigurationService) {
    
    $scope.model = {
    	allocation_warehouse_id: '',
    	default_warehouse_id: ''
    };

    $scope.warehouses = [];


    $scope.fetchWarehouses = function () {
		WarehouseService.read({
			filters: [{ field: 'active', value: 1 }],
			order: { field: 'name', type: 'asc' },
		}).success(function (response) {
			$scope.warehouses = response;
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
    }

    $scope.fetchConfigurations = function () {
    	$scope.loading = ConfigurationService.get()
	    	.success(function (response) {
				$scope.model = response;
			}).error(function (response) {
				toastr.error(response.msg || 'Error en el servidor');
			});
    }

    $scope.save = function () {
    	$scope.loading = ConfigurationService.save($scope.model)
			.success(function(response) {
				toastr.success('Registro guardado');
			})
			.error(function(response) {
				toastr.error(response.msg || 'Error en el servidor');
			});
    }


    $scope.$on('$viewContentLoaded', function (view) {
		$scope.fetchWarehouses();
		$scope.fetchConfigurations();
	});

});