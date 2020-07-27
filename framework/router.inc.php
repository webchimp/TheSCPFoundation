<?php

	class Router {

		protected $routes;
		protected $default_route;

		function __construct() {
			$this->routes = array();
			$this->default_route = '/home';
		}

		/**
		 * Get the default route
		 * @return string The default route
		 */
		function getDefaultRoute() {
			return $this->default_route;
		}

		/**
		 * Set the default route
		 * @param string $route Full route, defaults to '/home'
		 */
		function setDefaultRoute($route) {
			$this->default_route = $route;
		}

		/**
		 * Add a new route
		 * @param  string  $route     Parametrized route
		 * @param  string  $functName Handler function name
		 * @param  boolean $insert    If set, the route will be inserted at the beginning
		 */
		function addRoute($route, $functName, $insert = false) {
			if ($insert) {
				$this->routes = array_reverse($this->routes, true);
				$this->routes[$route] = $functName;
				$this->routes = array_reverse($this->routes, true);
			} else {
				$this->routes[$route] = $functName;
			}
		}

		/**
		 * Removes the specified route
		 * @param  string $route Parametrized route
		 * @return boolean       True if the route was found and removed, false otherwise
		 */
		function removeRoute($route) {
			if ( $this->isRoute($route) ) {
				unset( $this->routes[$route] );
				return true;
			}
			return false;
		}

		/**
		 * Check whether a given route exists or not
		 * @param  string $route Parametrized route
		 * @return boolean		True if the route exists, false otherwise
		 */
		function isRoute($route) {
			return isset( $this->routes[$route] );
		}

		/**
		 * Get the registered routes
		 * @return array The registered routes
		 */
		function getRoutes() {
			return $this->routes;
		}

		/**
		 * Retrieve the current request URI
		 * @return string The current request URI
		 */
		function getCurrentUrl() {
			global $site;
			# Routing stuff, first get the site url
			$site_url = trim($site->baseUrl(), '/');

			# Remove the protocol from it
			$domain = preg_replace('/^(http|https):\/\//', '', $site_url);

			# Now remove the path
			$segments = explode('/', $domain, 2);
			if (count($segments) > 1) {
				$domain = array_pop($segments);
			}

			# Get the request and remove the domain
			$request_uri = trim($_SERVER['REQUEST_URI'], '/');
			$request_uri = preg_replace("/".str_replace('/', '\/', $domain)."/", '', $request_uri, 1);
			$request_uri = ltrim($request_uri, '/');

			return $request_uri;
		}

		/**
		 * Remove all the registered routes
		 * @return nothing
		 */
		function clearRoutes() {
			$this->routes = array();
		}

		/**
		 * Try to match the given route with one of the registered handlers and process it
		 * @param  string $route  		The route to match
		 * @return boolean        		TRUE if the route matched with a handler, FALSE otherwise
		 */
		function matchRoute($spec_route) {
			global $site;
			$ret = false;
			# And try to match the route with the registered ones
			$matches = array();
			foreach ($this->routes as $route => $handler) {
				# Compile route into regular expression
				$a = preg_replace('/[\{}\[\]+?.,\\\^$|#\s]/', '\\$&', $route); // escapeRegExp
				$b = preg_replace('/\((.*?)\)/', '(?:$1)?', $a);                // optionalParam
				$c = preg_replace('/(\(\?)?:\w+/', '([^\/]+)', $b);             // namedParam
				$d = preg_replace('/\*\w+/', '(.*?)', $c);                      // splatParam
				$pattern = "~^{$d}$~";
				if ( preg_match($pattern, $spec_route, $matches) == 1) {
					# We've got a match, try to route with this handler
					$site->executeHook('router.beforeHandler', $handler);
					$ret = call_user_func($handler, $matches);
					$site->executeHook('router.afterHandler', $ret);
					if ($ret) {
						# Exit the loop only if the handler did its job
						break;
					}
				}
			}
			return $ret;
		}

		/**
		 * Process current request
		 * @return boolean TRUE if routing has succeeded, FALSE otherwise
		 */
		function routeRequest() {
			global $site;
			$ret = false;
			$request = $site->getRequest();
			$response = $site->getResponse();

			# Get the current URL
			$request_uri = $this->getCurrentUrl();

			# Save current request string
			$request->uri = $request_uri;

			# Get the segments
			$segments = explode('?', $request_uri);
			$cur_route = array_shift($segments);
			$request->parts = explode('/', $cur_route);

			# Now make sure the current route begins with '/' and doesn't end with '/'
			$cur_route = '/' . $cur_route;
			$cur_route = rtrim($cur_route, '/');

			# Make sure we have a valid route
			if ( empty($cur_route) ) {
				$cur_route = $this->default_route;
			}

			ob_start();

			$site->executeHook('router.beforeRouting', $cur_route);
			$ret = $this->matchRoute($cur_route);
			$site->executeHook('router.afterRouting', $cur_route);

			if (! $ret ) {
				$response->setStatus(404);
				$site->render('page-error');
			}

			$response->setBody( ob_get_clean() );
			$response->respond();

			return $ret;
		}
	}

?>