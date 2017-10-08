<?php

namespace SalesIgniter\Maintenance\Model\ResourceModel\Template;

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
        $this->_init('SalesIgniter\Maintenance\Model\Template', 'SalesIgniter\Maintenance\Model\ResourceModel\Template');
    }
}