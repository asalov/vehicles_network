<?php

return [
	// 'base_url' the url that point to HybridAuth Endpoint (where index.php and config.php are found)
	'base_url' => 'http://localhost/vehicles_network/login/authenticate',

	'providers' => [
		// Google
	    'Google' => [ // 'id' is your google client id
	       'enabled' => true,
	       'keys' => [ 
	       		'id' => '256376790807-93foviosp1ol333ls5pggfhp2j8vjr7m.apps.googleusercontent.com', 
	       		'secret' => '6BpIjI8XT_vVOqgHkplHR22x'
	       	],
	    ],

		// Facebook
	    'Facebook' => [ // 'id' is your facebook application id
	       'enabled' => true,
	       'keys' => [ 
	       		'id' => '1036250943094460', 
	       		'secret' => 'd76c8b3ed675a6f91b00c625f3b71c30' 
	       	],
	       'scope' => 'public_profile email',
	       'display' => 'page'
	    ],

		// Twitter
	    'Twitter' => [ // 'key' is your twitter application consumer key
	       'enabled' => true,
	       'keys' => [
	       		'key' => '4ZmsANguV99xi6FQ3T7yOcuey', 
	       		'secret' => 'PlmXVXbY5VyQfCnWGNKxHvKzowcUnRUiNMF4nFpToPIkylFXjg' 
	       	],
	    ],

	],
	'debug_mode' => false,
	// to enable logging, set 'debug_mode' to true, then provide here a path of a writable file
	'debug_file' => '',
];