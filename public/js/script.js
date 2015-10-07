$(document).ready(function(){

	// Get data for analyst
	$vehicleId = 17;

	// Get log info
	ajax('getLogs/' + $vehicleId, function(data){
		console.log(data);
	});

	// Get usage info
	// ajax('getUsageData/' + $vehicleId, function(data){
	// 	console.log(data);
	// });
});

function ajax(params, callback){
	var url = location.href;
	url = url.substr(0, url.lastIndexOf('/') + 1);
	
	$.ajax({
		method: 'get',
		url: url + params,
		dataType: 'json'
	}).done(function(data){
		callback(data);
	});	
}