<?php

namespace SalesIgniter\Maintenance\Block\Adminhtml\Renderer;

use Magento\User\Model\User;

class AdminName extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    protected $periodic;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        User $user,
        array $data = []
    )
    {
        $this->_user = $user;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        $this->_user->load($row->getAdminId());
       $adminUserName = $this->_user->getFirstname() . ' ' . $this->_user->getLastname();
        return $adminUserName;
    }
}
