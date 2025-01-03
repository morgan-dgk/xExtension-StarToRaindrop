<?php

class StarToRaindropExtension extends Minz_Extension {

  public function init() {
		$this->registerTranslates();

		$this->registerHook('entries_favorite', [$this, 'handleStar']);
		$this->registerController('starToRaindrop');
		$this->registerViews();
	}

	public function handleConfigureAction() {
		$this->registerTranslates();
		
		if (Minz_Request::isPost()) {
			$keyboard_shortcut = Minz_Request::param('keyboard_shortcut', '');
			FreshRSS_Context::$user_conf->pocket_keyboard_shortcut = $keyboard_shortcut;
			FreshRSS_Context::$user_conf->save();
		}
	}

	/**
	 * if isStarred, send each starredEntry to 
	 * addAction in the controller.
	 */
  public function handleStar(array $starredEntries, bool $isStarred): void {
    echo "handling star!";
    $this->registerTranslates();
		foreach ($starredEntries as $entry) {
			if ($isStarred){
				$this->addAction($entry);
			}
		}
	}

	public function addAction($id)
	{
		// $this->view->_layout(false);

		$entry_dao = FreshRSS_Factory::createEntryDao();
    $entry = $entry_dao->searchById($id);

    echo json_encode($entry);

		if ($entry === null) {
			echo json_encode(array('status' => 404));
			return;
		}

		$post_data = array(
			'link' => $entry->link(),
			'title' => $entry->title(),
      'created' => date('c', time())
      // 'collection' => $this->getCollectionId(FreshRSS_Context::$user_conf->collection_name)
		);

		$result = $this->postArticleToRaindrop($post_data);
		$result['response'] = array('title' => $entry->title());

		// This was causing error messages to appear when starring
		// echo json_encode($result);
	}

  private function postArticleToRaindrop($post_data)
  {
    var_dump($post_data);

    $access_token = FreshRSS_Context::$user_conf->access_token;

		$headers = array(
			'Content-Type: application/json; charset=UTF-8',
      'X-Accept: application/json',
      'Authorization: Bearer ' . $access_token
    );


		$curl = curl_init('https://api.raindrop.io/rest/v1/raindrop');
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));

		$response = curl_exec($curl);

		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$response_header = substr($response, 0, $header_size);
		$response_body = substr($response, $header_size);
		$response_headers = $this->httpHeaderToArray($response_header);

		return array(
			'response' => json_decode($response_body),
			'status' => curl_getinfo($curl, CURLINFO_HTTP_CODE),
			'errorCode' => isset($response_headers['x-error-code']) ? intval($response_headers['x-error-code']) : 0
		);
  }

  private function getCollectionId($access_token) 
  {
    $headers = array(
			'Content-Type: application/json; charset=UTF-8',
      'X-Accept: application/json',
      'Authorization: Bearer ' . $access_token
    );

    $curl = curl_init('https://api.raindrop.io/rest/v1/collections');
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, true);

    $response = curl_exec($curl);

	  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$response_header = substr($response, 0, $header_size);
		$response_body = substr($response, $header_size);
    $response_headers = $this->httpHeaderToArray($response_header);

    $data = json_decode($response_body);

    $collection_obj = array_filter($data->items, fn($collection) => strtolower($collection->title) == strtolower(FreshRSS_Context::$user_conf->collection_name));

    return json_encode($collection_obj);

  }


	private function httpHeaderToArray($header)
	{
		$headers = array();
		$headers_parts = explode("\r\n", $header);

		foreach ($headers_parts as $header_part) {
			// skip empty header parts
			if (strlen($header_part) <= 0) {
				continue;
			}

			// Filter the beginning of the header which is the basic HTTP status code
			if (strpos($header_part, ':')) {
				$header_name = substr($header_part, 0, strpos($header_part, ':'));
				$header_value = substr($header_part, strpos($header_part, ':') + 1);
				$headers[$header_name] = trim($header_value);
			}
		}

		return $headers;
	}
}
