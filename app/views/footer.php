		<footer>Vehicles Network <?php echo date('Y'); ?> &copy;</footer>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<?php if($this->get('showDatepicker') === true): ?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
	<?php endif; ?>
	<?php if($this->get('showVisualization') === true): ?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.6/d3.min.js"></script>
	<?php endif; ?>
	<?php if($this->get('showMap') === true): ?>
		<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>"></script>
	<?php endif; ?>
	<script src="<?php echo PATH; ?>js/script.js"></script>
</body>
</html>