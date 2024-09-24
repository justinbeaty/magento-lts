<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

/**
 * @category   Mage
 * @package    Mage_Eav
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
        $attributeTypeCode = $attributeObject->getEntityType()->getEntityTypeCode();
        $form = $this->getForm();
        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
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
        $additionalTypes = [];

        $response = new Varien_Object();
        $response->setTypes([]);
        Mage::dispatchEvent("adminhtml_{$attributeTypeCode}_attribute_types", ['response' => $response]);
        $_disabledTypes = [];
        $_hiddenFields = [];
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

        $scopes = [
            Mage_Eav_Model_Entity_Attribute::SCOPE_STORE => Mage::helper('eav')->__('Store View'),
            Mage_Eav_Model_Entity_Attribute::SCOPE_WEBSITE => Mage::helper('eav')->__('Website'),
            Mage_Eav_Model_Entity_Attribute::SCOPE_GLOBAL => Mage::helper('eav')->__('Global'),
        ];

        if ($attributeObject->getAttributeCode() == 'status') {
            unset($scopes[Mage_Eav_Model_Entity_Attribute::SCOPE_STORE]);
        }

        $response = new Varien_Object();
        $response->setScopes($scopes);
        $response->setAttribute($attributeObject);
        Mage::dispatchEvent("adminhtml_{$attributeTypeCode}_attribute_scopes", ['response' => $response]);

        $fieldset->addField('is_global', 'select', [
            'name'  => 'is_global',
            'label' => Mage::helper('eav')->__('Scope'),
            'title' => Mage::helper('eav')->__('Scope'),
            'note'  => Mage::helper('eav')->__('Declare attribute value saving scope'),
            'values' => $response->getScopes(),
        ], 'attribute_code');

        Mage::dispatchEvent("adminhtml_{$attributeTypeCode}_attribute_edit_prepare_form", [
            'form'      => $form,
            'attribute' => $attributeObject
        ]);

        return $this;
    }
}
