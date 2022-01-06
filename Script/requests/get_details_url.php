<?php


 
include_once("resources/functions/external/extract_url.php");
if (isset($_POST['url'])) {
	if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $_POST["url"], $match)) {
            $data = array('status' => 400);
        } else {
			$html_title = '';
			$image_urls = array();
			$html_body = '';

			$get_url = $_POST["url"];

			$get_image = getimagesize($get_url);
			if (!empty($get_image)) {
				$image_urls[] = $get_url;
				$html_title = 'Image';
			} else {
				$get_content = file_get_html($get_url);

				foreach ($get_content->find('title') as $element) {
					@$html_title = $element->plaintext;
				}

				if (empty($html_title)) {
					$html_title = '';
				}

				@$html_body = $get_content->find("meta[name='description']", 0)->content;
				$html_body = mb_substr($html_body, 0, 250, "utf-8");
				if ($html_body === false) {
					$html_body = '';
				}
				if (empty($html_body)) {
					@$html_body = $get_content->find("meta[property='og:description']", 0)->content;
					$html_body = mb_substr($html_body, 0, 250, "utf-8");
					if ($html_body === false) {
						$html_body = '';
					}
				}
				$image_urls = array();
				@$html_image = $get_content->find("meta[property='og:image']", 0)->content;
				if (!empty($html_image)) {
					if (preg_match('/[\w\-]+\.(jpg|png|gif|jpeg)/', $html_image)) {
						$image_urls[] = $html_image;
					}
				} else {
					foreach ($get_content->find('img') as $element) {
						if (!preg_match('/blank.(.*)/i', $element->src)) {
							if (preg_match('/[\w\-]+\.(jpg|png|gif|jpeg)/', $element->src)) {
								$image_urls[] = $element->src;
							}
						}
					}
				}
			}
			

			$so['story']['html_title'] = $html_title;
			$so['story']['html_image'] = $image_urls[0];
			$so['story']['html_body'] = $html_body;
			$so['story']['html_url'] = $_POST['url'];

			$data = array(
				'status' => 200,
				'title' => $html_title,
				'image' => $image_urls[0],
				'body' => $html_body,
				'url' => $_POST['url'],
				'html' => So_GetPage('story/extra/url-embed-publisher')
			);
		}
} else {
	$data = array(
		'status' => 400
	);
}

header("Content-type: application/json");
echo json_encode($data);
exit();