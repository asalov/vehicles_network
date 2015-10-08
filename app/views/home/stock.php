	
	<div><?php print_r($this->get('stockData')); ?></div>

	<?php if($this->get('permissionRestriction')): ?>
		<div class="alert alert-danger" role="alert">You do not have the required permission level to access this page.</div>
	<?php endif; ?>