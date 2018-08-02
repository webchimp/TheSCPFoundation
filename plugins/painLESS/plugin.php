<?php

	include dirname(__FILE__) . '/lib/less.php/Less.php';

	class painLESS {

		static function includeStyleHook($item) {
			global $site;
			$ret = false;
			$resource = $item['resource'];
			# Plugin options
			$cache_enabled = $site->getOption('painLESS_caching', false);
			$cache_time = $site->getOption('painLESS_time', 900);
			# Check file extension
			if ( strtolower( substr( $resource, -5 ) ) == '.less' ) {
				# Separate file parts
				$path = substr($resource, 0, strrpos($resource, '/'));
				$file = substr($resource, strrpos($resource, '/') + 1);
				$rel_path = str_replace($site->urlTo('/'), '', $path);
				$comp_file = str_replace('.less', '.css', $file);
				# Generate source/dest file names
				$src_file = $site->baseDir("/{$rel_path}/{$file}");
				$dest_file = $site->baseDir("/{$rel_path}/{$comp_file}");
				# Check files
				if ( file_exists($src_file) ) {
					if ( $cache_enabled && file_exists($dest_file) && time() - filemtime($dest_file) < $cache_time) {
						// Gotcha! Cache hit, don't parse again
					} else {
						// Cache is old, parse again
						try{
							$parser = new Less_Parser();
							$parser->parseFile( $src_file, $site->urlTo('/assets') );
							file_put_contents($dest_file, $parser->getCss());
						}catch(Exception $e){
							echo $error_message = $e->getMessage();
						}
					}
					$ret = sprintf('<link rel="stylesheet" type="text/css" href="%s">', $site->urlTo("/{$rel_path}/{$comp_file}"));
				}
			}
			return $ret;
		}
	}

	$site->registerHook('core.includeStyle', 'painLESS::includeStyleHook');
?>