$(document).ready(function(){
	'use strict';

	// Show extra vehicle info on user request
	$('.show-vehicle-info').click(function(e){
		e.preventDefault();

		var $link = $(this);
		var $vehicleId = $link.siblings('input[name=vehicle_id]').val();
		var $modelId = $link.siblings('input[name=model_id]').val();
		var $organizationId = $link.siblings('input[name=organization_id]').val();

		if(!$link.parent().hasClass('data-retrieved')){
			// Wait for AJAX request to finish before making the next one?

			// Get extra info
			ajax('getExtraInfo/' + $modelId + '/' + $organizationId, function(data){
				var $modelP = $('<p/>');
				var $organizationP = $('<p/>');
				var $modelLabel = $('<b>Model: </b>');
				var $organizationLabel = $('<b>Organization: </b>');

				$modelP.append($modelLabel);
				$modelP.append(data.model);

				$organizationP.append($organizationLabel);
				$organizationP.append(data.organization);

				$link.before($modelP);
				$link.before($organizationP);
			});

			// Get usage info
			ajax('getUsage/' + $vehicleId, function(data){
				if(data.length !== 0){
					var $h4 = $('<h4>Usage stats</h4>');

					var $startTimeP = $('<p/>');
					var $endTimeP = $('<p/>');
					var $driverP = $('<p/>');
					var $startTimeLabel = $('<b>Start time: </b>');
					var $endTimeLabel = $('<b>End time: </b>');
					var $driverLabel = $('<b>Driver: </b>');

					$startTimeP.append($startTimeLabel);
					$startTimeP.append(data.start_time);

					$endTimeP.append($endTimeLabel);
					$endTimeP.append(data.end_time);

					$driverP.append($driverLabel);
					$driverP.append(data.user);

					$link.before($h4);
					$link.before($startTimeP);
					$link.before($endTimeP);
					$link.before($driverP);
				}
			});

			// Get log info
			ajax('getLogs/' + $vehicleId, function(data){
				if(data.length !== 0){
					var $h4 = $('<h4>Log information</h4>');

					var $statusP = $('<p/>');
					var $logUrlP = $('<p/>');
					var $statusLabel = $('<b>Status: </b>');
					var $logUrlLabel = $('<b>Log file: </b>');
					var $logUrl = $('<a>', { href: data.link, text: 'Log', target: '_blank'});

					$statusP.append($statusLabel);
					$statusP.append(data.status);

					$logUrlP.append($logUrlLabel);
					$logUrlP.append($logUrl);

					$link.before($h4);
					$link.before($statusP);
					$link.before($logUrlP);
				}
			});

			$link.parent().addClass('data-retrieved');

			// Show less option?
			$link.hide();
		}
	});
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