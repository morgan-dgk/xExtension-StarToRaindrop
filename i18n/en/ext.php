<?php

return array(
	'starToRaindropIO' => array(
		'configure' => array(
      'client_id' => 'Client Id',
      'client_secret' => 'Client Secret',
      'redirect_uri' => 'Redirect URI',
			'client_id_description' => '<ul class="listedNumbers">
				<li>Go to <a href="https://getpocket.com/developer/apps/" target="_blank">Pocket\'s Developer Portal</a></li>
				<li>Create an Application with at least the \'Add\' permission</li>
				<li>Enter your Consumer Key and hit "Connect to Pocket"</li>
			</ul>
      <span>Details can be found on <a href="https://github.com/huffstler/FreshRSS-StarToPocket" target="_blank">GitHub</a>!',
			'connect_to_raindrop' => 'Connect to Raindrop',
			'access_token' => 'Access Token',
			'keyboard_shortcut' => 'Keyboard shortcut',
			'extension_disabled' => 'You need to enable the extension before you can connect to Raindrop!',
			'connected_to_raindrop' => 'You are connected to Raindrop!',
			'revoke_access' => 'Disconnect from Raindrop!'
		),
		'notifications' => array(
			'added_article_to_raindrop' => 'Successfully added <i>\'%s\'</i> to Raindrop!',
			'failed_to_add_article_to_raindrop' => 'Adding article to Raindrop failed! Raindrop API error code: %s',
			'ajax_request_failed' => 'Ajax request failed!',
			'authorized_success' => 'Authorization successful!',
			'authorized_aborted' => 'Authorization aborted!',
			'authorized_failed' => 'Authorization failed! Raindrop API error code: %s',
			'request_access_failed' => 'Access request failed! Raindrop API error code: %s',
			'article_not_found' => 'Can\'t find article!',
		)
	),
);
