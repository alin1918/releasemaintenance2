<?php

namespace SalesIgniter\Maintenance\Model;

class Ticket extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SalesIgniter\Maintenance\Model\ResourceModel\Tickets');
    }


}