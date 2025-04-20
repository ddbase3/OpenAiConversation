<?php

namespace OpenAiConversation\Test;

use PHPUnit\Framework\TestCase;
use OpenAiConversation\OpenAiConversationPlugin;
use Base3\Api\IContainer;

class OpenAiConversationPluginTest extends TestCase
{
    public function testInitSetsServicesInContainer()
    {
        $containerMock = $this->createMock(IContainer::class);

        $plugin = new OpenAiConversationPlugin($containerMock);
        $plugin->init();

        $this->assertTrue(true);
    }

    public function testCheckDependencies()
    {
        $containerMock = $this->createMock(IContainer::class);

        $plugin = new OpenAiConversationPlugin($containerMock);
        $dependencies = $plugin->checkDependencies();

        $this->assertTrue(true);
    }
}

