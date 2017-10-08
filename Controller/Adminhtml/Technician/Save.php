<?php

namespace SalesIgniter\Maintenance\Controller\Adminhtml\Technician;

use Magento\Backend\App\Action;

class Save extends Action
{

    /**
     * Demo factory
     *
     * @var \Genmato\Sample\Model\DemoFactory
     */
    private $itemFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Initialize Group Controller
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \SalesIgniter\Maintenance\Model\MaintainerToAdminFactory $itemFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->itemFactory = $itemFactory;
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SalesIgniter_Maintenance::technicians');
    }

    /**
     * Save item.
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('maintainer_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectPath = 'salesigniter_maintenance/technician';
        try {
            if ($id !== null) {
                $demoData = $this->itemFactory->create()->load((int)$id);
            } else {
                $demoData = $this->itemFactory->create();
            }
            $data = $this->getRequest()->getParams();
            $demoData->setData($data)->save();

            $this->messageManager->addSuccess(__('Saved item.'));
            $resultRedirect->setPath($redirectPath);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_getSession()->setItemData($data);

            $resultRedirect->setPath($redirectPath, ['maintainer_id' => $id]);
        }
        return $resultRedirect;
    }
} 
