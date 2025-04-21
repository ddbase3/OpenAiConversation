<?php

namespace OpenAiConversation\Conversation;

use Base3\Api\IConversation;
use Base3\Configuration\Api\IConfiguration;

abstract class AbstractOpenAiConversation implements IConversation {

	protected $apiKey;
	protected $model;
	protected $options = [];

	public function __construct(IConfiguration $configuration) {
		$cnf = $configuration->get('openaiconversation');
		if ($cnf == null) return;
		$this->apiKey = $cnf['apikey'];

		$this->model = 'gpt-4';

		$this->options = [
			'temperature' => 0.7,
			'max_tokens' => 1000
		];
	}

	public function configure(array $options): void {
		$this->options = array_merge($this->options, $options);
		if (isset($options['model'])) $this->model = $options['model'];
	}

	public function getModel(): string {
		return $this->model;
	}

	public function extractToolCall($response): ?array {
		return null;
	}

	public function isFinalResponse($response): bool {
		return true;
	}

	protected function sendRequest(array $messages, array $context = []) {

		$url = 'https://api.openai.com/v1/chat/completions';
		$model = $context['model'] ?? $this->model;

		$data = array_merge($this->options, $context, [
			'model' => $model,
			'messages' => $messages
		]);

		$headers = [
			'Content-Type: application/json',
			'Authorization: Bearer ' . $this->apiKey
		];

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			throw new \RuntimeException('OpenAI API request failed: ' . curl_error($ch));
		}

		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($httpCode !== 200) {
			throw new \RuntimeException("API request failed with status $httpCode: $result");
		}

		return json_decode($result, true);
	}
}

