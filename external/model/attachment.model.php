<?php

	/**
	 * Attachment Class
	 *
	 * Clase singular de attachment
	 *
	 * @version  1.0
	 * @author  Raul Vera <raul.vera@thewebchi.mp>
	 */
	class Attachment extends CROOD {

		public $id;
		public $slug;
		public $name;
		public $attachment;
		public $mime;
		public $created;
		public $modified;

		/**
		 * Initialization callback
		 * @return nothing
		 */
		function init($args = false) {

			$now = date('Y-m-d H:i:s');

			$this->table = 					'attachment';
			$this->table_fields = 			array('id', 'slug', 'name', 'attachment', 'mime', 'created', 'modified');
			$this->update_fields = 			array('slug', 'name', 'attachment', 'mime', 'modified');
			$this->singular_class_name = 	'Attachment';
			$this->plural_class_name = 		'Attachments';

			# MetaModel
			$this->meta_id = 				'id_attachment';
			$this->meta_table = 			'attachment_meta';


			if (! $this->id ) {

				$this->id = 0;
				$this->slug = '';
				$this->name = '';
				$this->attachment = '';
				$this->mime = '';
				$this->created = $now;
				$this->modified = $now;
			}

			else {

				$args = $this->preInit($args);

				# Enter your logic here
				# ----------------------------------------------------------------------------------

				//Fetch metas
				if (is_array($args) && in_array('fetch_metas', $args)) {
					$this->metas = $this->getMetas();
				}

				$is_image = false;
				switch ($this->mime) {
					case 'image/png':  $is_image = true; break;
					case 'image/gif':  $is_image = true; break;
					case 'image/jpeg': $is_image = true; break;
				}

				$this->url = $this->getUrl();
				$this->thumb = $is_image ? $this->getImage() : '';
				$this->isImage = $is_image;
			}
		}

		function getPath($echo = false, $upload_dir = null) {
			global $site;
			$ret = false;
			#
			if (!$upload_dir) {
				$upload_dir = $site->baseDir('/upload');
			}
			#
			$dir = date('Y/m', strtotime($this->created));
			$ret = "{$upload_dir}/{$dir}/{$this->attachment}";
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

		function getUrl($echo = false, $upload_dir = null) {
			global $site;
			$ret = false;
			#
			if (!$upload_dir) {
				$upload_dir = $site->urlTo('/upload');
			}
			#
			$dir = date('Y/m', strtotime($this->created));
			$ret = "{$upload_dir}/{$dir}/{$this->attachment}";
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

		function getImage($type = 'url', $size = 'thumbnail', $echo = false, $upload_dir = null) {
			global $site;
			$ret = false;
			if ( substr($this->mime, 0, 5) == 'image' ) {
				# Generate path
				$dir = date('Y/m', strtotime($this->created));
				# Generate the image object (just in case)
				switch ($this->mime) {
					case 'image/png':  $ext = 'png'; break;
					case 'image/gif':  $ext = 'gif'; break;
					case 'image/jpeg': $ext = 'jpg'; break;
				}
				#
				if (!$upload_dir) {
					$upload_dir = $site->urlTo('/upload');
				}
				#
				$image = array(
					'url' => "{$upload_dir}/{$dir}/{$this->slug}.{$ext}",
					'sizes' => array(
						'thumbnail' => "{$upload_dir}/{$dir}/{$this->slug}-thumb.{$ext}",
						'medium' => "{$upload_dir}/{$dir}/{$this->slug}-medium.{$ext}",
						'large' => "{$upload_dir}/{$dir}/{$this->slug}-large.{$ext}"
					)
				);
				# Return what the user wants
				switch ($type) {
					case 'url':
						$ret = isset( $image['sizes'][$size] ) ? $image['sizes'][$size] : $image['url'];
						break;
					case 'img':
						$ret = isset( $image['sizes'][$size] ) ? "<img src=\"{$image['sizes'][$size]}\" alt=\"\" />" : "<img src=\"{$image['url']}\" alt=\"\" />";
						break;
					case 'object':
						$ret = $image;
						break;
				}
				if ($echo) {
					echo $ret;
				}
			}
			return $ret;
		}
	}

	# ==============================================================================================

	/**
	 * Attachments Class
	 *
	 * Clase plural de attachment
	 *
	 * @version 1.0
	 * @author  Raul Vera <raul.vera@thewebchi.mp>
	 */
	class Attachments extends NORM {

		protected static $table = 					'attachment';
		protected static $table_fields = 			array('id', 'slug', 'name', 'attachment', 'mime', 'created', 'modified');
		protected static $singular_class_name = 	'Attachment';
		protected static $plural_class_name = 		'Attachments';

		static function getAt($id) {
			if ( is_numeric($id) ) {
				return self::getById($id);
			} else {
				return self::getBySlug($id);
			}
		}

		static function upload($file, $upload_dir = null) {
			global $site;
			$ret = null;
			//
			if (!$upload_dir) {
				$upload_dir = $site->baseDir('/upload');
			}
			//
			if( $file && $file['tmp_name'] ) {
				# Get name parts
				$name = substr( $file['name'], 0, strrpos($file['name'], '.') );
				$ext = substr( $file['name'], strrpos($file['name'], '.') + 1 );
				$ext = strtolower($ext);
				# Normalize JPEG extensions
				$ext = ($ext == 'jpeg') ? 'jpg' : $ext;
				# Check destination folder
				$year = date('Y');
				$month = date('m');
				$dest_dir = "{$year}/{$month}";
				if (! file_exists( "{$upload_dir}/{$dest_dir}" ) ) {
					@mkdir( "{$upload_dir}/{$year}" );
					@mkdir( "{$upload_dir}/{$year}/{$month}" );
				}
				# Generate a destination name
				$dest_name = $site->toAscii($name);
				$dest_path = "{$upload_dir}/{$dest_dir}/{$dest_name}.{$ext}";
				# Check whether the name exists nor not
				if ( file_exists($dest_path) ) {
					$dest_name = $site->toAscii( $name . uniqid() );
					$dest_path = "{$upload_dir}/{$dest_dir}/{$dest_name}.{$ext}";
				}
				# Get MIME type
				if ( $file['type'] ) {
					$mime = $file['type'];
				} else {
					switch ($ext) {
						case 'gif':
						case 'png':
							$mime = "image/{$ext}";
						case 'jpg':
							$mime = 'image/jpeg';
							break;
						case 'mpeg':
						case 'mp4':
						case 'ogg':
						case 'webm':
							$mime = "video/{$ext}";
							break;
						case 'pdf':
						case 'zip':
							$mime = "application/{$ext}";
							break;
						case 'csv':
						case 'xml':
							$mime = "text/{$ext}";
							break;
						default:
							$mime = 'application/octet-stream';
					}
				}
				# Crunching
				if ( substr($mime, 0, 5) == 'image' ) {
					$images = array(
						'thumbnail' => "{$upload_dir}/{$dest_dir}/{$dest_name}-thumb.{$ext}",
						'card' => "{$upload_dir}/{$dest_dir}/{$dest_name}-card.{$ext}",
						'medium' => "{$upload_dir}/{$dest_dir}/{$dest_name}-medium.{$ext}",
						'large' => "{$upload_dir}/{$dest_dir}/{$dest_name}-large.{$ext}"
					);
					require_once $site->baseDir('/external/lib/PHPThumb/ThumbLib.inc.php');
					try {
						# Thumbnail
						$thumb = PhpThumbFactory::create( $file['tmp_name'] );
						$thumb->adaptiveResize(150, 150);
						$thumb->save($images['thumbnail']);
						# Medium image
						$thumb = PhpThumbFactory::create( $file['tmp_name'] );
						$thumb->resize(480, 480);
						$thumb->save($images['medium']);
						# Large image
						$thumb = PhpThumbFactory::create( $file['tmp_name'] );
						$thumb->resize(1024, 1024);
						$thumb->save($images['large']);
					} catch (Exception $e) {
						error_log( $e->getMessage() );
					}
				}
				# Move the uploaded file
				move_uploaded_file($file['tmp_name'], $dest_path);
				# Create and save the attachment
				$attachment = new Attachment();
				$attachment->slug = $dest_name;
				$attachment->name = $name;
				$attachment->attachment = "{$dest_name}.{$ext}";
				$attachment->mime = $mime;
				$attachment->save();
				$ret = $attachment;
			}
			return $ret;
		}
	}
?>