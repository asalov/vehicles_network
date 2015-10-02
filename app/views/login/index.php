	<p>Login with social media</p>
	<ul>
		<li><a href="<?php echo PATH; ?>login/authenticate/facebook" target="_blank">Facebook</a></li>
		<li><a href="<?php echo PATH; ?>login/authenticate/twitter" target="_blank">Twitter</a></li>
		<li><a href="<?php echo PATH; ?>login/authenticate/google" target="_blank">Google</a></li>
	</ul>
	<?php if($this->get('error') !== null): ?>
		<p class="error"><?php echo esc($this->get('error')); ?></p>
	<?php endif; ?>