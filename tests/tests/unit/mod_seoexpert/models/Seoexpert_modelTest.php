<?php

require_once realpath(dirname(__FILE__) . '/../../../..') . '/enviroment.php';
require_once APPPATH.'modules/mod_seoexpert/models/seoexpert_model.php';
doLogin();
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-10 at 12:52:51.
 */
class Seoexpert_modelTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Seoexpert_model
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new Seoexpert_model();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @covers Seoexpert_model::getSettings
     * @todo   Implement testGetSettings().
     */
    public function testGetSettings() {
        $result = (boolean) $this->object->getSettings();
        $this->assertTrue($result);
        
        $result = (boolean) $this->object->getSettings('fdgdfg');
        $this->assertFalse($result);
    }

    /**
     * @covers Seoexpert_model::setSettings
     * @todo   Implement testSetSettings().
     */
    public function testSetSettings() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Seoexpert_model::getCategoriesByIdName
     * @todo   Implement testGetCategoriesByIdName().
     */
    public function testGetCategoriesByIdName() {
        $result = (boolean) $this->object->getCategoriesByIdName('s');
        $this->assertTrue($result);
        
        $result = (boolean) $this->object->getCategoriesByIdName('sssdgjdfgjfh');
        $this->assertFalse($result);
    }

    /**
     * @covers Seoexpert_model::getTopBrandsInCategory
     * @todo   Implement testGetTopBrandsInCategory().
     */
    public function testGetTopBrandsInCategory() {
        $result =  (boolean) $this->object->getTopBrandsInCategory(74);
        $this->assertTrue($result);
        
        $result =  (boolean) $this->object->getTopBrandsInCategory(74, 5);
        $this->assertTrue($result);
        
        $result =  (boolean) $this->object->getTopBrandsInCategory(74, 0);
        $this->assertFalse($result);
        
        $result =  (boolean) $this->object->getTopBrandsInCategory(74, 1, 'sdfsdf');
        $this->assertFalse($result);
        
        $result =  (boolean) $this->object->getTopBrandsInCategory(755554);
        $this->assertFalse($result);

    }

    /**
     * @covers Seoexpert_model::getLangIdByLocale
     * @todo   Implement testGetLangIdByLocale().
     */
    public function testGetLangIdByLocale() {
        $result = (boolean) $this->object->getLangIdByLocale();
        $this->assertTrue($result);
    
        $result = (boolean) $this->object->getLangIdByLocale('ru');
        $this->assertTrue($result);
        
        $result = (boolean) $this->object->getLangIdByLocale('rusdfsdf');
        $this->assertFalse($result);
        
    }

    /**
     * @covers Seoexpert_model::getBaseSettings
     * @todo   Implement testGetBaseSettings().
     */
    public function testGetBaseSettings() {
        $result = (boolean) $this->object->getBaseSettings(3);
        $this->assertTrue($result);
        
        $result = (boolean) $this->object->getBaseSettings('1234d4');
        $this->assertFalse($result);
        
        $result = (boolean) $this->object->getBaseSettings('3');
        $this->assertTrue($result);
        
    }

    /**
     * @covers Seoexpert_model::setBaseSettings
     * @todo   Implement testSetBaseSettings().
     */
    public function testSetBaseSettings() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Seoexpert_model::install
     * @todo   Implement testInstall().
     */
    public function testInstall() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Seoexpert_model::deinstall
     * @todo   Implement testDeinstall().
     */
    public function testDeinstall() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}
