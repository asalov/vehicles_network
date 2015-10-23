<?php

return [
	// 'base_url' the url that point to HybridAuth Endpoint (where index.php and config.php are found)
	'base_url' => 'YOUR_REDIRECT_URL',

	'providers' => [
		// Google
	    'Google' => [ // 'id' is your google client id
	       'enabled' => true,
	       'keys' => [ 
	       		'id' => 'YOUR_GOOGLE_APP_ID', 
	       		'secret' => 'YOUR_GOOGLE_APP_SECRET'
	       	],
	    ],

		// Facebook
	    'Facebook' => [ // 'id' is your facebook application id
	       'enabled' => true,
	       'keys' => [ 
	       		'id' => 'YOUR_FACEBOOK_APP_ID', 
	       		'secret' => 'YOUR_FACEBOOK_APP_SECRET' 
	       	],
	       'scope' => 'public_profile email',
	       'display' => 'page'
	    ],

		// Twitter
	    'Twitter' => [ // 'key' is your twitter application consumer key
	       'enabled' => true,
	       'keys' => [
	       		'key' => 'YOUR_TWITTER_APP_KEY', 
	       		'secret' => 'YOUR_TWITTER_APP_SECRET' 
	       	],
	       	'includeEmail' => true
	    ],

	],
	'debug_mode' => false,
	// to enable logging, set 'debug_mode' to true, then provide here a path of a writable file
	'debug_file' => '',
];