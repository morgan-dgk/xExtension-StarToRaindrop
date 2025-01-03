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

    $collection = $this->getCollectionId(FreshRSS_Context::$user_conf->collection_name);

    Minz_Log::debug("Adding article to Collection: " . json_encode($collection));

		if ($entry === null) {
			echo json_encode(array('status' => 404));
			return;
		}

		$post_data = array(
			'link' => $entry->link(),
			'title' => $entry->title(),
      'created' => date('c', time()),
      'collection' => $collection);

    $result = $this->postArticleToRaindrop($post_data);

	}

  private function postArticleToRaindrop($post_data)
  {

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

  private function getCollectionId($collection_name)
  {
    $headers = array(
      'Authorization: Bearer ' . FreshRSS_Context::$user_conf->access_token
    );

    $curl = curl_init('https://api.raindrop.io/rest/v1/collections');
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, true);

    $response = curl_exec($curl);
    $response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($response_code == 200) { 

      Minz_Log::debug('Raindrop API response: ' . json_encode($response));

	    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		  $response_body = substr($response, $header_size);

      $data = json_decode($response_body);

      $filtered_collections = array_filter($data->items, function ($collection) {
        Minz_Log::debug('Collection: '. json_encode($collection));
        return strtolower($collection->title) == strtolower(FreshRSS_Context::$user_conf->collection_name);
      });

      Minz_Log::debug('Collection found: ' . json_encode($filtered_collections));

      $collection_obj = $filtered_collections[0];

      $collection = array(
        "\$ref" => "collections",
        "\$id" => $collection_obj->_id
      );

      return $collection;

    }

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
