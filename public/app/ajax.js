/*app.factory('ClientService', ['$http', function($http) {
	return {
		get      : function (data) {
			return $http.get('clients/'+ data.id);
		},
		save     : function (data) {
			return (data.id) ? $http.patch('clients/'+ data.id, data) : $http.post('clients', data);
		},
		read     : function(data) {
			return $http.get('clients?'+ jQuery.param(data), data);
		},
		delete   : function(data) {
			return $http.delete('clients/'+ data.id);
		},
		activate : function(data) {
			return $http.post('clients/'+ data.id +'/activate');
		},
		deactivate : function(data) {
			return $http.post('clients/'+ data.id +'/deactivate');
		}
	} 
}]);*/
