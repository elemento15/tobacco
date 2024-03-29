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

app.factory('BrandTypeService', ['$http', function($http) {
	return {
		get      : function (data) {
			return $http.get('brand_types/'+ data.id);
		},
		//save     : function (data) {
		//	return (data.id) ? $http.patch('brand_types/'+ data.id, data) : $http.post('brand_types', data);
		//},
		read     : function(data) {
			return $http.get('brand_types?'+ jQuery.param(data), data);
		},
		//delete   : function(data) {
		//	return $http.delete('brand_types/'+ data.id);
		//},
		//activate : function(data) {
		//	return $http.post('brand_types/'+ data.id +'/activate');
		//},
		//deactivate : function(data) {
		//	return $http.post('brand_types/'+ data.id +'/deactivate');
		//}
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

app.factory('ConceptService', ['$http', function($http) {
	return {
		get      : function (data) {
			return $http.get('concepts/'+ data.id);
		},
		read     : function(data) {
			return $http.get('concepts?'+ jQuery.param(data), data);
		},
	} 
}]);

app.factory('StockService', ['$http', function($http) {
	return {
		get  : function (data) {
			return $http.get('stocks/'+ data.id);
		},
		read : function(data) {
			return $http.get('stocks?'+ jQuery.param(data), data);
		}
	} 
}]);

app.factory('SalespersonStockService', ['$http', function($http) {
	return {
		get  : function (data) {
			return $http.get('salesperson_stocks/'+ data.id);
		},
		read : function(data) {
			return $http.get('salesperson_stocks?'+ jQuery.param(data), data);
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
			return $http.post('movements/'+ data.id +'/cancel', data);
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
			return $http.post('allocations/'+ data.id +'/cancel', data);
		},
		getDetailAmounts : function (data) {
			return $http.post('allocations/getDetailAmounts', data);
		}
	} 
}]);

app.factory('ConfigurationService', ['$http', function($http) {
	return {
		get  : function () {
			return $http.get('configurations');
		},
		save : function (data) {
			return $http.post('configurations', data);
		}
	}
}]);

app.factory('ReportService', ['$http', function($http) {
	return {
		get : function (data) {
			return $http.get('reports?'+ jQuery.param(data), data);
		}
	}
}]);

app.factory('ChartService', ['$http', function($http) {
	return {
		sales : function (data) {
			return $http.get('charts/sales', data);
		},
		salesperson : function (data) {
			return $http.get('charts/salesperson', data);
		},
		weekly : function (data) {
			return $http.get('charts/weekly', data);
		}
	}
}]);