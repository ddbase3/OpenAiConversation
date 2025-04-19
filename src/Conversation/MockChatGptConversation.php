<?php

namespace OpenAiConversation\Conversation;

use Base3\Configuration\Api\IConfiguration;

class MockChatGptConversation extends SimpleChatGptConversation {

    private $mockResponse;
    private $mockRawResponse;

    public function __construct(IConfiguration $configuration, array $mockResponse = null, array $mockRawResponse = null) {
        parent::__construct($configuration);
        $this->mockResponse = $mockResponse ?? [
            'choices' => [
                ['message' => ['content' => 'This is a mock response.']]
            ]
        ];
        $this->mockRawResponse = $mockRawResponse ?? $this->mockResponse;
    }

    // Überschreibt sendRequest, um keine HTTP-Verbindung zu machen
    protected function sendRequest(array $messages, array $context = []) {
        return $this->mockRawResponse;
    }
}

