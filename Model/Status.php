<?php

namespace SalesIgniter\Maintenance\Model;

class Status extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SalesIgniter\Maintenance\Model\ResourceModel\Status');
    }


}