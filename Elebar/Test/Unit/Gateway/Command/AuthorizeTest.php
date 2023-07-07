<?php
namespace Rollpix\Elebar\Test\Unit\Elebar\Command;

use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Checkout\Model\Session;
use Rollpix\Elebar\Gateway\Command\Authorize;
use Rollpix\Elebar\Model\Ui\ConfigProvider;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AuthorizeTest extends \PHPUnit\Framework\TestCase
{
    public function testExecute()
    {
        $authorize = new \Rollpix\Elebar\Gateway\Command\Authorize();
        $this->assertNull($authorize->execute([]));
    }
}
