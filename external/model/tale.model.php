<?php

	/**
	 * Tale Class
	 *
	 * Tale
	 *
	 * @version  1.0
	 * @author   Rodrigo Tejero <rodrigo.tejero@chimp.mx>
	 */
	class Tale extends CROOD {

		public $id;
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

			$this->table = 					'tale';
			$this->table_fields = 			array('id', 'slug', 'title', 'content', 'created', 'modified');
			$this->update_fields = 			array('slug', 'title', 'content', 'modified');
			$this->singular_class_name = 	'Tale';
			$this->plural_class_name = 		'Tales';


			#metaModel
			$this->meta_id = 				'id_tale';
			$this->meta_table = 			'tale_meta';

			if (! $this->id ) {

				$this->id = '';
				$this->slug = '';
				$this->title = '';
				$this->content = '';
				$this->created = $now;
				$this->modified = $now;
				$this->metas = new stdClass();
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
	 * Tales Class
	 *
	 * Tales
	 *
	 * @version 1.0
	 * @author  Rodrigo Tejero <rodrigo.tejero@chimp.mx>
	 */
	class Tales extends NORM {

		protected static $table = 					'tale';
		protected static $table_fields = 			array('id', 'slug', 'title', 'content', 'created', 'modified');
		protected static $singular_class_name = 	'Tale';
		protected static $plural_class_name = 		'Tales';
	}
?>