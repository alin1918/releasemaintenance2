<?php

namespace SalesIgniter\Maintenance\Controller\Adminhtml\Ticket;

use Magento\Backend\App\Action;

class Save extends Action
{

    /**
     * Demo factory
     *
     * @var \Genmato\Sample\Model\DemoFactory
     */
    private $ticketFactory;

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

    protected $reservationOrdersFactory;

    protected $statusFactory;

    protected $stock;

    protected $attributeAction;

    protected $stockManagement;

    private $calendarHelper;

    private $localeResolver;

    private $serialRepo;

    private $SerialFactory;

    private $date;

    private $datetime;

    private $emailTicket;
    
    protected $productFactory;

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
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \SalesIgniter\Rental\Model\ReservationOrdersFactory $reservationOrdersFactory,
        \SalesIgniter\Maintenance\Model\TicketFactory $ticketFactory,
        \SalesIgniter\Maintenance\Model\StatusFactory $statusFactory,
        \SalesIgniter\Rental\Model\Product\Stock $stock,
        \Magento\Catalog\Model\Product\Action $attributeAction,
        \SalesIgniter\Rental\Helper\Calendar $calendarHelper,
        \SalesIgniter\Rental\Api\StockManagementInterface $stockManagement,
        \SalesIgniter\Rental\Api\SerialNumberDetailsRepositoryInterface $serialRepo,
        \SalesIgniter\Rental\Model\SerialNumberDetailsFactory $SerialFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \SalesIgniter\Maintenance\Model\EmailTicket $emailTicket,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->emailTicket = $emailTicket;
        $this->date = $date;
        $this->datetime = $datetime;
        $this->SerialFactory = $SerialFactory;
        $this->serialRepo = $serialRepo;
        $this->localeResolver = $context->getLocaleResolver();
        $this->calendarHelper = $calendarHelper;
        $this->stockManagement = $stockManagement;
        $this->attributeAction = $attributeAction;
        $this->stock = $stock;
        $this->statusFactory = $statusFactory;
        $this->reservationOrdersFactory = $reservationOrdersFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->ticketFactory = $ticketFactory;
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->productFactory = $productFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SalesIgniter_Maintenance::tickets');
    }

    /**
     * Save item.
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('ticket_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectPath = 'salesigniter_maintenance/ticket';
        try {            
            if ($id !== null) {
                $maintenanceTicket = $this->ticketFactory->create()->load((int)$id);
            } else {
                $maintenanceTicket = $this->ticketFactory->create();
            }
            $data = $this->getRequest()->getParams();
            if (isset($data['serials'])) {
                $data['serials'] = implode(',', $data['serials']);
            } else {
                $data['serials'] = '';
            }

            $prod = $this->productFactory->create()->load($data['product_id']);	            
            if ($prod) {
                if ($data['quantity'] > $prod->getSirentQuantity()) {
                    $redirectPath = 'salesigniter_maintenance/ticket/edit';
                    throw new \Magento\Framework\Exception\LocalizedException(__('Quantity is invalid'));
                }                
            }            
            
            $data['serials_shipped'] = $data['serials']; // to match reservationsorders column name
            $data['qty'] = $data['quantity']; // saveFromArray expects 'qty' as field not 'quantity'
            $data['qty_shipped'] = '';
            $data['qty_cancel'] = '';
            $data['order_type'] = 'maintenance'; // is added to reservationorders table to identify

            // check if status reserves inventory
            $status = $this->statusFactory->create()->load($data['status']);
            if ($status->getReserveInventory() == 1) {


                // check if there is an existing reservation
                if ($maintenanceTicket->getReservationorderId() != null || $maintenanceTicket->getReservationorderId() != '') {
                    // if there is - update it
                    $reservationOrder = $this->reservationOrdersFactory->create()->load($maintenanceTicket->getReservationorderId());
                    $this->stockManagement->saveReservation($reservationOrder, $data);
                } else {
                    // if there is not a reservation, add it
                    $id = $this->stockManagement->saveFromArray($data);
                    $data['reservationorder_id'] = $id->getReservationorderId();
                }
            }
            // TODO possibly if ticket is marked complete before end date, auto-update end date

            // to avoid blank ticket_id if is a new ticket which prevents saving ticket because of auto-increment
            if ($data['ticket_id'] == '') {
                unset($data['ticket_id']);
            }
            $maintenanceTicket->setData($data);
            $data['ticket_id'] = $maintenanceTicket->save()->getId();
            $this->updateSerials($data);
            if(isset($data['emailmaintainer'])){
                $this->emailTicket->send($data);
            }
            $this->messageManager->addSuccessMessage(__('Saved maintenance ticket.'));
            $resultRedirect->setPath($redirectPath);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_getSession()->setItemData($data);

            $resultRedirect->setPath($redirectPath, ['ticket_id' => $id]);
        }
        return $resultRedirect;
    }

    private function updateSerials($data)
    {
        // first set all to available for this maintenance ticket id
        $this->SerialFactory->create()->getResource()->updateMaintenanceIdToAvailable($data['ticket_id']);
        // update status and FK maintenance_ticket_id for specific serials selected
        if ($data['serials'] != '') {
            $serials = explode(',', $data['serials']);
            foreach ($serials as $serial) {
                $serialid = $this->SerialFactory->create()->getResource()->loadByProductIdandSerialNumber($serial, $data['product_id']);
                $serialItem = $this->SerialFactory->create()->load($serialid);
                $serialItem->setMaintenanceTicketId($data['ticket_id']);
                // if current date/time is within the ticket start/end time update the status also
                $timezone = new \DateTimeZone($this->date->getConfigTimezone());
                $dateNow = $this->date->date();
                $ticketStart = new \DateTime($data['start_date'], $timezone);
                $ticketEnd = new \DateTime($data['end_date'], $timezone);
                if ($dateNow >= $ticketStart && $dateNow <= $ticketEnd) {
                    $serialItem->setStatus('maintenance');
                }
                $serialItem->save();
            }
        }
    }
}
