<?php

namespace SalesIgniter\Maintenance\Block\Adminhtml\Status;

use Magento\Backend\Block\Widget\Form\Container;

class Edit extends Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Current record id
     *
     * @var bool|int
     */
    protected $itemId=false;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Remove Delete button if record can't be deleted.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'status_id';
        $this->_controller = 'adminhtml_status';
        $this->_blockGroup = 'SalesIgniter_Maintenance';

        parent::_construct();

        $itemId = $this->getStatusId();
        if (!$itemId) {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve the header text, either editing an existing record or creating a new one.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $demoId = $this->getDemoId();
        if (!$demoId) {
            return __('New Item');
        } else {
            return __('Edit Item');
        }
    }

    public function getItemId()
    {
        if (!$this->itemId) {
            $this->itemId=$this->coreRegistry->registry('current_item_id');
        }
        return $this->itemId;
    }

}
