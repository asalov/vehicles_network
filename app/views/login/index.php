	
	<h1>Login with social media</h1>
	<ul class="social-login">
		<li><a href="<?php echo PATH; ?>login/authenticate/facebook"><i class="fa fa-facebook" title="Facebook"></i></a></li>
		<li><a href="<?php echo PATH; ?>login/authenticate/twitter"><i class="fa fa-twitter" title="Twitter"></i></a></li>
		<li><a href="<?php echo PATH; ?>login/authenticate/google"><i class="fa fa-google-plus" title="Google+"></i></a></li>
	</ul>
	<?php if($this->get('error') !== null): ?>
		<div class="alert alert-danger" role="alert"><?php echo esc($this->get('error')); ?></div>
	<?php endif; ?>