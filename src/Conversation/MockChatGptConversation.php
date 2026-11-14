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

