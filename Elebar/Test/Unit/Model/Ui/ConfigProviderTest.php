<?php

namespace Rollpix\Elebar\Test\Unit\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Asset\Repository;
use Rollpix\Elebar\Gateway\Http\Client\ClientMock;
use Rollpix\Elebar\Helper\Data as RollpixHelper;
use Rollpix\Elebar\Model\CredentialsValidator;
use Rollpix\Elebar\Model\Ui\ConfigProvider;
class ConfigProviderTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(Repository::class);

        $this->repositoryMock->method('getUrl')->willReturn('elebarlogo.svg');
        /*$this->repositoryMock->method('getUrl')->with('Rollpix_Elebar::images/RollpixLogo.svg')->willReturn('elebarlogo.svg');
        $this->repositoryMock->method('getUrl')->with("Rollpix_Elebar::images/EcommerceDesktop.svg")->willReturn('ecommercedesktop.svg');
        $this->repositoryMock->method('getUrl')->with("Rollpix_Elebar::images/EcommerceMobile.svg")->willReturn('ecommercemobile.svg');
        $this->repositoryMock->method('getUrl')->with("Rollpix_Elebar::images/Banner_Vertical.png")->willReturn('banner-vertical.svg');
        $this->repositoryMock->method('getUrl')->with("Rollpix_Elebar::images/banner-horizontal.png")->willReturn('banner-horizontal.svg');*/

        $this->elebarHelperMock = $this->createMock(RollpixHelper::class);

        $this->elebarHelperMock->method('isActive')->willReturn(true);
        $this->elebarHelperMock->method('getTitle')->willReturn('Elebar');
        $this->elebarHelperMock->method('getDescription')->willReturn('Paga en un click con la app de Elebar');


        $this->credentialsValidatorMock = $this->createMock(CredentialsValidator::class);

        $this->credentialsValidatorMock->method('areCredentialsValid')->willReturn(true);


        $this->configProvider = new ConfigProvider($this->repositoryMock,$this->elebarHelperMock,$this->credentialsValidatorMock);
    }

    public function testGetConfig()
    {

        $this->assertEquals([
            'payment' => [
                ConfigProvider::CODE => [
                    'active' => true,
                    'title' => 'Elebar - Paga en un click con la app de Elebar',
                    'banner' => 'elebarlogo.svg',
                    'desktop_banner' => 'elebarlogo.svg',
                    'mobile_banner' => 'elebarlogo.svg',
                    'vertical_banner' => 'elebarlogo.svg',
                    'horizontal_banner' => 'elebarlogo.svg',
                ]
            ]
        ], $this->configProvider->getConfig());
    }
}
