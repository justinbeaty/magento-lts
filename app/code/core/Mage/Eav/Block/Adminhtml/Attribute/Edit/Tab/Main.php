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
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Eav_Block_Adminhtml_Attribute_Edit_Tab_Main extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
{
    /**
     * Adding product form elements for editing attribute
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $attributeObject = $this->getAttributeObject();
        /* @var $form Varien_Data_Form */
        $form = $this->getForm();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->getElement('base_fieldset');

        $fieldset->getElements()
            ->searchById('attribute_code')
            ->setData(
                'class',
                'validate-code-event ' . $fieldset->getElements()->searchById('attribute_code')->getData('class')
            )->setData(
                'note',
                $fieldset->getElements()->searchById('attribute_code')->getData('note')
                . Mage::helper('eav')->__('. Do not use "event" for an attribute code, it is a reserved keyword.')
            );

        $frontendInputElm = $form->getElement('frontend_input');
        $additionalTypes = array(
            array(
                'value' => 'price',
                'label' => Mage::helper('eav')->__('Price')
            ),
            array(
                'value' => 'media_image',
                'label' => Mage::helper('eav')->__('Media Image')
            )
        );
        if ($attributeObject->getFrontendInput() == 'gallery') {
            $additionalTypes[] = array(
                'value' => 'gallery',
                'label' => Mage::helper('eav')->__('Gallery')
            );
        }

        $response = new Varien_Object();
        $response->setTypes(array());
        Mage::dispatchEvent('adminhtml_category_attribute_types', array('response'=>$response));
        $_disabledTypes = array();
        $_hiddenFields = array();
        foreach ($response->getTypes() as $type) {
            $additionalTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
            if (isset($type['disabled_types'])) {
                $_disabledTypes[$type['value']] = $type['disabled_types'];
            }
        }
        Mage::register('attribute_type_hidden_fields', $_hiddenFields);
        Mage::register('attribute_type_disabled_types', $_disabledTypes);

        $frontendInputValues = array_merge($frontendInputElm->getValues(), $additionalTypes);
        $frontendInputElm->setValues($frontendInputValues);

        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $scopes = array(
            Mage_Eav_Model_Entity_Attribute::SCOPE_STORE =>Mage::helper('eav')->__('Store View'),
            Mage_Eav_Model_Entity_Attribute::SCOPE_WEBSITE =>Mage::helper('eav')->__('Website'),
            Mage_Eav_Model_Entity_Attribute::SCOPE_GLOBAL =>Mage::helper('eav')->__('Global'),
        );

        if (
            $attributeObject->getAttributeCode() == 'status'
            || $attributeObject->getAttributeCode() == 'tax_class_id'
        ) {
            unset($scopes[Mage_Eav_Model_Entity_Attribute::SCOPE_STORE]);
        }

        $fieldset->addField('is_global', 'select', array(
            'name'  => 'is_global',
            'label' => Mage::helper('eav')->__('Scope'),
            'title' => Mage::helper('eav')->__('Scope'),
            'note'  => Mage::helper('eav')->__('Declare attribute value saving scope'),
            'values'=> $scopes
        ), 'attribute_code');

        // frontend properties fieldset
        $fieldset = $form->addFieldset('front_fieldset', array('legend'=>Mage::helper('eav')->__('Frontend Properties')));

        $fieldset->addField('position', 'text', array(
            'name' => 'position',
            'label' => Mage::helper('eav')->__('Position'),
            'title' => Mage::helper('eav')->__('Position in Layered Navigation'),
            'note' => Mage::helper('eav')->__('Position of attribute in layered navigation block'),
            'class' => 'validate-digits',
        ));

        Mage::dispatchEvent("adminhtml_{$attributeObject->getEntityTypeCode()}_attribute_edit_prepare_form", array(
            'form'      => $form,
            'attribute' => $attributeObject
        ));

        return $this;
    }
}
