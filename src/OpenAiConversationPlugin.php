<?php declare(strict_types=1);

namespace OpenAiConversation;

use Base3\Api\IContainer;
use Base3Manager\Plugin\AbstractPlugin;

class OpenAiConversationPlugin extends AbstractPlugin {

	// Implementation of IPlugin

	public function init() {
		$this->container

			->set(
				$this->getName(),
				$this,
				IContainer::SHARED);
	
	}
}
