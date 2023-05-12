<?php

namespace Rollpix\Shipping\Block\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * @category   Rollpix
 * @package    Rollpix_Shipping
 * @author     rollpix@gmail.com
 * @website    https://www.rollpix.com
 */
class Version extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Rollpix\Shipping\Helper\Data $helper
     */
    protected $_helper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Rollpix\Shipping\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Rollpix\Shipping\Helper\Data $helper
    ) {
        $this->_helper = $helper;
        parent::__construct($context);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $extensionVersion   = $this->_helper->getExtensionVersion();
        $extensionTitle     = 'Custom Shipping';
        $versionLabel       = sprintf( '%s', $extensionVersion );
        $element->setValue($versionLabel);

        return $element->getValue();
    }
}
