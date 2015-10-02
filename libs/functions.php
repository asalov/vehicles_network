<?php

// Autoload classes
spl_autoload_register(function($class){
    $paths = [
        ROOT . DS . 'app' . DS . 'core' . DS . $class . '.php',
        ROOT . DS . 'app' . DS . 'controllers' . DS . $class . '.php',
        ROOT . DS . 'app' . DS . 'models' . DS . $class . '.php',
        ROOT . DS . 'libs' . DS . $class . '.php',
        ROOT . DS . 'libs' . DS . 'phpmailer' . DS . 'class.' . strtolower($class) . '.php'
    ];

    foreach($paths as $path){
        if(file_exists($path)){
            require_once $path;
            break;
        }
    }
});

// Escape output
function esc($str){
	return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Get class name
function getClassByName($name, $type = ''){
    $className = ucfirst($name);

    return $className .= ($type !== 'model') ? 'Controller' : 'Model';
}

// Redirect to page
function redirect($location){
    // Loose comparison allows for some flexibility
    $header = ($location != 404) ? 'Location: ' . $location : 'HTTP/1.0 404 Not Found';

    header($header);
    exit;
}

// Register the occurence of an unexpected error
function regError($msg){
    // Development
    echo '<pre>' . $msg . '</pre><hr>';

    /*
    // Production
    $logger = null;
    $mailer = new Mailer;

    // Initialize logger
    try{
        $logger = new FileLogger(LOG_FILE);
    }catch(Exception $e){
        try{
            $mailer->compose('info@alexsalov.com', ADMIN_EMAIL, PROJECT_NAME . ' - Logger error', $e->__toString());
            $mailer->sendMail();
        }catch(phpmailerException $pme){
            $mailer = null;

            // Write errors to default PHP log
            error_log($e->__toString());
            error_log($pme->__toString());
        }
    }

    // Log error
    if($logger !== null) $logger->error($msg);

    if($mailer !== null){
        try{
            // Alert admin by mail        
            $mailer->compose('info@alexsalov.com', ADMIN_EMAIL, PROJECT_NAME . ' - Unexpected error', $msg);
            $mailer->sendMail();
        }catch(phpmailerException $pme){
            ($logger !== null) ? $logger->error($pme->__toString()) : error_log($pme->__toString());
        }          
    }
    */

    // Display error page to user
    $error = new ErrorController;
    $error->show();

    redirect(404);
}