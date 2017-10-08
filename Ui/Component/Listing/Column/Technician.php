<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SalesIgniter\Maintenance\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Shows period type like daily, weekly, monthly, yearly
 * instead of period id for price by date
 */
class Technician extends Column
{

    private $_userFactory;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\User\Model\UserFactory $userFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_userFactory = $userFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
            $item['admin_id'] = $item['firstname'] . ' ' . $item['lastname'];
            }
        }

        return $dataSource;
    }
}
