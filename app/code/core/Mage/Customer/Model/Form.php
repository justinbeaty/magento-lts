<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer Form Model
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Form extends Mage_Eav_Model_Form
{
    /**
     * Current module pathname
     *
     * @var string
     */
    protected $_moduleName = 'customer';

    /**
     * Current EAV entity type code
     *
     * @var string
     */
    protected $_entityTypeCode = 'customer';

    /**
     * Get EAV Entity Form Attribute Collection for Customer
     * exclude 'created_at'
     *
     * @return Mage_Customer_Model_Resource_Form_Attribute_Collection
     */
    protected function _getFormAttributeCollection()
    {
        $collection = parent::_getFormAttributeCollection()
                    ->addFieldToFilter('ea.attribute_code', array('neq' => 'created_at'));

        $entity = $this->getEntity();
        $attributeSetId = null;

        if ($entity instanceof Mage_Customer_Model_Customer) {
            $group = Mage::getModel('customer/group')
                   ->load($entity->getGroupId());
            $attributeSetId = $group->getCustomerAttributeSetId();

        } else if ($entity instanceof Mage_Customer_Model_Address) {
            $customer = $entity->getCustomer();
            if ($customer) {
                $group = Mage::getModel('customer/group')
                       ->load($customer->getGroupId());
                $attributeSetId = $group->getCustomerAddressAttributeSetId();
            }
        }

        if (!is_null($attributeSetId) && $attributeSetId != 0) {
            $collection->filterAttributeSet($attributeSetId);
        }

        return $collection;
    }
}
