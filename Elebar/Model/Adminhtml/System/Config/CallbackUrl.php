<?php


namespace Rollpix\Elebar\Model\Adminhtml\System\Config;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Rollpix\Elebar\Helper\Data as RollpixHelper;
use Rollpix\Elebar\Model\CredentialsValidator;
use Rollpix\Elebar\Service\ApiService as RollpixService;

class CallbackUrl extends \Magento\Framework\App\Config\Value
{
    /**
     * @var RollpixHelper
     */
    private $elebarHelper;

    /**
     * @var RollpixService
     */
    private $elebarService;

    /**
     * @var CredentialsValidator
     */
    private $credentialsValidator;

    /**
     * @param RollpixHelper $elebarHelper
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        RollpixHelper $elebarHelper,
        RollpixService $elebarService,
        CredentialsValidator $credentialsValidator,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->elebarHelper = $elebarHelper;
        $this->elebarService = $elebarService;
        $this->credentialsValidator = $credentialsValidator;
        parent::__construct($context, $registry,$config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    protected function _afterLoad()
    {
        $callbackUrl = $this->elebarHelper->getCallbackUrl();
        /*TODO Llamar a la validacion de callback en el merchant y registrar la correcta URL en caso de ser diff*/
        if($this->credentialsValidator->validateCallback() == CredentialsValidator::CALLBACK_NOT_EQUALS){
            $this->elebarService->registerWebhook($callbackUrl);
        }
        $this->setValue($callbackUrl);
    }
}
