<?php
use \AcceptanceTester;
class CategoryAndProductOCACest
{
//---------------------------AUTORIZATION--------------------------------------- 
     
    
    /**
     * @group a
     */
    public function Login(AcceptanceTester $I){
        InitTest::Login($I);
    }
    
    
    
//----------------Create Parent, Sub1, Sub2 Product Category--------------------
    
    
    /**
     * @group a
     */
    public function CreateParentMainCategory (AcceptanceTester $I){
    $this->CreateCategory($I,$createNameCategory = OrdersListPage::$CrtCatName1,
                            $addParentCategory = null);
    }
    
    
    
    /**
     * @group a
     */
    public function CreateFirstChildCategory (AcceptanceTester $I){
        $this->CreateCategory($I,$createNameCategory = OrdersListPage::$CrtCatName2,
                                $addParentCategory = 'Основ');
    }
    
    
    
    /**
     * @group a
     */
    public function CreateSecondChildCategory (AcceptanceTester $I){
        $this->CreateCategory($I,$createNameCategory = OrdersListPage::$CrtCatName3,
                                $addParentCategory = 'First');
    }
    
    
    
    
    
    
    
    


//-------------------------Create Products--------------------------------------
    

    
    /**
     * @group a
     */
    public function CreateProductNameMin (AcceptanceTester $I){
        $this->CreateProduct($I,$nameProduct = OrdersListPage::$CrtPrdNameMin,
                                $priceProduct = OrdersListPage::$CrtPrdPriceMin,
                                $articleProduct = NULL,
                                $amountProduct = NULL,
                                $categoryProduct = OrdersListPage::$CrtCatName1ForSearch);
    }
    
     /**
     * @group a
     */
    public function CreateProductNameMax (AcceptanceTester $I){
        $this->CreateProduct($I,$nameProduct = OrdersListPage::$CrtPrdNameMax,
                                $priceProduct = OrdersListPage::$CrtPrdPriceMin,
                                $articleProduct = NULL,    
                                $amountProduct = NULL,
                                $categoryProduct = OrdersListPage::$CrtCatName1ForSearch);
    }
    
    
        /**
     * @group a
     */
    public function CreateProductPriceMin (AcceptanceTester $I){
        $this->CreateProduct($I,$nameProduct = 'Минимальная Цена Товара',
                                $priceProduct = OrdersListPage::$CrtPrdPriceMin,
                                $articleProduct = NULL,
                                $amountProduct = NULL,
                                $categoryProduct = OrdersListPage::$CrtCatName1ForSearch);
    }
    
    
    /**
     * @group a
     */
    public function CreateProductPriceMax (AcceptanceTester $I){
        $this->CreateProduct($I,$nameProduct = 'Максимальная Цена Товара',
                                $priceProduct = OrdersListPage::$CrtPrdPriceMax,
                                $articleProduct = NULL,
                                $amountProduct = NULL,
                                $categoryProduct = OrdersListPage::$CrtCatName1ForSearch);
    }
    
    /**
     * @group a
     */
    public function CreateProductArticleMin (AcceptanceTester $I){
        $this->CreateProduct($I,$nameProduct = 'Минимальний Арикул Товара',
                                $priceProduct = OrdersListPage::$CrtPrdPriceMin,
                                $articleProduct = OrdersListPage::$CrtPrdArticleMin,
                                $amountProduct = NULL,
                                $categoryProduct = OrdersListPage::$CrtCatName1ForSearch);
    }
    
    
    /**
     * @group a
     */
    public function CreateProductArticleMax (AcceptanceTester $I){
        $this->CreateProduct($I,$nameProduct = 'Максимальний Артикул Товара',
                                $priceProduct = OrdersListPage::$CrtPrdPriceMin,
                                $articleProduct = OrdersListPage::$CrtPrdArticleMax,
                                $amountProduct = NULL,
                                $categoryProduct = OrdersListPage::$CrtCatName2ForSearch);
    }

    
    /**
     * @group a
     */
    public function CreateProductAmountMin (AcceptanceTester $I){
        $this->CreateProduct($I,$nameProduct = 'Минимальное Количество Товара',
                                $priceProduct = OrdersListPage::$CrtPrdPriceMin,
                                $articleProduct = NULL,
                                $amountProduct = OrdersListPage::$CrtVarAmountMin,
                                $categoryProduct = OrdersListPage::$CrtCatName2ForSearch);
    }
    
    
    /**
     * @group a
     */
    public function CeateProductAmountMax (AcceptanceTester $I){
        $this->CreateProduct($I,$nameProduct = 'Максимальное Количество Товара',
                                $priceProduct = OrdersListPage::$CrtPrdPriceMin,
                                $articleProduct = NULL,
                                $amountProduct = OrdersListPage::$CrtVarAmountMax,
                                $categoryProduct = OrdersListPage::$CrtCatName2ForSearch);
    }
    
    /**
     * @group a
     */
    public function CreateVariantNameMin (AcceptanceTester $I){
        $this->CreateProduct($I,$nameProduct = 'Минимальное Название Варианта ',
                                $priceProduct = OrdersListPage::$CrtPrdPriceMin,
                                $articleProduct = NULL,
                                $amountProduct = NULL,
                                $categoryProduct = OrdersListPage::$CrtCatName2ForSearch,
                                $variantProduct = OrdersListPage::$CrtVarNameMin,
                                $variantPrice = OrdersListPage::$CrtVarPriceMin);
    }
    
    
    /**
     * @group a
     */
    public function CreateVariantNameMax (AcceptanceTester $I){
        $this->CreateProduct($I,$nameProduct = 'Максимальное Название Варианта',
                                $priceProduct = OrdersListPage::$CrtVarPriceMin,
                                $articleProduct = NULL,
                                $amountProduct = NULL,
                                $categoryProduct = OrdersListPage::$CrtCatName2ForSearch,
                                $variantProduct = OrdersListPage::$CrtVarNameMax,
                                $variantPrice = OrdersListPage::$CrtVarPriceMin);
    }
    
    
    /**
     * @group a
     */
    public function CreateVariantPriceMin (AcceptanceTester $I){
        $this->CreateProduct($I,$nameProduct = 'Минимальная Цена Варианта',
                                $priceProduct = OrdersListPage::$CrtVarPriceMin,
                                $articleProduct = NULL,
                                $amountProduct = NULL,
                                $categoryProduct = OrdersListPage::$CrtCatName3ForSearch,
                                $variantProduct = OrdersListPage::$CrtVarNameMin,
                                $variantPrice = OrdersListPage::$CrtVarPriceMin);
    }
    
    
    /**
     * @group a
     */
    public function CreateVariantPriceMax (AcceptanceTester $I){
        $this->CreateProduct($I,$nameProduct = 'Максимальная Цена Варианта',
                                $priceProduct = OrdersListPage::$CrtVarPriceMin,
                                $articleProduct = NULL,
                                $amountProduct = NULL,
                                $categoryProduct = OrdersListPage::$CrtCatName3ForSearch,
                                $variantProduct = OrdersListPage::$CrtVarNameMin,
                                $variantPrice = OrdersListPage::$CrtVarPriceMax);
    }
    
    /**
     * @group a
     */
    public function CreateVariantArticleMin (AcceptanceTester $I){
        $this->CreateProduct($I,$nameProduct = 'Минимальний Артикул Варианта',
                                $priceProduct = OrdersListPage::$CrtVarPriceMin,
                                $articleProduct = NULL,
                                $amountProduct = NULL,
                                $categoryProduct = OrdersListPage::$CrtCatName3ForSearch,
                                $variantProduct = OrdersListPage::$CrtVarNameMin,
                                $variantPrice = OrdersListPage::$CrtVarPriceMin,
                                $variantArticle = OrdersListPage::$CrtVarArticleMin);
    }
    
    
    /**
     * @group a
     */
    public function CreateVariantArticleMax (AcceptanceTester $I){
        $this->CreateProduct($I,$nameProduct = 'Максимальний Артикул Варианта',
                                $priceProduct = OrdersListPage::$CrtVarPriceMin,
                                $articleProduct = NULL,
                                $amountProduct = NULL,
                                $categoryProduct = OrdersListPage::$CrtCatName3ForSearch,
                                $variantProduct = OrdersListPage::$CrtVarNameMin,
                                $variantPrice = OrdersListPage::$CrtVarPriceMin,
                                $variantArticle = OrdersListPage::$CrtVarArticleMax);
    }
    
    
    /**
     * @group a
     */
    public function CreateVariantAmountMin (AcceptanceTester $I){
        $this->CreateProduct($I,$nameProduct = 'Минимальное Количество Варианта',
                                $priceProduct = OrdersListPage::$CrtVarPriceMin,
                                $articleProduct = NULL,
                                $amountProduct = NULL,
                                $categoryProduct = OrdersListPage::$CrtCatName3ForSearch,
                                $variantProduct = OrdersListPage::$CrtVarNameMin,
                                $variantPrice = OrdersListPage::$CrtVarPriceMin,
                                $variantArticle = NULL,
                                $variantAmount = OrdersListPage::$CrtVarAmountMin);
    }
    
    
    
    /**
     * @group a
     */
    public function CreateVariantAmountMax (AcceptanceTester $I){
        $this->CreateProduct($I,$nameProduct = 'Максимальное Количество Варианта',
                                $priceProduct = OrdersListPage::$CrtVarPriceMin,
                                $articleProduct = NULL,
                                $amountProduct = NULL,
                                $categoryProduct = OrdersListPage::$CrtCatName3ForSearch,
                                $variantProduct = OrdersListPage::$CrtVarNameMin,
                                $variantPrice = OrdersListPage::$CrtVarPriceMin,
                                $variantArticle = NULL,
                                $variantAmount = OrdersListPage::$CrtVarAmountMax);
    }
    












//---------------------------Protected Function---------------------------------
    
    
    
    
    
    protected function CreateCategory (AcceptanceTester $I, $createNameCategory = null,
                                                            $addParentCategory = null){
        $I->amOnPage(OrdersListPage::$CrtCategoryPageURL);                                                                                                                                     
        if(isset($createNameCategory)){
            $I->fillField(OrdersListPage::$CrtCategoryFieldName, $createNameCategory);                                                                                              
        }if(isset($addParentCategory)){            
            $I->click(OrdersListPage::$CrtCategorySelectMenu);                                                                                                                        
            $I->fillField(OrdersListPage::$CrtCategorySelectMenuInput,$addParentCategory);                                                                                              
            $I->click(OrdersListPage::$CrtCategorySelectMenuSetSearch);
        }$I->click(OrdersListPage::$CrtCategoryButtonSaveandBack); 
    }
    
    
    
    

    
    

    protected function CreateProduct (AcceptanceTester $I,  $nameProduct = NULL,
                                                            $priceProduct = NULL,
                                                            $articleProduct = NULL,
                                                            $amountProduct = NULL,
                                                            $categoryProduct = NULL,
                                                            $variantProduct = NULL,
                                                            $variantPrice = NULL,
                                                            $variantArticle = NULL,
                                                            $variantAmount = NULL){
        $I->amOnPage(OrdersListPage::$CrtProductPageURL);                                                                                                     
        if (isset($nameProduct)) {
            $I->fillField(OrdersListPage::$CrtProductNameProduct, $nameProduct);                                          
        }if(isset($priceProduct)){
            $I->fillField(OrdersListPage::$CrtProductPriceProduct, $priceProduct);                                                                            
        }if(isset($articleProduct)){
            $I->fillField(OrdersListPage::$CrtProductArticleProduct, $articleProduct);                           
        }if(isset($amountProduct)){
            $I->fillField(OrdersListPage::$CrtProductAmountProduct, $amountProduct);                            
        }if(isset($categoryProduct)){
            $I->click(OrdersListPage::$CrtProductCategoryProductSelectField);                                                         
            $I->fillField(OrdersListPage::$CrtProductCategoryProductSelectInput, $categoryProduct);                                               
            $I->click(OrdersListPage::$CrtProductCategoryProductSetSelect);                                         
        }if(isset($variantProduct)){                                                        
            $a = $I->grabTagCount($I, 'td select');
            $b = $I->comment("$a");
                if($a == 4){
                    $I->click(OrdersListPage::$CrtProductVariantButtonADD);                                                      
                    $I->fillField(OrdersListPage::$CrtProductVariantFieldName, $variantProduct);                                                        
                }elseif($a > 4){
                    $I->fillField(OrdersListPage::$CrtProductVariantFieldName, $variantProduct);                
                }
        }if(isset($variantPrice)){
            $a = $I->grabTagCount($I, 'td select');
            $b = $I->comment("$a");
                if($a == 4){
                  $I->click(OrdersListPage::$CrtProductVariantButtonADD);
                  $I->fillField(OrdersListPage::$CrtProductVariantFieldPrice, $variantPrice);                                         
                }elseif ($a > 4) {
                  $I->fillField(OrdersListPage::$CrtProductVariantFieldPrice, $variantPrice);                                               
                }
        }if(isset($variantArticle)){
            $a = $I->grabTagCount($I, 'td select');
            $b = $I->comment("$a");
                if($a == 4){
                   $I->click(OrdersListPage::$CrtProductVariantButtonADD);
                   $I->fillField(OrdersListPage::$CrtProductVariantFieldArticle, $variantArticle); 
                }elseif ($a > 4) {
                   $I->fillField(OrdersListPage::$CrtProductVariantFieldArticle, $variantArticle);
                }
        }if(isset($variantAmount)){
            $a = $I->grabTagCount($I, 'td select');
            $b = $I->comment("$a");
                if($a == 4){
                  $I->click(OrdersListPage::$CrtProductVariantButtonADD);
                  $I->fillField(OrdersListPage::$CrtProductVariantFieldAmount, $variantAmount);                       
                }elseif ($a > 4) {
                  $I->fillField(OrdersListPage::$CrtProductVariantFieldAmount, $variantAmount); 
                }
        }
        $I->wait('1');
        $I->click(OrdersListPage::$CrtProductButtonSaveandBack);
    }
    

    

}    
