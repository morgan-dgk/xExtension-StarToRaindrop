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
		foreach ($starredEntries as $entry) {
			if ($isStarred){

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
}
