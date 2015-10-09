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
				var $modelLabel = $('<span>', { class: 'label-span', text: 'Model '});
				var $organizationLabel = $('<span>', { class: 'label-span', text: 'Organization '});

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
					var $startTimeLabel = $('<span>', { class: 'label-span', text: 'Start time '});
					var $endTimeLabel = $('<span>', { class: 'label-span', text: 'End time '});
					var $driverLabel = $('<span>', { class: 'label-span', text: 'Driver '});

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
					var $statusLabel = $('<span>', { class: 'label-span', text: 'Status '});
					var $logUrlLabel = $('<span>', { class: 'label-span', text: 'Log file '});
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
	
	// Check if plugin is loaded
	if(jQuery().datepicker !== undefined){
		// Set up datepicker
		var datepickerOptions = {
			weekStart: 1
		};

		$('#start_date').datepicker(datepickerOptions);
		$('#end_date').datepicker(datepickerOptions);		
	}

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

	url = appendDateStr(url, options.start_date, ['a', 'b', 'c']);
	url = appendDateStr(url, options.end_date, ['d', 'e', 'f']);

	if(options.interval !== undefined) url += '&g=' + options.interval;

	console.log(url);
	return url;
}

function appendDateStr(url, dateOption, params){
	if(dateOption !== undefined){
		var date = new Date(dateOption);

		url += '&' + params[0] + '=' + date.getMonth() + '&' + params[1] + '=' + 
				date.getDate() + '&' + params[2] + '=' + date.getFullYear();
	}

	return url;
}