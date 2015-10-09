<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<?php if($this->get('showDatepicker') === true): ?>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker.min.css">	
	<?php endif; ?>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700">
	<link rel="stylesheet" href="<?php echo PATH; ?>css/style.css">
	<title><?php echo ($this->get('title')) ? $this->get('title') . ' | ' : '';?><?php echo MAIN_TITLE; ?></title>
</head>
<body>
	<div class="container">
	<?php if($this->get('userLogged') !== null): ?>
		<form action="<?php echo PATH; ?>login/logout" method="post">
			<button type="submit" class="btn btn-default">Logout</button>
		</form>
	<?php endif; ?>