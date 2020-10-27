<?php

	/**
	 * Class to handle manager
	 */

	class manager
	{		
		// Properties

		/**
		 * @var int The manager ID from the database
		 */
		public $managerid = null;

		/**
		 * @var string The handle of the manager
		 */
		public $handle = null;

		/**
		 * @var string The firstname of the manager
		 */
		public $firstname = null;

		/**
		 * @var string The lastname of the manager
		 */
		public $lastname = null;

		/**
		 * @var string The mobile number of the manager
		 */
		public $mobile = null;

		/**
		 * @var string The sex of the manager
		 */
		public $sex = null;

		/**
		 * @var string The password of the manager
		 */
		public $password = null;

		/**
		 * @var string The email of the manager
		 */
		public $email = null;

		/**
		 * @var string The level of the manager
		 */
		public $level = null;

		/**
		 * @var string When the manager is to be / was registered
		 */
		public $joined = null;

		/**
		 * @var string When the manager is to be / was updated
		 */
		public $updated = null;


		/**
		 * Sets the object's properties using the values in the supplied array
		 *
		 * @param assoc The property values
		 */

		public function __construct( $data=array() ) 
		{ 
			if ( isset( $data['managerid'] ) ) $this->managerid = (int) $data['managerid'];
			if ( isset( $data['handle'] ) ) $this->handle = $data['handle'];
			if ( isset( $data['firstname'] ) ) $this->firstname = $data['firstname'];
			if ( isset( $data['lastname'] ) ) $this->lastname = $data['lastname'];
			if ( isset( $data['mobile'] ) ) $this->mobile = $data['mobile'];
			if ( isset( $data['sex'] ) ) $this->sex = (int) $data['sex'];
			if ( isset( $data['password'] ) ) $this->password = md5($data['password']);
			if ( isset( $data['email'] ) ) $this->email = $data['email'];
			if ( isset( $data['dobirth'] ) ) $this->dobirth = $data['dobirth'];
			if ( isset( $data['level'] ) ) $this->level = (int) $data['level'];
			if ( isset( $data['joined'] ) ) $this->joined = (int) $data['joined'];
			if ( isset( $data['updated'] ) ) $this->updated = (int) $data['updated'];
		}

		/**
		 * Sets the object's properties using the edit form post values in the supplied array
		 *
		 * @param assoc The form post values
		 */

		public function storeFormValues ( $params ) 
		{
			// Store all the parameters
			$this->__construct( $params );

      		// Parse and store the publication date
			if ( isset($params['joined']) ) {
				$joined = explode ( '-', $params['joined'] );

				if ( count($joined) == 3 ) {
					list ( $y, $m, $d ) = $joined;
					$this->joined = mktime ( 0, 0, 0, $m, $d, $y );
				}
			}
		}

		/**
		 * Returns a manager object matching the given manager ID
		 *
		 * @param int The manager ID
		 * @return manager|false The manager object, or false if the record was not found or there was a problem
		 */

		public static function getById( $managerid ) 
		{
			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$sql = "SELECT *, UNIX_TIMESTAMP(joined) AS joined FROM managers WHERE managerid = :managerid";
			$st = $conn->prepare( $sql );
			$st->bindValue( ":managerid", $managerid, PDO::PARAM_INT );
			$st->execute();
			$row = $st->fetch();
			$conn = null;
			if ( $row ) return new manager( $row );
		}

		/**
		 * signin a manager
		 * @param string handle
		 * @param string password
		 */

		public static function signinuser( $handle, $password ) 
		{
			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			//$sql = "SELECT * FROM managers WHERE handle = :handle AND password = :password";
			$sql = "SELECT * FROM managers WHERE handle = :handle AND password = :password";
			$st = $conn->prepare( $sql );
			$st->bindValue( ":handle", $handle, PDO::PARAM_INT );
			$st->bindValue( ":password", $password, PDO::PARAM_INT );
			$st->execute();
			$row = $st->fetch();
			$conn = null;
			if ( $row ) {
				$_SESSION['loggedin_level'] = $row['level'];
				$_SESSION['loggedin_managerame'] = $row['firstname'] . ' ' . $row['lastname'];
				$_SESSION['loggedin_manager'] = $row['managerid'];
				return true;
			}	else return false;
		}


		/**
		 * Returns all (or a range of) manager objects in the DB
		 *
		 * @param int Optional The number of rows to return (default=all)
		 * @return Array|false A two-element array : results => array, a list of manager objects; totalRows => Total number of articles
		 */

		public static function getList( $level ) 
		{
			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM managers WHERE level = :level ORDER BY joined DESC";

			$st = $conn->prepare( $sql );
			$st->bindValue( ":level", $level, PDO::PARAM_INT );
			$st->execute();
			$list = array();

			while ( $row = $st->fetch() ) {
				$manager = new manager( $row );
				$list[] = $manager;
			}

			$conn = null;
			return $list;
		}

		/**
		 * Inserts the current manager object into the database, and sets its ID property.
		 */

		public function insert() 
		{
			if ( !is_null( $this->managerid ) ) trigger_error ( "manager::insert(): Attempt to insert an manager object that already has its ID property set (to $this->managerid).", E_USER_ERROR );

			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$sql = "INSERT INTO managers ( handle, firstname, lastname, mobile, sex, password, email, level, joined ) VALUES ( :handle, :firstname, :lastname, :mobile, :sex, :password, :email, :level, :joined )";
			$st = $conn->prepare ( $sql );
			$st->bindValue( ":handle", $this->handle, PDO::PARAM_STR );
			$st->bindValue( ":firstname", $this->firstname, PDO::PARAM_STR );
			$st->bindValue( ":lastname", $this->lastname, PDO::PARAM_STR );
			$st->bindValue( ":mobile", $this->mobile, PDO::PARAM_STR );
			$st->bindValue( ":sex", $this->sex, PDO::PARAM_STR );
			$st->bindValue( ":password", $this->password, PDO::PARAM_STR );
			$st->bindValue( ":email", $this->email, PDO::PARAM_STR );
			$st->bindValue( ":level", $this->level, PDO::PARAM_STR );
			$st->bindValue( ":joined", date('Y-m-d H:i:s'), PDO::PARAM_INT );
			$st->execute();
			$this->managerid = $conn->lastInsertId();
			$conn = null;
			return $this->managerid;
		}

		/**
		* Updates the current manager object in the database.
		*/

		public function update() 
		{
			if ( is_null( $this->managerid ) ) trigger_error ( "manager::update(): Attempt to update an manager object that does not have its ID property set.", E_USER_ERROR );
		   
			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$sql = "UPDATE managers SET handle=:handle, firstname=:firstname, lastname=:lastname, mobile=:mobile,  sex=:sex, email=:email, level=:level, updated=:updated WHERE managerid =:managerid";
			
			$st = $conn->prepare ( $sql );
			$st->bindValue( ":handle", $this->handle, PDO::PARAM_STR );
			$st->bindValue( ":firstname", $this->firstname, PDO::PARAM_STR );
			$st->bindValue( ":lastname", $this->lastname, PDO::PARAM_STR );
			$st->bindValue( ":sex", $this->sex, PDO::PARAM_STR );
			$st->bindValue( ":email", $this->email, PDO::PARAM_STR );
			$st->bindValue( ":level", $this->level, PDO::PARAM_STR );
			$st->bindValue( ":mobile", $this->mobile, PDO::PARAM_STR );
			$st->bindValue( ":updated", date('Y-m-d H:i:s'), PDO::PARAM_INT );
			$st->execute();
			$conn = null;
		}

		/**
		* Deletes the current manager object from the database.
		*/

		public function delete() 
		{
			if ( is_null( $this->managerid ) ) trigger_error ( "manager::delete(): Attempt to delete an manager object that does not have its ID property set.", E_USER_ERROR );

			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$st = $conn->prepare ( "DELETE FROM managers WHERE managerid = :managerid LIMIT 1" );
			$st->bindValue( ":managerid", $this->managerid, PDO::PARAM_INT );
			$st->execute();
			$conn = null;
		}

	}
