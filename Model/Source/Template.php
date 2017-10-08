<?php

namespace SalesIgniter\Maintenance\Model\Source;

use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Price By Date types ids to their names (day, week, month, etc)
 */
class Template extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    protected $_optionFactory;

    protected $_templateFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $optionFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $optionFactory,
        \SalesIgniter\Maintenance\Model\TemplateFactory $templateFactory
    ) {
        $this->_templateFactory = $templateFactory;
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
        $templates = $this->_templateFactory->create()->getCollection();

        foreach($templates as $template){
            $this->_options[] = [
                'value' => $template->getTemplateId(),
                'label' => $template->getTitle(),
            ];

        }
        return $this->_options;
    }

}
