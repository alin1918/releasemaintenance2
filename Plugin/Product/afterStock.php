<?php
namespace SalesIgniter\Maintenance\Plugin\Product;

use SalesIgniter\Rental\Model\Product\Stock;

class afterStock
{
    /**
     * Subtracts maintenance quantity from available quantity for a product
     * 
     * @param Stock $subject
     * @param $result
     */
    
    public function afterGetAvailableQuantity(Stock $subject, $product, $startDate = '', $endDate = '', $result)
    {
        return $result - $product->getMaintenanceQuantity();
    }
}