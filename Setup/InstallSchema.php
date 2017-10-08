<?php

namespace SalesIgniter\Maintenance\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table as DdlTable;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        $installer = $setup;
        $installer->startSetup();

        /** Install Maintenance Tickets Table */
        $tablename = $installer->getTable('simaintenance_ticket');
        $ddlTable = $installer->getConnection()->newTable($tablename);
        $ddlTable->addColumn(
            'ticket_id',
            DdlTable::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ]
        )->addColumn(
            'date_added',
            DdlTable::TYPE_DATETIME,
            null,
            ['nullable' => true]
        )->addColumn(
            'product_id',
            DdlTable::TYPE_INTEGER,
            null,
            ['nullable' => false]
        )->addColumn(
            'quantity',
            DdlTable::TYPE_INTEGER,
            255,
            ['nullable' => false]
        )->addColumn(
            'serials',
            DdlTable::TYPE_TEXT,
            null,
            ['nullable' => true]
        )->addColumn(
            'cost',
            DdlTable::TYPE_FLOAT,
            null,
            ['nullable' => true]
        )->addColumn(
            'summary',
            DdlTable::TYPE_TEXT,
            null,
            ['nullable' => true]
        )->addColumn(
            'description',
            DdlTable::TYPE_TEXT,
            '64k',
            ['nullable' => true]
        )->addColumn(
            'comments',
            DdlTable::TYPE_TEXT,
            '64k',
            ['nullable' => true]
        )->addColumn(
            'maintainer_id',
            DdlTable::TYPE_INTEGER,
            null,
            ['nullable' => true]
        )->addColumn(
            'start_date',
            DdlTable::TYPE_DATETIME,
            null,
            ['nullable' => true]
        )->addColumn(
            'end_date',
            DdlTable::TYPE_DATETIME,
            null,
            ['nullable' => true]
        )->addColumn(
            'status',
            DdlTable::TYPE_INTEGER,
            null,
            ['nullable' => false]
        )->addColumn(
            'added_from',
            DdlTable::TYPE_TEXT,
            null,
            ['nullable' => true]
        )->addColumn(
            'specific_dates',
            DdlTable::TYPE_INTEGER,
            null,
            ['nullable' => true]
        )->addIndex(
            $installer->getIdxName(
                $tablename,
                ['product_id'],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            ['product_id'],
            []
        );
        $installer->getConnection()->createTable($ddlTable);



        /** Install Maintainer_to_admin Table */
        $tablename = $installer->getTable('simaintenance_maintainer_to_admin');
        $ddlTable = $installer->getConnection()->newTable($tablename);
        $ddlTable->addColumn(
            'maintainer_id',
            DdlTable::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ]
        )->addColumn(
            'admin_id',
            DdlTable::TYPE_INTEGER,
            null,
            ['nullable' => false]
        );
        $installer->getConnection()->createTable($ddlTable);



        /** Install Auotmated Maintenance Table */
        $tablename = $installer->getTable('simaintenance_automated');
        $ddlTable = $installer->getConnection()->newTable($tablename);
        $ddlTable->addColumn(
            'automated_id',
            DdlTable::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ]
        )->addColumn(
            'product_id',
            DdlTable::TYPE_INTEGER,
            null,
            ['nullable' => false]
        )->addColumn(
            'frequency_quantity',
            DdlTable::TYPE_INTEGER,
            null,
            ['nullable' => false]
        )->addColumn(
            'frequency_type',
            DdlTable::TYPE_INTEGER,
            null,
            ['nullable' => false]
        )->addColumn(
            'template_id',
            DdlTable::TYPE_INTEGER,
            null,
            ['nullable' => false]
        )->addColumn(
            'start_date',
            DdlTable::TYPE_DATETIME,
            null,
            ['nullable' => false]
        )->addColumn(
            'maintainer_id',
            DdlTable::TYPE_INTEGER,
            null,
            ['nullable' => false]
        )->addIndex(
            $installer->getIdxName(
                $tablename,
                ['product_id'],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            ['product_id'],
            []
        );
        $installer->getConnection()->createTable($ddlTable);


        /** Install Maintenance Templates Table */
        $tablename = $installer->getTable('simaintenance_templates');
        $ddlTable = $installer->getConnection()->newTable($tablename);
        $ddlTable->addColumn(
            'template_id',
            DdlTable::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ]
        )->addColumn(
            'title',
            DdlTable::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'template',
            DdlTable::TYPE_TEXT,
            '64k',
            ['nullable' => false]
        )->addColumn(
            'store_id',
            DdlTable::TYPE_INTEGER,
            null,
            ['nullable' => true, 'default' => null]
        );
        $installer->getConnection()->createTable($ddlTable);


        /** Install Maintenance Status Table */
        $tablename = $installer->getTable('simaintenance_status');
        $ddlTable = $installer->getConnection()->newTable($tablename);
        $ddlTable->addColumn(
            'status_id',
            DdlTable::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ]
        )->addColumn(
            'status',
            DdlTable::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'status_system',
            DdlTable::TYPE_TEXT,
            null,
            ['nullable' => false]
        )->addColumn(
            'reserve_inventory',
            DdlTable::TYPE_SMALLINT,
            null,
            ['nullable' => false]
        );
        $installer->getConnection()->createTable($ddlTable);


        $installer->endSetup();
    }
}