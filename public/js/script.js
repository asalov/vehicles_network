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
	
	// Show extra options
	$('#show_extra').click(function(){
		$(this).parents('.checkbox').next().toggleClass('hidden');
	});

	// Get stock data
	$('#get_stock_data').click(function(e){
		e.preventDefault();

		var $optionsForm = $(this).parent().prev();
		var $stockName = $optionsForm.find('#stock_name').val();
		var $startDate = $optionsForm.find('#start_date').val().trim();
		var $endDate = $optionsForm.find('#end_date').val().trim();
		var $interval = $optionsForm.find('#interval').val().trim();

		var options = {};

		if($startDate.length > 0) options.start_date = $startDate;
		if($endDate.length > 0) options.end_date = $endDate;
		if($interval.length > 0) options.interval = $interval;
		
		// getStockData($stockName, options);
		window.location = getStockData($stockName, options);
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

function getStockData(stockName, options){
	var url = 'http://ichart.finance.yahoo.com/table.csv?s=' + stockName;

	if(options.start_date !== undefined){
		var startDate = new Date(options.start_date);

		// from 15/3/2000 until 31/1/2010.

		// http://ichart.yahoo.com/table.csv?s=GOOG&a=2
		// http://ichart.yahoo.com/table.csv?s=GOOG&a=2&b=15
		// http://ichart.yahoo.com/table.csv?s=GOOG&a=2&b=1&c=2000 

		url += '&a=' + startDate.getMonth() + '&b=' + startDate.getDate() + '&c=' + startDate.getFullYear();
	}

	if(options.end_date !== undefined){
		var endDate = new Date(options.end_date);
		// http://ichart.yahoo.com/table.csv?s=GOOG&a=0&b=1&c=2000&d=0
		// http://ichart.yahoo.com/table.csv?s=GOOG&a=0&b=1&c=2000&d=0&e=31
		// http://ichart.yahoo.com/table.csv?s=GOOG&a=0&b=1&c=2000&d=0&e=31&f=2010

		url += '&d=' + endDate.getMonth() + '&e=' + endDate.getDate() + '&f=' + endDate.getFullYear(); 
	}

	if(options.interval !== undefined) url += '&g=' + options.interval;

	console.log(url);
	return url;
}