<?php

namespace Rollpix\Elebar\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Asset\Repository;
use Rollpix\Elebar\Gateway\Http\Client\ClientMock;
use Rollpix\Elebar\Helper\Data;
use Rollpix\Elebar\Model\CredentialsValidator;
use Rollpix\Elebar\Model\CategoryValidator;

class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'elebar_gateway';

    /**
     * @var Repository
     */
    protected $_assetRepo;

    /**
     * @var Data
     */
    protected $elebarHelper;

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
        Data $elebarHelper,
        CredentialsValidator $credentialsValidator,
        CategoryValidator $categoryValidator
    )
    {
        $this->_assetRepo = $assetRepo;
        $this->elebarHelper = $elebarHelper;
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
                    'active' => $this->elebarHelper->isActive() && $this->credentialsValidator->areCredentialsValid() && $this->categoryValidator->ValidateCategories(),
                    'title' => $this->elebarHelper->getTitle() . ' - ' . $this->elebarHelper->getDescription(),
                    'banner' => $this->_assetRepo->getUrl("Rollpix_Elebar::images/RollpixLogo.svg"),
                    'desktop_banner' => $this->_assetRepo->getUrl("Rollpix_Elebar::images/EcommerceDesktop.svg"),
                    'mobile_banner' => $this->_assetRepo->getUrl("Rollpix_Elebar::images/EcommerceMobile.svg"),
                    'vertical_banner' => $this->_assetRepo->getUrl("Rollpix_Elebar::images/Banner_Vertical.png"),
                    'horizontal_banner' => $this->_assetRepo->getUrl("Rollpix_Elebar::images/banner-horizontal.png"),
                ]
            ]
        ];
    }
}
