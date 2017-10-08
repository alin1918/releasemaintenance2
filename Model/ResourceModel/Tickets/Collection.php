<?php

namespace SalesIgniter\Maintenance\Model\ResourceModel\Tickets;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'ticket_id';
    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SalesIgniter\Maintenance\Model\Ticket', 'SalesIgniter\Maintenance\Model\ResourceModel\Tickets');
    }

    public function joinStatusTable()
    {
        $this->getSelect()
            ->joinLeft(
                ['status' => $this->getTable('simaintenance_status')],
                "main_table.status = status.status_id",
                ['status'   =>  'status.status']
            );
        return $this;
    }
}