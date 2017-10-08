<?php
namespace SalesIgniter\Maintenance\Block\Adminhtml\Ticket\Edit;

use Magento\Framework\Json\EncoderInterface as JsonEncoder,
    Magento\Backend\Block\Template\Context,
    Magento\Backend\Block\Template as BlockTemplate;

class Ticket
    extends BlockTemplate
{

    /**
     * @var JsonEncoder
     */
    protected $_jsonEncoder;

    protected $urlBuilder;



    /**
     * Content constructor.
     * @param Context $context
     * @param JsonEncoder $JsonEncoder
     * @param ProductCollection $Collection
     * @param StockStateInterface $StockState
     * @param RentalStock $RentalStock
     * @param array $data
     */
    public function __construct(
        Context $context,
        JsonEncoder $JsonEncoder,
        \Magento\Backend\Model\UrlInterface $urlBuilder,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $data
        );
        $this->urlBuilder = $urlBuilder;
        $this->_jsonEncoder = $JsonEncoder;
    }


    public function getSerialUrl()
    {
        return $this->urlBuilder->getUrl('salesigniter_maintenance/ajax/getserials',
                ['_secure' => $this->getRequest()->isSecure()]
            );
    }

    public function getUrlQty()
    {
        return $this->urlBuilder->getUrl(
            'salesigniter_rental/ajax/availableqty'
        );
    }
}
