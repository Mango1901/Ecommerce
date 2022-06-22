<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Test\Unit\Model;

use Aheadworks\AdvancedReports\Model\Config;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Test for \Aheadworks\AdvancedReports\Model\Config
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    private $model;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->model = $objectManager->getObject(
            Config::class,
            ['scopeConfig' => $this->scopeConfigMock]
        );
    }

    /**
     * Testing of getOrderStatus method
     */
    public function testGetOrderStatus()
    {
        $value = 'complete';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_ORDER_STATUS)
            ->willReturn($value);
        $this->assertSame($value, $this->model->getOrderStatus());
    }

    /**
     * Testing of getLocaleWeekend method
     */
    public function testGetLocaleWeekend()
    {
        $value = '0,6';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_LOCALE_WEEKEND)
            ->willReturn($value);
        $this->assertSame($value, $this->model->getLocaleWeekend());
    }

    /**
     * Testing of getLocaleFirstday method
     */
    public function testGetLocaleFirstday()
    {
        $value = '0';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_LOCALE_FIRSTDAY)
            ->willReturn($value);
        $this->assertSame($value, $this->model->getLocaleFirstday());
    }
}
