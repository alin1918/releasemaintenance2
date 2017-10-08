<?php
/**
 * Edit demo item form
 *
 * @package Genmato_Sample
 * @author  Vladimir Kerkhoff <support@genmato.com>
 * @created 2015-11-16
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */
namespace SalesIgniter\Maintenance\Block\Adminhtml\Ticket\Edit;

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

    protected $_technicians;

    protected $_status;

    protected $_yesno;
    
    public $urlBuilder;


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
        \SalesIgniter\Maintenance\Model\TicketFactory $modelDataFactory,
        \SalesIgniter\Common\Model\Source\Product $productSource,
        \SalesIgniter\Common\Model\Source\TypeDayToYear $typeDayToYear,
        \SalesIgniter\Maintenance\Model\Source\Template $templates,
        \SalesIgniter\Maintenance\Model\Source\Technician $maintainer,
        \SalesIgniter\Maintenance\Model\Source\Status $status,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        \Magento\Backend\Model\UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->_yesno = $yesno;
        $this->_status = $status;
        $this->_technicians = $maintainer;
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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Ticket Information')]);


        $productOptions = $this->productSource->getAllOptions();

        $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::SHORT
        );
        $timeFormat = $this->_localeDate->getTimeFormat(
            \IntlDateFormatter::SHORT
        );

        $fieldset->addField(
            'date_added',
            'date',
            [
                'name' => 'date_added',
                'style' => 'width:38%;',
                'date_format' => 'yyyy-MM-dd',
                'time_format' => 'HH:mm',
                'label' => __('Date Added'),
                'title' => __('Date Added'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'start_date',
            'date',
            [
                'name' => 'start_date',
                'style' => 'width:38%;',
                'date_format' => 'yyyy-MM-dd',
                'time_format' => 'HH:mm',
                'label' => __('Scheduled Maintenance Start Date'),
                'title' => __('Scheduled Maintenance Start Date'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'end_date',
            'date',
            [
                'name' => 'end_date',
                'style' => 'width:38%;',
                'date_format' => 'yyyy-MM-dd',
                'time_format' => 'HH:mm',
                'label' => __('Scheduled Maintenance End Date'),
                'title' => __('Scheduled Maintenance End Date'),
                'required' => true
            ]
        );

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
            'serials',
            'multiselect',
            [
                'name' => 'serials',
                'label' => __('Serials'),
                'title' => __('Serials'),
                'required' => false
                //'values'    =>  $serials
            ]
        );

        $fieldset->addField(
            'quantity',
            'text',
            [
                'name' => 'quantity',
                'label' => __('Quantity'),
                'title' => __('Quantity'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'cost',
            'text',
            [
                'name' => 'cost',
                'label' => __('Cost'),
                'title' => __('Cost'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'summary',
            'text',
            [
                'name' => 'summary',
                'label' => __('Summary'),
                'title' => __('Summary'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'comments',
            'textarea',
            [
                'name' => 'comments',
                'label' => __('Comments'),
                'title' => __('Comments'),
                'required' => true
            ]
        );

        $maintainers = $this->_technicians->getAllOptions();

        $ajaxurl = $this->urlBuilder->getUrl('salesigniter_maintenance/ajax/getserials');

        $fieldset->addField(
            'maintainer_id',
            'select',
            [
                'name' => 'maintainer_id',
                'label' => __('Technician'),
                'title' => __('Technician'),
                'required' => true,
                'values'   => $maintainers
            ]
        );

        $fieldset->addField(
            'emailmaintainer',
            'checkboxes',
            [
                'name' => 'emailmaintainer[]',
                'label' => __('Email Maintainer?'),
                'title' => __('Email Maintainer?'),
                'required' => false,
                'values'    =>  [
                    ['value' => 'yes','label' => __('Notify Maintainer')]
                    ]
            ]
        );

        $yesno = $this->_yesno->toOptionArray();

//        $fieldset->addField(
//            'specific_dates',
//            'select',
//            [
//                'name' => 'specific_dates',
//                'label' => __('Specific Dates'),
//                'title' => __('Specific Dates'),
//                'required' => true,
//                'values' => $yesno
//            ]
//        );



        $fieldset->addField(
            'start_date_orig',
            'hidden',
            [
                'name' => 'start_date_orig',
                'required' => false
            ]
        );

        $fieldset->addField(
            'end_date_orig',
            'hidden',
            [
                'name' => 'end_date_orig',
                'required' => false
            ]
        );

        $fieldset->addField(
            'skiputc',
            'hidden',
            [
                'name' => 'skiputc',
                'required' => false
            ]
        );

        $status = $this->_status->getAllOptions();

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'values' => $status
            ]
        );

        $fieldset->addField(
            'quantity_already_reserved',
            'hidden',
            [
                'name' => 'quantity_already_reserved',
                'required' => false
            ]
        );


        $fieldset->addField(
            'product_id_orig',
            'hidden',
            [
                'name' => 'product_id_orig',
                'required' => false
            ]
        );

        $fieldset->addField(
            'ticket_id',
            'hidden',
            [
                'name' => 'ticket_id',
                'required' => false
            ]
        );


        if ($itemData->getReservationorderId() !== null) {
            // If edit add reservationorder id
            $form->addField('reservationorder_id', 'hidden', ['name' => 'reservationorder_id', 'value' => $itemData->getId()]);
        }

        if ($this->_backendSession->getItemData()) {
            $form->addValues($this->_backendSession->getItemData());
            $this->_backendSession->setItemData(null);
        } else {
            if($itemData->hasStatus() == false){
                $defaultStatus = '2';
            } else {
                $defaultStatus = $itemData->getStatus();
            }
            $form->addValues(
                [
                    'quantity_already_reserved' => $itemData->getQuantity(),
                    'skiputc' => '1',
                    'ticket_id' => $itemData->getTicketId(),
                    'date_added' => $itemData->getDateAdded(),
                    'product_id' => $itemData->getProductId(),
                    'product_id_orig' => $itemData->getProductId(),
                    'quantity' => $itemData->getQuantity(),
                    'serials' => $itemData->getSerials(),
                    'cost' => $itemData->getCost(),
                    'summary' => $itemData->getSummary(),
                    'description' => $itemData->getDescription(),
                    'comments' => $itemData->getComments(),
                    'start_date' => $itemData->getStartDate(),
                    'end_date' => $itemData->getEndDate(),
                    'start_date_orig' => $itemData->getStartDate(),
                    'end_date_orig' => $itemData->getEndDate(),
                    'maintainer_id' => $itemData->getMaintainerId(),
                    'status' => $defaultStatus,
                    'added_from' => 'admin',
                    'specific_dates' => $itemData->getSpecificDates(),
                    'reservationorder_id' => $itemData->getReservationorderId()
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
