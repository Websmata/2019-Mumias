<?php

	require( "config.php" );
	session_start();

	//Find the requested page
	$open = isset( $_GET['open'] ) ? $_GET['open'] : "";

	$content = array();
	$content['sitename'] = strlen(as_option('sitename')) ? as_option('sitename') : SITENAME;
	$managerid = isset( $_SESSION['loggedin_manager'] ) ? $_SESSION['loggedin_manager'] : "";
	$level = isset( $_SESSION['loggedin_level'] ) ? $_SESSION['loggedin_level'] : "";
	$managerame = isset( $_SESSION['loggedin_managerame'] ) ? $_SESSION['loggedin_managerame'] : "";
		
	if ($open == 'install') {
		errMissingTables();
		exit();
	}
	
	if ( $open != "signin" && $open != "signout" && $open != "register" && !$managerid ) {
		$open = 'signin';
	}

	switch ( $open ) {
		case 'signin':
			require( CORE . "manager.php" );
			$content['manager'] = new manager;
			$content['page'] = array(
					'type' => 'form',
					'action' => 'index.php?open='.$open,
					'fields' => array( 
						'handle' => array('label' => 'Username:', 'type' => 'text'),				
						'password' => array('label' => 'Password:', 'type' => 'password'),
					),
			
					'buttons' => array('signin' => array('label' => 'Login to your Account')),			
				);
			
			$content['title'] = "Login to Your Account";
			if ( isset( $_POST['signin'] ) ) {
				$managerid = manager::signinuser($_POST['handle'], md5($_POST['password']));
				if (isset($managerid)) {
					header( "Location: index.php" );
				} else {
					$content['errorMessage'] = "Incorrect username or password. Please try again.";
				}
			}
			break;

		case 'register':
			require( CORE . "manager.php" );
			$content['manager'] = new manager;			
			$content['page'] = array(
					'type' => 'form',
					'action' => 'index.php?open='.$open,
					'fields' => array( 
						'firstname' => array('label' => 'First Name:', 'type' => 'text', 'tags' => 'required '),
						'lastname' => array('label' => 'Last Name:', 'type' => 'text', 'tags' => 'required '),
						'sex' => array('label' => 'Sex:', 'type' => 'radio', 
							'options' => array(
								'male' => array('name' => 'Male', 'value' => 1),
								'female' => array('name' => 'Female', 'value' => 2),
								), 'value' => 1, 'tags' => 'required '),
						'mobile' => array('label' => 'Mobile:', 'type' => 'text', 'tags' => 'required '),
						'email' => array('label' => 'Email:', 'type' => 'email', 'tags' => 'required '),
						'handle' => array('label' => 'Username:', 'type' => 'text', 'tags' => 'required '),
						'password' => array('label' => 'Password:', 'type' => 'password', 'tags' => 'required '),
					),
					
					'hidden' => array('level' => 1),		
					'buttons' => array('register' => array('label' => 'Register')),
				);
			
			$content['title'] = "Register as a Manager";
			if ( isset( $_POST['register'] ) ) {
				$manager = new manager;
				$manager->storeFormValues( $_POST );
				$managerid = $manager->insert();
				if ($managerid) {
					$_SESSION['loggedin_level'] = $_POST['level'];
					$_SESSION['loggedin_manager'] = $managerid;
					header( "Location: index.php" );
				} else {
					$content['errorMessage'] = "Unable to register you at the moment. Please try again later.";
				}
			}
			break;
		
		case 'transporter_new':
			require( CORE . "transporter.php" );
			$content['class'] = new transporter;			
			$content['page'] = array(
					'type' => 'form',
					'action' => 'index.php?open='.$open,
					'fields' => array(
						'fullname' => array('label' => 'Full Name:', 'type' => 'text', 'tags' => 'required '),
						'mobile' => array('label' => 'Mobile Number:', 'type' => 'text', 'tags' => 'required '),
						'email' => array('label' => 'Email Address:', 'type' => 'text', 'tags' => 'required '),
						'address' => array('label' => 'Location:', 'type' => 'text', 'tags' => 'required '),
						'rate' => array('label' => 'Cost rate per KM:', 'type' => 'text', 'tags' => 'required '),
					),
					
					'hidden' => array('manager' => 1),		
					'buttons' => array(
						'saveclose' => array('label' => 'Save & Close'),
						'saveadd' => array('label' => 'Save & Add'),
					),
				);
			
			$content['title'] = "Add a Transporter";
			if ( isset( $_POST['saveclose'] ) ) {
				$class = new transporter;
				$class->storeFormValues( $_POST );
				$transporterid = $class->insert();
				if ($transporterid) {
					header( "Location: index.php?open=transporter_all" );
				} else {
					$content['errorMessage'] = "Unable to add a transporter at the moment. Please try again later.";
				}
			} else if ( isset( $_POST['saveadd'] ) ) {
				$class = new transporter;
				$class->storeFormValues( $_POST );
				$transporterid = $class->insert();
				if ($transporterid) {
					header( "Location: index.php?open=class_new" );
				} else {
					$content['errorMessage'] = "Unable to add a transporter at the moment. Please try again later.";
				}
			}
			break;
			
		case 'transporter_view':
			require( CORE . "transporter.php" );
			$transporterid = $_GET["transporterid"];
			$class = transporter::getById( (int)$transporterid );
			$content['title'] = "Edit Transporter";
			//$content['link'] = '<a href="index.php?open=transporter_delete&&transporterid='.$transporterid.'" onclick="return confirm(\'Delete This Transporter? This action is irrevesible!\')" style="float:right;">DELETE ROOM</a>';	
			$content['page'] = array(
					'type' => 'form',
					'action' => 'index.php?open='.$open.'&&transporterid='.$transporterid,
					'fields' => array(
						'fullname' => array('label' => 'Full Name:', 'type' => 'text', 'tags' => 'required ', 'value' => $class->fullname),
						'mobile' => array('label' => 'Mobile Number:', 'type' => 'text', 'tags' => 'required ', 'value' => $class->mobile),
						'email' => array('label' => 'Email Address:', 'type' => 'text', 'tags' => 'required ', 'value' => $class->email),
						'address' => array('label' => 'Location:', 'type' => 'text', 'tags' => 'required ', 'value' => $class->address),
						'rate' => array('label' => 'Cost rate per KM:', 'type' => 'text', 'tags' => 'required ', 'value' => $class->rate),
					),
					
					'hidden' => array('level' => 1),		
					'buttons' => array(
						'saveChanges' => array('label' => 'Save Changes'),
						'cancel' => array('label' => 'Cancel Changes'),
					),
				);
			
			if ( isset( $_POST['saveChanges'] ) ) {
				$class->storeFormValues( $_POST );
				$class->update();
				header( "Location: index.php?open=transporter_view&&transporterid=".$transporterid."&&status=changesSaved" );
			} elseif ( isset( $_POST['cancel'] ) ) {
				header( "Location: index.php?open=transporter_all" );
			} 
			break;
			
		case 'transporter_all':
			require( CORE . "transporter.php" );
			$managerid = $_SESSION["loggedin_manager"];
			$dbitems = transporter::getList( $managerid );
			$listitems = array();
			foreach ( $dbitems as $dbitem ) {
				$listitems[$dbitem->transporterid] = array($dbitem->fullname, $dbitem->mobile, $dbitem->email, $dbitem->address, $dbitem->rate.' /=');
			}
			
			$content['title'] = "Transporters (".count($dbitems).")";
			$content['page'] = array(
					'type' => 'table',
					'headers' => array( 'fullname', 'mobile', 'email', 'address', 'rate' ),
					'items' => $listitems,
					'onclick' => 'open=transporter_view&&transporterid=',
				);
			$content['link'] = '<a href="index.php?open=transporter_new" style="float:right">Add a Transporter</a>';
			
			break;
		
		case 'farmer_new':
			require( CORE . "farmer.php" );
			$content['farmer'] = new farmer;			
			$content['page'] = array(
					'type' => 'form',
					'action' => 'index.php?open='.$open,
					'fields' => array(
						'firstname' => array('label' => 'First Name:', 'type' => 'text', 'tags' => 'required '),
						'lastname' => array('label' => 'Last Name:', 'type' => 'text', 'tags' => 'required '),
						'email' => array('label' => 'Email Address:', 'type' => 'text', 'tags' => 'required '),
						'mobile' => array('label' => 'Mobile Number:', 'type' => 'text', 'tags' => 'required '),
						'address' => array('label' => 'Physical Address:', 'type' => 'textarea', 'rows' => 2, 'tags' => 'required '),
						'sex' => array('label' => 'Sex:', 'type' => 'radio', 
							'options' => array(
							'male' => array('name' => 'Male', 'value' => 1), 
							'female' => array('name' => 'Female', 'value' => 2)
							), 'value' => 1),
						'handle' => array('label' => 'Username:', 'type' => 'text', 'tags' => 'required '),
						'password' => array('label' => 'Password:', 'type' => 'password', 'tags' => 'required '),
					),
					
					'hidden' => array('manager' => 1),		
					'buttons' => array(
						'saveclose' => array('label' => 'Save & Close'),
						'saveadd' => array('label' => 'Save & Add'),
					),
				);
			
			$content['title'] = "Add a Farmer";
			if ( isset( $_POST['saveclose'] ) ) {
				$farmer = new farmer;
				$farmer->storeFormValues( $_POST );
				$farmerid = $farmer->insert();
				if ($farmerid) {
					header( "Location: index.php?open=farmer_all" );
				} else {
					$content['errorMessage'] = "Unable to add a farmer at the moment. Please try again later.";
				}
			} else if ( isset( $_POST['saveadd'] ) ) {
				$farmer = new farmer;
				$farmer->storeFormValues( $_POST );
				$farmerid = $farmer->insert();
				if ($farmerid) {
					header( "Location: index.php?open=farmer_new" );
				} else {
					$content['errorMessage'] = "Unable to add a farmer at the moment. Please try again later.";
				}
			}
			break;
		
		case 'farmer_view':
			require( CORE . "farmer.php" );
			$farmerid = $_GET["farmerid"];
			$farmer = farmer::getById( (int)$farmerid );
			$content['title'] = "Edit farmer";
			//$content['link'] = '<a href="index.php?open=farmer_delete&&farmerid='.$farmerid.'" onclick="return confirm(\'Delete This farmer? This action is irrevesible!\')" style="float:right;">DELETE farmer</a>';	
			$content['page'] = array(
					'type' => 'form',
					'action' => 'index.php?open='.$open.'&&farmerid='.$farmerid,
					'fields' => array(
						'firstname' => array('label' => 'First Name:', 'type' => 'text', 'tags' => 'required ', 'value' => $farmer->firstname),
						'lastname' => array('label' => 'Last Name:', 'type' => 'text', 'tags' => 'required ', 'value' => $farmer->lastname),
						'email' => array('label' => 'Email Address:', 'type' => 'text', 'tags' => 'required ', 'value' => $farmer->email),
						'mobile' => array('label' => 'Mobile Number:', 'type' => 'text', 'tags' => 'required ', 'value' => $farmer->mobile),
						'address' => array('label' => 'Physical Address:', 'type' => 'textarea', 'rows' => 2, 'tags' => 'required ', 'value' => $farmer->address),
						'sex' => array('label' => 'Sex:', 'type' => 'radio', 
							'options' => array(
							'male' => array('name' => 'Male', 'value' => 1), 
							'female' => array('name' => 'Female', 'value' => 2)
							), 'value' => $farmer->sex),
						'handle' => array('label' => 'Username:', 'type' => 'text', 'tags' => 'required ', 'value' => $farmer->handle),
					),
					
					'hidden' => array('level' => 1),		
					'buttons' => array(
						'saveChanges' => array('label' => 'Save Changes'),
						'cancel' => array('label' => 'Cancel Changes'),
					),
				);
			
			if ( isset( $_POST['saveChanges'] ) ) {
				$farmer->storeFormValues( $_POST );
				$farmer->update();
				header( "Location: index.php?open=farmer_view&&farmerid=".$farmerid."&&status=changesSaved" );
			} elseif ( isset( $_POST['cancel'] ) ) {
				header( "Location: index.php?open=farmer_all" );
			} 
			break;
			
		case 'account':
			require( CORE . "manager.php" );
			$content['manager'] = manager::getById( (int)$_SESSION["loggedin_manager"] );
			$content['title'] = $content['manager']->firstname . ' ' .$content['manager']->lastname.
			' '.($content['manager']->sex == 1 ? '(M)' : '(F)' );
			break;
			
		case 'signout';
			unset( $_SESSION['loggedin_level'] );
			unset( $_SESSION['loggedin_managerame'] );
			unset( $_SESSION['loggedin_manager'] );
			header( "Location: index.php" );
			break;
				
		case 'database';
			errMissingTables();
			break;
		 	
		case 'manager_all':
			require( CORE . "manager.php" );
			$managers = manager::getList(5);
			$listitems = array();
			foreach ( $managers as $manager ) {
				$listitems[] = array($manager->firstname. ' ' . $manager->lastname, $manager->handle, ($manager->sex ==1) ? 'M' : 'F', $manager->mobile, $manager->email);
			}
			
			$content['title'] = "Managers";
			$content['page'] = array(
					'type' => 'table',
					'headers' => array( 'Name', 'username', 'sex', 'mobile phone', 'email'), 
					'items' => $listitems,
				);
			break;
			
		case 'settings':
			$content['title'] = "Your Site Preferences";
			$content['page'] = array(
					'type' => 'form',
					'action' => 'index.php?open='.$open,
					'fields' => array( 
						'sitename' => array('label' => 'Site Name:', 'type' => 'text', 'tags' => 'required ', 'value' => $content['sitename']),
					),
					
					'hidden' => array('level' => 1),		
					'buttons' => array(
						'saveChanges' => array('label' => 'Save Changes'),
					),
				);
			
			if ( isset( $_POST['saveChanges'] ) ) {
				$sitename = $_POST['sitename'];
				as_update_option('sitename', $sitename);
				
				$filename = "config.php";
				$lines = file($filename, FILE_IGNORE_NEW_LINES );
				$lines[12] = '	define( "SITENAME", "'.$sitename.'"  );';
				file_put_contents($filename, implode("\n", $lines));
		
				header( "Location: index.php?pg=settings&&status=changesSaved" );
			} 
			break;
		 
		case 'payment_new':
			require( CORE . "farmer.php" );			
			$farmers = farmer::getList();
			$farmerlist = array();
			foreach ( $farmers as $farmer ) $farmerlist[$farmer->farmerid] = $farmer->firstname . " " . $farmer->lastname;
			
			require( CORE . "transporter.php" );			
			$transporters = transporter::getList(false);
			$transporterlist = array();
			foreach ( $transporters as $transporter ) $transporterlist[$transporter->transporterid] = $transporter->name . " @ " . $transporter->rate . "/=";
			
			$content['page'] = array(
					'type' => 'form',
					'action' => 'index.php?open='.$open,
					'fields' => array(
						'booked' => array('label' => 'payment Date:', 'type' => 'text', 'tags' => 'required ', 'value' =>date('Y-m-d')),
						'weight' => array('label' => 'Est. Weight:', 'type' => 'text', 'tags' => 'required '),
						'address' => array('label' => 'Farm Location:', 'type' => 'text', 'tags' => 'required '),
						'farmerid' => array('label' => 'Select farmer:', 'type' => 'select', 'options' => $farmerlist, 'value' => 1),
						'transporterid' => array('label' => 'Select Transporter:', 'type' => 'select', 'options' => $transporterlist, 'value' => 1),
					),
					
					'hidden' => array('manager' => 1),		
					'buttons' => array(
						'paymentnew' => array('label' => 'Finish this payment'),
					),
				);
			
			$content['title'] = "Add a payment";
			
			if ( isset( $_POST['paymentnew'] ) ) {
				require( CORE . "payment.php" );
				$payment = new payment;
				$payment->storeFormValues( $_POST );
				$paymentid = $payment->insert();
				if ($paymentid) {
					header( "Location: index.php?open=payments_all" );
				} else {
					$content['errorMessage'] = "Unable to add a payment at the moment. Please try again later.";
				}
			}
			break;
		 
		case 'payment_view':
			require( CORE . "payment.php" );
			$paymentid = $_GET["paymentid"];
			$payment = payment::getById( (int)$paymentid );
			
			require( CORE . "farmer.php" );				
			$farmers = farmer::getList(false);
			$farmerlist = array();
			foreach ( $farmers as $farmer ) $farmerlist[$farmer->farmerid] = $farmer->firstname . " " . $farmer->lastname;
			
			require( CORE . "transporter.php" );			
			$transporters = transporter::getList();
			$transporterlist = array();
			$transporterlist[] = 'Clear from  Transporter'; 
			foreach ( $transporters as $transporter ) $transporterlist[$transporter->transporterid] = $transporter->name . " @ " . $transporter->rate . "/=";
			
			$content['page'] = array(
					'type' => 'form',
					'action' => 'index.php?open='.$open.'&&paymentid='.$paymentid,
					'fields' => array(
						'booked' => array('label' => 'payment Date:', 'type' => 'text', 'tags' => 'required ', 'value' => $payment->booked),
						'weight' => array('label' => 'Est. Weight:', 'type' => 'text', 'tags' => 'required ', 'value' => $payment->weight),
						'address' => array('label' => 'Farm Location:', 'type' => 'text', 'tags' => 'required ', 'value' => $payment->address),
						'farmerid' => array('label' => 'Select farmer:', 'type' => 'select', 'options' => $farmerlist, 'value' => $payment->farmerid),
						'transporterid' => array('label' => 'Select Transporter:', 'type' => 'select', 'options' => $transporterlist, 'value' => $payment->transporterid),
					),
					
					'hidden' => array('manager' => 1),		
					'buttons' => array(
						'updatepayment' => array('label' => 'Update this payment'),
						'cancelpayment' => array('label' => 'Cancel this payment'),
						'deletepayment' => array('label' => 'Delete this payment'),
					),
				);
			
			$content['title'] = "Manage payment";
			
			if ( isset( $_POST['updatepayment'] ) ) {
				$payment->storeFormValues( $_POST );
				$payment->update();
				header( "Location: index.php?open=payment_view&&paymentid=".$paymentid."&&status=changesSaved" );
			} elseif ( isset( $_POST['cancelpayment'] ) ) {
				$payment->cancel();
				header( "Location: index.php" );
			} elseif ( isset( $_POST['deletepayment'] ) ) {
				$payment->delete();
				header( "Location: index.php" );
			} elseif ( isset( $_POST['cancel'] ) ) {
				header( "Location: index.php" );
			} 
			break;
		
		case 'payment_cancel':
			require( CORE . "payment.php" );
			$payments = payment::getCancelled();
			$listitems = array();
			foreach ( $payments as $payment ) {
				$listitems[$payment->paymentid] = array($payment->booked, $payment->farmerid, 'Cancelled');
			}
			
			$content['title'] = 'Cancelled payments | <a href="index.php">Active payments</a>';
			$content['link'] = '<a href="index.php?open=payment_new" style="float:right">New payment</a>';
			$content['page'] = array(
				'type' => 'table',
				'headers' => array( 'booked', 'weight', 'address', 'farmerid', 'transporterid' ), 
				'items' => $listitems,
				'onclick' => 'open=payment_view&&paymentid=',
			);
			break;
			
		case 'payment_all':
			require( CORE . "payment.php" );
			$payments = payment::getList();
			$listitems = array();
			foreach ( $payments as $payment ) {
				$listitems[$payment->paymentid] = array($payment->booked, $payment->weight, $payment->address, $payment->farmerid, $payment->transporterid . '/=');
			}
			
			$content['title'] = 'All payments | <a href="index.php?open=payment_cancel">Cancelled</a>';
			$content['link'] = '<a href="index.php?open=payment_new" style="float:right">New payment</a>';
			$content['page'] = array(
				'type' => 'table',
				'headers' => array( 'booked', 'weight', 'address', 'farmerid', 'transporterid' ), 
				'items' => $listitems,
				'onclick' => 'open=payment_view&&paymentid=',
			);
			break;
			
		default:
			require( CORE . "farmer.php" );
			$dbitems = farmer::getList();
			$listitems = array();
			foreach ( $dbitems as $dbitem ) {
				$listitems[$dbitem->farmerid] = array($dbitem->firstname . " ".$dbitem->lastname, $dbitem->email, $dbitem->mobile, $dbitem->address);
			}
			
			$content['title'] = "Farmer (".count($listitems).")";
			$content['page'] = array(
					'type' => 'table',
					'headers' => array( 'fullname', 'email', 'mobile', 'address' ),
					'items' => $listitems,
					'onclick' => 'open=farmer_view&&farmerid=',
				);
			$content['link'] = '<a href="index.php?open=farmer_new" style="float:right">Add a Farmer</a>';
			
			break;
				
	}
	
	require ( CORE . "page.php" );