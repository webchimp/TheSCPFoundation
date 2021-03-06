<?php

	class Response {

		/**
		 * Current response body
		 * @var string
		 */
		protected $body;

		/**
		 * Current response status code (HTTP status)
		 * @var integer
		 */
		protected $status;

		/**
		 * Current response headers
		 * @var array
		 */
		protected $headers;

		/**
		 * Constructor
		 */
		function __construct() {
			$this->body = '';
			$this->status = 200;
			$this->headers = array();
		}

		/**
		 * Write to the current response body, appends data
		 * @param  string $data Raw response data
		 */
		function write($data) {
			$this->body .= $data;
		}

		/**
		 * Set the body for the current response, replaces contents (if any)
		 * @param string $data Raw response body
		 */
		function setBody($data) {
			$this->body = $data;
		}

		/**
		 * Get the status code for the current response
		 * @return integer Current response status code
		 */
		function getStatus() {
			return $this->status;
		}

		/**
		 * Set the status code for the current response
		 * @param integer $code A valid HTTP response code (200, 404, 500, etc.)
		 */
		function setStatus($code) {
			$this->status = $code;
		}

		/**
		 * Get the current response body
		 * @return string The response body
		 */
		function getBody() {
			return $this->body;
		}

		/**
		 * Set the value of an specific header for the current response
		 * @param string $name  Header name
		 * @param string $value Header value
		 */
		function setHeader($name, $value) {
			$this->headers[$name] = $value;
		}

		/**
		 * Get the value of an specific header for the current response
		 * @param  string $name Header name
		 * @return mixed        Header value or Null if it's not set
		 */
		function getHeader($name) {
			return isset( $this->headers[$name] ) ? $this->headers[$name] : null;
		}

		/**
		 * Get the array of headers for the current response
		 */
		function getHeaders() {
			return $this->headers;
		}

		/**
		 * Do an HTTP redirection
		 * @param  string $url URL to redirect to
		 */
		function redirect($url) {
			header("Location: {$url}");
			exit;
		}

		/**
		 * Flush headers and response body
		 * @return boolean This will always return True
		 */
		function respond() {
			http_response_code($this->status);
			# Send headers
			foreach ($this->headers as $header => $value) {
				header("{$header}: {$value}");
			}
			# Send response
			echo $this->getBody();
			return true;
		}

		function ajaxRespond($result, $data = null, $message = null) {
			$ret = array();
			$ret['result'] = $result;
			if ($data) {
				$ret['data'] = $data;
			}
			if ($message) {
				$ret['message'] = $message;
			}
			$this->setHeader('Content-Type', 'application/json');
			$this->setBody( json_encode($ret) );
			$this->respond();
			return true;
		}
	}

?>