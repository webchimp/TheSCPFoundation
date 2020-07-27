<?php

	/**
	 * SCP Class
	 *
	 * Secure. Contain. Protect.
	 *
	 * @version  1.0
	 * @author   Rodrigo Tejero <rodrigo.tejero@chimp.mx>
	 */
	class SCP extends CROOD {

		public $id;
		public $item_number;
		public $object_class;
		public $name;
		public $special_containment_procedures;
		public $description;
		public $version;
		public $created;
		public $modified;

		/**
		 * Initialization callback
		 * @return nothing
		 */
		function init($args = false) {

			$now = date('Y-m-d H:i:s');

			$this->table = 					'scp';
			$this->table_fields = 			array('id', 'item_number', 'object_class', 'name', 'special_containment_procedures', 'description', 'version', 'created', 'modified');
			$this->update_fields = 			array('item_number', 'object_class', 'name', 'special_containment_procedures', 'description', 'version', 'modified');
			$this->singular_class_name = 	'SCP';
			$this->plural_class_name = 		'SCPs';


			#metaModel
			$this->meta_id = 				'id_scp';
			$this->meta_table = 			'scp_meta';

			if (! $this->id ) {

				$this->id = '';
				$this->item_number = '';
				$this->object_class = '';
				$this->name = '';
				$this->special_containment_procedures = '';
				$this->description = '';
				$this->version = 0;
				$this->created = $now;
				$this->modified = $now;
				$this->metas = new stdClass();
			}

			else {

				$args = $this->preInit($args);

				# ----------------------------------------------------------------------------------

				$this->object_class_slug = strtolower($this->object_class);

				$this->version = intval($this->version);

				$this->fetchMetas();
				$this->resolveTags();
				$this->getRelations();

				# ----------------------------------------------------------------------------------

				$args = $this->postInit($args);
			}
		}

		public function getWikidotPage() {

			global $site;
			$dbh = $site->getDatabase();

			try {

				$sql = "SELECT url, content AS content FROM page WHERE url = 'http://www.scp-wiki.net/" . strtolower($this->item_number) . "';";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$row = $stmt->fetch();
				$this->page = $row;

			} catch (PDOException $e) { }
		}

		public function getRelations() {

			global $site;
			$dbh = $site->getDatabase();

			try {

				$sql = "SELECT * FROM scp_relationship WHERE id_scp1 = {$this->id}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$rows = $stmt->fetchAll();
				$this->relations = $rows;

			} catch (PDOException $e) { }
		}

		public function resolveTags() {

			$scp_tag = Oppai::newInstance('scp', 'tag');
			$this->tags = $scp_tag->resolveAllBySCP($this->id, ['slug', 'name']);
			foreach($this->tags as $k => $tag) {

				$this->tags[$k] = Tags::getById($tag->id_tag);
			}
		}
	}

	# ==============================================================================================

	/**
	 * SCPs Class
	 *
	 * Secure. Contain. Protect.
	 *
	 * @version 1.0
	 * @author  Rodrigo Tejero <rodrigo.tejero@chimp.mx>
	 */
	class SCPs extends NORM {

		protected static $table = 					'scp';
		protected static $table_fields = 			array('id', 'item_number', 'object_class', 'name', 'special_containment_procedures', 'description', 'version', 'created', 'modified');
		protected static $singular_class_name = 	'SCP';
		protected static $plural_class_name = 		'SCPs';
	}
?>