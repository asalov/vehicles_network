	<?php $vehicles = $this->get('vehicles'); ?>

	<h1>Vehicle list</h1>
	<?php if(!empty($vehicles)): ?>
		<ul class="list-group">	
		<?php foreach($vehicles as $vehicle): ?>
			<li class="list-group-item">
				<p><span class="label-span">Vehicle plate </span><?php echo esc($vehicle->plate); ?></p>
				<input type="hidden" name="vehicle_id" value="<?php echo esc($vehicle['id']); ?>">						
				<input type="hidden" name="model_id" value="<?php echo esc($vehicle->Vehicle_model_idVehicle_model); ?>">						
				<input type="hidden" name="organization_id" value="<?php echo esc($vehicle->VehicleOwner_idOrganization); ?>">
				<a href="#" class="show-vehicle-info">Show more</a>
			</li>
		<?php endforeach; ?>
		</ul>
		<div class="modal fade" id="deleteNoteModal" tabindex="-1" role="dialog" aria-labelledby="deleteNoteLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" 
						aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="deleteNoteLabel">Delete note</h4>
					</div>
					<div class="modal-body">Are you sure you want to delete this note?</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
						<button type="button" class="btn btn-primary" data-dismiss="modal" id="delete_confirmation">Yes</button>
					</div>
				</div>
			</div>
		</div>
	<?php else: ?>
		<div class="alert alert-info" role="alert">No vehicle information.</div>
	<?php endif; ?>