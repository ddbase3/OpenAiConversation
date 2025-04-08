<?php declare(strict_types=1);

namespace OpenAiConversation;

use Base3\Core\ServiceLocator;
use Base3Manager\Plugin\AbstractPlugin;

class OpenAiConversationPlugin extends AbstractPlugin {

	// Implementation of IPlugin

	public function init() {
		$this->servicelocator

			->set(
				$this->getName(),
				$this,
				ServiceLocator::SHARED);
	
	}
}
