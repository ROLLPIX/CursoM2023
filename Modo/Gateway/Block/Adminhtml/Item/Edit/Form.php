<?php

namespace Modo\Gateway\Block\Adminhtml\Item\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Store\Model\System\Store;
use Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Store $systemStore
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('gateway_form');
        $this->setTitle(__('Item Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('gateway_item');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );


        $form->setHtmlIdPrefix('post_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getItemId()) {
            $fieldset->addField('item_id', 'hidden', ['name' => 'item_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Vendor Name'), 'title' => __('Vendor Name'), 'required' => true]
        );

        $fieldset->addField(
            'cuit',
            'text',
            ['name' => 'cuit', 'label' => __('Vendor CUIT'), 'title' => __('Vendor CUIT'), 'required' => true]
        );
        
        $fieldset->addField(
            'user',
            'text',
            ['name' => 'user', 'label' => __('Username'), 'title' => __('Username'), 'required' => true]
        );
        
        $fieldset->addField(
            'password',
            'text',
            ['name' => 'password', 'label' => __('Password'), 'title' => __('Password'), 'required' => true]
        );
        
        $fieldset->addField(
            'storeID',
            'text',
            ['name' => 'storeID', 'label' => __('Store ID'), 'title' => __('Store ID'), 'required' => true]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'options' => ['1' => __('Enabled'), '0' => __('Disabled')]
            ]
        );
        if (!$model->getId()) {
            $model->setData('status', '1');
        }

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
