$(document).ready(function(){
	'use strict';

	// Create loading spinner
	var $spinner = $('<i>', { class: 'fa fa-spinner fa-spin hidden'});
	$('body').append($spinner);

	// Show extra vehicle info on user request
	$('.show-vehicle-info').click(function(e){
		e.preventDefault();

		$spinner.removeClass('hidden');

		var $link = $(this);

		var $vehicleId = $link.prevAll('input[name=vehicle_id]').val();
		var $modelId = $link.prevAll('input[name=model_id]').val();
		var $organizationId = $link.prevAll('input[name=organization_id]').val();

		if(!$link.parent().hasClass('data-retrieved')){
			// Get extra info
			ajax('analyst/getExtraInfo/' + $modelId + '/' + $organizationId, function(data){
				var $model = addLine('Model', data.model);
				var $organization = addLine('Organization', data.organization);

				$link.before($model);
				$link.before($organization);
			});

			// Get usage info
			ajax('analyst/getUsage/' + $vehicleId, function(data){
				if(data.length !== 0){
					var $h4 = $('<h4>Usage stats</h4>');

					$link.before($h4);

					for(var i = 0; i < data.length; i++){
						var $div = $('<div/>');
						var $startTime = addLine('Start time', data[i].start_time);
						var $endTime = addLine('End time', data[i].end_time);
						var $driver = addLine('Driver', data[i].user);

						$div.append($startTime);
						$div.append($endTime);
						$div.append($driver);

						$link.before($div);
					}
				}
			});

			// ajax('analyst/getSensors/' + $vehicleId, function(data){
			// 	if(data.length !== 0){
			// 		var $h4 = $('<h4>Vehicle sensors</h4>');

			// 		var $ul = $('<ul>');

			// 		for(var i = 0; i < data.length; i++){
			// 			var $li = $('<li>', { text: data[i].name});

			// 			$ul.append($li);
			// 		}

			// 		$link.before($h4);
			// 		$link.before($ul);
			// 	}
			// });

			// Get log info
			ajax('analyst/getLogs/' + $vehicleId, function(data){
				if(data.length !== 0){
					var $h4 = $('<h4>Log information</h4>');

					var $tb = $('<table>', { class: 'table table-striped'});
					var $thFile = $('<th>', { text: 'File name'});
					var $thSensor = $('<th>', { text: 'Sensor type'});
					var $thLink = $('<th>', { text: 'Link'});
					var $thNotes = $('<th>', { text: 'Notes'});

					$tb.append($thFile);
					$tb.append($thSensor);
					$tb.append($thLink);
					$tb.append($thNotes);

					for(var i = 0; i < data.length; i++){
						var $tr = $('<tr>', { class: 'log'});

						var logLink = data[i].link;
						var logName = logLink.substr(logLink.lastIndexOf('/') + 1);

						var $fileName = $('<td>', { text: logName});	
						var $sensor = $('<td>', { text: data[i].sensor, class: 'sensor-type'});
						var $logUrl = $('<td>', { html: $('<a>', { href: logLink, text: 'File', target: '_blank'}) });

						var $notes = $('<td>',{ class: 'log-notes', contenteditable: true});

						$tr.append($fileName);
						$tr.append($sensor);
						$tr.append($logUrl);
						$tr.append($notes);

						$tb.append($tr);
					}

					var $button = $('<button>', {id: 'show_log_data', class: 'btn btn-primary', text: 'Show data'});

					$link.before($h4);
					$link.before($tb);
					$link.before($button);
				}
			});

			$link.parent().addClass('data-retrieved');

			// Show less option?
			$link.hide();

			$(document).ajaxStop(function(){
				$spinner.addClass('hidden');
			});
		}
	});
	
	// Check if plugin is loaded
	if(jQuery().datepicker !== undefined){
		// Set up datepicker
		var datepickerOptions = {
			weekStart: 1,
			daysOfWeekDisabled: [0, 6] // Disable weekends (since data does not include them)
		};

		$('#start_date').datepicker(datepickerOptions);
		$('#end_date').datepicker(datepickerOptions);		
	}

	// Show extra options
	$('#show_extra').click(function(){
		$(this).parents('.checkbox').next().toggleClass('hidden');
	});

	// Get stock data
	$('#download_stock_data').click(function(){
		var $optionsForm = $(this).parent().prev();
		var $stockName = $optionsForm.find('#stock_name').val();
		
		window.location = downloadStockData($stockName, setStockOptions($optionsForm));
	});

	// Visualize stock data
	$('#visualize_stock_data').click(function(){
		// Fix this repetition
		var $optionsForm = $(this).parent().prev();
		var $stockName = $optionsForm.find('#stock_name').val();
		
		var stockUrl = downloadStockData($stockName, setStockOptions($optionsForm));
		var urlParams = stockUrl.substr(stockUrl.lastIndexOf('?') + 1);

		var ajaxOptions = {
			method: 'post',
			data: urlParams,
			returnType: 'text'
		};

		ajax('director/getStockData', function(returnData){
			var data = d3.csv.parse(returnData);
			
			// console.log(data);

			for(var i = 0; i < data.length; i++){
				data[i].Low = parseFloat(data[i].Low);
				data[i].High = parseFloat(data[i].High);
				data[i].Open = parseFloat(data[i].Open);
				data[i].Close = parseFloat(data[i].Close);
				data[i].Date = new Date(data[i].Date);
			}

			visualizeData(data);
		}, ajaxOptions);

		$('html, body').animate({ scrollTop: $(document).height() }, 1000);
	});

	// Show drive path
	if($('#map').length > 0){
		$spinner.removeClass('hidden');

		var ajaxOptions = { returnType: 'text'};

		ajax('getGPSData', function(data){
			var values = d3.csv.parseRows(data);
			var sessionInfo = {
				coordinates: []
			};

			var averageSpeed = 0;
			var count = 0;

			for(var i = 0; i < values.length; i++){
				/*
					latitude = values[i][0]
					longitude = values[i][1]
					altitude = values[i][2]
					date = values[i][3]
					start/stop = values[i][4]
					seconds run = values[i][5]
					meters run = values[i][6]
					m/s = values[i][7]
				*/

				if(values[i][0] === '') continue; // Skip empty lines

				var latitude = parseFloat(values[i][0]);
				var longitude = parseFloat(values[i][1]);

				sessionInfo.coordinates.push({ lat: latitude, lng: longitude});
				
				averageSpeed += parseFloat(values[i][7]);
				count++;

				// Set values at last loop iteration
				if(count > 1 && (parseInt(values[i][4]) === 1)){
					var date = new Date(1989, 11, 31);
					date.setSeconds(values[i][3]);

					sessionInfo.timestamp = date;
					sessionInfo.duration = (values[i - 1][5]);
					sessionInfo.distance = (values[i - 1][6] / 1000).toFixed(2);
					sessionInfo.avgSpeed = ((averageSpeed / count) / (1000 / 3600)).toFixed(1);
				}
			}

			showDrivePath($('#map')[0], sessionInfo);
			$spinner.addClass('hidden');
		}, ajaxOptions);
	}

	// Add notes to log file
	// $('.list-group').on('blur', '.log-notes', function(){
	// 	var ajaxOptions = {
	// 		method: 'post',
	// 		data: {},
	// 		returnType: 'text'
	// 	};

	// 	ajax('analyst/saveAnnotation', function(){

	// 	}, ajaxOptions);
	// });

	$('.list-group').on('click', '#show_log_data', function(){
		$spinner.removeClass('hidden');

		var $logs = $(this).prev().find('.log');

		var logData = {};
		$logs.each(function(){
			var $log = $(this);
			var $sensor = $log.find('.sensor-type').text();

			if($sensor !== 'GPS' && $sensor !== 'Timer'){
				var sensorType = $sensor.replace(/\s/g, '');

				logData[sensorType] = $log.find('a').attr('href');
			}
		});

		var ajaxOptions = {
			method: 'post',
			data: { logs: logData}
		};

		var $visualizationDiv = $('<div>', { class: 'visualization'});
		$(this).parent().append($visualizationDiv);

		ajax('analyst/getLogContents', function(returnData){
			// for(var i = 0; i < data.length; i++){
			// 	var d = new Date(data[i][0]);

			// 	data[i][0] = (d.getHours() < 6) ? new Date(data[i][0] + ' pm') : new Date(data[i][0]);
			// 	console.log(data[i][0]);
			// 	data[i][1] = parseInt(data[i][1]);
			// }

			combineData(returnData);

			$spinner.addClass('hidden');
		}, ajaxOptions);
	});

	$('#visualization_vectors').change(function(){
		var $vector = $(this);
		var $checkboxes = $vector.parent().next().find('.sensors').find('input[type=checkbox]');
		var $selected = $vector.val();

		$checkboxes.attr('disabled', false);
		$checkboxes.prop('checked', false);

		var $tempCheckbox = $checkboxes.filter('.temp');
		var $weightCheckbox = $checkboxes.filter('.weight');
		var $speedCheckbox = $checkboxes.filter('.speed');

		switch($selected){
			case 'weight-temp':
				$weightCheckbox.attr('disabled', true);
				$weightCheckbox.prop('checked', true);
				$speedCheckbox.attr('disabled', true);
			break;
			case 'weight-speed':
				$weightCheckbox.attr('disabled', true);
				$weightCheckbox.prop('checked', true);
				$speedCheckbox.attr('disabled', true);
				$speedCheckbox.prop('checked', true);
				$tempCheckbox.attr('disabled', true);
			break;
			case 'speed-temp':
				$weightCheckbox.attr('disabled', true);
				$speedCheckbox.attr('disabled', true);
				$speedCheckbox.prop('checked', true);
			break;
			default:
				$weightCheckbox.attr('disabled', true);
				$speedCheckbox.attr('disabled', true);
			break;
		}
	});

	$('#visualize_log_data').click(function(e){
		e.preventDefault();

		$spinner.removeClass('hidden');

		var $checkboxes = $(this).parent().prev().find('input[type=checkbox]');
		var logData = {};

		$checkboxes.each(function(){
			var $box = $(this);

			if($box.prop('checked')){
				var sensorType = $box.parent().text().replace(/\s/g, '');

				logData[sensorType] = $box.parent().next().val();
			}
		});

		// Start date
		// End date
		// Vectors

		/*
			[
				'start' => '05/01/2015',
				'end' => '08/01/2015',
				'vectors' => '',
				'logs' => [
					'key' => 'value'
				]
			]
		*/

		var ajaxOptions = {
			method: 'post',
			data: { logs: logData}
		};

		ajax('getLogContents', function(returnData){
			combineData(returnData);

			$spinner.addClass('hidden');
		}, ajaxOptions);		
	});
});

function ajax(destination, callback, options){
	var method = 'get';
	var data = {};
	var returnType = 'json';

	if(options !== undefined){
		if(options.method !== undefined) method = options.method;
		if(options.returnType !== undefined) returnType = options.returnType;
		if(options.data !== undefined) data = options.data;
	}

	var url = location.href;

	if(url[url.length - 1] === '/') url = url.substr(0, url.length - 1);

	url = url.substr(0, url.lastIndexOf('/') + 1);
	
	$.ajax({
		method: method,
		url: url + destination,
		dataType: returnType,
		data: data
	}).done(function(response){
		callback(response);
	});	
}

function setStockOptions(form){
	var $startDate = form.find('#start_date').val().trim();
	var $endDate = form.find('#end_date').val().trim();
	var $interval = form.find('#interval').val().trim();

	var options = {};

	if($startDate.length > 0) options.start_date = $startDate;
	if($endDate.length > 0) options.end_date = $endDate;
	if($interval.length > 0) options.interval = $interval;

	return options;
}

function downloadStockData(stockName, options){
	var url = 'http://ichart.finance.yahoo.com/table.csv?s=' + stockName;

	url = appendDateStr(url, options.start_date, ['a', 'b', 'c']);
	url = appendDateStr(url, options.end_date, ['d', 'e', 'f']);

	if(options.interval !== undefined) url += '&g=' + options.interval;

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

// Show last driven path
function showDrivePath(mapElement, sessionInfo){
	var coordinates = sessionInfo.coordinates;
	var timestamp = sessionInfo.timestamp;
	var mapCenter = {lat: coordinates[0].lat, lng: coordinates[0].lng};

	var map = new google.maps.Map(mapElement, {
		center: mapCenter,
		zoom: 14,
		mapTypeId: google.maps.MapTypeId.HYBRID
	});

	var drivePath = new google.maps.Polyline({
		path: coordinates,
		geodesic: true,
		strokeColor: '#FF0000',
		strokeOpacity: 1.0,
		strokeWeight: 5
	});

	var $div = $('<div>');
	var $h3 = $('<h3>', { text: 'Drive session information'});
	var $date = addLine('Date', timestamp.getDate() + '/' + addZero(timestamp.getMonth() + 1) + '/' + timestamp.getFullYear());
	var $endTime = addLine('End', addZero(timestamp.getHours()) + ':' + addZero(timestamp.getMinutes()) + ':' + timestamp.getSeconds());
	
	var start = timestamp;
	start.setSeconds(timestamp.getSeconds() - sessionInfo.duration);

	var $startTime = addLine('Start', addZero(start.getHours()) + ':' + addZero(start.getMinutes()) + ':' + start.getSeconds());
	
	var $duration = addLine('Duration', secsToMins(sessionInfo.duration) + ' min');
	var $distance = addLine('Distance', sessionInfo.distance + ' km');
	var $speed = addLine('Average speed', sessionInfo.avgSpeed + ' km/h');

	$div.append($h3);
	$div.append($date);
	$div.append($startTime);
	$div.append($endTime);
	$div.append($duration);
	$div.append($distance);
	$div.append($speed);

	var infoWindow = new google.maps.InfoWindow({
		content: $div[0]
	});

	var marker = new google.maps.Marker({
		position: mapCenter,
		map: map,
		title: 'Session info'
	});

	marker.addListener('click', function(){
		infoWindow.open(map, marker);
	});

	drivePath.setMap(map);

	$(mapElement).removeClass('hidden');

	map.addListener('idle', function(){
		setTimeout(function(){
			marker.setAnimation(google.maps.Animation.DROP);
		}, 200);
	});
}

// Change name?
function addLine(labelTxt, data, element){
	var el = (element !== undefined) ? element : 'p'; 
	var $parent = $('<' + el + '/>');

	var $label = $('<span>', { class: 'label-span', text: labelTxt + ' '});

	$parent.append($label);
	$parent.append(data);

	return $parent;
}

function secsToMins(time){
	var mins = Math.floor(time / 60);
	var seconds = time % 60;

	return addZero(mins) + ':' + addZero(seconds);
}

function addZero(num){
	return (num < 10) ? '0' + num : num;
}

function visualizeData(data){
	$('.visualization').empty();

	var w = $('.container').width();
	var h = 600;
	var margin = {
		top: 20,
		right: 20,
		bottom:50,
		left: 50
	};
	var colors = d3.scale.category10();

	var excluded = ['Volume', 'Adj Close', 'Date'];
	var xAxisVar = 'Date';
	var keys = d3.keys(data[0]).filter(function(key){ return excluded.indexOf(key) === -1; });
	var stockData = keys.map(function(name){
		return {
			name: name,
			values: data.map(function(d){
				return {name: name, date: d[xAxisVar], value: d[name]};
			}).reverse()
		};
	});

	colors.domain(keys);

	var xScale = d3.time.scale()
					.domain(d3.extent(data, function(d){
						return d.Date;
					}))
					.range([margin.left + 2, w - 200]);

	var yScale = d3.scale.linear()
					.domain([
						d3.min(stockData, function(c){
							return d3.min(c.values, function(d){ return d.value; });
						}), 
						d3.max(stockData, function(c){
							return d3.max(c.values, function(d){ return d.value; });
						})
					])
					.range([h - (margin.bottom + margin.left), 10]);

	var xAxis = d3.svg.axis()
				  .scale(xScale)
				  .tickFormat(d3.time.format('%d %b %Y'));

	var yAxis = d3.svg.axis()
				  .scale(yScale)
				  .orient('left')
				  .ticks(7);

	var svg = d3.select('.visualization')
				.append('svg')
				.attr('width', w)
				.attr('height', h);

	svg.append('g')
	   .attr('transform', 'translate(0, ' + (h - (margin.bottom + 48)) + ')')
	   .attr('class', 'axis')
	   .call(xAxis)
	   .selectAll('text')  
	   .style('text-anchor', 'end')
	   .attr('dx', '-.8em')
	   .attr('dy', '.15em')
	   .attr('transform', 'rotate(-65)' );

	svg.append('g')
	   .attr('transform', 'translate(' + margin.left + ', 0)')
	   .attr('class', 'axis')
	   .call(yAxis);

	var showLine = d3.svg.line()
					 .x(function(d){ return xScale(d.date); })
					 .y(function(d){ return yScale(d.value); });


	var lines = svg.selectAll('.line')
					.data(stockData)
					.enter()
					.append('g')
					.attr('class', 'line')
					.attr('id', function(d){ return 'line' + d.name; })
					.on('click', function(d){
						d3.select(this).classed('hidden', true);

						var graphLegend = d3.select('.legend-item[data-graph=line-' + d.name.toLowerCase() + ']');
						graphLegend.selectAll('rect').classed('disabled', true);
						graphLegend.selectAll('text').classed('disabled', true);
					})
					.on('mouseover', function(){
						var line = d3.select(this).selectAll('path');

						line.attr('stroke-width', 6).transition().duration(1000);
					})
					.on('mouseout', function(){
						var line = d3.select(this).selectAll('path');

						line.attr('stroke-width', 4).transition().duration(1000);
					});					

	lines.append('path')
		 .attr('d', function(d){ return showLine(d.values); })
		 .attr('stroke', function(d){ return colors(d.name); })
		 .attr('stroke-width', 4)
		 .attr('fill', 'none'); 

	lines.selectAll('circle')
		 .data(function(d){ return d.values; })
		 .enter()
		 .append('circle')
		 .attr('cx', function(d){ return xScale(d.date); })
		 .attr('cy', function(d){ return yScale(d.value); })
		 .attr('r', 3)
		 .attr('stroke', function(d){ return colors(d.name); })
		 .attr('stroke-width', 2)
		 .attr('fill', '#fff');

	var legend = svg.append('g')
		  			.attr('class', 'legend');

	var legendItem = legend.selectAll('.legend-item')
							.data(keys)
							.enter()
							.append('g')
							.attr('class', 'legend-item')
							.attr('data-graph', function(d){ return 'line-' + d.toLowerCase(); })
				  			.on('click', function(d){
				  				var item = d3.select(this);
				  				var id = '#line' + d;
								var hidden = !d3.select(id).classed('hidden');

								d3.select(id).classed('hidden', hidden);
								item.selectAll('rect').classed('disabled', hidden);
								item.selectAll('text').classed('disabled', hidden);
				  			});

	legendItem.append('rect')
			  .attr('x', w - 125)
			  .attr('y', function(d, i){ return 15 + (i * 30); })
			  .attr('width', 15)
			  .attr('height', 15)
			  .style('fill', function(d){ return colors(d); });

	legendItem.append('text')
			  .attr('x', w - 100)
			  .attr('y', function(d, i){ return 27 + (i * 30); })
			  .attr('width', 100)
			  .attr('height', 30)
			  .style('fill', function(d){return colors(d); })
			  .text(function(d){ return d; });

	lines.selectAll('path')
		 .attr('stroke-dasharray', function(){
			var length = d3.select(this).node().getTotalLength();
			return length + ' ' + length;  
		})
		 .attr('stroke-dashoffset', function(){ return d3.select(this).node().getTotalLength(); })
		 .transition()
		 .duration(2000)
		 .attr('stroke-dashoffset', 0);
}

function combineData(data){
	$('.visualization').empty();

	var w = $('.list-group-item').width();
	var h = 700;
	var margin = {
		top: 20,
		right: 20,
		bottom:100,
		left: 50
	};
	var colors = d3.scale.category10();

	// console.log(data);

	// Do it with d3
	// var keys = d3.keys(data).filter(function(key){ return key !== xAxisVar; });
	
	var xAxisVar = 'date';
	var keys = [];
	for(var i = 0; i < data.length; i++){
		var objKeys = Object.keys(data[i]);

		if(keys.indexOf(objKeys[1]) == -1) keys.push(objKeys[1]);
	}

	colors.domain(keys);

	var logData = [];

	for(var i = 0; i < keys.length; i++){
		var name = keys[i];
		var values = [];

		for(var j = 0; j < data.length; j++){
			if(data[j][name] !== undefined) values.push({name: name, date: new Date(data[j][xAxisVar]), value: data[j][name]});
		}

		logData.push({ name: name, values: values});
	}

	// var logData = keys.map(function(name){
	// 	return {
	// 		name: name,
	// 		values: data.map(function(d){
	// 			return {name: name, date: new Date(d[xAxisVar]), value: d[name]};
	// 		})
	// 	};
	// });

	// console.log(logData);

	var xScale = d3.time.scale()
					.domain(d3.extent(data, function(d){
						return new Date(d.date);
					}))
					.range([margin.left + 2, w - 200]);

	var yScale = d3.scale.linear()
					.domain([
						d3.min(logData, function(c){
							return d3.min(c.values, function(d){ return d.value; });
						}), 
						d3.max(logData, function(c){
							return d3.max(c.values, function(d){ return d.value; });
						})
					])
					.range([h - (margin.bottom + margin.left), 10]);

	var xAxis = d3.svg.axis()
				  .scale(xScale)
				  .tickFormat(d3.time.format('%e %b %Y %H:%M'));

	var yAxis = d3.svg.axis()
				  .scale(yScale)
				  .orient('left')
				  .ticks(7);

	var svg = d3.select('.visualization')
				.append('svg')
				.attr('width', w)
				.attr('height', h);

	svg.append('g')
	   .attr('transform', 'translate(0, ' + (h - (margin.bottom + 48)) + ')')
	   .attr('class', 'axis')
	   .call(xAxis)
	   .selectAll('text')  
	   .style('text-anchor', 'end')
	   .attr('dx', '-.8em')
	   .attr('dy', '.15em')
	   .attr('transform', 'rotate(-65)' );

	svg.append('g')
	   .attr('transform', 'translate(' + margin.left + ', 0)')
	   .attr('class', 'axis')
	   .call(yAxis);

	var showLine = d3.svg.line()
					 .x(function(d){ console.log(xScale(d.date)); return xScale(d.date); })
					 .y(function(d){ return yScale(d.value); });


	var lines = svg.selectAll('.line')
					.data(logData)
					.enter()
					.append('g')
					.attr('class', 'line')
					.attr('id', function(d){ return 'line' + d.name; })
					.on('click', function(d){
						d3.select(this).classed('hidden', true);

						var graphLegend = d3.select('.legend-item[data-graph=line-' + d.name.toLowerCase() + ']');
						graphLegend.selectAll('rect').classed('disabled', true);
						graphLegend.selectAll('text').classed('disabled', true);
					})
					.on('mouseover', function(){
						var line = d3.select(this).selectAll('path');

						line.attr('stroke-width', 6).transition().duration(1000);
					})
					.on('mouseout', function(){
						var line = d3.select(this).selectAll('path');

						line.attr('stroke-width', 4).transition().duration(1000);
					});					

	lines.append('path')
		 .attr('d', function(d){ return showLine(d.values); })
		 .attr('stroke', function(d){ return colors(d.name); })
		 .attr('stroke-width', 4)
		 .attr('fill', 'none'); 

	// lines.selectAll('circle')
	// 	 .data(function(d){ return d.values; })
	// 	 .enter()
	// 	 .append('circle')
	// 	 .attr('cx', function(d){ return xScale(d.date); })
	// 	 .attr('cy', function(d){ return yScale(d.value); })
	// 	 .attr('r', 3)
	// 	 .attr('stroke', function(d){ return colors(d.name); })
	// 	 .attr('stroke-width', 2)
	// 	 .attr('fill', '#fff');

	var legend = svg.append('g')
		  			.attr('class', 'legend');

	var legendItem = legend.selectAll('.legend-item')
							.data(keys)
							.enter()
							.append('g')
							.attr('class', 'legend-item')
							.attr('data-graph', function(d){ return 'line-' + d.toLowerCase(); })
				  			.on('click', function(d){
				  				var item = d3.select(this);
				  				var id = '#line' + d;
								var hidden = !d3.select(id).classed('hidden');

								d3.select(id).classed('hidden', hidden);
								item.selectAll('rect').classed('disabled', hidden);
								item.selectAll('text').classed('disabled', hidden);
				  			});

	legendItem.append('rect')
			  .attr('x', w - 125)
			  .attr('y', function(d, i){ return 15 + (i * 30); })
			  .attr('width', 15)
			  .attr('height', 15)
			  .style('fill', function(d){ return colors(d); });

	legendItem.append('text')
			  .attr('x', w - 100)
			  .attr('y', function(d, i){ return 27 + (i * 30); })
			  .attr('width', 100)
			  .attr('height', 30)
			  .style('fill', function(d){return colors(d); })
			  .text(function(d){ return d; });

	lines.selectAll('path')
		 .attr('stroke-dasharray', function(){
			var length = d3.select(this).node().getTotalLength();
			return length + ' ' + length;  
		})
		 .attr('stroke-dashoffset', function(){ return d3.select(this).node().getTotalLength(); })
		 .transition()
		 .duration(2000)
		 .attr('stroke-dashoffset', 0);
}