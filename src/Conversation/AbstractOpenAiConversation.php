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

use Base3\CoreFoundation\Api\IConversation;
use Base3\CoreFoundation\Configuration\Api\IConfiguration;
use RuntimeException;

abstract class AbstractOpenAiConversation implements IConversation {

	protected string $apiKey = '';
	protected string $model = 'gpt-6-luna';
	protected array $options = [
		'max_completion_tokens' => 4096,
		'reasoning_effort' => 'none'
	];

	public function __construct(IConfiguration $configuration) {
		$config = $configuration->get('openai');
		if (is_array($config)) {
			$this->apiKey = trim((string) ($config['apikey'] ?? ''));
		}
	}

	public function configure(array $options): void {
		if (isset($options['model'])) {
			$this->model = (string) $options['model'];
			unset($options['model']);
		}
		$this->options = array_merge($this->options, $options);
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

	/**
	 * Creates the single request payload for the existing Chat Completions transport.
	 *
	 * @param array $messages User and system messages, optionally with image content
	 * @param array $context Request-scoped model options
	 * @return array OpenAI Chat Completions request data
	 * @throws RuntimeException When no API key has been configured
	 */
	protected function buildRequest(array $messages, array $context = []): array {
		if ($this->apiKey === '') {
			throw new RuntimeException('OpenAI API key is not configured.');
		}

		$model = $context['model'] ?? $this->model;
		$options = array_merge($this->options, $context);
		unset($options['model'], $options['messages']);

		return array_merge($options, [
			'model' => $model,
			'messages' => $messages
		]);
	}

	/**
	 * Sends an ordinary Chat Completions request with bounded connection and total timeouts.
	 *
	 * @param array $messages Messages to send
	 * @param array $context Request-scoped options
	 * @return array Decoded API response
	 * @throws RuntimeException On connection failures, HTTP errors, or malformed responses
	 */
	protected function sendRequest(array $messages, array $context = []): array {
		$payload = json_encode($this->buildRequest($messages, $context), JSON_THROW_ON_ERROR);
		$ch = curl_init('https://api.openai.com/v1/chat/completions');
		if ($ch === false) {
			throw new RuntimeException('Could not initialize OpenAI request.');
		}

		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
				'Authorization: Bearer ' . $this->apiKey
			],
			CURLOPT_POSTFIELDS => $payload,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT => 120
		]);

		$result = curl_exec($ch);
		$error = $result === false ? curl_error($ch) : '';
		$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($result === false) {
			throw new RuntimeException('OpenAI request failed: ' . $error);
		}
		if ($httpCode !== 200) {
			throw new RuntimeException($this->formatApiError($httpCode, $result));
		}

		$decoded = json_decode($result, true);
		if (!is_array($decoded) || !isset($decoded['choices']) || !is_array($decoded['choices'])) {
			throw new RuntimeException('OpenAI returned an invalid Chat Completions response.');
		}
		return $decoded;
	}

	/** Returns an HTTP error without echoing provider responses, prompts, or image data. */
	protected function formatApiError(int $httpCode, string $result): string {
		$decoded = json_decode($result, true);
		$code = is_array($decoded) ? ($decoded['error']['code'] ?? '') : '';
		$code = is_string($code) ? preg_replace('/[^a-zA-Z0-9_.-]/', '', $code) : '';
		return 'OpenAI HTTP ' . $httpCode . ($code !== '' ? ' (' . substr($code, 0, 64) . ')' : '');
	}
}
