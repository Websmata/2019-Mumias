<?php
	$success = '';
	$errorhtml = '';
	$suggest = '';
	$buttons = array();
	$fields = array();
	$hidden = array();
	require( CORE . "manager.php" );
	$content['manager'] = new manager;

	if ( isset( $_POST['DatabaseSetup'] ) ) {
		$sitename = $_POST['sitename'];
		$database = $_POST['database'];
		$username = $_POST['username'];
		$password = $_POST['password'];
				
		$filename = "config.php";
		$lines = file($filename, FILE_IGNORE_NEW_LINES );
		$lines[5] = '	define( "DB_DSN", "mysql:host=localhost;dbname='.$database.'" );';
		$lines[6] = '	define( "DB_USER", "'.$username.'" );';
		$lines[7] = '	define( "DB_PASS", "'.$password.'"  );';
		$lines[12] = '	define( "SITENAME", "'.$sitename.'"  );';
		file_put_contents($filename, implode("\n", $lines));
		header("location: ".SITEURL);
	}
	
	if ( isset( $_POST['CreateTables'] ) ) {
		require( CORE . "base.php" );		
		header("location: ".SITEURL);
	}
	
	if ( isset( $_POST['FixTables'] ) ) {
		require( CORE . "base.php" );		
		header("location: ".SITEURL);
	}
	
	if ( isset( $_POST['Supermanager'] ) ) {
		as_new_option('sitename', SITENAME);
		as_new_option('siteurl', SITEURL);
	
		$manager = new manager;
		$manager->storeFormValues( $_POST );
		$manager->insert();		
		header("location: ".SITEURL);
	}

	?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo isset($as_err['errtitle']) ? $as_err['errtitle'] : 'Set up things' ?></title>
		<style>
body { font-family: arial,sans-serif;font-size:0px; text-align: center; background: #000;color: #000; }
h1{ font-size:20px; }
input[type="text"], input[type="email"], input[type="password"], textarea{
font-size:12px; padding:5px; width:100%;color:#000;border:1px solid #f00;border-radius: 5px;background:#EEE; }
table{ width:80%; margin:10px; text-align: left; }
input[type="submit"]{ background:#f00;color:#FFF;padding:10px 20px; border:1px solid #000; font-size:15px; border-radius: 5px; }
img { border: 0;}
.rounded { border-radius: 8px; }
.rounded_i { border-radius: 5px 5px 0px 0px; }
.rounded_ii { border: 1px solid #f00; margin-top:5px; padding:10px; border-radius: 0px 0px 5px 5px; }
#content { margin: 0 auto; width: 600px; }
.title-section { background-color: #f00; color: #fff;font-weight: bold;padding: 5px; }
#debug { margin-top: 50px;text-align:left; }
.main-section { border: 1px solid #f00; background:#FFF;margin: 5px; padding:5px;font-size:15px; }
.mid-section { border: 1px solid #f00;background:#FFF;margin-top: 10px;font-size:15px; }
		</style>
	</head>
	<body>
		<div id="content">
		  <div class="main-section rounded">
			<div class="title-section rounded_i">	
				<h1><?php echo $as_err['errtitle'] ?></h1>
			</div>
			<div class="mid-section">	
				<p><?php echo $as_err['errsumm'] ?></p>
			</div>	
			<form method="post" action="<?php //echo SITEURL ?>" class="rounded_ii">
			<p><?php echo @$as_err['errfull'] ?></p>			
<?php 
		switch ($as_err['errno']){
			case 1:
				$db_name = explode('dbname=', DB_DSN);			
				$fields = array(
					'sitename' => array('label' => 'System Name:', 'type' => 'text', 'value' => SITENAME),
					'database' => array('label' => 'Database Name:', 'type' => 'text', 'value' => $db_name[1]),
					'username' => array('label' => 'Database username:', 'type' => 'text', 'value' => DB_USER),
					'password' => array('label' => 'Database Password:', 'type' => 'password', 'value' => DB_PASS),
					
				);
				$buttons = array('DatabaseSetup' => 'Connect to the Database');
				break;

			case 2: 
				$fields = array(
					'firstname' => array('label' => 'First Name:', 'type' => 'text'),
					'lastname' => array('label' => 'Last Name:', 'type' => 'text'),
					'sex' => array('label' => 'Sex:', 'type' => 'radio', 
						'options' => array(
						'male' => array('name' => 'Male', 'value' => 1), 
						'female' => array('name' => 'Female', 'value' => 2)
						), 'value' => 1),
					'mobile' => array('label' => 'Mobile:', 'type' => 'text'),
					'email' => array('label' => 'Email:', 'type' => 'email'),
					'handle' => array('label' => 'Username:', 'type' => 'text'),
					'password' => array('label' => 'Password:', 'type' => 'password'),
				);
				$buttons = array('Supermanager' => 'Create Your Account');
				$hidden = array('level' => '5');
				break;
			case 3:
			
				break;
				
			case 4:
				$buttons = array('CreateTables' => 'Create Database Tables');
				break;
				
			case 5:
				$buttons = array('FixTables' => 'Fix Database Tables');			
				break;
				
		} 
		?>
	<?php if (count($fields)) { ?>
			<table class="table">
	<?php foreach($fields as $name => $field) { ?>
				<tr>	
						<th><?php echo $field['label'] ?></th>	
						<td>
				<?php 
				switch ($field['type']) {
					case 'radio':
						if (isset($field['options'])) {
							foreach ($field['options'] as $option)
							 echo '<label><input type="radio" name="'.$name.'" value="'.@$option['value'].'" ' .
							 ($field['value'] == $option['value'] ? 'checked ' : '') . ' /> '.
							 @$option['name'].' </label>';
						}
						break;
					default:
						echo'<input type="'.$field['type'].'" size="24" name="'.$name.'" value="'.@$field['value'].'" autocomplete="off" />';
				} ?>
						</td>
		<?php if (isset($fielderrors[$name])) 
			echo '<td class="msg-error"><small>'.$fielderrors[$name].'<small></td>';
		else ?>
				<td></td>
					</tr>			
	<?php } ?>
			</table>
				<?php } 
		foreach ($buttons as $name => $value)
			echo '<input type="submit" name="'.$name.'" value="'.$value.'" />';
		foreach ($hidden as $name => $value)
			echo '<input type="hidden" name="'.$name.'" value="'.$value.'" />';
	?>	
<?php ?>
			</form>
		  </div>
		</div>
	</body>
</html>	