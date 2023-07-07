<?php
namespace Rollpix\Elebar\Logger;

class LoggerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Logger
     */
    private $logger;

    protected function setUp(): void
    {
        $this->logger = new \Rollpix\Elebar\Logger\Logger(
            'name'
        );
    }

    public function testSetName()
    {
        $this->assertNull($this->logger->setName('name'));
    }
}