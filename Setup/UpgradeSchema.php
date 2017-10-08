<?php

namespace SalesIgniter\Maintenance\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table as DdlTable;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.20160927') < 0) {
            $tableName = $setup->getTable('simaintenance_ticket'); // existing table
            $setup->getConnection()->addColumn(
                $tableName,
                'reservationorder_id',
                [
                    'type' => DdlTable::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'FK reservation order id',
                ]
            );
        }
        $setup->endSetup();

        if (version_compare($context->getVersion(), '1.0.20161220') < 0) {
            $tableName = $setup->getTable('sirental_serialnumber_details'); // existing table
            $setup->getConnection()->addColumn(
                $tableName,
                'maintenance_ticket_id',
                [
                    'type' => DdlTable::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'FK maintenance ticket id',
                ]
            );
        }
        $setup->endSetup();
    }
}
