<?php
namespace Modo\Gateway\Test\Unit\Gateway\Command;

use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Checkout\Model\Session;
use Modo\Gateway\Gateway\Command\Authorize;
use Modo\Gateway\Model\Ui\ConfigProvider;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AuthorizeTest extends \PHPUnit\Framework\TestCase
{
    public function testExecute()
    {
        $authorize = new \Modo\Gateway\Gateway\Command\Authorize();
        $this->assertNull($authorize->execute([]));
    }
}
