	
	<div class="login-screen">
		<h1>Login with social media</h1>
		<ul class="social-login list-group">
			<li>
				<a class="list-group-item" href="<?php echo PATH; ?>login/authenticate/facebook">
					<i class="fa as-fa-facebook" title="Facebook"></i>
				</a>
			</li>
			<li>
				<a class="list-group-item" href="<?php echo PATH; ?>login/authenticate/twitter">
					<i class="fa as-fa-twitter" title="Twitter"></i>
				</a>
			</li>
			<li>
				<a class="list-group-item" href="<?php echo PATH; ?>login/authenticate/google">
					<i class="fa as-fa-google-plus" title="Google+"></i>
				</a>
			</li>
		</ul>
		<?php if($this->get('error') !== null): ?>
			<div class="alert alert-danger" role="alert"><?php echo esc($this->get('error')); ?></div>
		<?php endif; ?>		
	</div>
