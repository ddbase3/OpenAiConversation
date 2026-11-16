<?php declare(strict_types=1);

namespace Base3\OpenAiConversation\Test\Conversation;

use Base3\CoreFoundation\Configuration\Api\IConfiguration;
use Base3\OpenAiConversation\Conversation\ImageChatGptConversation;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use RuntimeException;

final class OpenAiRequestTest extends TestCase {

	public function testImageModelUsesSupportedChatCompletionsParameters(): void {
		$config = $this->createMock(IConfiguration::class);
		$config->method('get')->with('openai')->willReturn(['apikey' => 'test-key']);
		$conversation = new ImageChatGptConversation($config);
		$conversation->configure(['temperature' => 0.6, 'max_completion_tokens' => 4096]);

		$messages = [[
			'role' => 'user',
			'content' => [
				['type' => 'text', 'text' => 'Describe this photo.'],
				['type' => 'image_url', 'image_url' => ['url' => 'data:image/png;base64,AA==']]
			]
		]];
		$request = (new ReflectionMethod($conversation, 'buildRequest'))->invoke($conversation, $messages);

		self::assertSame('gpt-6-luna', $request['model']);
		self::assertSame('none', $request['reasoning_effort']);
		self::assertSame(4096, $request['max_completion_tokens']);
		self::assertSame($messages, $request['messages']);
		self::assertArrayNotHasKey('max_tokens', $request);
	}

	public function testMissingApiKeyFailsBeforeNetworkAccess(): void {
		$config = $this->createMock(IConfiguration::class);
		$config->method('get')->willReturn([]);
		$conversation = new ImageChatGptConversation($config);

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('API key is not configured');
		(new ReflectionMethod($conversation, 'buildRequest'))->invoke($conversation, []);
	}

	public function testProviderErrorDoesNotExposeProviderBody(): void {
		$config = $this->createMock(IConfiguration::class);
		$config->method('get')->willReturn(['apikey' => 'test-key']);
		$conversation = new ImageChatGptConversation($config);
		$response = json_encode(['error' => [
			'code' => 'model_not_found',
			'message' => 'Secret request context that must never be echoed'
		]]);

		$error = (new ReflectionMethod($conversation, 'formatApiError'))->invoke($conversation, 404, $response);
		self::assertSame('OpenAI HTTP 404 (model_not_found)', $error);
		self::assertStringNotContainsString('Secret request context', $error);
	}
}
