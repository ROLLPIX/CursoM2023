<?php

namespace Rollpix\Elebar\Model;

use Rollpix\Elebar\Service\ApiService as RollpixService;
use Rollpix\Elebar\Helper\Data as RollpixHelper;
use Magento\Catalog\Model\ProductRepository;

class CategoryValidator
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
     * @var \Magento\Checkout\Model\Session
     */
    private $_checkoutSession;
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;

    public function __construct(
        RollpixHelper  $elebarHelper,
        RollpixService $elebarService,
        ProductRepository $productRepository,
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        $this->elebarHelper = $elebarHelper;
        $this->elebarService = $elebarService;
        $this->_checkoutSession = $checkoutSession;
        $this->_productRepository = $productRepository;
    }

    /**
     * @return int
     */
    public function ValidateCategories(): bool
    {
        if(!$this->elebarHelper->validateCategory()) return true;
        $categoryArrayelebar = $this->elebarHelper->getCategoriesEnableRollpix();
        $quoteitems = $this->_checkoutSession->getQuote()->getAllVisibleItems();
        foreach ($quoteitems as $item){
            $prod = $this->_productRepository->getById($item->getProductId());
            $categories[] = $prod->getCategoryIds();
        }
        if(isset($categoryArrayelebar)){
            $currentcategoriesRollpix = explode( ',', $categoryArrayelebar);
            foreach ($categories as $ProductHunt)
                foreach ($currentcategoriesRollpix as $cat)
                    if(!in_array(intval($cat), $ProductHunt))
                      return false;
        }
        return true;
    }
}
