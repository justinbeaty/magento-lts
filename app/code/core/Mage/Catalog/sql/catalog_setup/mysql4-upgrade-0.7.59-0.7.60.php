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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;

/* @var Mage_Catalog_Model_Resource_Eav_Mysql4_Setup $installer */

$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('catalog/product_option'), 'image_size_x', 'smallint unsigned not null after `file_extension`');
$installer->getConnection()->addColumn($installer->getTable('catalog/product_option'), 'image_size_y', 'smallint unsigned not null after `image_size_x`');
$installer->endSetup();
