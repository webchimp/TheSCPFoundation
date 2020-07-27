<?php

	/**
	 * Oppai (.)(.)
	 *
	 * The relationship resolver
	 *
	 * @author   raul.vera@thewebchi.mp
	 * @version  0.1
	 * @license  MIT
	 */

	class Oppai {

		protected $table_a;
		protected $table_b;

		private function __construct($table_a, $table_b) {
			$this->table_a = $table_a;
			$this->table_b = $table_b;
		}

		static function newInstance($table_a, $table_b) {
			$ret = new Oppai($table_a, $table_b);
			return $ret;
		}

		public function __call($method, $params) {
			$ret = false;
			$matches = array();
			# Run the regular expression
			$res = preg_match('/^(unlinkAll|resolveAll)(?:by)([A-Za-z]+)$/i', $method, $matches);
			if ($res === 1) {
				$operation = get_item($matches, 1, '');
				switch ($operation) {
					case 'unlinkAll':
						$field = get_item($matches, 2, '');
						$value = get_item($params, 0, '');
						$ret = $this->unlinkAll("id_{$field}", $value);
					break;
					case 'resolveAll':
						$field = get_item($matches, 2, '');
						$value = get_item($params, 0, '');
						$ret = $this->resolveAll("id_{$field}", $value);
					break;
					default:
						print_a($matches);
						exit;
					break;
				}
			}
			return $ret;
		}

		function linkAll($value_a, $values_b) {
			global $site;
			$dbh = $site->getDatabase();
			$table = "{$this->table_a}_{$this->table_b}";
			if ($values_b) {
				try {
					#
					$dbh->query('START TRANSACTION');
					#
					foreach ($values_b as $value_b) {
						$sql = "INSERT INTO {$table} (id_{$this->table_a}, id_{$this->table_b}) VALUES (:value_a, :value_b)";
						$stmt = $dbh->prepare($sql);
						$stmt->bindValue(':value_a', $value_a);
						$stmt->bindValue(':value_b', $value_b);
						$stmt->execute();
					}
					#
					$dbh->query('COMMIT');
					#
				} catch (PDOException $e) {
					log_to_file($e->getMessage(), 'oppai');
				}
			}
		}

		function link($value_a, $value_b) {
			global $site;
			$dbh = $site->getDatabase();
			$table = "{$this->table_a}_{$this->table_b}";
			try {
				$sql = "INSERT INTO {$table} (id_{$this->table_a}, id_{$this->table_b}) VALUES (:value_a, :value_b)";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':value_a', $value_a);
				$stmt->bindValue(':value_b', $value_b);
				$stmt->execute();
			} catch (PDOException $e) {
				log_to_file($e->getMessage(), 'oppai');
			}
		}

		function unlinkAll($field, $value) {
			global $site;
			$dbh = $site->getDatabase();
			$table = "{$this->table_a}_{$this->table_b}";
			try {
				$sql = "DELETE FROM {$table} WHERE {$field} = :value";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':value', $value);
				$stmt->execute();
			} catch (PDOException $e) {
				log_to_file($e->getMessage(), 'oppai');
			}
		}

		// function resolve($value_a, $value_b) {
		// 	//
		// }

		function resolveAll($field, $value) {
			global $site;
			$ret = null;
			$dbh = $site->getDatabase();
			$table = "{$this->table_a}_{$this->table_b}";
			try {
				$sql = "SELECT id_{$this->table_b} FROM {$table} WHERE {$field} = :value";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':value', $value);
				$stmt->execute();
				$rows = $stmt->fetchAll();
				$ret = $rows;
			} catch (PDOException $e) {
				log_to_file($e->getMessage(), 'oppai');
			}
			return $ret;
		}
	}

?>