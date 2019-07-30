app.controller('AllocationsController', function ($scope, $http, $route, $location, $ngConfirm, $uibModal, $timeout, 
	                                              toastr, AllocationService, SalespersonService, BrandService) {

	$scope.table = {
		data: [],
		page: 1,
		total: 0,
		items: 5,
		search: '',
		selected: null,
		order: { field: 'rec_date', type: 'desc' },
		filters: { active: '1', warehouse_id: '' }
	};

	$scope.model = {
		id: 0,
		rec_date: '',
		salesperson_id: '',
		type: 'E',
		active: '',
		cancel_user_id: '',
		cancel_date: '',
		comments: '',
		details: [],
		_saving: false
	};

	$scope.show_form = false;

	$scope.tpls = {
		new_button       : 'partials/_tpls/new_button.html',
		search           : 'partials/_tpls/index_search.html',
		actions          : 'partials/_tpls/index_actions.html',
		filter_status    : 'partials/_tpls/index_filter_status.html',
		change_status    : 'partials/_tpls/index_change_status.html',
	};

});