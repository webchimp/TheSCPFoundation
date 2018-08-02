<?php

	class Request {

		/**
		 * HTTP method used to make the current request (get, post, etc.)
		 * @var string
		 */
		public $type;

		/**
		 * Request parts (controller, action, id and extra fragments)
		 * @var string
		 */
		public $parts;

		/**
		 * Constructor
		 */
		function __construct() {
			$this->type = strtolower( isset( $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : $_SERVER['REQUEST_METHOD'] );
			$this->parts = array();
		}

		/**
		 * Check whether the current request is secure (HTTPS) or not
		 * @return boolean True if the request was made via HTTPS, False otherwise
		 */
		function isSecure() {
			global $site;
			return $site->isSecureRequest();
		}

		/**
		 * Check whether the current request was made via AJAX or not
		 * @return boolean True if the request was made via AJAX, False otherwise
		 */
		function isAjax() {
			global $site;
			return $site->isAjaxRequest();
		}

		/**
		 * Check whether the current request was made via POST or not
		 * @return boolean True if the request was made via POST, False otherwise
		 */
		function isPost() {
			return !!$_POST;
		}

		/**
		 * Get a variable from the $_REQUEST superglobal
		 * @param  string $name    Variable name
		 * @param  string $default Default value to return if the variable is not set
		 * @return mixed           Variable value or $default
		 */
		function param($name = '', $default = '') {
			$ret = $name ? ( isset( $_REQUEST[$name] ) ? $_REQUEST[$name] : $default ) : $_REQUEST;
			return $ret;
		}

		/**
		 * Get a variable from the $_GET superglobal
		 * @param  string $name    Variable name
		 * @param  string $default Default value to return if the variable is not set
		 * @return mixed           Variable value or $default
		 */
		function get($name = '', $default = '') {
			$ret = $name ? ( isset( $_GET[$name] ) ? $_GET[$name] : $default ) : $_GET;
			return $ret;
		}

		/**
		 * Get a variable from the $_POST superglobal
		 * @param  string $name    Variable name
		 * @param  string $default Default value to return if the variable is not set
		 * @return mixed           Variable value or $default
		 */
		function post($name = '', $default = '') {
			$ret = $name ? ( isset( $_POST[$name] ) ? $_POST[$name] : $default ) : $_POST;
			return $ret;
		}

		/**
		 * Get a variable from the $_SESSION superglobal
		 * @param  string $name    Variable name
		 * @param  string $default Default value to return if the variable is not set
		 * @return mixed           Variable value or $default
		 */
		function session($name = '', $default = '') {
			$ret = $name ? ( isset( $_SESSION[$name] ) ? $_SESSION[$name] : $default ) : $_SESSION;
			return $ret;
		}

		/**
		 * Get a file from the $_FILES superglobal
		 * @param  string $name File key
		 * @return mixed        Array with file properties or Null
		 */
		function files($name = '') {
			$ret = $name ? ( isset( $_FILES[$name] ) ? $_FILES[$name] : null ) : $_FILES;
			return $ret;
		}

		/**
		 * Get a variable from the $_SERVER superglobal
		 * @param  string $name    Variable name
		 * @param  string $default Default value to return if the variable is not set
		 * @return mixed           Variable value or $default
		 */
		function server($name, $default = '') {
			return isset( $_SERVER[$name] ) ? $_SERVER[$name] : $default;
		}

		/**
		 * Check the $_REQUEST superglobal, with or without a specific item
		 * @param  string  $name Item name
		 * @return boolean       True if the item was found (or the array is not empty), False otherwise
		 */
		function hasParam($name = null) {
			return $name === null ? !!$_REQUEST : isset( $_REQUEST[$name] );
		}

		/**
		 * Check the $_GET superglobal, with or without a specific item
		 * @param  string  $name Item name
		 * @return boolean       True if the item was found (or the array is not empty), False otherwise
		 */
		function hasGet($name = null) {
			return $name === null ? !!$_GET : isset( $_GET[$name] );
		}

		/**
		 * Check the $_POST superglobal, with or without a specific item
		 * @param  string  $name Item name
		 * @return boolean       True if the item was found (or the array is not empty), False otherwise
		 */
		function hasPost($name = null) {
			return $name === null ? !!$_POST : isset( $_POST[$name] );
		}

		/**
		 * Check the $_SESSION superglobal, with or without a specific item
		 * @param  string  $name Item name
		 * @return boolean       True if the item was found (or the array is not empty), False otherwise
		 */
		function hasSession($name = null) {
			return $name === null ? !!$_SESSION : isset( $_SESSION[$name] );
		}

		/**
		 * Check the $_FILES superglobal, with or without a specific item
		 * @param  string  $name Item name
		 * @return boolean       True if the item was found (or the array is not empty), False otherwise
		 */
		function hasFiles($name = null) {
			return $name === null ? !!$_FILES : isset( $_FILES[$name] );
		}

		/**
		 * Check the $_SERVER superglobal, with or without a specific item
		 * @param  string  $name Item name
		 * @return boolean       True if the item was found (or the array is not empty), False otherwise
		 */
		function hasServer($name = null) {
			return $name === null ? !!$_SERVER : isset( $_SERVER[$name] );
		}
	}

?>