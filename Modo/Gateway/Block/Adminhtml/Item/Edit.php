<?php

namespace Modo\Gateway\Block\Adminhtml\Item;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry; 
use Magento\Backend\Block\Widget\Form\Container;

class Edit extends Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'item_id';
        $this->_blockGroup = 'Modo_Gateway';
        $this->_controller = 'adminhtml_Item';

        parent::_construct();

        if ($this->_isAllowedAction('Modo_Gateway::gateway_item_save')) {
            $this->buttonList->update('save', 'label', __('Guardar Vendor'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('Modo_Gateway::gateway_item_delete')) {
            $this->buttonList->update('delete', 'label', __('Borrar Vendor'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('gateway_item')->getId()) {
            return __("Editar Vendor '%1'", $this->escapeHtml($this->_coreRegistry->registry('gateway_item')->getTitle()));
        } else {
            return __('Nuevo Vendor');
        }
    }

    /**
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('gateway/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }
}
