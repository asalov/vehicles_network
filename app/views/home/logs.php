	<?php $logs = $this->get('logs'); ?>

	<h1>Visualize log data</h1>
	<div class="form-group">
		<div class="form-group">
			<label for="start_date">Start date</label>
			<input type="text" class="form-control" id="start_date">
		</div>
		<div class="form-group">
			<label for="end_date">End date</label>
			<input type="text" class="form-control" id="end_date">
		</div>
		<div class="form-group">
			<label for="">Select visualization vectors</label>
			<select id="visualization_vectors" class="form-control">
				<option value="time-temp">Time/Temperature</option>
				<option value="weight-temp">Weight/Temperature</option>
				<option value="weight-speed">Weight/Speed</option>
				<option value="speed-temp">Speed/Temperature</option>
			</select>
		</div>
		<div class="form-group">
			<label for="sensors">Choose sensors</label>
			<?php foreach($logs as $log): ?>
				<div class="checkbox sensors">
					<label>
						<input type="checkbox" class="<?php echo esc($log['type']); ?>"><?php echo esc($log['sensor']); ?>
					</label>
					<input type="hidden" name="log_link" value="<?php echo esc($log['link']); ?>">
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="form-group">
		<button type="button" class="btn btn-primary btn-lg" id="visualize_log_data">Visualize data</button>
	</div>
	<div class="visualization"></div>