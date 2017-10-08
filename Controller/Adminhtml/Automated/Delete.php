<?php
/**
 * Delete controller
 *
 * @package Genmato_Sample
 * @author  Vladimir Kerkhoff <support@genmato.com>
 * @created 2015-11-13
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */
namespace SalesIgniter\Maintenance\Controller\Adminhtml\Automated;

use Magento\Backend\App\Action;

class Delete extends Action
{

    protected $itemFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \SalesIgniter\Maintenance\Model\AutomatedFactory $itemFactory
    ) {
        parent::__construct($context);
        $this->itemFactory = $itemFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $param = 'automated_id';
        $id = $this->getRequest()->getParam($param);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                $this->itemFactory->create()->load($id)->delete();
                // display success message
                $this->messageManager->addSuccess(__('The item has been deleted.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', [$param => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a listing to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
} 
