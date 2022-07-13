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
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml attributes block
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Eav_Block_Adminhtml_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'eav';
        $this->_controller = 'adminhtml_attribute';
        if ($entity_type = Mage::registry('entity_type')) {
            $this->_headerText = Mage::helper('eav')->__('Manage %s Attributes', Mage::helper('eav')->formatTypeCode($entity_type));
        } else {
            $this->_headerText = Mage::helper('eav')->__('Manage Attributes');
        }
        $this->_addButtonLabel = Mage::helper('eav')->__('Add New Attribute');
        parent::__construct();
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-eav-attribute';
    }

}
