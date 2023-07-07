<?php

namespace Modo\Gateway\Model;

use Modo\Gateway\Service\ApiService as ModoService;
use Modo\Gateway\Helper\Data as ModoHelper;
use Magento\Catalog\Model\ProductRepository;

class CategoryValidator
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
     * @var \Magento\Checkout\Model\Session
     */
    private $_checkoutSession;
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;

    public function __construct(
        ModoHelper  $modoHelper,
        ModoService $modoService,
        ProductRepository $productRepository,
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        $this->modoHelper = $modoHelper;
        $this->modoService = $modoService;
        $this->_checkoutSession = $checkoutSession;
        $this->_productRepository = $productRepository;
    }

    /**
     * @return int
     */
    public function ValidateCategories(): bool
    {
        if(!$this->modoHelper->validateCategory()) return true;
        $categoryArraymodo = $this->modoHelper->getCategoriesEnableModo();
        $quoteitems = $this->_checkoutSession->getQuote()->getAllVisibleItems();
        foreach ($quoteitems as $item){
            $prod = $this->_productRepository->getById($item->getProductId());
            $categories[] = $prod->getCategoryIds();
        }
        if(isset($categoryArraymodo)){
            $currentcategoriesModo = explode( ',', $categoryArraymodo);
            foreach ($categories as $ProductHunt)
                foreach ($currentcategoriesModo as $cat)
                    if(!in_array(intval($cat), $ProductHunt))
                      return false;
        }
        return true;
    }
}
