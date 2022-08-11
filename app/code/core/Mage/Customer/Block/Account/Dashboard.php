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
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer dashboard block
 *
 * @method string getRefererUrl()
 * @method $this setRefererUrl(string $value)
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Block_Account_Dashboard extends Mage_Core_Block_Template
{
    protected $_subscription = null;

    /**
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    /**
     * @return string
     */
    public function getAccountUrl()
    {
        return Mage::getUrl('customer/account/edit', array('_secure'=>true));
    }

    /**
     * @return string
     */
    public function getAddressesUrl()
    {
        return Mage::getUrl('customer/address/index', array('_secure'=>true));
    }

    /**
     * @param Mage_Customer_Model_Address $address
     * @return string
     */
    public function getAddressEditUrl($address)
    {
        return Mage::getUrl('customer/address/edit', array('_secure'=>true, 'id'=>$address->getId()));
    }

    /**
     * @return string
     */
    public function getOrdersUrl()
    {
        return Mage::getUrl('customer/order/index', array('_secure'=>true));
    }

    /**
     * @return string
     */
    public function getReviewsUrl()
    {
        return Mage::getUrl('review/customer/index', array('_secure'=>true));
    }

    /**
     * @return string
     */
    public function getWishlistUrl()
    {
        return Mage::getUrl('customer/wishlist/index', array('_secure'=>true));
    }

    /**
     * @todo LTS add tags URL
     */
    public function getTagsUrl()
    {
    }

    /**
     * @return Mage_Newsletter_Model_Subscriber
     */
    public function getSubscriptionObject()
    {
        if (is_null($this->_subscription)) {
            $this->_subscription = Mage::getModel('newsletter/subscriber')->loadByCustomer($this->getCustomer());
        }

        return $this->_subscription;
    }

    /**
     * @return string
     */
    public function getManageNewsletterUrl()
    {
        return $this->getUrl('*/newsletter/manage');
    }

    /**
     * @return string
     */
    public function getSubscriptionText()
    {
        if ($this->getSubscriptionObject()->isSubscribed()) {
            return Mage::helper('customer')->__('You are currently subscribed to our newsletter.');
        }

        return Mage::helper('customer')->__('You are currently not subscribed to our newsletter.');
    }

    /**
     * @return false|Mage_Customer_Model_Address[]
     */
    public function getPrimaryAddresses()
    {
        $addresses = $this->getCustomer()->getPrimaryAddresses();
        if (empty($addresses)) {
            return false;
        }
        return $addresses;
    }

    /**
     * Get back url in account dashboard
     *
     * This method is copypasted in:
     * Mage_Wishlist_Block_Customer_Wishlist  - because of strange inheritance
     * Mage_Customer_Block_Address_Book - because of secure url
     *
     * @return string
     */
    public function getBackUrl()
    {
        // the RefererUrl must be set in appropriate controller
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/');
    }
}
