<?php

namespace OuterEdge\AdditionalProduct\Controller\Checkout\Cart;

class AddPlugin
{
    public function beforeExecute(\Magento\Checkout\Controller\Cart $subject)
    {
        $related    = $subject->getRequest()->getParam('related_product');
        $additional = $subject->getRequest()->getParam('additional_product');

        if (!empty($additional)) {
            if (!empty($related)) {
                $additional = $related . ',' . $additional;
            }
            $subject->getRequest()->setParams(['related_product' => $additional]);
        }

        return null;
    }
}
