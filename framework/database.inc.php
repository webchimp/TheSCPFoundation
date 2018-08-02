<?php

	class Database {

		public $dbh;

		function __construct($settings) {
			global $site;
			if ($settings['driver'] == 'mysql') {
				try {
					$dsn = sprintf('mysql:host=%s;dbname=%s', $settings['host'], $settings['name']);
					$this->dbh = new PDO($dsn, $settings['user'], $settings['pass']);
					# Change error and fetch mode
					if ($this->dbh) {
						$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						$this->dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
					}
				} catch (PDOException $e) {
					error_log( $e->getMessage() );
					$site->errorMessage( 'Database error ' . $e->getCode() );
				}
			}
		}
	}

?>