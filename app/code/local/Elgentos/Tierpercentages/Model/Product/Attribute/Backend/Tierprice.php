<?php
class Elgentos_Tierpercentages_Model_Product_Attribute_Backend_Tierprice extends Mage_Catalog_Model_Product_Attribute_Backend_Tierprice
{
    public function afterSave($object)
    {
        $this->_getResource()->deleteProductPrices($object, $this->getAttribute());
        $tierPrices = $object->getData($this->getAttribute()->getName());

        if (!is_array($tierPrices)) {
            return $this;
        }

        $prices = array();
        foreach ($tierPrices as $tierPrice) {
            if (empty($tierPrice['price_qty']) || !isset($tierPrice['price']) || !empty($tierPrice['delete'])) {
                continue;
            }

            $useForAllGroups = $tierPrice['cust_group'] == Mage_Customer_Model_Group::CUST_GROUP_ALL;
            $customerGroupId = !$useForAllGroups ? $tierPrice['cust_group'] : 0;
            $priceKey = join('-', array(
                $tierPrice['website_id'],
                intval($useForAllGroups),
                $customerGroupId,
                $tierPrice['price_qty']
            ));
            //Add new column "percentage"
            $prices[$priceKey] = array(
                'website_id'        => $tierPrice['website_id'],
                'all_groups'        => intval($useForAllGroups),
                'customer_group_id' => $customerGroupId,
                'qty'               => $tierPrice['price_qty'],
                'value'             => $tierPrice['price'],
                'percentage'         => $tierPrice['percentage'],
            );
        }

        if ($this->getAttribute()->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE) {
            if ($storeId = $object->getStoreId()) {
                $websites = array(Mage::app()->getStore($storeId)->getWebsite());
            }
            else {
                $websites = Mage::app()->getWebsites();
            }

            $baseCurrency   = Mage::app()->getBaseCurrencyCode();
            $rates          = $this->_getWebsiteRates();
            foreach ($websites as $website) {
                /* @var $website Mage_Core_Model_Website */
                if (!is_array($object->getWebsiteIds()) || !in_array($website->getId(), $object->getWebsiteIds())) {
                    continue;
                }
                if ($rates[$website->getId()]['code'] != $baseCurrency) {
                    foreach ($prices as $data) {
                        $priceKey = join('-', array(
                            $website->getId(),
                            $data['all_groups'],
                            $data['customer_group_id'],
                            $data['qty']
                        ));
                        if (!isset($prices[$priceKey])) {
                            $prices[$priceKey] = $data;
                            $prices[$priceKey]['website_id'] = $website->getId();
                            $prices[$priceKey]['value'] = $data['value'] * $rates[$website->getId()]['rate'];
                        }
                    }
                }
            }
        }

        foreach ($prices as $data) {
            $this->_getResource()->insertProductPrice($object, $data);
        }

        return $this;
    }
}