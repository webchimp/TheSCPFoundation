<?php
	/**
	 * routes.inc.php
	 * Add your additional routes here
	 */

	# Status route
	# ----------------------------------------------------------------------------------------------
	function route_test() {
		global $site;
		$request = $site->getRequest();
		$response = $site->getResponse();
		$data = [];
		$data['time'] = date('Y-m-d H:i:s');
		return $response->ajaxRespond('success', $data);
	}
	$site->getRouter()->addRoute('/test', 'route_test', true);


	# Site Search
	# ----------------------------------------------------------------------------------------------
	function route_search($id) {
		global $site;
		$dbh = $site->getDatabase();
		$request = $site->getRequest();
		$response = $site->getResponse();
		$data = [];
		$query = urldecode($id[1]);

		try {
			$sql = "SELECT * FROM scp WHERE MATCH (item_number, `name`) AGAINST (:query IN BOOLEAN MODE) LIMIT 0, 30;";
			$stmt = $dbh->prepare($sql);
			$stmt->bindValue(':query', "/{$query}*/");
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_CLASS, 'SCP', ['pdoargs' => ['fetch_metas']]);
			$data['query'] = $query;
			$data['results_number'] = count($results);
			$data['search_results'] = $results;

		} catch (PDOException $e) {}

		return $response->ajaxRespond('success', $data);
	}
	$site->getRouter()->addRoute('/search/:id', 'route_search', true);

	# Directory route
	# ----------------------------------------------------------------------------------------------
	function route_directory($id) {
		global $site;
		$dbh = $site->getDatabase();
		$request = $site->getRequest();
		$response = $site->getResponse();

		try {
			$sql = "SELECT item_number, `name`, object_class FROM scp WHERE id < 1000";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$scps = $stmt->fetchAll();

			array_unshift ($scps, (object)['item_number' => 'scp-000']);

			$series = array_chunk($scps, 100);

			$data = [];
			$data['series'] = $series;

			$site->render('page-directory', $data);

		} catch (PDOException $e) {}

		return true;
	}
	$site->getRouter()->addRoute('/directory/series/:id', 'route_directory', true);

	# Object scrapper
	# ----------------------------------------------------------------------------------------------
	function route_scrapper() {

		global $site;
		$dbh = $site->getDatabase();
		$scps = SCPs::all(['conditions' => 'id > 3800 AND id <= 4000 AND (description REGEXP \'href="\/scp\' OR special_containment_procedures REGEXP \'href="\/scp\')']);

		foreach($scps as $scp) {

			echo "<h1>{$scp->item_number} {$scp->name}</h1>";

			$pattern = "/href=\"\/(scp-(?:\d*(?:[A-za-z0-9-]*)?))\"/";
			preg_match_all($pattern, $scp->description . ' - ' . $scp->special_containment_procedures, $matches);

			print_a($matches);

			foreach($matches[1] as $match) {

				$scp2 = SCPs::getByItemNumber($match);

				if($scp2) {

					$sql = "INSERT IGNORE INTO scp_relationship VALUES (:value_a, :value_b)";
					$stmt = $dbh->prepare($sql);
					$stmt->bindValue(':value_a', $scp->id);
					$stmt->bindValue(':value_b', $scp2->id);
					$stmt->execute();
				}
			}
		}

		return true;
	}
	$site->getRouter()->addRoute('/scrapper', 'route_scrapper', true);

	# Object route
	# ----------------------------------------------------------------------------------------------
	function route_object($id) {
		global $site;
		$request = $site->getRequest();
		$response = $site->getResponse();

		$scp_id = urldecode($id[1]);

		$scp = SCPs::getByItemNumber($scp_id);

		$prev_scp = $scp->getMeta('prev_scp') ?: SCPs::get(['conditions' => "id = (SELECT MAX(id) FROM scp WHERE id < {$scp->id} AND name != '[ACCESS DENIED]')"]);
		$next_scp = $scp->getMeta('next_scp') ?: SCPs::get(['conditions' => "id = (SELECT MIN(id) FROM scp WHERE id > {$scp->id} AND name != '[ACCESS DENIED]')"]);

		if($prev_scp && is_object($prev_scp)) $scp->updateMeta('prev_scp', $prev_scp->item_number);
		if($next_scp && is_object($next_scp)) $scp->updateMeta('next_scp', $next_scp->item_number);

		$relations_html = '';
		foreach($scp->relations as $relation) {
			$relation_scp = SCPs::getById($relation->id_scp2);
			$relations_html .= '<a data-scp-tooltip="' . $relation_scp->item_number . '" href="' . $site->urlTo("/object/{$relation_scp->item_number}") . '">' . $relation_scp->item_number . '</a>';
		}

		$data = [];
		$data['id'] = $scp_id;
		$data['scp'] = $scp;
		$data['prev_scp'] = $scp->getMeta('prev_scp');
		$data['next_scp'] = $scp->getMeta('next_scp');
		$data['relations_html'] = $relations_html;

		//get_scp_images($scp);

		$site->render('page-object', $data);
		return true;
	}
	$site->getRouter()->addRoute('/object/:id', 'route_object', true);

	# Object Supplement route
	# ----------------------------------------------------------------------------------------------
	function route_object_supplement($id) {
		global $site;
		$request = $site->getRequest();
		$response = $site->getResponse();

		$scp_id = urldecode($id[1]);
		$supplement_slug = urldecode($id[2]);

		$scp = SCPs::getByItemNumber($scp_id);
		$supplement =Supplements::getBySlug($supplement_slug);

		$data = [];
		$data['scp'] = $scp;
		$data['supplement'] = $supplement;

		$site->render('page-object-supplement', $data);
		return true;
	}
	$site->getRouter()->addRoute('/object/:id/:id', 'route_object_supplement', true);

	# Tale route
	# ----------------------------------------------------------------------------------------------
	function route_tale($id) {
		global $site;
		$request = $site->getRequest();
		$response = $site->getResponse();

		$tale_id = urldecode($id[1]);

		$tale = Tales::getBySlug($tale_id);

		$data = [];
		$data['tale'] = $tale;

		$site->render('page-tale', $data);
		return true;
	}
	$site->getRouter()->addRoute('/tale/:id', 'route_tale', true);

	# Tooltip Helper route
	# ----------------------------------------------------------------------------------------------
	function route_tooltip_helper($id) {
		global $site;
		$request = $site->getRequest();
		$response = $site->getResponse();

		$scp_id = urldecode($id[1]);

		$scp = SCPs::getByItemNumber($scp_id);

		$data = [];
		$data['scp'] = $scp;

		$site->render('object-tooltip', $data);
		return true;
	}
	$site->getRouter()->addRoute('/helper/tooltip/:id', 'route_tooltip_helper', true);
?>