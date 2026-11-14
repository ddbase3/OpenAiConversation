<?php declare(strict_types=1);

/***********************************************************************
 * This file is part of OpenAiConversation for BASE3 Framework.
 *
 * OpenAiConversation provides conversation implementations.
 * It supplies OpenAI-oriented chat and image conversation APIs.
 * These implementations reuse BASE3 configuration contracts.
 *
 * Developed by Daniel Dahme
 * Licensed under GPL-3.0
 * https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * https://base3.de
 ***********************************************************************/

namespace Base3\OpenAiConversation\Conversation;

use Base3\CoreFoundation\Configuration\Api\IConfiguration;

class ImageChatGptConversation extends AbstractOpenAiConversation {

	public function __construct(IConfiguration $configuration) {
		parent::__construct($configuration);
		$this->model = 'gpt-4-turbo';
		$this->options['max_tokens'] = 2000;
	}

	public function chat(array $messages, array $context = []): string {
		$response = $this->sendRequest($messages, $context);
		return $response['choices'][0]['message']['content'] ?? '';
	}

	public function raw(array $messages, array $context = []) {
		return $this->sendRequest($messages, $context);
	}
}

