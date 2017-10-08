<?php

namespace SalesIgniter\Maintenance\Model;

class Automated extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SalesIgniter\Maintenance\Model\ResourceModel\Automated');
    }

    public function getTypeById($id){
        switch ($id){
            case "1":
                return __('Day');
                break;
            case "2":
                return __('Week');
                break;
            case "3":
                return __('Month');
                break;
            case "4":
                return __('Year');
                break;
        }
    }


}