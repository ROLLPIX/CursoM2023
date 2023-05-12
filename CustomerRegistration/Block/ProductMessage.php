<?php
namespace Rollpix\CustomerRegistration\Block;

use Magento\Framework\View\Element\Template;

class ProductMessage extends Template
{
    public function getProductMessage()
    {
        return "¡Agrega al carrito y obtén un descuento!";
    }
}