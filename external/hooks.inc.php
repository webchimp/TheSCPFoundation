<?php
	/**
	 * hooks.inc.php
	 * Add your hook handlers here
	 */

	function hook_template_footer() {
		global $site;
		$site->addScriptVar('constants', array(
			'siteUrl' => $site->urlTo('/'),
			'slug' => json_encode( $site->getSlugs() )
		));
	}
	$site->registerHook('template.htmlFooter', 'hook_template_footer');

?>