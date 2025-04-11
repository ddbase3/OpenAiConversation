<?php declare(strict_types=1);

namespace OpenAiConversation;

use PHPUnit\Framework\TestCase;
use Base3\Api\IContainer;
use PHPUnit\Framework\MockObject\MockObject;

class OpenAiConversationPluginTest extends TestCase
{
    /**
     * @var OpenAiConversationPlugin
     */
    private $plugin;

    /**
     * @var MockObject|IContainer
     */
    private $container;

    protected function setUp()
    {
        // Mocking the IContainer
        $this->container = $this->createMock(IContainer::class);

        // Erstellen der OpenAiConversationPlugin-Instanz und Übergabe des gemockten Containers direkt im Konstruktor
        $this->plugin = new OpenAiConversationPlugin($this->container);  // Container im Konstruktor übergeben
    }

    public function testInitRegistersPluginInContainer()
    {
        // Setze die Erwartung, dass set mit dem Namen des Plugins und dem Plugin selbst als Parameter aufgerufen wird.
        $this->container->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo($this->plugin->getName()),  // Name des Plugins
                $this->equalTo($this->plugin),             // Plugin selbst
                $this->equalTo(IContainer::SHARED)        // Erwarteter Lebenszyklus
            );

        // Rufe die init-Methode auf
        $this->plugin->init();
    }
}

