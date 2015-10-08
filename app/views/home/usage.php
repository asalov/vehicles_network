	<?php $vehicles = $this->get('vehicles'); ?>

	<h1>Vehicle list</h1>
	<?php if(!empty($vehicles)): ?>
		<ul class="list-group">	
		<?php foreach($vehicles as $vehicle): ?>
			<li class="list-group-item">
				<p><b>Vehicle plate: </b><?php echo esc($vehicle->plate); ?></p>
				<input type="hidden" name="vehicle_id" value="<?php echo esc($vehicle['id']); ?>">						
				<input type="hidden" name="model_id" value="<?php echo esc($vehicle->Vehicle_model_idVehicle_model); ?>">						
				<input type="hidden" name="organization_id" value="<?php echo esc($vehicle->VehicleOwner_idOrganization); ?>">
				<a href="#" class="show-vehicle-info">Show more</a>					
			</li>
		<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<div class="alert alert-info" role="alert">No vehicle information.</div>
	<?php endif; ?>