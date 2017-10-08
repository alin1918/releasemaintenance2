<?php

namespace SalesIgniter\Maintenance\Block\Adminhtml\Renderer;

class TemplateName extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function __construct(
        \Magento\Backend\Block\Context $context,

        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->templateFactory = $templateFactory;
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        return '';
    }
}
