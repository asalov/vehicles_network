	
	<?php if($this->get('recommendations') !== null): ?>
		<div><?php echo esc($this->get('recommendations')); ?></div>	
	<?php else: ?>
		<div class="alert alert-info">You have no assigned vehicles.</div>
	<?php endif; ?>