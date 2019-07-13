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
		}
	} 
}]);
