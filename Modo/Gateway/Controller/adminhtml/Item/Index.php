<?php
namespace Modo\Gateway\Controller\Adminhtml\Item;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Modo_Gateway::item');
        $resultPage->addBreadcrumb(__('Modo Vendors'), __('Modo Vendors'));
        $resultPage->addBreadcrumb(__('Manage Modo Vendors'), __('Manage Modo Vendors'));
        $resultPage->getConfig()->getTitle()->prepend(__('Modo Vendors'));

        return $resultPage;
    }

    /**
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Modo_Gateway::gateway_item');
    }


}
