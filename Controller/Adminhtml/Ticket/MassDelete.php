<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SalesIgniter\Maintenance\Controller\Adminhtml\Ticket;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use SalesIgniter\Maintenance\Model\ResourceModel\Tickets\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    protected $stockManagement;

    protected $ticketFactory;

    protected $SerialFactory;


    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \SalesIgniter\Rental\Api\StockManagementInterface $stockManagement,
        \SalesIgniter\Rental\Model\SerialNumberDetailsFactory $SerialFactory,
        \SalesIgniter\Maintenance\Model\TicketFactory $ticketFactory
    )
    {
        $this->SerialFactory = $SerialFactory;
        $this->ticketFactory = $ticketFactory;
        $this->stockManagement = $stockManagement;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SalesIgniter_Maintenance::tickets');
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach ($collection as $item) {
            $resid = $item->getReservationorderId();
            if($resid){
                $this->stockManagement->deleteReservationById($resid);
            }
            $this->updateSerials($item);
            $item->delete();
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Updates all serials to available for this ticket since it is being deleted
     *
     * @param $item
     */

    private function updateSerials($item){
        $this->SerialFactory->create()->getResource()->updateMaintenanceIdToAvailable($item->getId());
    }
}
