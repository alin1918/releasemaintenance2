<?php


namespace SalesIgniter\Maintenance\Block\Adminhtml\Status\Edit;

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

    protected $_yesno;


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
        \SalesIgniter\Maintenance\Model\StatusFactory $modelDataFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        array $data = []
    ) {
        $this->_yesno = $yesno;
        $this->modelDataFactory = $modelDataFactory;
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
            'status',
            'text',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true
            ]
        );

        $yesno = $this->_yesno->toOptionArray();

        $fieldset->addField(
            'reserve_inventory',
            'select',
            [
                'name' => 'reserve_inventory',
                'label' => __('Reserve Inventory'),
                'title' => __('Reserve Inventory'),
                'required' => true,
                'values' => $yesno
            ]
        );


        if ($itemData->getAutomatedId() !== null) {
            // If edit add id
            $form->addField('status_id', 'hidden', ['name' => 'status_id', 'value' => $itemData->getId()]);
        }

        if ($this->_backendSession->getItemData()) {
            $form->addValues($this->_backendSession->getItemData());
            $this->_backendSession->setItemData(null);
        } else {
            $form->addValues(
                [
                    'status_id' => $itemData->getStatusId(),
                    'status' => $itemData->getStatus(),
                    'status_system' => $itemData->getStatusSystem(),
                    'reserve_inventory' => $itemData->getReserveInventory(),
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
