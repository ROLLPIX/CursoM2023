<?php


namespace Modo\Gateway\Model\Adminhtml\System\Config;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Modo\Gateway\Helper\Data as ModoHelper;
use Modo\Gateway\Model\CredentialsValidator;
use Modo\Gateway\Service\ApiService as ModoService;

class CallbackUrl extends \Magento\Framework\App\Config\Value
{
    /**
     * @var ModoHelper
     */
    private $modoHelper;

    /**
     * @var ModoService
     */
    private $modoService;

    /**
     * @var CredentialsValidator
     */
    private $credentialsValidator;

    /**
     * @param ModoHelper $modoHelper
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        ModoHelper $modoHelper,
        ModoService $modoService,
        CredentialsValidator $credentialsValidator,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->modoHelper = $modoHelper;
        $this->modoService = $modoService;
        $this->credentialsValidator = $credentialsValidator;
        parent::__construct($context, $registry,$config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    protected function _afterLoad()
    {
        $callbackUrl = $this->modoHelper->getCallbackUrl();
        /*TODO Llamar a la validacion de callback en el merchant y registrar la correcta URL en caso de ser diff*/
        if($this->credentialsValidator->validateCallback() == CredentialsValidator::CALLBACK_NOT_EQUALS){
            $this->modoService->registerWebhook($callbackUrl);
        }
        $this->setValue($callbackUrl);
    }
}
