<?php

	class farmer
	{ 
		public $farmerid = null;
		public $firstname = null;
		public $lastname = null;
		public $handle = null;
		public $email = null;
		public $mobile = null;
		public $sex = null;
		public $address = null;
		public $password = null;
		public $created = null;
		public $updated = null;

		public function __construct( $data=array() ) 
		{
			if ( isset( $data['farmerid'] ) ) $this->farmerid = (int) $data['farmerid'];
			if ( isset( $data['firstname'] ) ) $this->firstname =  $data['firstname'];
			if ( isset( $data['lastname'] ) ) $this->lastname =  $data['lastname'];
			if ( isset( $data['handle'] ) ) $this->handle = $data['handle'];
			if ( isset( $data['email'] ) ) $this->email = $data['email'];
			if ( isset( $data['mobile'] ) ) $this->mobile = $data['mobile'];
			if ( isset( $data['sex'] ) ) $this->sex = $data['sex'];
			if ( isset( $data['address'] ) ) $this->address = $data['address'];
			if ( isset( $data['password'] ) ) $this->password = md5($data['password']);
			if ( isset( $data['created'] ) ) $this->created = (int) $data['created'];
			if ( isset( $data['updated'] ) ) $this->updated = (int) $data['updated'];
		}

		public function storeFormValues ( $params ) 
		{
			$this->__construct( $params );

			if ( isset($params['created']) ) {
				$created = explode ( '-', $params['created'] );

				if ( count($created) == 3 ) {
					list ( $y, $m, $d ) = $created;
					$this->created = mktime ( 0, 0, 0, $m, $d, $y );
				}
			}
		}

		public static function getById( $farmerid ) 
		{
			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$sql = "SELECT *, UNIX_TIMESTAMP(created) AS created FROM farmers WHERE farmerid = :farmerid";
			$st = $conn->prepare( $sql );
			$st->bindValue( ":farmerid", $farmerid, PDO::PARAM_INT );
			$st->execute();
			$row = $st->fetch();
			$conn = null;
			if ( $row ) return new farmer( $row );
		}

		public static function signinuser( $handle, $password ) 
		{
			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$sql = "SELECT * FROM farmers WHERE handle = :handle AND password = :password";
			$st = $conn->prepare( $sql );
			$st->bindValue( ":handle", $handle, PDO::PARAM_INT );
			$st->bindValue( ":password", $password, PDO::PARAM_INT );
			$st->execute();
			$row = $st->fetch();
			$conn = null;
			if ( $row ) {
				$_SESSION['loggedin_level'] = $row['level'];
				$_SESSION['loggedin_fullname'] = $row['firstname'] . ' ' . $row['lastname'];
				$_SESSION['loggedin_user'] = $row['farmerid'];
				return true;
			}	else return false;
		}

		public static function getList() 
		{
			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$sql = "SELECT * FROM farmers ORDER BY email ASC";

			$st = $conn->prepare( $sql );
			$st->execute();
			$list = array();

			while ( $row = $st->fetch() ) {
				$farmer = new farmer( $row );
				$list[] = $farmer;
			}

			$conn = null;
			return $list;
		}

		public function insert() 
		{
			if ( !is_null( $this->farmerid ) ) trigger_error ( "farmer::insert(): Attempt to insert an farmer object that already has its ID property set (to $this->farmerid).", E_USER_ERROR );

			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$sql = "INSERT INTO farmers ( firstname, lastname, handle, email, created, mobile, address, sex, password ) VALUES ( :firstname, :lastname, :handle, :email, :created, :mobile, :address, :sex, :password)";
			$st = $conn->prepare ( $sql );
			$st->bindValue( ":firstname", $this->firstname, PDO::PARAM_STR );
			$st->bindValue( ":lastname", $this->lastname, PDO::PARAM_STR );
			$st->bindValue( ":handle", $this->handle, PDO::PARAM_STR );
			$st->bindValue( ":email", $this->email, PDO::PARAM_STR );
			$st->bindValue( ":mobile", $this->mobile, PDO::PARAM_STR );
			$st->bindValue( ":address", $this->address, PDO::PARAM_STR );
			$st->bindValue( ":sex", $this->sex, PDO::PARAM_STR );
			$st->bindValue( ":password", $this->password, PDO::PARAM_STR );
			$st->bindValue( ":created", date('Y-m-d H:i:s'), PDO::PARAM_INT );
			$st->execute();
			$this->farmerid = $conn->lastInsertId();
			$conn = null;
			return $this->farmerid;
		}

		public function update() 
		{
			if ( is_null( $this->farmerid ) ) trigger_error ( "farmer::update(): Attempt to update an farmer object that does not have its ID property set.", E_USER_ERROR );
		   
			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$sql = "UPDATE farmers SET firstname=:firstname, lastname=:lastname, handle=:handle, email=:email, mobile=:mobile, address=:address, sex=:sex, updated=:updated WHERE farmerid=:farmerid";
			$st = $conn->prepare ( $sql );
			$st->bindValue( ":firstname", $this->firstname, PDO::PARAM_STR );
			$st->bindValue( ":lastname", $this->lastname, PDO::PARAM_STR );
			$st->bindValue( ":handle", $this->handle, PDO::PARAM_STR );
			$st->bindValue( ":email", $this->email, PDO::PARAM_STR );
			$st->bindValue( ":mobile", $this->mobile, PDO::PARAM_STR );
			$st->bindValue( ":address", $this->address, PDO::PARAM_STR );
			$st->bindValue( ":sex", $this->sex, PDO::PARAM_STR );
			$st->bindValue( ":updated", date('Y-m-d H:i:s'), PDO::PARAM_INT );
			$st->bindValue( ":farmerid", $this->farmerid, PDO::PARAM_INT );
			$st->execute();
			$conn = null;
		}

		public function delete() 
		{

			if ( is_null( $this->farmerid ) ) trigger_error ( "farmer::delete(): Attempt to delete an farmer object that does not have its ID property set.", E_USER_ERROR );

			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$st = $conn->prepare ( "DELETE FROM farmers WHERE farmerid = :farmerid LIMIT 1" );
			$st->bindValue( ":farmerid", $this->farmerid, PDO::PARAM_INT );
			$st->execute();
			$conn = null;
		}

	}
