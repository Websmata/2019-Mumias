<?php
			if ( isset( $_GET['error'] ) ) {
				switch ( $_GET['error'] ) {
					case "postNotFound":
						$content['errorMessage'] = "Error: post not found.";
						break;
					case "userNotFound":
						$content['errorMessage'] = "Error: user not found.";
						break;
				}
			}

			if ( isset( $_GET['status'] ) ) {
				switch ( $_GET['status'] ) {
					case "postsaved":
						$content['statusMessage'] = "A new Vacancy has been saved.";
						break;
					case "changesSaved":
						$content['statusMessage'] = "Your changes have been saved.";
						break;
					case "applicationreceived":
						$content['statusMessage'] = "Your job application was successful.";
						break;
					
					case "postDeleted":
						$content['statusMessage'] = "That Vacancy has been deleted successfully!.";
						break;
					case "managersaved":
						$content['statusMessage'] = "A new user has been saved.";
						break;
					case "changesSaved":
						$content['statusMessage'] = "Your changes have been saved.";
						break;
					case "userDeleted":
						$content['statusMessage'] = "That user has been deleted successfully!.";
						break;
				}
			}
		?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?php echo (isset($content['title']) ? $content['title'].' - ' : '').$content['sitename'] ?></title>
		<link rel="stylesheet" type="text/css" href="template/style.css" />
	<?php if ( isset( $content['calendarScript'] ) ) { ?>
	<link rel="stylesheet" type="text/css" media="all" href="calendar/jsDatePick.css" />
        <script type="text/javascript" src="calendar/jsDatePick.full.1.1.js"></script>
		<?php echo $content['calendarScript']; ?>
	<?php } ?>	
	
		<script language="javascript" type="text/javascript">
		function clearText(field){

			if (field.defaultValue == field.value) field.value = '';
			else if (field.value == '') field.value = field.defaultValue;

		}
		</script>

	</head>
	<body>
	
<div id="tooplate_body_wrapper">
	<div id="tooplate_wrapper">
    	
        <div id="tooplate_header">
            <div id="header_right"><h1 id="sitename" style="float: left;margin-left: 50px;"><?php echo $content['sitename'] ?></h1> <?php if (isset($userid)) { ?><pre style="float: left; background: #fff;color: #000; padding: 2px 5px; margin-left: 5px; border-radius: 3px;"><b><?php echo $fullname; ?></b></pre><?php } ?>
                <div id="tooplate_menu">
                   <ul>
						<?php echo as_navigation($open) ?>
					</ul>      	
                </div>
            </div>
        </div>
        <div id="tooplate_main">
			<?php if ( isset( $content['errorMessage'] ) ) { ?>
				<div class="errorMessage"><?php echo $content['errorMessage'] ?></div>
			<?php } 
			if ( isset( $content['statusMessage'] ) ) { ?>
				<div class="statusMessage"><?php echo $content['statusMessage'] ?></div>
			<?php } ?>
        	<div class="cleaner h20"></div>