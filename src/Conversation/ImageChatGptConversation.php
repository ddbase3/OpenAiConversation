<?php

namespace OpenAiConversation\Conversation;

use Base3\Configuration\Api\IConfiguration;

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

