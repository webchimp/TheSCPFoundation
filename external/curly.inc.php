<?php
	/**
	 * Curly
	 * @author 	biohzrdmx <github.com/biohzrdmx>
	 * @version 1.0
	 * @license MIT
	 * @uses    Cacher <github.com/biohzrdmx/cacher-php>
	 * @example Basic usage:
	 *
	 *    // Just grab a new instance, the boolean parameter controls caching:
	 *    $curly = Curly::newInstance(false)
	 *    	->setMethod('get')
	 *    	->setURL('http://api.icndb.com/jokes/random')
	 *    	->setParams([ 'limitTo' => 'nerdy' ])
	 *    	->execute();
	 *    // Then just get the response, you may even specify the format ('plain' or 'json')
	 *    $res = $curly->getResponse('json');
	 *    // And just use the returned data
	 *    if ($res && $res->type == 'success') {
	 *    	# Error checking may vary, here the API sets a } `type` member
	 *    	echo $res->value->joke;
	 *    } else {
	 *    	echo 'API error: ' . $curly->getError();
	 *    }
	 *
	 */
	class Curly {

		protected $method;
		protected $url;
		protected $params;
		protected $fields;
		protected $headers;
		protected $response;
		protected $options;
		protected $caching;
		protected $error;
		protected $info;

		function __construct() {
			$this->method = 'get';
			$this->url = 'http://localhost';
			$this->params = array();
			$this->fields = array();
			$this->headers = array();
			$this->options = array();
			$this->response = '';
			$this->error = '';
			$this->info = '';
			$this->caching = true;
		}

		/**
		 * Simple shim for baseDir (for Hummingbird/Dragonfly you should use the built-in method)
		 * @param  string $path Path
		 * @return string       Fully-qualified path
		 */
		static function baseDir($path) {
			$dir = dirname(__FILE__);
			$ret = "{$dir}{$path}";
			return $ret;
		}

		/**
		 * Return a new instance
		 * @param  boolean $caching Whether to use caching or not
		 * @return object           New Curly instance
		 */
		static function newInstance($caching = true) {
			$new = new self();
			$new->caching = $caching;
			return $new;
		}

		/**
		 * Set the HTTP method ('get, 'post', 'put', etc)
		 * @param string $method The HTTP verb, in lowercase
		 * @return object This instance, for chaining
		 */
		function setMethod($method) {
			$this->method = $method;
			return $this;
		}

		/**
		 * Set the URL
		 * @param string $url The URL to which the request will be made
		 * @return object This instance, for chaining
		 */
		function setURL($url) {
			$this->url = $url;
			return $this;
		}

		/**
		 * Set the params for the current request
		 * @param array $params Array of named params (associative) that will be passed on the URL
		 * @return object This instance, for chaining
		 */
		function setParams($params) {
			$this->params = $params;
			return $this;
		}

		/**
		 * Set the fields for the current request
		 * @param array $fields Array of named fields (associative) that will be passed on the request body (application/x-www-form-urlencoded)
		 * @return object This instance, for chaining
		 */
		function setFields($fields) {
			$this->fields = $fields;
			return $this;
		}

		/**
		 * Set the headers for the current request
		 * @param array $headers Array of named headers (associative) that will be passed to the request
		 * @return object This instance, for chaining
		 */
		function setHeaders($headers) {
			$this->headers = $headers;
			return $this;
		}

		/**
		 * Set additional CuRL options for the current request
		 * @param array $options Array of named options and its values (associative) in the form `array('CURLOPT_RETURNTRANSFER' => true)`
		 * @return object This instance, for chaining
		 */
		function setOptions($options) {
			$this->options = $options;
			return $this;
		}

		/**
		 * Get the response of the current request, once executed
		 * @param  string $format Format specifier, can be `plain` for the raw response or `json` which will try to parse the response into an object
		 * @return mixed          The raw response (plain) or False/Object (json)
		 * @return object This instance, for chaining
		 */
		function getResponse($format = 'plain') {
			$ret = '';
			switch ($format) {
				case 'json':
					$ret = @json_decode($this->response);
				break;
				default:
					$ret = $this->response;
				break;
			}
			return $ret;
		}

		/**
		 * Get the last error
		 * @return string The last error string, if any
		 */
		function getError() {
			return $this->error;
		}

		/**
		 * Get request info
		 * @return array The current request info (status, headers, etc)
		 */
		function getInfo() {
			return $this->info;
		}

		/**
		 * Execute the current request
		 * @return object This instance, for chaining
		 */
		function execute() {
			if ($this->caching) {
				$url = $this->url;
				$query = http_build_query($this->params);
				if ($query && $this->method == 'get') {
					$url = "{$this->url}?{$query}";
				}
				$hash = md5($url);
				$data = Cacher::getFromCache($hash, 900);
				if (! $data ) {
					$this->_execute();
					$data = json_decode($this->response);
					Cacher::saveToCache($hash, $data);
				} else {
					$this->response = json_encode( $data );
				}
			} else {
				$this->_execute();
			}
			return $this;
		}

		/**
		 * Execute helper, you shouldn't worry about this
		 */
		protected function _execute() {
			# Create query string
			$query = http_build_query($this->params);
			$url = $this->url;
			if ($query) {
				$url = "{$this->url}?{$query}";
			}
			# Open connection
			$ch = curl_init();
			# Set the url, number of POST vars, POST data, etc
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			# Extra options
			if ($this->options) {
				foreach ($this->options as $key => $value) {
					curl_setopt($ch, $key, $value);
				}
			}
			# Add headers
			if ($this->headers) {
				$headers = array();
				foreach ($this->headers as $key => $value) {
					$headers[] = "{$key}: {$value}";
				}
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			}
			# SSL
			if ( preg_match('/https:\/\//', $url) === 1 ) {
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
				curl_setopt($ch, CURLOPT_CAINFO, $this->baseDir('/cacert.pem'));
			}
			# POST/PUT/DELETE
			if ($this->method != 'get') {
				if ( is_array($this->fields) ) {
					$fields = http_build_query($this->fields);
					curl_setopt($ch, CURLOPT_POST, count($this->fields));
					curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				} else {
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($this->method));
					curl_setopt($ch, CURLOPT_POSTFIELDS, $this->fields);
				}
			}
			# Execute request
			$this->response = curl_exec($ch);
			$this->error = curl_error($ch);
			$this->info = curl_getinfo($ch);
			# Close connection
			curl_close($ch);
		}
	}

?>