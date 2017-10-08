<?php

namespace SalesIgniter\Maintenance\Model\ResourceModel;

class Tickets extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     * Get table name from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('simaintenance_ticket', 'ticket_id');
    }
}