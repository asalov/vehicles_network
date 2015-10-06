<?php

return [
	// 'base_url' the url that point to HybridAuth Endpoint (where index.php and config.php are found)
	'base_url' => 'http://localhost:8080/aasig2/login/authenticate',

	'providers' => [
		// Google
	    'Google' => [ // 'id' is your google client id
	       'enabled' => true,
	       'keys' => [ 
	       		'id' => '899871457691-lt2t37dk7m97chpoeunl7i0j3f070f9d.apps.googleusercontent.com', 
	       		'secret' => 'Oac7ZcQXKRpRRLokZA6AF4XI'
	       	],
	    ],

		// Facebook
	    'Facebook' => [ // 'id' is your facebook application id
	       'enabled' => true,
	       'keys' => [ 
	       		'id' => '855940797834635', 
	       		'secret' => 'a46f17e1cb60bc31cdb18cf86af2423d' 
	       	],
	       'scope' => 'public_profile',
	       'display' => 'page'
	    ],

		// Twitter
	    'Twitter' => [ // 'key' is your twitter application consumer key
	       'enabled' => true,
	       'keys' => [
	       		'key' => 'yqmi9fEfoRzefMrolgtzgCVPc', 
	       		'secret' => 'r40FG5GOcqj0LB8HkiwWGSLYF0OYzoQMkJIX27J17HpDcsTqD3' 
	       	],
	    ],

	],
	'debug_mode' => false,
	// to enable logging, set 'debug_mode' to true, then provide here a path of a writable file
	'debug_file' => '',
];