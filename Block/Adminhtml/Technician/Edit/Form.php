<?php
/**
 * Edit demo item form
 *
 * @package Genmato_Sample
 * @author  Vladimir Kerkhoff <support@genmato.com>
 * @created 2015-11-16
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */
namespace SalesIgniter\Maintenance\Block\Adminhtml\Technician\Edit;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic
{

    /**
     * @var
     */
    protected $modelDataFactory;

    protected $_technician;


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
        \SalesIgniter\Maintenance\Model\MaintainerToAdminFactory $modelDataFactory,
        \SalesIgniter\Maintenance\Model\Source\Technician $technicianSource,
        array $data = []
    ) {
        $this->_technician = $technicianSource;
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

        $technicians = $this->_technician->getAllAdmins();

        $fieldset->addField(
            'admin_id',
            'select',
            [
                'name' => 'admin_id',
                'label' => __('Technician'),
                'title' => __('Technician'),
                'required' => true,
                'values'    =>  $technicians
            ]
        );



        if ($itemData->getAutomatedId() !== null) {
            // If edit add id
            $form->addField('maintainer_id', 'hidden', ['name' => 'maintainer_id', 'value' => $itemData->getId()]);
        }

        if ($this->_backendSession->getItemData()) {
            $form->addValues($this->_backendSession->getItemData());
            $this->_backendSession->setItemData(null);
        } else {
            $form->addValues(
                [
                    'maintainer_id' => $itemData->getMaintainerId(),
                    'admin_id' => $itemData->getAdminId()
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
