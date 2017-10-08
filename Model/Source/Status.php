<?php

namespace SalesIgniter\Maintenance\Model\Source;

use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Price By Date types ids to their names (day, week, month, etc)
 */
class Status extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    protected $_optionFactory;

    protected $_itemFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $optionFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $optionFactory,
        \SalesIgniter\Maintenance\Model\StatusFactory $itemFactory
    ) {
        $this->_itemFactory = $itemFactory;
        $this->_optionFactory = $optionFactory;
    }

    /**
     * Retrieve all period types
     *
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        $statuses = $this->_itemFactory->create()->getCollection();

        foreach($statuses as $status){
            $this->_options[] = [
                'value' => $status->getId(),
                'label' => $status->getStatus()
            ];

        }
        return $this->_options;
    }

}
