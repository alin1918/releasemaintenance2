<?php

namespace SalesIgniter\Maintenance\Block\Adminhtml\Template\Edit;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic
{

    /**
     * @var
     */
    protected $modelDataFactory;

    protected $productSource;

    protected $_typeDayToYear;

    protected $_templates;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Genmato\Sample\Model\DemoFactory $demoDataFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \SalesIgniter\Maintenance\Model\TemplateFactory $modelDataFactory,
        \SalesIgniter\Common\Model\Source\Product $productSource,
        \SalesIgniter\Common\Model\Source\TypeDayToYear $typeDayToYear,
        \SalesIgniter\Maintenance\Model\Source\Template $templates,
        array $data = []
    ) {
        $this->_templates = $templates;
        $this->_typeDayToYear = $typeDayToYear;
        $this->modelDataFactory = $modelDataFactory;
        $this->productSource = $productSource;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form for render
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $itemId = $this->_coreRegistry->registry('current_item_id');

        if ($itemId === null) {
            $itemData = $this->modelDataFactory->create();
        } else {
            $itemData = $this->modelDataFactory->create()->load($itemId);
        }

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Basic Information')]);

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'template',
            'textarea',
            [
                'name' => 'template',
                'label' => __('Template'),
                'title' => __('Template'),
                'required' => true
            ]
        );




        if ($itemData->getAutomatedId() !== null) {
            // If edit add id
            $form->addField('template_id', 'hidden', ['name' => 'template_id', 'value' => $itemData->getId()]);
        }

        if ($this->_backendSession->getItemData()) {
            $form->addValues($this->_backendSession->getItemData());
            $this->_backendSession->setItemData(null);
        } else {
            $form->addValues(
                [
                    'template_id' => $itemData->getTemplateId(),
                    'title' => $itemData->getTitle(),
                    'template' => $itemData->getTemplate(),
                    'store_id' => $itemData->getStoreId(),
                ]
            );
        }

        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save'));
        $form->setMethod('post');
        $this->setForm($form);
    }
}
