<?php

namespace Modo\Gateway\Test\Unit\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Asset\Repository;
use Modo\Gateway\Gateway\Http\Client\ClientMock;
use Modo\Gateway\Helper\Data as ModoHelper;
use Modo\Gateway\Model\CredentialsValidator;
use Modo\Gateway\Model\Ui\ConfigProvider;
class ConfigProviderTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(Repository::class);

        $this->repositoryMock->method('getUrl')->willReturn('modologo.svg');
        /*$this->repositoryMock->method('getUrl')->with('Modo_Gateway::images/ModoLogo.svg')->willReturn('modologo.svg');
        $this->repositoryMock->method('getUrl')->with("Modo_Gateway::images/EcommerceDesktop.svg")->willReturn('ecommercedesktop.svg');
        $this->repositoryMock->method('getUrl')->with("Modo_Gateway::images/EcommerceMobile.svg")->willReturn('ecommercemobile.svg');
        $this->repositoryMock->method('getUrl')->with("Modo_Gateway::images/Banner_Vertical.png")->willReturn('banner-vertical.svg');
        $this->repositoryMock->method('getUrl')->with("Modo_Gateway::images/banner-horizontal.png")->willReturn('banner-horizontal.svg');*/

        $this->modoHelperMock = $this->createMock(ModoHelper::class);

        $this->modoHelperMock->method('isActive')->willReturn(true);
        $this->modoHelperMock->method('getTitle')->willReturn('MODO');
        $this->modoHelperMock->method('getDescription')->willReturn('Paga en un click con la app de MODO');


        $this->credentialsValidatorMock = $this->createMock(CredentialsValidator::class);

        $this->credentialsValidatorMock->method('areCredentialsValid')->willReturn(true);


        $this->configProvider = new ConfigProvider($this->repositoryMock,$this->modoHelperMock,$this->credentialsValidatorMock);
    }

    public function testGetConfig()
    {

        $this->assertEquals([
            'payment' => [
                ConfigProvider::CODE => [
                    'active' => true,
                    'title' => 'MODO - Paga en un click con la app de MODO',
                    'banner' => 'modologo.svg',
                    'desktop_banner' => 'modologo.svg',
                    'mobile_banner' => 'modologo.svg',
                    'vertical_banner' => 'modologo.svg',
                    'horizontal_banner' => 'modologo.svg',
                ]
            ]
        ], $this->configProvider->getConfig());
    }
}
