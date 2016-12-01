<?php

namespace OuterEdge\AdditionalProduct\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $select = $setup->getConnection()
            ->select()
            ->from(
                ['c' => $setup->getTable('catalog_product_link_type')]
            )
            ->where(
                "c.code='additional' AND c.link_type_id=?",
                \OuterEdge\AdditionalProduct\Model\Product\Link::LINK_TYPE_ADDITIONAL
            );
        $result = $setup->getConnection()->fetchAll($select);

        if (!$result) {
            $data = [
                [
                    'link_type_id' => \OuterEdge\AdditionalProduct\Model\Product\Link::LINK_TYPE_ADDITIONAL,
                    'code' => 'additional',
                ]
            ];

            $setup->getConnection()->insertMultiple($setup->getTable('catalog_product_link_type'), $data);

            /**
            * install product link attributes
            */
            $data = [
                [
                   'link_type_id' => \OuterEdge\AdditionalProduct\Model\Product\Link::LINK_TYPE_ADDITIONAL,
                   'product_link_attribute_code' => 'position',
                   'data_type' => 'int',
                ],
            ];

            $setup->getConnection()
                ->insertMultiple($setup->getTable('catalog_product_link_attribute'), $data);
        }
    }
}



