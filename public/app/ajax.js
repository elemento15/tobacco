app.factory('RoleService', ['$http', function($http) {
	return {
		get  : function (data) {
			return $http.get('roles/'+ data.id);
		},
		read : function(data) {
			return $http.get('roles?'+ jQuery.param(data), data);
		}
	} 
}]);

app.factory('BrandService', ['$http', function($http) {
	return {
		get      : function (data) {
			return $http.get('brands/'+ data.id);
		},
		save     : function (data) {
			return (data.id) ? $http.patch('brands/'+ data.id, data) : $http.post('brands', data);
		},
		read     : function(data) {
			return $http.get('brands?'+ jQuery.param(data), data);
		},
		delete   : function(data) {
			return $http.delete('brands/'+ data.id);
		},
		activate : function(data) {
			return $http.post('brands/'+ data.id +'/activate');
		},
		deactivate : function(data) {
			return $http.post('brands/'+ data.id +'/deactivate');
		}
	} 
}]);

app.factory('SalespersonService', ['$http', function($http) {
	return {
		get      : function (data) {
			return $http.get('salespersons/'+ data.id);
		},
		save     : function (data) {
			return (data.id) ? $http.patch('salespersons/'+ data.id, data) : $http.post('salespersons', data);
		},
		read     : function(data) {
			return $http.get('salespersons?'+ jQuery.param(data), data);
		},
		delete   : function(data) {
			return $http.delete('salespersons/'+ data.id);
		},
		activate : function(data) {
			return $http.post('salespersons/'+ data.id +'/activate');
		},
		deactivate : function(data) {
			return $http.post('salespersons/'+ data.id +'/deactivate');
		},
		prices : function(data) {
			return $http.get('salespersons/'+ data.id +'/prices');
		},
		savePrices : function(data) {
			return $http.post('salespersons/'+ data.id +'/prices', data);
		}
	} 
}]);

app.factory('WarehouseService', ['$http', function($http) {
	return {
		get      : function (data) {
			return $http.get('warehouses/'+ data.id);
		},
		save     : function (data) {
			return (data.id) ? $http.patch('warehouses/'+ data.id, data) : $http.post('warehouses', data);
		},
		read     : function(data) {
			return $http.get('warehouses?'+ jQuery.param(data), data);
		},
		delete   : function(data) {
			return $http.delete('warehouses/'+ data.id);
		},
		activate : function(data) {
			return $http.post('warehouses/'+ data.id +'/activate');
		},
		deactivate : function(data) {
			return $http.post('warehouses/'+ data.id +'/deactivate');
		}
	} 
}]);

app.factory('UserService', ['$http', function($http) {
	return {
		get      : function (data) {
			return $http.get('users/'+ data.id);
		},
		save     : function (data) {
			return (data.id) ? $http.patch('users/'+ data.id, data) : $http.post('users', data);
		},
		read     : function(data) {
			return $http.get('users?'+ jQuery.param(data), data);
		},
		delete   : function(data) {
			return $http.delete('users/'+ data.id);
		},
		activate : function(data) {
			return $http.post('users/'+ data.id +'/activate');
		},
		deactivate : function(data) {
			return $http.post('users/'+ data.id +'/deactivate');
		}
	} 
}]);

app.factory('MovementService', ['$http', function($http) {
	return {
		get      : function (data) {
			return $http.get('movements/'+ data.id);
		},
		save     : function (data) {
			return (data.id) ? $http.patch('movements/'+ data.id, data) : $http.post('movements', data);
		},
		read     : function (data) {
			return $http.get('movements?'+ jQuery.param(data), data);
		},
		delete   : function (data) {
			return $http.delete('movements/'+ data.id);
		},
		cancel   : function (data) {
			return $http.post('movements/'+ data.id +'/cancel');
		}
	} 
}]);

app.factory('AllocationService', ['$http', function($http) {
	return {
		get      : function (data) {
			return $http.get('allocations/'+ data.id);
		},
		save     : function (data) {
			return (data.id) ? $http.patch('allocations/'+ data.id, data) : $http.post('allocations', data);
		},
		read     : function (data) {
			return $http.get('allocations?'+ jQuery.param(data), data);
		},
		delete   : function (data) {
			return $http.delete('allocations/'+ data.id);
		},
		cancel   : function (data) {
			return $http.post('allocations/'+ data.id +'/cancel');
		}
	} 
}]);