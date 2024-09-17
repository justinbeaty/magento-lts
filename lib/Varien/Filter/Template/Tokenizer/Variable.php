<?php
/**
 * Maho
 *
 * @category   Varien
 * @package    Varien_Filter
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://openmage.org)
 * @copyright  Copyright (c) 2024 Maho (https://mahocommerce.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Template constructions variables tokenizer
 *
 * @category   Varien
 * @package    Varien_Filter
 */

class Varien_Filter_Template_Tokenizer_Variable extends Varien_Filter_Template_Tokenizer_Abstract
{
    /**
     * Tokenize string and return getted variable stack path
     *
     * @return array
     */
    #[\Override]
    public function tokenize()
    {
        $actions = [];
        $parameterName = '';
        $variableSet = false;
        do {
            if ($this->isWhiteSpace()) {
                // Ignore white spaces
                continue;
            } elseif ($this->char() != '.' && $this->char() != '(') {
                // Property or method name
                $parameterName .= $this->char();
            } elseif ($this->char() == '(') {
                // Method declaration
                $methodArgs = $this->getMethodArgs();
                $actions[] = ['type' => 'method',
                                   'name' => $parameterName,
                                   'args' => $methodArgs];
                $parameterName = '';
            } elseif ($parameterName != '') {
                // Property or variable declaration
                if ($variableSet) {
                    $actions[] = ['type' => 'property',
                                       'name' => $parameterName];
                } else {
                    $variableSet = true;
                    $actions[] = ['type' => 'variable',
                                       'name' => $parameterName];
                }
                $parameterName = '';
            }
        } while ($this->next());

        if ($parameterName != '') {
            if ($variableSet) {
                $actions[] = ['type' => 'property',
                                       'name' => $parameterName];
            } else {
                $actions[] = ['type' => 'variable',
                                   'name' => $parameterName];
            }
        }

        return $actions;
    }

    /**
     * Get string value for method args
     *
     * @return string
     */
    public function getString()
    {
        $value = '';
        if ($this->isWhiteSpace()) {
            return $value;
        }
        $qouteStart = $this->isQuote();

        if ($qouteStart) {
            $breakSymbol = $this->char();
        } else {
            $breakSymbol = false;
            $value .= $this->char();
        }

        while ($this->next()) {
            if (!$breakSymbol && ($this->isWhiteSpace() || $this->char() == ',' || $this->char() == ')')) {
                $this->prev();
                break;
            } elseif ($breakSymbol && $this->char() == $breakSymbol) {
                break;
            } elseif ($this->char() == '\\') {
                $this->next();
                $value .= $this->char();
            } else {
                $value .= $this->char();
            }
        }

        return $value;
    }

    /**
     * Return true if current char is a number
     *
     * @return boolean
     */
    public function isNumeric()
    {
        return $this->char() >= '0' && $this->char() <= '9';
    }

    /**
     * Return true if current char is quote or apostrophe
     *
     * @return boolean
     */
    public function isQuote()
    {
        return $this->char() == '"' || $this->char() == "'";
    }

    /**
     * Return array of arguments for method.
     *
     * @return array
     */
    public function getMethodArgs()
    {
        $value = [];
        $numberStr = '';

        while ($this->next() && $this->char() != ')') {
            if ($this->isWhiteSpace() || $this->char() == ',') {
                continue;
            } elseif ($this->isNumeric()) {
                $value[] = $this->getNumber();
            } else {
                $value[] = $this->getString();
            }
        }

        return $value;
    }

    /**
     * Return number value for method args
     *
     * @return float
     */
    public function getNumber()
    {
        $value = $this->char();
        while (($this->isNumeric() || $this->char() == '.') && $this->next()) {
            $value .= $this->char();
        }

        if (!$this->isNumeric()) {
            $this->prev();
        }

        return (float) $value;
    }
}
