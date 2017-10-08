<?php

namespace SalesIgniter\Maintenance\Model\ResourceModel;

class Template extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     * Get table name from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('simaintenance_templates', 'template_id');
    }
}