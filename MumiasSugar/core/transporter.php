<?php

	class transporter
	{ 
		public $transporterid = null;
		public $fullname = null;
		public $mobile = null;
		public $email = null;
		public $address = null;
		public $rate = null;
		public $created = null;
		public $updated = null;
		
		public function __construct( $data=array() ) 
		{
			if ( isset( $data['transporterid'] ) ) $this->transporterid = (int) $data['transporterid'];
			if ( isset( $data['fullname'] ) ) $this->fullname =  $data['fullname'];
			if ( isset( $data['mobile'] ) ) $this->mobile = $data['mobile'];
			if ( isset( $data['email'] ) ) $this->email = $data['email'];
			if ( isset( $data['address'] ) ) $this->address = $data['address'];
			if ( isset( $data['rate'] ) ) $this->rate = $data['rate'];
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

		public static function getById( $transporterid ) 
		{
			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$sql = "SELECT *, UNIX_TIMESTAMP(created) AS created FROM transporters WHERE transporterid = :transporterid";
			$st = $conn->prepare( $sql );
			$st->bindValue( ":transporterid", $transporterid, PDO::PARAM_INT );
			$st->execute();
			$row = $st->fetch();
			$conn = null;
			if ( $row ) return new transporter( $row );
		}

		public static function getList($free = true) 
		{
			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			
			if ($free) $sql = "SELECT * FROM transporters ORDER BY transporterid DESC";
			else $sql = 'SELECT * FROM transporters 
			INNER JOIN transporters ON transporters.transporterid = payments.transporterid 
			WHERE payments.transporterid=0 ORDER BY transporterid DESC';
			
			$st = $conn->prepare( $sql );
			$st->execute();
			$list = array();

			while ( $row = $st->fetch() ) {
				$transporter = new transporter( $row );
				$list[] = $transporter;
			}

			$sql = "SELECT FOUND_ROWS() AS totalRows";
			$totalRows = $conn->query( $sql )->fetch();
			$conn = null;
			return $list;
		}

		public function insert() 
		{
			if ( !is_null( $this->transporterid ) ) trigger_error ( "transporter::insert(): Attempt to insert an transporter object that already has its ID property set (to $this->transporterid).", E_USER_ERROR );

			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$sql = "INSERT INTO transporters ( fullname, rate, created ) VALUES ( :fullname, :rate, :created)";
			$st = $conn->prepare ( $sql );
			$st->bindValue( ":fullname", $this->fullname, PDO::PARAM_STR );
			//$st->bindValue( ":mobile", $this->mobile, PDO::PARAM_STR );
			//$st->bindValue( ":email", $this->email, PDO::PARAM_STR );
			//$st->bindValue( ":address", $this->address, PDO::PARAM_STR );
			$st->bindValue( ":rate", $this->rate, PDO::PARAM_STR );
			$st->bindValue( ":created", date('Y-m-d H:i:s'), PDO::PARAM_INT );
			$st->execute();
			$this->transporterid = $conn->lastInsertId();
			$conn = null;
			return $this->transporterid;
		}

		public function update() 
		{
			if ( is_null( $this->transporterid ) ) trigger_error ( "transporter::update(): Attempt to update an transporter object that does not have its ID property set.", E_USER_ERROR );
		   
			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$sql = "UPDATE transporters SET fullname=:fullname, mobile=:mobile, email=:email, address=:address, rate=:rate, updated=:updated WHERE transporterid = :transporterid";
			$st = $conn->prepare ( $sql );
			$st->bindValue( ":fullname", $this->fullname, PDO::PARAM_STR );
			$st->bindValue( ":mobile", $this->mobile, PDO::PARAM_STR );
			$st->bindValue( ":email", $this->email, PDO::PARAM_STR );
			$st->bindValue( ":address", $this->address, PDO::PARAM_STR );
			$st->bindValue( ":rate", $this->rate, PDO::PARAM_STR );
			$st->bindValue( ":updated", date('Y-m-d H:i:s'), PDO::PARAM_INT );
			$st->bindValue( ":transporterid", $this->transporterid, PDO::PARAM_INT );
			$st->execute();
			$conn = null;
		}

		public function delete() 
		{

			if ( is_null( $this->transporterid ) ) trigger_error ( "transporter::delete(): Attempt to delete an transporter object that does not have its ID property set.", E_USER_ERROR );

			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$st = $conn->prepare ( "DELETE FROM transporters WHERE transporterid = :transporterid LIMIT 1" );
			$st->bindValue( ":transporterid", $this->transporterid, PDO::PARAM_INT );
			$st->execute();
			$conn = null;
		}

	}
