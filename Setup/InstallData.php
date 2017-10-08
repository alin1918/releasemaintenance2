<?php

namespace SalesIgniter\Maintenance\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as CatalogAttribute;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $statusFactory;

    public function __construct(
        \SalesIgniter\Maintenance\Model\StatusFactory $statusFactory
    )
    {
        $this->statusFactory = $statusFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        $defaultStatusArray = [
            [
                'status'=>'Awaiting Parts', // status
                'status_system'=>'Awaiting Parts', // system status
                'reserve_inventory'=>1 // reserve inventory
            ],
            [
                'status'=>'New', // status
                'status_system'=>'New', // system status
                'reserve_inventory'=>1 // reserve inventory
            ],
            [
                'status'=>'Complete', // status
                'status_system'=>'Complete', // system status
                'reserve_inventory'=>0 // reserve inventory
            ],
            [
                'status'=>'In Progress', // status
                'status_system'=>'In Progress', // system status
                'reserve_inventory'=>1 // reserve inventory
            ],
        ];

        foreach($defaultStatusArray as $defaultStatus){
            $this->statusFactory->create()->setData($defaultStatus)->save();
        }
    }
}
