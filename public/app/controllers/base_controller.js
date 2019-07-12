function BaseController($scope, $route, $location, $ngConfirm, ModelService, toastr) {
	var me = this;
	var index = me.index || '/';
	var pagination = {
		page: 1,
		total: 1,
		limit: 5
	};

	// Must specify $scope.data i.e:
	// $scope.data = {
	// 	id: 0,
	// 	field1: '',
	// 	field2: '',
	// 	field3: ''
	// }

	// grid data
	$scope.list = [];

	// other
	$scope.title = me.title.new || 'Nuevo Registro';
	$scope.search = '';
	$scope.pageInfo = '1/1';
	$scope.pageDetailInfo = '1/1';

	$scope.savingForm = false;

	// templates / partials
	$scope.tpls = {
		new_button       : 'partials/_tpls/new_button.html',
		new_button_sm    : 'partials/_tpls/new_button_small.html',
		search           : 'partials/_tpls/index_search.html',
		paginator        : 'partials/_tpls/index_paginator.html',
		paginator_detail : 'partials/_tpls/index_paginator_detail.html',
		actions          : 'partials/_tpls/index_actions.html',
		filter_status    : 'partials/_tpls/index_filter_status.html',
		filter_cancel    : 'partials/_tpls/index_filter_cancel.html',
		change_status    : 'partials/_tpls/index_change_status.html',
		form_toolbar     : 'partials/_tpls/form_toolbar.html',
	}

	$scope.new = function () {
		// go to module new
		location.href = '/#'+ index +'-new/';
	}

	$scope.close = function () {
		// return to module index
		location.href = '/#' + index;
	}

	$scope.save = function () {
		var data = me.validation();
		
		if (data) {
			$scope.savingForm = true;
			
			ModelService.save(data)
				.success(function(response) {
					toastr.success('Registro guardado');
					$location.path(index);
					$scope.savingForm = false;
				})
				.error(function(response) {
					if (response.errors) {
						response.errors.forEach(function (item) {
							toastr.error(item);
						});
					} else {
						toastr.error(response.msg || 'Error en el servidor');
					}
					$scope.savingForm = false;
				});
		}
	}

	$scope.read = function () {
		ModelService.read({
			page: pagination.page,
			filters: $scope.mapFiltersBase(),
			search: $scope.search
		}).success(function (response) {
			$scope.list = response.data;
			$scope.setPagination(response, pagination);
			if ($scope.afterRead) $scope.afterRead();
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
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
						ModelService.delete({
							id: id
						}).success(function (response) {
							toastr.warning('Registro eliminado');
							$scope.paginate('first', true);
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

	$scope.view = function (id) {
		// go to module edit
		location.href = '/#'+ index +'-edit/'+ id;
	}

	$scope.edit = function (id) {
		$scope.data.id = id;
		$scope.title = me.title.edit || 'Editar Registro';
		
		ModelService.get({
			id : id
		}).success(function(response) {
			$scope.data = response;
			if ($scope.afterEdit) $scope.afterEdit();
		}).error(function(response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	$scope.clearSearch = function () {
		$scope.search = '';
		$scope.paginate('first', true);
	}

	$scope.searchData = function () {
		$scope.paginate('first', true);
	}

	$scope.changeFilter = function () {
		$scope.paginate('first', true);
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

	$scope.setPagination = function (data, pagination) {
		pagination.total = data.last_page;
		$scope.pageInfo = (pagination.page) +'/'+ pagination.total;
	}

	$scope.setPaginationDetail = function (data, pagination) {
		$scope.pagination_detail.total = data.last_page;
		$scope.pageDetailInfo = ($scope.pagination_detail.page) +'/'+ $scope.pagination_detail.total;
	}

	$scope.paginate = function (type, force) {
		var data = pagination;
		var page = data.current_page;

		switch (type) {
			case 'first' :
				data.page = 1;
				break;
			case 'previous' :
				if (data.page > 1) {
					data.page--;
				}
				break;
			case 'next' :
				if (data.page < data.total) {
					data.page++;
				}
				break;
			case 'last' :
				data.page = data.total;
				break;
		}

		if (page != data.page || force) {
			$scope.read(); // read only if page has changed
		}
	}

	$scope.mapFiltersBase = function () {
		if ($scope.mapFilters) {
			return $scope.mapFilters();
		} else {
			var filters = [];

			$.map($scope.filters, function (value, index) {
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
		if ($route.current.$$route.originalPath == index) {
			if ($scope.beforeRead) $scope.beforeRead();
			$scope.read(); // index view
		} else {
			var id = $route.current.params.id;
			if (id) {
				if ($scope.beforeEdit) $scope.beforeEdit();
				$scope.edit(id); // edit mode
			}
		}
	});
}
