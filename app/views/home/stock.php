	
	<?php 
		if($this->get('accessGranted') == true):
			if($this->get('stockName') !== null): ?>
				<h1>Historical stock data</h1>
				<p><span class="label-span">Organization</span> <?php echo esc($this->get('companyName')); ?></p>
				<div class="checkbox">
					<label><input type="checkbox" id="show_extra">Show extra options</label>
				</div>
				<div class="extra_options form-group hidden">
					<input type="hidden" id="stock_name" value="<?php echo esc($this->get('stockName')); ?>">
					<div class="form-group">
						<label for="start_date">Start date</label>
						<input type="text" class="form-control" id="start_date">
					</div>
					<div class="form-group">
						<label for="end_date">End date</label>
						<input type="text" class="form-control" id="end_date">
					</div>
					<div class="form-group">
						<label for="interval">Interval</label>
						<select id="interval" class="form-control">
							<option value=""></option>
							<option value="d">Daily</option>
							<option value="w">Weekly</option>
							<option value="m">Monthly</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<button type="button" class="btn btn-primary btn-lg" id="download_stock_data">Download data</button>
					<button type="button" class="btn btn-primary btn-lg" id="visualize_stock_data">Visualize data</button>
				</div>
				<div class="visualization"></div>
		<?php 
			else: 
		?>
			<div class="alert alert-info">No stock data to show</div> 
		<?php endif; 
		else: 
	?>
		<div class="alert alert-danger" role="alert">You do not have the required permission level to access this page.</div>
	<?php endif; ?>