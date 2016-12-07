<?php

namespace OuterEdge\AdditionalProduct\Model\Checkout\Cart;

class AddPlugin
{
    /**
     * Construct
     *
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \Magento\Bundle\Model\Option $optionModel
     * @param \Magento\Framework\Message\ManagerInterface $managerInterface
     */
    public function __construct(
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Bundle\Model\Option $optionModel,
        \Magento\Framework\Message\ManagerInterface $managerInterface
    ) {
        $this->_productModel     = $productModel;
        $this->_optionModel      = $optionModel;
        $this->_managerInterface = $managerInterface;
    }

    public function aroundAddProductsByIds(\Magento\Checkout\Model\Cart $subject, callable $proceed, $productIds)
    {
        if (!empty($productIds)) {
            foreach ($productIds as $key => $productId) {

                $productId = (int)$productId;
                if (!$productId) {
                    continue;
                }
                $product = $this->_productModel->load($productId);

                if ($product->getTypeId() === \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {

                    $options = $this->_optionModel
                        ->getResourceCollection()
                        ->setProductIdFilter($product->getId())
                        ->setPositionOrder();
                    $options->joinValues($product->getStore()->getWebsiteId());

                    $selections = $product->getTypeInstance()->getSelectionsCollection(
                        $product->getTypeInstance()->getOptionsIds($product), $product);

                    $params = [];
                    foreach ($selections as $sel) {
                        $params['bundle_option'][$sel->getOptionId()] = [$sel->getId() => $sel->getSelectionId()];
                    }

                    if ($params) {
                        try {
                            $subject->addProduct($product, $params);
                        } catch (\Exception $e) {
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
