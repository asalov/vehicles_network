	<?php $sensors = $this->get('sensors'); ?>
	
	<?php if(!empty($sensors)) : ?>
	<h1>Visualize log data</h1>
	<form id="log_form">
		<input type="hidden" name="vehicle_id" value="<?php echo esc($this->get('vehicleId')); ?>">
		<div class="form-group">
			<div class="form-group">
				<label for="start_date">Start date</label>
				<input type="text" class="form-control" id="start_date" required>
			</div>
			<div class="form-group">
				<label for="end_date">End date</label>
				<input type="text" class="form-control" id="end_date" required>
			</div>
			<div class="form-group">
				<label for="">Select visualization vectors</label>
				<select id="visualization_vectors" class="form-control" required>
					<option value=""></option>
					<option value="time-temp">Time/Temperature</option>
					<option value="weight-temp" disabled>Weight/Temperature</option>
					<option value="weight-speed" disabled>Weight/Speed</option>
					<option value="speed-temp" disabled>Speed/Temperature</option>
				</select>
			</div>
			<div class="form-group">
				<label for="sensors">Choose sensors</label>
				<?php foreach($sensors as $sensor): ?>
					<div class="checkbox sensors">
						<label>
							<input type="checkbox" class="<?php echo esc($sensor['type']); ?>"><?php echo esc($sensor['name']); ?>
						</label>
						<input type="hidden" name="sensor_id" value="<?php echo esc($sensor['id']); ?>">
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="form-group">
			<button type="submit" class="btn btn-primary btn-lg" id="visualize_log_data">Visualize data</button>
			<?php if($this->get('addNotes') !== null): ?>
			<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" 
				data-target="#addNoteModal">Add note</button>
			<?php endif; ?>
		</div>
	</form>
	<div class="modal fade" id="addNoteModal" tabindex="-1" role="dialog" aria-labelledby="addNoteLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" 
        		aria-label="Close"><span aria-hidden="true">&times;</span></button>
        		<h4 class="modal-title" id="addNoteLabel">Add a note</h4>
      		</div>
	      	<div class="modal-body">
	      		<textarea name="note" id="note" class="form-control" rows="4"></textarea>
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        	<button type="button" class="btn btn-primary" data-dismiss="modal" id="save_note">Save note</button>
	      	</div>
    	</div>
  	</div>
</div>
	<div class="visualization"></div>
	<?php else: ?>
		<div class="alert alert-info" role="alert">No log data available for vehicle.</div>
	<?php endif; ?>