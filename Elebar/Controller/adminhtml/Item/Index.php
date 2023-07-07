<?php
namespace Rollpix\Elebar\Controller\Adminhtml\Item;

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
        $resultPage->setActiveMenu('Rollpix_Elebar::item');
        $resultPage->addBreadcrumb(__('Rollpix Vendors'), __('Rollpix Vendors'));
        $resultPage->addBreadcrumb(__('Manage Rollpix Vendors'), __('Manage Rollpix Vendors'));
        $resultPage->getConfig()->getTitle()->prepend(__('Rollpix Vendors'));

        return $resultPage;
    }

    /**
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Rollpix_Elebar::gateway_item');
    }


}
