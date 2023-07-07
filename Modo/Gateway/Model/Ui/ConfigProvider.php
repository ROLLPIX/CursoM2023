<?php

namespace Modo\Gateway\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Asset\Repository;
use Modo\Gateway\Gateway\Http\Client\ClientMock;
use Modo\Gateway\Helper\Data;
use Modo\Gateway\Model\CredentialsValidator;
use Modo\Gateway\Model\CategoryValidator;

class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'modo_gateway';

    /**
     * @var Repository
     */
    protected $_assetRepo;

    /**
     * @var Data
     */
    protected $modoHelper;

    /**
     * @var CredentialsValidator
     */
    protected $credentialsValidator;

    /**
     * @var CategoryValidator
     */
    protected $categoryValidator;
    public function __construct(
        Repository $assetRepo,
        Data $modoHelper,
        CredentialsValidator $credentialsValidator,
        CategoryValidator $categoryValidator
    )
    {
        $this->_assetRepo = $assetRepo;
        $this->modoHelper = $modoHelper;
        $this->credentialsValidator = $credentialsValidator;
        $this->categoryValidator = $categoryValidator;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'active' => $this->modoHelper->isActive() && $this->credentialsValidator->areCredentialsValid() && $this->categoryValidator->ValidateCategories(),
                    'title' => $this->modoHelper->getTitle() . ' - ' . $this->modoHelper->getDescription(),
                    'banner' => $this->_assetRepo->getUrl("Modo_Gateway::images/ModoLogo.svg"),
                    'desktop_banner' => $this->_assetRepo->getUrl("Modo_Gateway::images/EcommerceDesktop.svg"),
                    'mobile_banner' => $this->_assetRepo->getUrl("Modo_Gateway::images/EcommerceMobile.svg"),
                    'vertical_banner' => $this->_assetRepo->getUrl("Modo_Gateway::images/Banner_Vertical.png"),
                    'horizontal_banner' => $this->_assetRepo->getUrl("Modo_Gateway::images/banner-horizontal.png"),
                ]
            ]
        ];
    }
}
