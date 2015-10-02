	
	<form action="<?php echo PATH; ?>login/logout" method="post">
		<input type="submit" value="Logout">
	</form>

	<p>Hello, <?php echo esc($this->get('name')); ?>!</p>
	<p>Your role is <?php echo esc($this->get('role')); ?></p>