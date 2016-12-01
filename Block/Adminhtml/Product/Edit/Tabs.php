<?php

namespace OuterEdge\AdditionalProduct\Block\Adminhtml\Product\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Accordion;
use Magento\Backend\Block\Widget\Tabs as WigetTabs;
use Magento\Backend\Model\Auth\Session;
use Magento\Catalog\Helper\Catalog;
use Magento\Catalog\Helper\Data;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;
use Magento\Framework\Translate\InlineInterface;

class Tabs extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs
{
    /**
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareLayout()
    {
        $product = $this->getProduct();

        if (!($setId = $product->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }

        if ($setId) {
            $tabAttributesBlock = $this->getLayout()->createBlock(
                $this->getAttributeTabBlock(),
                $this->getNameInLayout() . '_attributes_tab'
            );
            $advancedGroups = [];

            foreach ($this->getGroupCollection($setId) as $group) {
                /** @var $group \Magento\Eav\Model\Entity\Attribute\Group*/
                $attributes = $product->getAttributes($group->getId(), true);

                foreach ($attributes as $key => $attribute) {
                    $applyTo = $attribute->getApplyTo();
                    if (!$attribute->getIsVisible() || !empty($applyTo) && !in_array($product->getTypeId(), $applyTo)
                    ) {
                        unset($attributes[$key]);
                    }
                }

                if ($attributes) {
                    $tabData = [
                        'label' => __($group->getAttributeGroupName()),
                        'content' => $this->_translateHtml(
                            $tabAttributesBlock->setGroup($group)->setGroupAttributes($attributes)->toHtml()
                        ),
                        'class' => 'user-defined',
                        'group_code' => $group->getTabGroupCode() ?: self::BASIC_TAB_GROUP_CODE,
                    ];

                    if ($tabData['group_code'] === self::BASIC_TAB_GROUP_CODE) {
                        $this->addTab($group->getAttributeGroupCode(), $tabData);
                    } else {
                        $advancedGroups[$group->getAttributeGroupCode()] = $tabData;
                    }
                }
            }

            /* Don't display website tab for single mode */
            if (!$this->_storeManager->isSingleStoreMode()) {
                $this->addTab(
                    'websites',
                    [
                        'label' => __('Websites'),
                        'content' => $this->_translateHtml(
                            $this->getLayout()->createBlock(
                                'Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Websites'
                            )->toHtml()
                        ),
                        'group_code' => self::BASIC_TAB_GROUP_CODE
                    ]
                );
            }

            if (isset($advancedGroups['advanced-pricing'])) {
                $this->addTab('advanced-pricing', $advancedGroups['advanced-pricing']);
                unset($advancedGroups['advanced-pricing']);
            }

            if ($this->_moduleManager->isEnabled('Magento_CatalogInventory')
                && $this->getChildBlock('advanced-inventory')
            ) {
                $this->addTab(
                    'advanced-inventory',
                    [
                        'label' => __('Advanced Inventory'),
                        'content' => $this->_translateHtml(
                            $this->getChildHtml('advanced-inventory')
                        ),
                        'group_code' => self::ADVANCED_TAB_GROUP_CODE
                    ]
                );
            }

            /**
             * Do not change this tab id
             */
            if ($this->getChildBlock('customer_options')) {
                $this->addTab('customer_options', 'customer_options');
                $this->getChildBlock('customer_options')->setGroupCode(self::ADVANCED_TAB_GROUP_CODE);
            }

            $this->addTab(
                'related',
                [
                    'label' => __('Related Products'),
                    'url' => $this->getUrl('catalog/*/related', ['_current' => true]),
                    'class' => 'ajax',
                    'group_code' => self::ADVANCED_TAB_GROUP_CODE
                ]
            );

            $this->addTab(
                'upsell',
                [
                    'label' => __('Up-sells'),
                    'url' => $this->getUrl('catalog/*/upsell', ['_current' => true]),
                    'class' => 'ajax',
                    'group_code' => self::ADVANCED_TAB_GROUP_CODE
                ]
            );

            $this->addTab(
                'crosssell',
                [
                    'label' => __('Cross-sells'),
                    'url' => $this->getUrl('catalog/*/crosssell', ['_current' => true]),
                    'class' => 'ajax',
                    'group_code' => self::ADVANCED_TAB_GROUP_CODE
                ]
            );

             $this->addTab(
                'additional',
                [
                    'label' => __('Additional'),
                    'url' => $this->getUrl('catalog/*/additional', ['_current' => true]),
                    'class' => 'ajax',
                    'group_code' => self::ADVANCED_TAB_GROUP_CODE
                ]
            );

            if (isset($advancedGroups['design'])) {
                $this->addTab('design', $advancedGroups['design']);
                unset($advancedGroups['design']);
            }

            if ($this->getChildBlock('product-alerts')) {
                $this->addTab('product-alerts', 'product-alerts');
                $this->getChildBlock('product-alerts')->setGroupCode(self::ADVANCED_TAB_GROUP_CODE);
            }

            if (isset($advancedGroups['autosettings'])) {
                $this->addTab('autosettings', $advancedGroups['autosettings']);
                unset($advancedGroups['autosettings']);
            }

            foreach ($advancedGroups as $groupCode => $group) {
                $this->addTab($groupCode, $group);
            }
        }

        return parent::_prepareLayout();
    }

}
