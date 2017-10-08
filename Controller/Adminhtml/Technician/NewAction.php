<?php

namespace SalesIgniter\Maintenance\Controller\Adminhtml\Technician;

use Magento\Backend\App\Action;

class NewAction extends Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Initialize Group Controller
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SalesIgniter_Maintenance::technicians');
    }

    /**
     * Edit item. Forward to new action.
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $itemId = $this->getRequest()->getParam('maintainer_id');
        $this->_coreRegistry->register('current_item_id', $itemId);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        if ($itemId === null) {
            $resultPage->addBreadcrumb(__('New'), __('New'));
            $resultPage->getConfig()->getTitle()->prepend(__('New'));
        } else {
            $resultPage->addBreadcrumb(__('Edit'), __('Edit'));
            $resultPage->getConfig()->getTitle()->prepend(__('Edit'));
        }

        $resultPage->getLayout()
            ->addBlock('SalesIgniter\Maintenance\Block\Adminhtml\Technician\Edit', 'maintenancetechnician', 'content')
            ->setEditMode((bool)$itemId);

        return $resultPage;
    }
} 
