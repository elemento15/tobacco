app.controller('ReportsController', function ($scope, $http, $route, $location, $uibModal, toastr, ReportService) {


	$scope.rpt01 = {
		date_ini: moment().subtract(6, 'days').toDate(),
		date_end: moment().toDate(),
		date_ini_opened: false,
		date_end_opened: false,
		data: [],
		summary: {
			price: 0,
			cost:  0,
			diff:  0,
			items: 0,
			packs: 0
		}
	};

	$scope.rpt02 = {
		data: [],
		summary: {
			packs: 0,
			boxes: 0,
			amount: 0
		}
	}

	$scope.runRpt = function (opt) {
		if (opt == 1) {
			var rpt = $scope.rpt01;
			
			$scope.loading = ReportService.get({
					type: 'DateRangeSales',
					ini_date: moment(rpt.date_ini).format('YYYY-MM-DD'),
					end_date: moment(rpt.date_end).format('YYYY-MM-DD'),
				}).success(function(response) {
					rpt.data = response;
				})
				.error(function(response) {
					toastr.error(response.msg || 'Error en el servidor');
				});
		}

		if (opt == 2) {
			var rpt = $scope.rpt02;

			$scope.loading = ReportService.get({
					type: 'SalesPersonSummary'
				}).success(function(response) {
					rpt.data = response;
				})
				.error(function(response) {
					toastr.error(response.msg || 'Error en el servidor');
				});
		}
	}

	$scope.downloadRpt = function (opt) {
		if (opt == 1) {
			var rpt = $scope.rpt01;
			var ini = moment(rpt.date_ini).format('YYYY-MM-DD');
			var end = moment(rpt.date_end).format('YYYY-MM-DD');

			window.open('download?type=DateRangeSales&ini_date='+ ini +'&end_date='+ end);
		}

		if (opt == 2) {
			window.open('download?type=SalesPersonSummary');
		}
	}

	$scope.openDatePicker = function (opt) {
		switch(opt) {
			case 'rpt01-ini' : 
				$scope.rpt01.date_ini_opened = true;
				break;
			case 'rpt01-end' : 
				$scope.rpt01.date_end_opened = true;
				break;
		}
	}


	$scope.$watch('rpt01.data', function(newVal, oldVal) {
		$scope.rpt01.summary = {
			price: 0,
			cost:  0,
			diff:  0,
			items: 0,
			packs: 0
		};

		// calculate summary
		newVal.forEach(function (item) {
			$scope.rpt01.summary.price += item.price;
			$scope.rpt01.summary.cost += item.cost;
			$scope.rpt01.summary.diff += item.price - item.cost;
			$scope.rpt01.summary.items += item.items;
			$scope.rpt01.summary.packs += item.packs;
		});
	});

	$scope.$watch('rpt02.data', function(newVal, oldVal) {
		$scope.rpt02.summary = {
			packs: 0,
			boxes: 0,
			amount: 0
		};

		// calculate summary
		newVal.forEach(function (item) {
			$scope.rpt02.summary.packs += item.packs;
			$scope.rpt02.summary.boxes += item.boxes;
			$scope.rpt02.summary.amount += item.amount;
		});
	});


    $scope.$on('$viewContentLoaded', function (view) {
		// ---
	});

});