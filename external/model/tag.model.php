<?php

	/**
	 * Tag Class
	 *
	 * SCP Object Tag
	 *
	 * @version  1.0
	 * @author   Rodrigo Tejero <rodrigo.tejero@chimp.mx>
	 */
	class Tag extends CROOD {

		public $id;
		public $slug;
		public $name;
		public $created;
		public $modified;

		/**
		 * Initialization callback
		 * @return nothing
		 */
		function init($args = false) {

			$now = date('Y-m-d H:i:s');

			$this->table = 					'tag';
			$this->table_fields = 			array('id', 'slug', 'name', 'created', 'modified');
			$this->update_fields = 			array('slug', 'name', 'modified');
			$this->singular_class_name = 	'Tag';
			$this->plural_class_name = 		'Tags';


			if (! $this->id ) {

				$this->id = '';
				$this->slug = '';
				$this->name = '';
				$this->created = $now;
				$this->modified = $now;
			}

			else {

				$args = $this->preInit($args);

				# Enter your logic here
				# ----------------------------------------------------------------------------------



				# ----------------------------------------------------------------------------------

				$args = $this->postInit($args);
			}
		}
	}

	# ==============================================================================================

	/**
	 * Tags Class
	 *
	 * SCP Object Tags
	 *
	 * @version 1.0
	 * @author  Rodrigo Tejero <rodrigo.tejero@chimp.mx>
	 */
	class Tags extends NORM {

		protected static $table = 					'tag';
		protected static $table_fields = 			array('id', 'slug', 'name', 'created', 'modified');
		protected static $singular_class_name = 	'Tag';
		protected static $plural_class_name = 		'Tags';
	}
?>