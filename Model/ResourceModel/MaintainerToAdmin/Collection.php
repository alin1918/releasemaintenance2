<?php

namespace SalesIgniter\Maintenance\Model\ResourceModel\MaintainerToAdmin;

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
        $this->_init('SalesIgniter\Maintenance\Model\MaintainerToAdmin', 'SalesIgniter\Maintenance\Model\ResourceModel\MaintainerToAdmin');
    }

    public function joinAdminTable()
    {
        $this->getSelect()
            ->joinLeft(
                ['admin_user' => $this->getTable('admin_user')],
                "main_table.maintainer_id = admin_user.user_id"
            );
        return $this;
    }
}