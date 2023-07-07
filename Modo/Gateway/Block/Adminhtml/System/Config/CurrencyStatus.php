<?php

namespace Modo\Gateway\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;

class CurrencyStatus extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $status = 'success';
        $code = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        if($code != 'ARS'){
            $status = 'error';
            $code .= ' - Solo transacciones en ARS estan disponibles en este momento';
        }
        return '<div class="control-value"><span class="' . $status . '">' . $code . '</span></div>';
    }

    /**
     * @param AbstractElement $element
     * @param string $html
     *
     * @return string
     */
    protected function _decorateRowHtml(AbstractElement $element, $html)
    {
        return '<tr id="row_' . $element->getHtmlId() . '" class="row_payment_other_modo_validation_credentials">' . $html . '</tr>';
    }
}