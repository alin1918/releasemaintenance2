<?php

namespace SalesIgniter\Maintenance\Block\Adminhtml\Renderer;

class MaintenancePeriod extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function __construct(
        \Magento\Backend\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        $frequency = 0;
        return $frequency;
    }
}
