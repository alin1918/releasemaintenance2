<?php
/**
 * Delete controller
 *
 * @package Genmato_Sample
 * @author  Vladimir Kerkhoff <support@genmato.com>
 * @created 2015-11-13
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */
namespace SalesIgniter\Maintenance\Controller\Adminhtml\Ticket;

use Magento\Backend\App\Action;

class Delete extends Action
{

    protected $itemFactory;

    protected $ResorderFactory;

    protected $SerialFactory;

    protected $stockManagement;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \SalesIgniter\Maintenance\Model\TicketFactory $itemFactory,
        \SalesIgniter\Rental\Api\StockManagementInterface $stockManagement,
        \SalesIgniter\Rental\Model\SerialNumberDetailsFactory $SerialFactory
    ) {
        parent::__construct($context);
        $this->SerialFactory = $SerialFactory;
        $this->stockManagement = $stockManagement;
        $this->itemFactory = $itemFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SalesIgniter_Maintenance::tickets');
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $param = 'ticket_id';
        $id = $this->getRequest()->getParam($param);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $ticket = $this->itemFactory->create()->load($id);
                $reservationorderid = $ticket->getReservationorderId();
                $this->itemFactory->create()->load($id)->delete();
                if($reservationorderid) {
                    $this->stockManagement->deleteReservationById($reservationorderid);
                }
                if($ticket->getSerials() !== '') {
                    $this->updateSerials($ticket);
                }
                // display success message
                $this->messageManager->addSuccess(__('The item has been deleted.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', [$param => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a listing to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

    private function updateSerials($ticket)
    {
            // update status and FK maintenance_ticket_id
            $serials = explode(',',$ticket->getSerials());
            foreach($serials as $serial) {
                $serialid = $this->SerialFactory->create()->getResource()->loadByProductIdandSerialNumber($serial,$ticket['product_id']);
                $this->SerialFactory->create()->load($serialid)->setStatus('available')->setMaintenanceTicketId('0')->save();
            }

    }
} 
