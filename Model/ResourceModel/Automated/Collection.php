<?php

namespace SalesIgniter\Maintenance\Model\ResourceModel\Automated;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SalesIgniter\Maintenance\Model\Automated', 'SalesIgniter\Maintenance\Model\ResourceModel\Automated');
    }
}