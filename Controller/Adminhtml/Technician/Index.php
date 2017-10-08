<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SalesIgniter\Maintenance\Controller\Adminhtml\Technician;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action as BackendAction;

class Index extends BackendAction
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SalesIgniter_Maintenance::technicians');
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('SalesIgniter_Maintenance::technician');
        $resultPage->addBreadcrumb(__('Rental'), __('Rental'));
        $resultPage->addBreadcrumb(__('Maintenance Technician'), __('Maintenance Technician'));
        $resultPage->getConfig()->getTitle()->prepend(__('Maintenance Technician'));

        return $resultPage;
    }
}
