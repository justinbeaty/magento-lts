<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @copyright  Copyright (c) 2021 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order create newsletter block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Newsletter extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_newsletter');
    }

    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Newsletter Subscription');
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-newsletter';
    }

    protected function _toHtml()
    {
        if (! Mage::getSingleton('adminhtml/quote')->getIsOldCustomer()) {
            return parent::_toHtml();
        }
        return '';
    }
}
