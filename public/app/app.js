var app = angular.module('mainApp', ['ngRoute', 'ui.bootstrap', 'ui.select', 'toastr', 'cp.ngConfirm', 'cgBusy']);

app.config(function ($routeProvider, $provide, toastrConfig) {
	angular.extend(toastrConfig, {
		autoDismiss: false,
		containerId: 'toast-container',
		maxOpened: 0,    
		newestOnTop: true,
		positionClass: 'toast-bottom-right',
		preventDuplicates: false,
		preventOpenDuplicates: false,
		target: 'body'
	});
	
	$routeProvider
		.when('/',{
				controller: 'HomeController',
				templateUrl: '/partials/home.html'
			})
		.when('/salespersons',{
				controller: 'SalespersonsController',
				templateUrl: '/partials/salespersons/index.html'
			})
		.when('/brands',{
				controller: 'BrandsController',
				templateUrl: '/partials/brands/index.html'
			})
		.when('/warehouses',{
				controller: 'WarehousesController',
				templateUrl: '/partials/warehouses/index.html'
			})
		.when('/stocks',{
				controller: 'StocksController',
				templateUrl: '/partials/warehouses/stocks.html'
			})
		.when('/users',{
				controller: 'UsersController',
				templateUrl: '/partials/users/index.html'
			})
		.when('/movements',{
				controller: 'MovementsController',
				templateUrl: '/partials/movements/index.html'
			})
		.when('/allocations',{
				controller: 'AllocationsController',
				templateUrl: '/partials/distributions/allocations/index.html'
			})
		.when('/liquidations',{
				controller: 'LiquidationsController',
				templateUrl: '/partials/distributions/liquidations/index.html'
			})
		.when('/devolutions',{
				controller: 'DevolutionsController',
				templateUrl: '/partials/distributions/devolutions/index.html'
			})

		.otherwise({ redirectTo: '/' });

	// regular expression definitions
	app.regexpRFC = /^([A-Z,Ñ,&]{3,4}([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])[A-Z|\d]{3})$/;
	app.regexpEmail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

	// localization for moment.js
	moment.locale('es', {
		months : 'Enero_Febrero_Marzo_Abril_Mayo_Junio_Julio_Agosto_Septiembre_Octibre_Noviembre_Diciembre'.split('_'),
	    monthsShort : 'Ene_Feb_Mar_Abr_May_Jun_Jul_Ago_Sep_Oct_Nov_Dic'.split('_'),
	    monthsParseExact : true,
	    weekdays : 'Domingo_Lunes_Martes_Miercoles_Jueves_Viernes_Sabado'.split('_'),
	    weekdaysShort : 'Dom._Lun._Mar._Mie._Jue._Vie._Sab.'.split('_'),
	    weekdaysMin : 'Do_Lu_Ma_Mi_Ju_Vi_Sa'.split('_'),
	    weekdaysParseExact : true,
	});
});

app.directive('stringToNumber', function() {
	return {
		require: 'ngModel',
		link: function (scope, element, attrs, ngModel) {
			ngModel.$parsers.push(function(value) {
				return '' + value;
			});
			ngModel.$formatters.push(function(value) {
				return  parseFloat(value, 10);
			});
		}
	}
});

// overrride cg-busy defaults
app.value('cgBusyDefaults',{
	message: 'Espere un momento',
	//minDuration: 10000
});