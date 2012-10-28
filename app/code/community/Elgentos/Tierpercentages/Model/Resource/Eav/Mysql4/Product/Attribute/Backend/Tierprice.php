<?php

class Elgentos_Tierpercentages_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Tierprice extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Tierprice
{
    public function loadPriceData($productId, $websiteId = null)
    {
        $adapter = $this->_getReadAdapter();

        $columns = array(
            'price_id'      => $this->getIdFieldName(),
            'website_id'    => 'website_id',
            'all_groups'    => 'all_groups',
            'cust_group'    => 'customer_group_id',
            'price_qty'     => 'qty',
            'price'         => 'value',
            'percentage'    => 'percentage',
        );

        $select  = $adapter->select()
            ->from($this->getMainTable(), $columns)
            ->where('entity_id=?', $productId)
            ->order('qty');

        if (!is_null($websiteId)) {
            if ($websiteId == '0') {
                $select->where('website_id = ?', $websiteId);
            } else {
                $select->where('website_id IN(?)', array(0, $websiteId));
            }
        }

        return $adapter->fetchAll($select);
    }
}