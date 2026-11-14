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

namespace Base3\OpenAiConversation\Test\Conversation;

use PHPUnit\Framework\TestCase;
use Base3\OpenAiConversation\Conversation\MockChatGptConversation;
use Base3\CoreFoundation\Configuration\Api\IConfiguration;

class MockChatGptConversationTest extends TestCase
{
    public function testChatReturnsMockedResponse()
    {
        // Mock für IConfiguration
        $configMock = $this->createMock(IConfiguration::class);
        $configMock->method('get')->willReturnMap([
            ['apiKey', 'test-api-key'],
            ['model', 'gpt-4']
        ]);

        // Beispiel-Mock-Antwort
        $mockResponse = [
            'choices' => [
                ['message' => ['content' => 'This is a mock response.']]
            ]
        ];

        // MockChatGptConversation mit der Beispielantwort
        $conv = new MockChatGptConversation($configMock, $mockResponse);

        $messages = [
            ['role' => 'user', 'content' => 'Hello, mock me!']
        ];

        // Test, ob die Mock-Antwort zurückgegeben wird
        $result = $conv->chat($messages);
        $this->assertEquals('This is a mock response.', $result);
    }

    public function testChatReturnsDefaultMockResponse()
    {
        // Mock für IConfiguration
        $configMock = $this->createMock(IConfiguration::class);
        $configMock->method('get')->willReturnMap([
            ['apiKey', 'test-api-key'],
            ['model', 'gpt-4']
        ]);

        // Standard-Mock-Antwort ohne Übergabe eines eigenen Mocks
        $conv = new MockChatGptConversation($configMock);

        $messages = [
            ['role' => 'user', 'content' => 'Hello, mock me!']
        ];

        // Test, ob die Standardantwort zurückgegeben wird
        $result = $conv->chat($messages);
        $this->assertEquals('This is a mock response.', $result);
    }
}

