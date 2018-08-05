<?php
	/**
	 * utilities.inc.php
	 * Add your additional functions here
	 */

	function scrape_scp($id, $content = false) {

		global $site;

		require $site->baseDir('/external/lib/simple_html_dom.php');

		if(!$content) {

			$curly = Curly::newInstance(false)
				->setMethod('get')
				->setURL('http://www.scp-wiki.net/' . $id)
				->execute();

			$res_info = $curly->getInfo();
			$res = $curly->getResponse('plain');

			if($res_info['http_code'] == 404) return;

		} else {

			$res = $content;
		}

		$res = trim(preg_replace('/\s+/', ' ', $res));
		$html = str_get_html($res);

		$page_title = $html->find('#page-title', 0)->innertext;
		$page_content = $html->find('#page-content', 0);
		$featured_image = $html->find('.scp-image-block img', 0);
		$featured_image_caption = $html->find('.scp-image-block .scp-image-caption p', 0);

		if($featured_image && $featured_image->src) {

			copy($featured_image->src, $site->baseDir('/temp/' . $id . '.jpg'));
		}

		$item_number = '';
		$object_class = '';
		$description = '';

		$strongs = $page_content->find('strong');

		foreach($strongs as $strong) {

			if($strong->plaintext == 'Item #:') {

				$item_number = $strong->parent();
				$item_number = str_replace('<strong>Item #:</strong> ', '', $item_number->innertext);
			}

			if($strong->plaintext == 'Object Class:') {

				$object_class = $strong->parent();
				$object_class = str_replace('<strong>Object Class:</strong> ', '', $object_class->innertext);
			}

			if(!in_array($object_class, ['Safe', 'Euclid', 'Keter'])) {

				$esoteric_object_class = $object_class;
				$object_class = 'Esoteric';
			}

		}
		$re = '/<p><strong>(Secure |Special )?Containment Pro(?:tocol|cedure)(s?)(:?)<\/strong>(:?)(.*?)<p><strong>Description/i';
		preg_match_all($re, $res, $matches, PREG_SET_ORDER, 0);

		if($matches) {

			$special_containment_procedures = $matches[0][count($matches[0])-1];
			$special_containment_procedures = '<p>' . $special_containment_procedures;
		}

		$re = '/<p><strong>Description(:?)<\/strong>(:?)(.*)<div class="footer-wikiwalk-nav">/';
		preg_match_all($re, $res, $matches, PREG_SET_ORDER, 0);

		if($matches) {

			$description = $matches[0][count($matches[0])-1];
			$description = '<p>' . $description;

		} else {

			$re = '/<p><strong>Description:<\/strong>(.*)<\/div> <div class="page-tags">/';
			preg_match_all($re, $res, $matches, PREG_SET_ORDER, 0);

			if($matches) {

				$description = $matches[0][count($matches[0])-1];
				$description = '<p>' . $description;

			} else {

				$re = '/<p><strong>Description<\/strong>:(.*)<\/div> <div class="page-tags">/';
				preg_match_all($re, $res, $matches, PREG_SET_ORDER, 0);

				if($matches) {

					$description = $matches[0][count($matches[0])-1];
					$description = '<p>' . $description;
				}
			}
		}

		#--------------------------------------------------------

		$scp = SCPs::getByItemNumber($id);

		$scp->object_class = $object_class;
		$scp->special_containment_procedures = $special_containment_procedures;
		$scp->description = $description;

		$scp->save();

		if($featured_image) {

			$scp->updateMeta('image', $id . '.jpg');
		}

		if(isset($esoteric_object_class)) {

			$scp->updateMeta('esoteric_object_class', $esoteric_object_class);
		}

		if($featured_image_caption) {

			$scp->updateMeta('image_caption', $featured_image_caption->plaintext);
		}

		#--------------------------------------------------------

		$page_tags = $html->find('.page-tags span', 0);
		$tags = [];

		foreach($page_tags->find('a') as $tag) {

			$tag_plaintext = $tag->plaintext;

			if($tag_plaintext[0] != '_' && !in_array($tag_plaintext, ['esoteric-class'])) {

				$tags[] = $tag_plaintext;
				$tag = Tags::getBySlug($tag_plaintext);

				if(!$tag) {

					$tag_name = str_replace('-', ' ', $tag_plaintext);
					$tag_name = ucwords($tag_name);

					$tag = new Tag();
					$tag->slug = $tag_plaintext;
					$tag->name = $tag_name;
					$tag->save();
				}

				if($tag) {

					$scp_tag = Oppai::newInstance('scp', 'tag');
					$scp_tag->link($scp->id, $tag->id);
				}
			}
		}
	}

	function get_scp_images($scp) {
		global $site;

		//Create scp folder in files
		$scp_folder = $site->baseDir('/files/' . strtolower($scp->item_number));
		if (!file_exists($scp_folder)) mkdir($scp_folder, 0777, true);

		//Move featured image to folder
		$featured_image = $scp->getMeta('image');
		if($featured_image) {
			copy($site->baseDir('/temp/' . $featured_image), $scp_folder . '/' . $featured_image);
		}

		$special_containment_procedures = $scp->special_containment_procedures;
		$description = $scp->description;

		$pattern = '/<img[^>]+src="([^">]+)"/';

		preg_match_all($pattern, $special_containment_procedures, $matches);

		if(isset($matches[1]) && is_array($matches[1])) {

			foreach($matches[1] as $match) {

				$image_info = pathinfo($match);
				copy($match, $scp_folder . '/' . $image_info['basename']);
			}

			$special_containment_procedures = preg_replace_callback($pattern, function($m) {

				$image_info = pathinfo($m[1]);
				return '<img src="%baseDir%/files/scp-~~/' . $image_info['basename'] . '"';
			}, $special_containment_procedures);

			$special_containment_procedures = str_replace('scp-~~', strtolower($scp->item_number), $special_containment_procedures);

			$scp->special_containment_procedures = $special_containment_procedures;
		}

		preg_match_all($pattern, $description, $matches);

		if(isset($matches[1]) && is_array($matches[1])) {

			foreach($matches[1] as $match) {

				$image_info = pathinfo($match);
				copy($match, $scp_folder . '/' . $image_info['basename']);
			}

			$description = preg_replace_callback($pattern, function($m) {

				$image_info = pathinfo($m[1]);
				return '<img src="%baseDir%/files/scp-~~/' . $image_info['basename'] . '"';
			}, $description);

			$description = str_replace('scp-~~', strtolower($scp->item_number), $description);

			$scp->description = $description;
		}

		$scp->save();
	}

	function prepare_dirs($text) {
		global $site;
		$text = str_replace('%baseDir%', $site->baseUrl('/'), $text);
		$text = str_replace('%filesDir%', $site->getOption('files_dir', $site->baseUrl('/files/')), $text);
		return $text;
	}

	function get_excerpt( $content, $length = 40, $more = '...' ) {
		$excerpt = strip_tags( trim( $content ) );
		$words = str_word_count( $excerpt, 2 );
		if ( count( $words ) > $length ) {
			$words = array_slice( $words, 0, $length, true );
			end( $words );
			$position = key( $words ) + strlen( current( $words ) );
			$excerpt = substr( $excerpt, 0, $position ) . $more;
		}
		return $excerpt;
	}
?>