<?php

class PocketButtonExtension extends Minz_Extension {
	public function init() {
		$this->registerTranslates();

		// New, watching for star activity
		$this->registerHook('entries_favorite', [$this, 'handleStar']);

		$this->registerController('pocketButton');
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
	 * whatever function is called for saving to pocket
	 */
	public function handleStar(array $starredEntries, bool $isStarred): void {
		$this->registerTranslates();

		/**
		 * For loop for each entry in starredEntries
		 * 		Build the url_array for each entry
		 * 		Call MR::forward with the url_array for each entry
		 * 		Controller addAction() should take care of the rest
		 */

		foreach ($starredEntries as $entry)
		{
			$url_array = [
				'c' => 'pocketButton',
				'a' => 'add',
				'params' => [
					'id' => $entry,
				]
			];
	
			Minz_Request::forward($url_array);

		}

	}
}
