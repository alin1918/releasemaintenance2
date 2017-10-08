<?php
/**
 * Edit demo item form
 *
 * @package Genmato_Sample
 * @author  Vladimir Kerkhoff <support@genmato.com>
 * @created 2015-11-16
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */
namespace SalesIgniter\Maintenance\Block\Adminhtml\Automated\Edit;

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
        \SalesIgniter\Maintenance\Model\AutomatedFactory $modelDataFactory,
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

        $productOptions = $this->productSource->getAllOptions();

        $fieldset->addField(
            'product_id',
            'select',
            [
                'name' => 'product_id',
                'label' => __('Product'),
                'title' => __('Product'),
                'required' => true,
                'values'    =>  $productOptions
            ]
        );

        $fieldset->addField(
            'frequency_quantity',
            'text',
            [
                'name' => 'frequency_quantity',
                'label' => __('Frequency Quantity'),
                'title' => __('Frequency Quantity'),
                'required' => true
            ]
        );

        $typeDayToYear = $this->_typeDayToYear->getAllOptions();

        $fieldset->addField(
            'frequency_type',
            'select',
            [
                'name' => 'frequency_type',
                'label' => __('Frequency Type'),
                'title' => __('Frequency Type'),
                'required' => true,
                'values' => $typeDayToYear
            ]
        );

        $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::MEDIUM
        );
        $timeFormat = $this->_localeDate->getTimeFormat(
            \IntlDateFormatter::MEDIUM
        );

        $fieldset->addField(
            'start_date',
            'date',
            [
                'name' => 'start_date',
                'style' => 'width:38%;',
                'date_format' => $dateFormat,
                'time_format' => $timeFormat,
                'label' => __('Start Date'),
                'title' => __('Start Date'),
                'required' => true
            ]
        );

        $templateNames = $this->_templates->getAllOptions();

        $fieldset->addField(
            'template_id',
            'select',
            [
                'name' => 'template_id',
                'label' => __('Template'),
                'title' => __('Template'),
                'required' => true,
                'values' => $templateNames
            ]
        );

        $fieldset->addField(
            'maintainer_id',
            'select',
            [
                'name' => 'maintainer_id',
                'label' => __('Technician'),
                'title' => __('Technician'),
                'required' => true
            ]
        );


        if ($itemData->getAutomatedId() !== null) {
            // If edit add id
            $form->addField('reservationorder_id', 'hidden', ['name' => 'automated_id', 'value' => $itemData->getId()]);
        }

        if ($this->_backendSession->getItemData()) {
            $form->addValues($this->_backendSession->getItemData());
            $this->_backendSession->setItemData(null);
        } else {
            $form->addValues(
                [
                    'automated_id' => $itemData->getReservationorderId(),
                    'product_id' => $itemData->getProductId(),
                    'start_date' => $itemData->getStartDate(),
                    'frequency_type' => $itemData->getFrequencyType(),
                    'template_id' => $itemData->getTemplateId(),
                    'maintainer_id' => $itemData->getMaintainerId(),
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
