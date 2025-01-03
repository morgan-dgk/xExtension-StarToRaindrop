<?php

class FreshExtension_starToRaindrop_Controller extends Minz_ActionController
{

  private $base_url = 'https://api.raindrop.io/v1/oauth/';

	public function jsVarsAction()
	{
		$extension = Minz_ExtensionManager::findExtension('StarToRaindrop');

		$this->view->stp_vars = json_encode(array(
			'keyboard_shortcut' => FreshRSS_Context::$user_conf->pocket_keyboard_shortcut,
			'i18n' => array(
				'added_article_to_pocket' => _t('ext.starToRaindrop.notifications.added_article_to_raindrop', '%s'),
				'failed_to_add_article_to_raindrop' => _t('ext.starToRaindrop.notifications.failed_to_add_article_to_raindrop', '%s'),
				'ajax_request_failed' => _t('ext.starToRaindrop.notifications.ajax_request_failed'),
				'article_not_found' => _t('ext.starToRaindrop.notifications.article_not_found'),
			)
		));

		$this->view->_layout(false);

		header('Content-Type: application/javascript; charset=utf-8');
	}

	public function indexAction()
  {

    $code = Minz_Request::paramString('code') ?: '';

    $post_data = array(
      'grant_type' => 'authorization_code',
      'code' => $code,
      'client_id' => FreshRSS_Context::$user_conf->client_id,
      'client_secret' => FreshRSS_Context::$user_conf->client_secret,
      'redirect_uri' => FreshRSS_Context::$user_conf->redirect_uri
    );

    $url = $this->base_url . 'access_token';

		$result = $this->curlPostRequest($url, $post_data);
    $url_redirect = array('c' => 'extension', 'a' => 'configure', 'params' => array('e' => 'StarToRaindrop'));

		if ($result['status'] == 200) {
			FreshRSS_Context::$user_conf->access_token = $result['response']->access_token;
			FreshRSS_Context::$user_conf->refresh_token = $result['response']->refresh_token;
      FreshRSS_Context::$user_conf->save();

			Minz_Request::good(_t('ext.starToRaindrop.notifications.authorized_success'), $url_redirect);
		} else {
			if ($result['errorCode'] == 158) {
				Minz_Request::bad(_t('ext.starToRaindrop.notifications.authorized_aborted'), $url_redirect);
			} else {
				Minz_Request::bad(_t('ext.starToRaindrop.notifications.authorized_failed', $result['errorCode']), $url_redirect);
			}
		}
	}

  public function requestAccessAction()
  {
  
    $client_id = Minz_Request::paramString('client_id') ?: '';
    $client_secret = Minz_Request::paramString('client_secret') ?: '';
    $redirect_uri = Minz_Request::paramString('redirect_uri') ?: '';
    $collection = Minz_Request::paramString('collection') ?: '';


    FreshRSS_Context::$user_conf->client_id = $client_id;
    FreshRSS_Context::$user_conf->client_secret = $client_secret;
    FreshRSS_Context::$user_conf->redirect_uri = $redirect_uri;
    FreshRSS_Context::$user_conf->collection_name = $collection;
    FreshRSS_Context::$user_conf->save();

    $target = $this->base_url . 'authorize?client_id=' . $client_id . '&redirect_uri=' . $redirect_uri;
    
    header('Location: ' . $target);
    exit();
  }

	public function revokeAccessAction()
  {
	  FreshRSS_Context::$user_conf->access_token = '';
    FreshRSS_Context::$user_conf->refresh_token = '';
    FreshRSS_Context::$user_conf->client_secret = '';
		FreshRSS_Context::$user_conf->save();

		$url_redirect = array('c' => 'extension', 'a' => 'configure', 'params' => array('e' => 'StarToRaindrop'));
		Minz_Request::forward($url_redirect);
	}

	private function curlPostRequest($url, $post_data)
	{
		$headers = array(
			'Content-Type: application/json; charset=UTF-8',
		);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
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
