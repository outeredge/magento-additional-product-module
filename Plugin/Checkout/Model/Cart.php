<?php

namespace OuterEdge\AdditionalProduct\Plugin\Checkout\Model;

use Magento\Checkout\Model\Cart as MagentoCart;
use Magento\Catalog\Model\Product;
use Magento\Bundle\Model\Option;
use Magento\Framework\Message\ManagerInterface;
use Magento\Catalog\Model\Product\Type;
use Exception;

class Cart
{
    /**
     * @param Product $productModel
     * @param Option $optionModel
     * @param ManagerInterface $managerInterface
     */
    public function __construct(
        Product $productModel,
        Option $optionModel,
        ManagerInterface $managerInterface
    ) {
        $this->_productModel     = $productModel;
        $this->_optionModel      = $optionModel;
        $this->_managerInterface = $managerInterface;
    }

    public function aroundAddProductsByIds(MagentoCart $subject, callable $proceed, $productIds)
    {
        if (!empty($productIds)) {
            foreach ($productIds as $key => $productId) {
                $productId = (int)$productId;
                if (!$productId) {
                    continue;
                }
                $product = $this->_productModel->load($productId);

                if ($product->getTypeId() === Type::TYPE_BUNDLE) {
                    $options = $this->_optionModel
                        ->getResourceCollection()
                        ->setProductIdFilter($product->getId())
                        ->setPositionOrder();
                    $options->joinValues($product->getStore()->getWebsiteId());

                    $selections = $product->getTypeInstance()->getSelectionsCollection(
                        $product->getTypeInstance()->getOptionsIds($product),
                        $product
                    );

                    $params = [];
                    foreach ($selections as $sel) {
                        $params['bundle_option'][$sel->getOptionId()] = [$sel->getId() => $sel->getSelectionId()];
                    }

                    if ($params) {
                        try {
                            $subject->addProduct($product, $params);
                        } catch (Exception $e) {
                            $this->_managerInterface->addError(__("We don't have as many of some products as you want."));
                        }
                    }
                    unset($productIds[$key]);
                }
            }
        }

        return $proceed($productIds);
    }
}
