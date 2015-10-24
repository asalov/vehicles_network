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

				$link.before([$model, $organization]);
			});

			// Get usage info
			ajax('analyst/getUsage/' + $vehicleId, function(data){
				if(data.length > 0){
					var $h4 = $('<h4>', { text: 'Usage stats'});

					$link.before($h4);

					for(var i = 0; i < data.length; i++){
						var $div = $('<div/>');
						var $startTime = addLine('Start time', data[i].start_time);
						var $endTime = addLine('End time', data[i].end_time);
						var $driver = addLine('Driver', data[i].user);
						var $viewLogs = $('<a>', { href: location.href + '/logs/' + $vehicleId, text: 'View log data'});

						$div.append([$startTime, $endTime, $driver]);

						$link.before([$div, $viewLogs]);
					}
				}
			});

			// Get notes
			ajax('analyst/getAnnotations/' + $vehicleId, function(data){
				if(data.length > 0){
					var $h4 = $('<h4>', { text: 'Notes'});

					var $tb = $('<table>', { class: 'table table-striped notes'});

					var $thDate = $('<th>', { text: 'Date'});
					var $thTxt = $('<th>', { text: 'Text'});
					var $thUser = $('<th>', { text: 'User'});

					$tb.append([$thDate, $thTxt, $thUser]);

					for(var i = 0; i < data.length; i++){
						var $tr = $('<tr/>');

						var $date = $('<td>', { text: data[i].created_at});
						var $text = $('<td>', { text: data[i].content});
						var $user = $('<td>', { text: data[i].first_name + ' ' + data[i].last_name});

						$tr.append([$date, $text, $user]);

						if(data[i].is_owner === true){
							var $extraTd = $('<td>');
							var $deleteBtn = $('<button>', {
								text: 'Delete',
								type: 'button',
								class: 'btn btn-danger delete-note'
							});

							$deleteBtn.attr('data-id', data[i].id);
							$deleteBtn.attr('data-toggle', 'modal');
							$deleteBtn.attr('data-target', '#deleteNoteModal');

							$extraTd.append($deleteBtn);
							$tr.append($extraTd);
						}
						
						$tb.append($tr);
					}

					$link.before([$h4, $tb]);
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
			daysOfWeekDisabled: [0, 6], // Disable weekends (since data does not include them)
			endDate: new Date()
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
		$spinner.removeClass('hidden');

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

			for(var i = 0; i < data.length; i++){
				data[i].Low = parseFloat(data[i].Low);
				data[i].High = parseFloat(data[i].High);
				data[i].Open = parseFloat(data[i].Open);
				data[i].Close = parseFloat(data[i].Close);
				data[i].Date = new Date(data[i].Date);
			}

			visualizeStockData(data);				

			$spinner.addClass('hidden');
		}, ajaxOptions);
	});

	// Show drive path
	if($('#map').length > 0){
		$spinner.removeClass('hidden');

		var ajaxOptions = { returnType: 'text'};

		ajax('driver/getGPSData', function(data){
			if(data.length > 0){
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
			}else{
				var $feedback = $('<div>', { class: 'alert alert-info', text: 'You have no assigned vehicles.', role: 'alert'});
				$('#map').after($feedback);
			}

			$spinner.addClass('hidden');
		}, ajaxOptions);
	}

	// Add notes to log file
	$('#addNoteModal').on('shown.bs.modal', function(){
 		$(this).find('textarea').focus();
	});

	// Save note
	$('#save_note').click(function(){
		$spinner.removeClass('hidden');

		var $note = $(this).parent().prev().find('textarea');
		var $vehicleId = $('input[name=vehicle_id').val();

		var ajaxOptions = {
			method: 'post',
			data: { vehicle_id: $vehicleId, text: $note.val()},
			returnType: 'text'
		};
		
		ajax('analyst/addAnnotation', function(data){
			if(data !== null){
				var $success = $('<div>', { 
					class: 'alert alert-success alert-dismissable', 
					role: 'alert', 
					text: 'Note added successfully!'
				});
				var dismissHTML = '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' + 
									'<span aria-hidden="true">&times;</span></button>';
				
				$success.append($(dismissHTML));
				$('#log_form').append($success);
			}
		}, ajaxOptions);

		$note.val('');
		$spinner.addClass('hidden');
	});

	// Delete note
	$('#deleteNoteModal').on('show.bs.modal', function(e){
		$('#delete_confirmation').data('id', $(e.relatedTarget).data('id'));
	});

	$('#delete_confirmation').click(function(){
		var $btn = $(this);
		var $annotationId = $btn.data('id');

		ajax('analyst/deleteAnnotation/' + $annotationId, function(data){
			if(data === 1) $('table.notes').find('.delete-note[data-id=' + $annotationId + ']').parents('tr').remove();
		});
	});

	// Disable checkboxes by default
	$('.sensors input[type=checkbox]:not(.temp)').attr('disabled', true);

	$('#visualization_vectors').change(function(){
		var $vector = $(this);
		var $checkboxes = $vector.parent().next().find('.sensors').find('input[type=checkbox].temp');
		var $selected = $vector.val();

		// $checkboxes.attr('disabled', false);
		// $checkboxes.prop('checked', false);

		// var $tempCheckbox = $checkboxes.filter('.temp');
		// var $weightCheckbox = $checkboxes.filter('.weight');
		// var $speedCheckbox = $checkboxes.filter('.speed');

		// switch($selected){
		// 	case 'weight-temp':
		// 		$weightCheckbox.attr('disabled', true);
		// 		$weightCheckbox.prop('checked', true);
		// 		$speedCheckbox.attr('disabled', true);
		// 	break;
		// 	case 'weight-speed':
		// 		$weightCheckbox.attr('disabled', true);
		// 		$weightCheckbox.prop('checked', true);
		// 		$speedCheckbox.attr('disabled', true);
		// 		$speedCheckbox.prop('checked', true);
		// 		$tempCheckbox.attr('disabled', true);
		// 	break;
		// 	case 'speed-temp':
		// 		$weightCheckbox.attr('disabled', true);
		// 		$speedCheckbox.attr('disabled', true);
		// 		$speedCheckbox.prop('checked', true);
		// 	break;
		// 	default:
		// 		$weightCheckbox.attr('disabled', true);
		// 		$speedCheckbox.attr('disabled', true);
		// 	break;
		// }
	});

	$('#visualize_log_data').click(function(e){
		var $form = $(this).parents('form');
		var formValid = $form[0].checkValidity();

		if(formValid){
			e.preventDefault();

			$spinner.removeClass('hidden');

			var $checkboxes = $form.find('input[type=checkbox]');
			var sensors = [];

			$checkboxes.each(function(){
				var $box = $(this);

				if($box.prop('checked')) sensors.push($box.parent().next().val());
			});

			var postData = {
				start_date: $form.find('#start_date').val(),
				end_date: $form.find('#end_date').val(),
				// vectors: $form.find('#visualization_vectors').val(),
				sensors: sensors
			};

			var ajaxOptions = {
				method: 'post',
				data: postData
			};

			ajax('analyst/getLogs', function(returnData){
				if(returnData.length > 0){
					visualizeLogData(returnData);
				}else{
					if($form.find('.alert-info').length === 0){
						var $feedback = $('<div>', { class: 'alert alert-info', text: 'No data found.', role: 'alert'});
						$form.append($feedback);

						$('.visualization').empty();
					}
				}

				$spinner.addClass('hidden');
			}, ajaxOptions);	
		}
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

	var root = 'public/';

	url = url.substr(0, url.lastIndexOf(root) + root.length);
	
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

	$div.append([$h3, $date, $startTime, $endTime, $duration, $distance, $speed]);

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

	$parent.append([$label, data]);
	// $parent.append(data);

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

function visualizeStockData(data){
	var w = $('.container').width();
	var h = 600;
	var margin = {
		top: 20,
		right: 100,
		bottom:50,
		left: 50
	};

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

	generateGraph(w, h, margin, { x: xScale, y: yScale}, { x: xAxis, y: yAxis}, keys, stockData);
}

function visualizeLogData(data){
	var w = $('.container').width();
	var h = 600;
	var margin = {
		top: 20,
		right: 240,
		bottom:70,
		left: 50
	};

	// console.log(data);

	// Do it with d3
	// var keys = d3.keys(data).filter(function(key){ return key !== xAxisVar; });
	
	var xAxisVar = 'date';
	var keys = [];
	for(var i = 0; i < data.length; i++){
		var objKeys = Object.keys(data[i]);

		if(keys.indexOf(objKeys[1]) == -1) keys.push(objKeys[1]);
	}

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
					.range([margin.left + 2, w - 300]);

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

	generateGraph(w, h, margin, { x: xScale, y: yScale}, { x: xAxis, y: yAxis}, keys, logData);

	// var svg = d3.select('.visualization')
	// 			.append('svg')
	// 			.attr('width', w)
	// 			.attr('height', h);

	// svg.append('g')
	//    .attr('transform', 'translate(0, ' + (h - (margin.bottom + 48)) + ')')
	//    .attr('class', 'axis')
	//    .call(xAxis)
	//    .selectAll('text')  
	//    .style('text-anchor', 'end')
	//    .attr('dx', '-.8em')
	//    .attr('dy', '.15em')
	//    .attr('transform', 'rotate(-65)' );

	// svg.append('g')
	//    .attr('transform', 'translate(' + margin.left + ', 0)')
	//    .attr('class', 'axis')
	//    .call(yAxis);

	// var showLine = d3.svg.line()
	// 				 .x(function(d){ return xScale(d.date); })
	// 				 .y(function(d){ return yScale(d.value); });


	// var lines = svg.selectAll('.line')
	// 				.data(logData)
	// 				.enter()
	// 				.append('g')
	// 				.attr('class', 'line')
	// 				.attr('id', function(d){ return 'line' + d.name; })
	// 				.on('click', function(d){
	// 					d3.select(this).classed('hidden', true);

	// 					var graphLegend = d3.select('.legend-item[data-graph=line-' + d.name.toLowerCase() + ']');
	// 					graphLegend.selectAll('rect').classed('disabled', true);
	// 					graphLegend.selectAll('text').classed('disabled', true);
	// 				})
	// 				.on('mouseover', function(){
	// 					var line = d3.select(this).selectAll('path');

	// 					line.attr('stroke-width', 6).transition().duration(1000);
	// 				})
	// 				.on('mouseout', function(){
	// 					var line = d3.select(this).selectAll('path');

	// 					line.attr('stroke-width', 4).transition().duration(1000);
	// 				});					

	// lines.append('path')
	// 	 .attr('d', function(d){ return showLine(d.values); })
	// 	 .attr('stroke', function(d){ return colors(d.name); })
	// 	 .attr('stroke-width', 4)
	// 	 .attr('fill', 'none'); 

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

	// var legend = svg.append('g')
	// 	  			.attr('class', 'legend');

	// var legendItem = legend.selectAll('.legend-item')
	// 						.data(keys)
	// 						.enter()
	// 						.append('g')
	// 						.attr('class', 'legend-item')
	// 						.attr('data-graph', function(d){ return 'line-' + d.toLowerCase(); })
	// 			  			.on('click', function(d){
	// 			  				var item = d3.select(this);
	// 			  				var id = '#line' + d;
	// 							var hidden = !d3.select(id).classed('hidden');

	// 							d3.select(id).classed('hidden', hidden);
	// 							item.selectAll('rect').classed('disabled', hidden);
	// 							item.selectAll('text').classed('disabled', hidden);
	// 			  			});

	// legendItem.append('rect')
	// 		  .attr('x', w - 260)
	// 		  .attr('y', function(d, i){ return 15 + (i * 30); })
	// 		  .attr('width', 15)
	// 		  .attr('height', 15)
	// 		  .style('fill', function(d){ return colors(d); });

	// legendItem.append('text')
	// 		  .attr('x', w - 240)
	// 		  .attr('y', function(d, i){ return 27 + (i * 30); })
	// 		  .attr('width', 100)
	// 		  .attr('height', 30)
	// 		  .style('fill', function(d){return colors(d); })
	// 		  .text(function(d){ return d; });

	// lines.selectAll('path')
	// 	 .attr('stroke-dasharray', function(){
	// 		var length = d3.select(this).node().getTotalLength();
	// 		return length + ' ' + length;  
	// 	})
	// 	 .attr('stroke-dashoffset', function(){ return d3.select(this).node().getTotalLength(); })
	// 	 .transition()
	// 	 .duration(2000)
	// 	 .attr('stroke-dashoffset', 0);
}

function generateGraph(w, h, margin, scales, axis, keys, data){
	$('.visualization').empty();
	$('.alert-info').remove();
	
	var colors = d3.scale.category10();
	colors.domain(keys);

	var svg = d3.select('.visualization')
				.append('svg')
				.attr('width', w)
				.attr('height', h);

	svg.append('g')
	   .attr('transform', 'translate(0, ' + (h - (margin.bottom + 48)) + ')')
	   .attr('class', 'axis')
	   .call(axis.x)
	   .selectAll('text')  
	   .style('text-anchor', 'end')
	   .attr('dx', '-.8em')
	   .attr('dy', '.15em')
	   .attr('transform', 'rotate(-65)' );

	svg.append('g')
	   .attr('transform', 'translate(' + margin.left + ', 0)')
	   .attr('class', 'axis')
	   .call(axis.y);

	var showLine = d3.svg.line()
					 .x(function(d){ return scales.x(d.date); })
					 .y(function(d){ return scales.y(d.value); });


	var lines = svg.selectAll('.line')
					.data(data)
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
		 .attr('cx', function(d){ return scales.x(d.date); })
		 .attr('cy', function(d){ return scales.y(d.value); })
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
			  .attr('x', w - (margin.right + 25))
			  .attr('y', function(d, i){ return 15 + (i * 30); })
			  .attr('width', 15)
			  .attr('height', 15)
			  .style('fill', function(d){ return colors(d); });

	legendItem.append('text')
			  .attr('x', w - margin.right)
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

	$('html, body').animate({ scrollTop: $(document).height() }, 1000);	
}