<?php

namespace SalesIgniter\Maintenance\Controller\Adminhtml\Ajax;

class getSerials extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'SalesIgniter_Rental::send';

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Catalog\Model\Product\AttributeSet\SuggestedSet
     */
    protected $suggestedSet;
    /**
     * @var \SalesIgniter\Rental\Model\ResourceModel\SerialNumberDetailsCollectionFactory
     */
    private $serialDetailsFactory;
    /**
     * @var \Magento\Framework\DB\Helper
     */
    private $resourceHelper;

    protected $_publicActions = ['getserials','execute'];

    private $itemFactory;


    /** @noinspection PhpHierarchyChecksInspection */
    /**
     * @param \Magento\Backend\App\Action\Context                                            $context
     * @param \Magento\Framework\DB\Helper                                                   $resourceHelper
     * @param \SalesIgniter\Rental\Model\ResourceModel\SerialNumberDetails\CollectionFactory $serialDetailsCollectionFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory                               $resultJsonFactory
     */

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\DB\Helper $resourceHelper,
        \SalesIgniter\Rental\Model\SerialNumberDetailsFactory $serialDetailsFactory,
        \SalesIgniter\Maintenance\Model\TicketFactory $itemFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->itemFactory = $itemFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->serialDetailsFactory = $serialDetailsFactory;
        $this->resourceHelper = $resourceHelper;
    }

    protected function _isAllowed(){
        return true;
    }

    /** @noinspection PhpHierarchyChecksInspection */
    /**
     * Action for attribute set selector
     *
     * @return \Magento\Framework\Controller\Result\Json
     */

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData(
            $this->getSerials($this->getRequest()->getParam('productid'),$this->getRequest()->getParam('maintenanceTicketId'))
        );
        return $resultJson;
    }

    /**
     * Get a list of serials with status of available or
     * maintenance if it is assigned to this ticket id
     *
     * @param int    $productId
     *
     * @return array
     */
    private function getSerials($productId,$maintenanceTicketId)
    {
        $collection = $this->serialDetailsFactory->create()->getCollection();
//        $collection->addFieldToFilter(
//            'product_id',
//            ['eq' => $productId]
//        )->addFieldToFilter(
//            'status',
//            ['eq' => 'available']
//        );
        $collection->getSelect()->where('(product_id = ' . $productId . ' AND status = \'available\')  OR (status = \'maintenance\' AND maintenance_ticket_id = ' . $maintenanceTicketId . ')');

          $data = $collection->getData();
        $data = $this->addSelectedSerial($data,$maintenanceTicketId);
        return $data;
    }

    /**
     * Adds to data the info to know if the serial was previously selected on the ticket so
     * that the multi-select has it selected
     *
     * @param $data
     */
    private function addSelectedSerial($data,$maintenanceTicketId){
        $ticket = $this->itemFactory->create()->load($maintenanceTicketId);
        if($ticket->hasSerials()){
        for($i=0;$i<count($data);++$i){
                $serialsArray = explode(',',$ticket->getSerials());
                if(array_search($data[$i]['serialnumber'],$serialsArray) !== false){
                    $data[$i]['selected'] = 'yes';
                } else {
                    $data[$i]['selected'] = 'no';
                }
            }
        }
        return $data;
    }


}