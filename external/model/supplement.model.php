<?php

	/**
	 * Supplement Class
	 *
	 * Secure. Contain. Protect.
	 *
	 * @version  1.0
	 * @author   Rodrigo Tejero <rodrigo.tejero@chimp.mx>
	 */
	class Supplement extends CROOD {

		public $id;
		public $id_scp;
		public $slug;
		public $title;
		public $content;
		public $created;
		public $modified;

		/**
		 * Initialization callback
		 * @return nothing
		 */
		function init($args = false) {

			$now = date('Y-m-d H:i:s');

			$this->table = 					'supplement';
			$this->table_fields = 			array('id', 'id_scp', 'slug', 'title', 'content', 'created', 'modified');
			$this->update_fields = 			array('id_scp', 'slug', 'title', 'content', 'modified');
			$this->singular_class_name = 	'Supplement';
			$this->plural_class_name = 		'Supplements';


			#metaModel
			$this->meta_id = 				'id_supplement';
			$this->meta_table = 			'supplement_meta';

			if (! $this->id ) {

				$this->id = 0;
				$this->id_scp = 0;
				$this->slug = '';
				$this->title = '';
				$this->content = '';
				$this->created = $now;
				$this->modified = $now;
				$this->metas = new stdClass();
			}

			else {

				$args = $this->preInit($args);

				# ----------------------------------------------------------------------------------

				$this->fetchMetas();

				# ----------------------------------------------------------------------------------

				$args = $this->postInit($args);
			}
		}
	}

	# ==============================================================================================

	/**
	 * Supplements Class
	 *
	 * Secure. Contain. Protect.
	 *
	 * @version 1.0
	 * @author  Rodrigo Tejero <rodrigo.tejero@chimp.mx>
	 */
	class Supplements extends NORM {

		protected static $table = 					'supplement';
		protected static $table_fields = 			array('id', 'id_scp', 'slug', 'title', 'content', 'created', 'modified');
		protected static $singular_class_name = 	'Supplement';
		protected static $plural_class_name = 		'Supplements';
	}
?>