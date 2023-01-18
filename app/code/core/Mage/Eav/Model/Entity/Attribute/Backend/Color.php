<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Backend model for color attribute
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Entity_Attribute_Backend_Color extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Prepare data for save
     *
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function beforeSave($object)
    {
        $attr = $this->getAttribute();
        $attrCode = $attr->getAttributeCode();

        if ($object->getData($attrCode)) {
            $regex = Varien_Data_Form_Element_Color::VALIDATION_REGEX_WITH_HASH;
            $errorMessage = 'Color must be in hexadecimal format with the hash character';

            if (!(bool)preg_match($regex, (string)$object->getData($attrCode))) {
                Mage::throwException(Mage::helper('eav')->__($errorMessage));
            }
        } else if($attr->getData('is_required')) {
            $errorMessage = '"%s" is a required value.';
            Mage::throwException(Mage::helper('eav')->__($errorMessage, $attr->getData('frontend_label')));
        }

        return parent::beforeSave($object);
    }
}
