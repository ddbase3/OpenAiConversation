<?php

namespace OpenAiConversation\Test;

use Base3\Configuration\Api\IConfiguration;
use Base3\Core\AbstractOutput;

class ImageTest extends AbstractOutput {

	private $configuration;

	public function __construct(IConfiguration $configuration) {
		$this->configuration = $configuration;
	}

	public function getOutput(string $out = 'html', bool $final = false): string {

		return 'Disabled';

		$imagePath = 'https://contourz.photo/v/763611031a364a42911818f06784b03f/anna-maria-madzharova-sofia-bulgaria-2023-contourz-photography.jpg';
		$contents = file_get_contents($imagePath);
		$b64str = base64_encode($contents);

		$conversation = new \OpenAiConversation\Conversation\ImageChatGptConversation($this->configuration);

		$messages = [
			[ 'role' => 'system', 'content' => 'You are an assistant that returns detailed, well-structured HTML content. All your responses must be valid HTML using proper elements like <p>, <ul>, <strong>, <em>, etc. Do not include code blocks or markdown formatting.' ],
			[ 'role' => 'user', 'content' => [
				['type' => 'text', 'text' => 'Create a detailed description of the image, focusing on ballet, pose, dancewear, colors, lighting, and mood.'],
				['type' => 'image_url', 'image_url' => ['url' => 'data:image/jpeg;base64,' . $b64str]]
			]]
		];

		$response = $conversation->chat($messages);

		$out = '<h1>Image Test</h1>';
		$out .= '<p><img src="' . $imagePath . '" style="height:200px;"></p>';
		$out .= '<p><strong>Prompt:</strong></p>';
		$out .= '<p>' . $messages[1]['content'][0]['text'] . '</p>';
		$out .= '<p><strong>Response:</strong></p>';
		$out .= $response;

		return $out;
	}
}
