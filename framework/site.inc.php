<?php

	class Site {

		private static $instance;

		protected $profile;
		protected $globals;
		protected $base_url;
		protected $base_dir;
		protected $request;
		protected $response;
		protected $router;
		protected $database;
		protected $plugins;
		protected $script_vars;
		protected $dirs;
		protected $scripts;
		protected $styles;
		protected $enqueued_scripts;
		protected $enqueued_styles;
		protected $metas;
		protected $site_title;
		protected $page_title;
		protected $pass_salt;
		protected $token_salt;
		protected $hooks;
		protected $slugs;
		protected $pages;

		public static function getInstance() {
			if (null === static::$instance) {
				static::$instance = new static();
			}
			return static::$instance;
		}

		/**
		 * Private constructor to prevent creating a new instance of the *Site* via the `new` operator from outside of this class.
		 *
		 * @return void
		 */
		protected function __construct() {
			$this->pages = array();
			$this->slugs = array();
			$this->hooks = array();
			$this->script_vars = array();
			$this->scripts = array();
			$this->styles = array();
			$this->enqueued_scripts = array();
			$this->enqueued_styles = array();
			$this->metas = array();
			$this->hooks = array();
		}

		function initialize($settings) {
			# Load settings
			$this->profile = $settings[PROFILE];
			$this->globals = $settings['shared'];
			$this->base_dir = BASE_DIR;
			$this->base_url = $this->profile['site_url'];
			# Create the Request and Response objects
			$this->request = new Request();
			$this->response = new Response();
			# Create the Router object
			$this->router = new Router();
			# Create the Database object
			$this->database = new Database($this->profile['database']);
			# Initialize variables
			$this->pass_salt = $this->globals['pass_salt'];
			$this->token_salt = $this->globals['token_salt'];
			$this->site_title = $this->globals['site_name'];
			# Registered plugins
			$this->plugins = $this->profile['plugins'];
			# Default dirs
			$this->dirs = array(
				'plugins' => '/plugins',
				'pages'   => '/templates/pages',
				'partials'   => '/templates/partials',
				'images'  => '/assets/images',
				'scripts' => '/assets/scripts',
				'styles'  => '/assets/styles'
			);
			# Default page
			$this->addPage('home', 'page-home');
			# And default route
			$this->router->addRoute('/:page', 'Site::getPage');
			#
			$this->page_title = $this->site_title;
			return $this;
		}

		function getDatabase() {
			return $this->database->dbh;
		}

		function getRequest() {
			return $this->request;
		}

		function getResponse() {
			return $this->response;
		}

		function getRouter() {
			return $this->router;
		}

		function getPlugins() {
			return $this->plugins;
		}

		static function getPage($params) {
			global $site;
			$ret = false;
			if ( is_array($params) ) {
				$slug = isset( $params[1] ) ? $params[1] : 'home';
			} else {
				$slug = $params;
			}
			$slug = ltrim( rtrim($slug, '/'), '/' );
			if ( isset( $site->pages[$slug] ) ) {
				$template = $site->pages[$slug];
				$site->render($template);
				$ret = true;
			}
			return $ret;
		}

		function render($template, $data = array(), $dir = null) {
			$request = $this->getRequest();
			$dir = $dir ? $dir : $this->getDir('pages');
			$include = sprintf('%s/%s.php', $dir, $template);
			# Check whether the template exists or not
			if ( file_exists($include) ) {
				# Expand data
				extract($data, EXTR_SKIP);
				# Set body slug
				$this->addBodyClass( trim( str_replace('/', '-', $template), '-' ) );
				# Import globals
				extract($GLOBALS, EXTR_REFS | EXTR_SKIP);
				# Hide function parameters
				unset($data);
				unset($template);
				# Include file
				include $include;
			} else {
				$this->errorMessage("View error: template '{$include}' does not exist.");
			}
		}

		function partial($partial, $data = array(), $dir = null) {
			if ( is_array($partial) ) {
				foreach ($partial as $p) {
					$this->partial($p, $data, $dir);
				}
			} else {
				$dir = $dir ? $dir : $this->getDir('partials');
				$div = strrpos($partial, '/');
				$path = substr($partial, 0, $div);
				$file = substr($partial, ++$div);
				$partial = $path ? "{$path}/_{$file}" : "_{$file}";
				$include = sprintf('%s/%s.php', $dir, $partial);
				# Check whether the template exists or not
				if ( file_exists($include) ) {
					# Expand data
					extract($data, EXTR_SKIP);
					# Import globals
					extract($GLOBALS, EXTR_REFS | EXTR_SKIP);
					# Hide function parameters
					unset($data);
					unset($partial);
					# Include file
					include $include;
				} else {
					$this->errorMessage("View error: partial '{$include}' does not exist");
				}
			}
		}

		/**
		 * Check if the current request was made via AJAX
		 * @return boolean Whether the request was made via AJAX or not
		 */
		function isAjaxRequest() {
			return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
		}

		/**
		 * Check if the current request was made via HTTPS
		 * @return boolean Whether the request was made via HTTPS or not
		 */
		function isSecureRequest() {
			$https = isset( $_SERVER['HTTPS'] ) ? $_SERVER['HTTPS'] : 'off';
			return ($https != '' && $https != 'off');
		}

		/**
		 * Get the specified directory path
		 * @param  string  $dir  Directory name
		 * @param  boolean $full Whether to return a relative or fully-qualified path
		 * @return mixed         The path to the specified directory or False if it doesn't exist
		 */
		function getDir($dir, $full = true) {
			if ( isset( $this->dirs[$dir] ) ) {
				return ($full ? $this->baseDir( $this->dirs[$dir] ) : $this->dirs[$dir]);
			}
			return false;
		}

		/**
		 * Set the path to the specified directory
		 * @param string $dir  Directory name, if it exists it will be overwritten
		 * @param string $path Path to the directory, relative to the site root
		 */
		function setDir($dir, $path) {
			$this->dirs[$dir] = $path;
		}

		/**
		 * Get base folder
		 * @param  string  $path Path to append
		 * @param  boolean $echo Whether to print the resulting string or not
		 * @return string        The well-formed path
		 */
		function baseDir($path = '', $echo = false) {
			$ret = sprintf('%s%s', $this->base_dir, $path);
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

		/**
		 * Get base URL
		 * @param  string  $path     Path to append
		 * @param  boolean $echo     Whether to print the resulting string or not
		 * @param  string  $protocol Protocol to override default http (https, ftp, etc)
		 * @return string            The well-formed URL
		 */
		function baseUrl($path = '', $echo = false, $protocol = null) {
			$base_url = rtrim($this->base_url, '/');
			if (!$protocol && $this->isSecureRequest() ) {
				$base_url = str_replace('http://', 'https://', $base_url);
			} else if ($protocol) {
				$protocol .= strrpos($protocol, ':') > 0 ? '' : ':';
				$base_url = str_replace('http:', $protocol, $base_url);
			}
			if ( !empty($path) && $path[0] != '/' ) {
				$path = '/' . $path;
			}
			$ret = sprintf('%s%s', $base_url, $path);
			# Print and/or return the result
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

		/**
		 * Redirect to given route
		 * @param  string $route    Route to redirect to
		 * @param  string $protocol Protocol to override default http (https, ftp, etc)
		 */
		function redirectTo($route, $protocol = null, $http_response_code = 302) {
			global $site;
			if ( preg_match('/^(http:\/\/|https:\/\/).*/', $route) !== 1 ) {
				$url = $this->baseUrl($route, false, $protocol);
			} else {
				$url = $route;
			}
			$header = sprintf('Location: %s', $url);
			header($header, true, $http_response_code);
			$site->executeHook('core.redirect', $route);
			exit;
		}

		/**
		 * Get a well formed url to the specified route or page slug
		 * @param  string  $route    Route or page slug
		 * @param  boolean $echo     Whether to print out the resulting url or not
		 * @param  string  $protocol Protocol to override default http (https, ftp, etc)
		 * @return string            The resulting url
		 */
		function urlTo($route, $echo = false, $protocol = null) {
			$url = $this->baseUrl($route, false, $protocol);
			if ($echo) {
				echo $url;
			}
			return $url;
		}

		/**
		 * Get a well formed url to the specified image file and optionally echo it
		 * @param  string  $filename Image file name (e.g. 'logo.png')
		 * @param  boolean $echo     Whether to print out the resulting url or not
		 * @return string            The resulting url
		 */
		function img($filename, $echo = true) {
			$dir = $this->getDir('images', false);
			$ret = $this->urlTo( sprintf('%s/%s', $dir, $filename), $echo);
			return $ret;
		}

		/**
		 * Get the current slug list
		 * @param  boolean $echo Whether to print the result or not
		 * @return string        String with space-delimited slugs
		 */
		function bodyClass($echo = true) {
			$ret = implode(' ', $this->slugs);
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

		/**
		 * Append a class to the body classes array
		 * @param mixed $class 	Class name or array with class names
		 */
		function addBodyClass($class) {
			if ($class) {
				if ( is_array($class) ) {
					foreach ($class as $item) {
						$this->addBodyClass($item);
					}
				} else {
					if (! in_array($class, $this->slugs) ) {
						$this->slugs[] = $class;
					}
				}
			}
		}

		/**
		 * Check whether the given slug is on the current list of slugs
		 * @param  string  $slug The slug
		 * @return boolean       True if the slug is in the slugs array, False otherwise
		 */
		function hasSlug($slug) {
			return in_array($slug, $this->slugs);
		}

		/**
		 * Retrieve the current list of slugs
		 */
		function getSlugs() {
			return $this->slugs;
		}

		/**
		 * Add a new page to the whitelist
		 * @param  string $slug     Page slug
		 * @param  string $template Page template name (without extension)
		 */
		function addPage($slug, $template = '') {
			if ( empty($template) ) {
				$template = $slug;
			}
			$this->pages[$slug] = $template;
		}

		/**
		 * Removes the specified page
		 * @param  string $slug  Page slug
		 * @return boolean       True if the page was found and removed, false otherwise
		 */
		function removePage($slug) {
			if ( isset( $this->pages[$slug] ) ) {
				unset( $this->pages[$slug] );
				return true;
			}
			return false;
		}

		/**
		 * Sanitize the given string (slugify it)
		 * @param  string $str       The string to sanitize
		 * @param  array  $replace   Optional, an array of characters to replace
		 * @param  string $delimiter Optional, specify a custom delimiter
		 * @return string            Sanitized string
		 */
		function toAscii($str, $replace = array(), $delimiter = '-') {
			setlocale(LC_ALL, 'en_US.UTF8');
			# Remove spaces
			if( !empty($replace) ) {
				$str = str_replace((array)$replace, ' ', $str);
			}
			# Remove non-ascii characters
			$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
			# Remove non alphanumeric characters and lowercase the result
			$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
			$clean = strtolower(trim($clean, '-'));
			# Remove other unwanted characters
			$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
			return $clean;
		}

		/**
		 * Hash the specified token
		 * @param  mixed  $action  Action name(s), maybe a single string or an array of strings
		 * @param  boolean $echo   Whether to output the resulting string or not
		 * @return string          The hashed token
		 */
		function hashToken($action, $echo = false) {
			if ( is_array($action) ) {
				$action_str = '';
				foreach ($action as $item) {
					$action_str .= $item;
				}
				$ret = md5($this->token_salt.$action_str);
			} else {
				$ret = md5($this->token_salt.$action);
			}
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

		/**
		 * Hash the specified password
		 * @param  string  $password 	Plain-text password
		 * @param  boolean $echo   		Whether to output the resulting string or not
		 * @return string          		The hashed password
		 */
		function hashPassword($password, $echo = false) {
			$ret = md5($this->pass_salt.$password);
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

		/**
		 * Validate the given token with the specified action
		 * @param  string $token  Hashed token
		 * @param  string $action Action name
		 * @return boolean        True if the token is valid, False otherwise
		 */
		function validateToken($token, $action) {
			$check = $this->hashToken($action);
			return ($token == $check);
		}

		/**
		 * Register a hook listener
		 * @param  string  $hook      Hook name
		 * @param  string  $functName Callback function name
		 * @param  boolean $prepend   Whether to add the listener at the beginning or the end
		 */
		function registerHook($hook, $functName, $prepend = false) {
			if (! isset( $this->hooks[$hook] ) ) {
				$this->hooks[$hook] = array();
			}
			if ($prepend) {
				array_unshift($this->hooks[$hook], $functName);
			} else {
				array_push($this->hooks[$hook], $functName);
			}
		}

		/**
		 * Execute a hook (run each listener incrementally)
		 * @param  string $hook   	Hook name
		 * @param  mixed  $params 	Parameter to pass to each callback function
		 * @return mixed          	The processed data or the same data if no callbacks were found
		 */
		function executeHook($hook, $param = '') {
			if ( isset( $this->hooks[$hook] ) ) {
				$hooks = $this->hooks[$hook];
				$ret = true;
				foreach ($hooks as $hook) {
					$ret = call_user_func($hook, $param);
				}
				return $ret;
			}
			return false;
		}

		/**
		 * Get the specified option from the current profile
		 * @param  string $key     Option name
		 * @param  string $default Default value
		 * @return mixed           The option value (array, string, integer, boolean, etc)
		 */
		function getOption($key, $default = '') {
			$ret = $default;
			if ( isset( $this->profile[$key] ) ) {
				$ret = $this->profile[$key];
			}
			return $ret;
		}

		/**
		 * Get the specified option from the global profile
		 * @param  string $key     Option name
		 * @param  string $default Default value
		 * @return mixed           The option value (array, string, integer, boolean, etc)
		 */
		function getGlobal($key, $default = '') {
			$ret = $default;
			if ( isset( $this->globals[$key] ) ) {
				$ret = $this->globals[$key];
			}
			return $ret;
		}

		/**
		 * Set the page title
		 * @param string $title New page title
		 */
		function setPageTitle($title) {
			$this->page_title = $title;
		}

		/**
		 * Set the site title
		 * @param string $title New site title
		 */
		function setSiteTitle($title) {
			$this->site_title = $title;
		}

		/**
		 * Return page title with optional prefix/suffix
		 * @param  string $prefix    Prefix to prepend
		 * @param  string $suffix    Suffix to append
		 * @param  string $separator Separator character
		 * @return string            Formatted and escaped title
		 */
		function getPageTitle($prefix = '', $suffix = '', $separator = '-') {
			$ret = $this->page_title;
			if (! empty($prefix) ) {
				$ret = sprintf('%s %s %s', htmlspecialchars($prefix), $separator, $ret);
			}
			if (! empty($suffix) ) {
				$ret = sprintf('%s %s %s', $ret, $separator, htmlspecialchars($suffix));
			}
			return $ret;
		}

		/**
		 * Get the site name
		 * @param  boolean $echo Print the result?
		 * @return string        Site name
		 */
		function getSiteTitle($echo = false) {
			$ret = $this->site_title;
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

		/**
		 * Add an stylesheet to the list
		 * @param  string $name      Name of the stylesheet
		 * @param  string $url       URL to the stylesheet (absolute)
		 * @param boolean $external  Whether the file is external (e.g. from CDN) or not
		 * @param  array  $requires  Array of stylesheets this stylesheet depends on (they'll be automatically added to the page)
		 */
		function registerStyle($name, $url, $external = false, $requires = array(), $attrs = array()) {
			global $site;
			$dir = $this->getDir('styles', false);
			$this->styles[$name] = array(
				'resource' => $external ? $url : $site->baseUrl("{$dir}/{$url}"),
				'requires' => $requires,
				'external' => $external,
				'attrs' => $attrs
			);
		}

		/**
		 * Add an script to the list
		 * @param  string $name      Name of the script
		 * @param  string $url       URL to the script (absolute)
		 * @param boolean $external  Whether the file is external (e.g. from CDN) or not
		 * @param  array  $requires  Array of scripts this script depends on (they'll be automatically added to the page)
		 */
		function registerScript($name, $url, $external = false, $requires = array(), $attrs = array()) {
			global $site;
			$dir = $this->getDir('scripts', false);
			$this->scripts[$name] = array(
				'resource' => $external ? $url : $site->baseUrl("{$dir}/{$url}"),
				'requires' => $requires,
				'external' => $external,
				'attrs' => $attrs
			);
		}

		/**
		 * Add an script variable
		 * @param string $var   Variable name
		 * @param mixed  $value Variable value
		 */
		function addScriptVar($var, $value) {
			$this->script_vars[$var] = $value;
		}

		/**
		 * Remove an script variable
		 * @param  string $var Variable name
		 * @return nothing
		 */
		function removeScriptVar($var) {
			unset( $this->script_vars[$var] );
		}

		/**
		 * Print the registered script variables
		 * @return nothing
		 */
		function includeScriptVars() {
			global $site;
			$vars = '';
			if ($this->script_vars) {
				foreach ($this->script_vars as $var => $value) {
					if ( is_array($value) || is_object($value) ) {
						$value = json_encode($value);
					} elseif (! is_numeric($value) ) {
						$value = "'{$value}'";
					}
					$vars .= "var {$var} = {$value};\n";
				}
				$output = $site->executeHook('core.includeScriptVars', $vars);
				$output = $output ? $output : sprintf("<script type=\"text/javascript\">\n%s</script>", $vars);
				echo($output."\n");
			}
		}

		/**
		 * Add a previously registered stylesheet to the inclusion queue
		 * @param  string $name Name of the registered stylesheet
		 */
		function enqueueStyle($name) {
			if ( isset( $this->styles[$name] ) ) {
				if (! isset($this->enqueued_styles[$name]) ) {
					$item = $this->styles[$name];
					foreach ($item['requires'] as $dep) {
						$this->enqueueStyle($dep);
					}
					$this->enqueued_styles[$name] = $name;
				}
			}
		}

		/**
		 * Add a previously registered script to the inclusion queue
		 * @param  string $name 	Name of the registered script
		 */
		function enqueueScript($name) {
			if ( isset( $this->scripts[$name] ) ) {
				if (! isset($this->enqueued_scripts[$name]) ) {
					$item = $this->scripts[$name];
					foreach ($item['requires'] as $dep) {
						$this->enqueueScript($dep);
					}
					$this->enqueued_scripts[$name] = $name;
				}
			}
		}

		/**
		 * Remove a previously enqueued stylesheet from the inclusion queue
		 * @param string $name  Name of the enqueued stylesheet
		 * @param boolean $dependencies Dequeue dependencies too (not recommended)
		 */
		function dequeueStyle($name, $dependencies = false) {
			if ( isset( $this->styles[$name] ) ) {
				if ( isset($this->enqueued_styles[$name]) ) {
					$item = $this->styles[$name];
					if ($dependencies) {
						foreach ($item['requires'] as $dep) {
							$this->dequeueStyle($dep);
						}
					}
					unset( $this->enqueued_styles[$name] );
				}
			}
		}

		/**
		 * Remove a previously enqueued script from the inclusion queue
		 * @param string $name  Name of the enqueued script
		 * @param boolean $dependencies Dequeue dependencies too (not recommended)
		 */
		function dequeueScript($name, $dependencies = false) {
			if ( isset( $this->scripts[$name] ) ) {
				if ( isset($this->enqueued_scripts[$name]) ) {
					$item = $this->scripts[$name];
					if ($dependencies) {
						foreach ($item['requires'] as $dep) {
							$this->dequeueScript($dep);
						}
					}
					unset( $this->enqueued_scripts[$name] );
				}
			}
		}

		/**
		 * Output the specified style
		 * @param  string $style 	Registered style name
		 */
		function includeStyle($style) {
			global $site;
			if ( isset( $this->styles[$style] ) ) {
				$item = $this->styles[$style];
				$output = $site->executeHook('core.includeStyle', $item);
				$attrs = '';
				if ( $item['attrs'] ) {
					foreach ($item['attrs'] as $key => $value) {
						$attrs .= ($key == $value || $value === '') ? " {$key}" : sprintf(' %s="%s"', $key, $value);
					}
				}
				$output = $output ? $output : sprintf('<link rel="stylesheet" type="text/css" href="%s"%s>', $item['resource'], $attrs);
				if ( trim($output) ) {
					echo($output."\n");
				}
			}
		}

		/**
		 * Output the specified script
		 * @param  string $script 	Registered script name
		 */
		function includeScript($script) {
			global $site;
			if ( isset( $this->scripts[$script] ) ) {
				$item = $this->scripts[$script];
				$output = $site->executeHook('core.includeScript', $item);
				$attrs = '';
				if ( $item['attrs'] ) {
					foreach ($item['attrs'] as $key => $value) {
						$attrs .= ($key == $value || $value === '') ? " {$key}" : sprintf(' %s="%s"', $key, $value);
					}
				}
				$output = $output ? $output : sprintf('<script type="text/javascript" src="%s"%s></script>', $item['resource'], $attrs);
				if ( trim($output) ) {
					echo($output."\n");
				}
			}
		}

		/**
		 * Output all the registered stylesheets
		 */
		function includeStyles() {
			global $site;
			foreach ($this->enqueued_styles as $style) {
				$this->includeStyle($style);
			}
			$site->executeHook('core.includeStyles');
		}

		/**
		 * Output all the registered scripts
		 */
		function includeScripts() {
			global $site;
			foreach ($this->enqueued_scripts as $script) {
				$this->includeScript($script);
			}
			$site->executeHook('core.includeScripts');
		}

		/**
		 * Retrieve all the registered stylesheets
		 */
		function getStyles() {
			return $this->styles;
		}

		/**
		 * Retrieve all the registered scripts
		 */
		function getScripts() {
			return $this->scripts;
		}

		/**
		 * Display a generic error message
		 * @param  string $message The error message
		 */
		function errorMessage($message) {
			$markup = '<!DOCTYPE html> <html lang="en"> <head> <meta charset="UTF-8"> <title>{$title}</title> <style> body { font-family: sans-serif; font-size: 14px; background: #F8F8F8; } div.center { width: 960px; margin: 0 auto; padding: 1px 0; } p.message { padding: 15px; border: 1px solid #DDD; background: #F1F1F1; color: #656565; } </style> </head> <body> <div class="center"> <p class="message">{$message}</p> </div> </body> </html>';
			$markup = str_replace('{$title}', $this->getSiteTitle(), $markup);
			$markup = str_replace('{$message}', $message, $markup);
			echo $markup;
			exit;
		}

		/**
		 * Add a meta tag to the site
		 * @param string $name      Meta name
		 * @param string $content   Meta content (optional)
		 * @param string $attribute Attribute to use for 'name' (charset, etc)
		 */
		function addMeta($name, $content = '', $attribute = 'name') {
			$this->metas[$name] = array(
				'name' => $name,
				'content' => $content,
				'attribute' => $attribute
			);
		}

		/**
		 * Remove a meta tag from the site
		 * @param  string $name Meta name
		 * @return nothing
		 */
		function removeMeta($name) {
			unset( $this->metas[$name] );
		}

		/**
		 * Print the meta tags added to the site
		 * @return nothing
		 */
		function metaTags() {
			foreach ($this->metas as $meta) {
				echo( $meta['content'] ?
						"<meta {$meta['attribute']}=\"{$meta['name']}\" content=\"{$meta['content']}\">\n" :
						"<meta {$meta['attribute']}=\"{$meta['name']}\">\n"
					);
			}
		}

		/**
		 * Private clone method to prevent cloning of the instance of the *Site* instance.
		 *
		 * @return void
		 */
		private function __clone() {
			//
		}

		/**
		 * Private unserialize method to prevent unserializing of the *Site* instance.
		 *
		 * @return void
		 */
		private function __wakeup() {
			//
		}

	}

?>