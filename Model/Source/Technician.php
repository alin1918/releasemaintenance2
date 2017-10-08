<?php

namespace SalesIgniter\Maintenance\Model\Source;

use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Price By Date types ids to their names (day, week, month, etc)
 */
class Technician extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    protected $_optionFactory;

    protected $_maintainerFactory;
    
    protected $userFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $optionFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $optionFactory,
        \SalesIgniter\Maintenance\Model\MaintainerToAdminFactory $maintainerFactory,
        \Magento\User\Model\UserFactory $userFactory
    ) {
        $this->userFactory = $userFactory;
        $this->_maintainerFactory = $maintainerFactory;
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
        $maintainers = $this->_maintainerFactory->create()->getCollection()->joinAdminTable();

        foreach($maintainers as $technician){
            $this->_options[] = [
                'value' => $technician->getId(),
                'label' => $technician->getFirstname() . ' ' . $technician->getLastname(),
            ];

        }
        return $this->_options;
    }
    
    public function getAllAdmins()
    {
        $users = $this->userFactory->create()->getCollection();
        foreach($users as $user){
            $this->_options[] = [
                'value' => $user->getId(),
                'label' => $user->getFirstname() . ' ' . $user->getLastname(),
            ];

        }
        return $this->_options;
    }

}
