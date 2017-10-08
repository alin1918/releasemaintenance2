<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SalesIgniter\Maintenance\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Shows period type like daily, weekly, monthly, yearly
 * instead of period id for price by date
 */
class Frequency extends Column
{

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
        PriceCurrencyInterface $priceFormatter,
        array $components = [],
        array $data = []
    ) {
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
                switch($item['frequency_type']) {
                    case 1:
                        $type = __('Day(s)');
                        break;
                    case 2:
                        $type = __('Week(s)');
                        break;
                    case 3:
                        $type = __('Month(s)');
                        break;
                    case 4:
                        $type = __('Year(s)');
                        break;
                }
                        $item[$this->getData('name')] = __('Every ') . $item[$this->getData('name')] . ' ' . $type;

            }
        }

        return $dataSource;
    }
}
