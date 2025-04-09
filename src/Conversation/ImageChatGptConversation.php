<?php

namespace OpenAiConversation\Conversation;

class ImageChatGptConversation extends AbstractOpenAiConversation {

	public function __construct() {
		parent::__construct();
		$this->model = 'gpt-4-turbo';
		$this->options['max_tokens'] = 1500;
	}

	public function chat(array $messages, array $context = []): string {
		$response = $this->sendRequest($messages, $context);
		return $response['choices'][0]['message']['content'] ?? '';
	}

	public function raw(array $messages, array $context = []) {
		return $this->sendRequest($messages, $context);
	}
}

