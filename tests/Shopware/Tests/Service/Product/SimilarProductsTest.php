<?php

namespace Shopware\Tests\Service\Product;

use Shopware\Bundle\StoreFrontBundle\Struct\Context;
use Shopware\Bundle\StoreFrontBundle\Struct\ListProduct;
use Shopware\Models\Article\Article;
use Shopware\Models\Category\Category;
use Shopware\Tests\Service\TestCase;

class SimilarProductsTest extends TestCase
{
    protected function getProduct(
        $number,
        Context $context,
        Category $category = null
    ) {
        $data = parent::getProduct($number, $context, $category);

        return $this->helper->createArticle($data);
    }

    /**
     * @param $productId
     * @param $similarProductIds
     */
    private function linkSimilarProduct($productId, $similarProductIds)
    {
        foreach ($similarProductIds as $similarProductId) {
            Shopware()->Db()->insert('s_articles_similar', array(
                'articleID' => $productId,
                'relatedarticle' => $similarProductId
            ));
        }
    }


    public function testSimilarProduct()
    {
        $context = $this->getContext();

        $number = 'testSimilarProduct';
        $article = $this->getProduct($number, $context);

        $similarNumbers = array();
        $similarProducts = array();
        for ($i=0; $i<4; $i++) {
            $similarNumber = 'SimilarProduct-' . $i;
            $similarNumbers[] = $similarNumber;
            $similarProduct = $this->getProduct($similarNumber, $context);
            $similarProducts[] = $similarProduct->getId();
        }
        $this->linkSimilarProduct($article->getId(), $similarProducts);

        $product = Shopware()->Container()->get('shopware_storefront.list_product_service')
            ->get($number, $context);

        $similarProducts = Shopware()->Container()->get('shopware_storefront.similar_products_service')
            ->get($product, $context);

        $this->assertCount(4, $similarProducts);

        /**@var $similarProduct ListProduct*/
        foreach ($similarProducts as $similarProduct) {
            $this->assertInstanceOf('\Shopware\Bundle\StoreFrontBundle\Struct\ListProduct', $similarProduct);
            $this->assertContains($similarProduct->getNumber(), $similarNumbers);
        }
    }

    public function testSimilarProductsList()
    {
        $context = $this->getContext();

        $number = 'testSimilarProductsList';
        $number2 = 'testSimilarProductsList2';

        $article = $this->getProduct($number, $context);
        $article2 = $this->getProduct($number2, $context);

        $similarNumbers = array();
        $similarProducts = array();
        for ($i=0; $i<4; $i++) {
            $similarNumber = 'SimilarProduct-' . $i;
            $similarNumbers[] = $similarNumber;
            $similarProduct = $this->getProduct($similarNumber, $context);
            $similarProducts[] = $similarProduct->getId();
        }

        $this->linkSimilarProduct($article->getId(), $similarProducts);
        $this->linkSimilarProduct($article2->getId(), $similarProducts);

        $products = Shopware()->Container()->get('shopware_storefront.list_product_service')
            ->getList(array($number, $number2), $context);

        $similarProductList = Shopware()->Container()->get('shopware_storefront.similar_products_service')
            ->getList($products, $context);

        $this->assertCount(2, $similarProductList);

        foreach ($products as $product) {
            $similarProducts = $similarProductList[$product->getNumber()];

            $this->assertCount(4, $similarProducts);

            /**@var $similarProduct ListProduct*/
            foreach ($similarProducts as $similarProduct) {
                $this->assertInstanceOf('\Shopware\Bundle\StoreFrontBundle\Struct\ListProduct', $similarProduct);
                $this->assertContains($similarProduct->getNumber(), $similarNumbers);
            }
        }
    }

    public function testSimilarProductsByCategory()
    {
        $number = __FUNCTION__;
        $context = $this->getContext();
        $category = $this->helper->createCategory();

        $this->getProduct($number, $context, $category);

        for ($i=0; $i<4; $i++) {
            $similarNumber = 'SimilarProduct-' . $i;
            $this->getProduct($similarNumber, $context, $category);
        }

        $product = Shopware()->Container()->get('shopware_storefront.list_product_service')
            ->get($number, $context);

        $similar = Shopware()->Container()->get('shopware_storefront.similar_products_service')
            ->get($product, $context);

        $this->assertCount(3, $similar);

        foreach ($similar as $similarProduct) {
            $this->assertInstanceOf(
                'Shopware\Bundle\StoreFrontBundle\Struct\ListProduct',
                $similarProduct
            );
        }
    }
}
