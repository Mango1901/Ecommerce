<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Test\Unit\Model\Source;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReports\Model\Source\Period;
use Magento\Framework\Locale\ListsInterface;
use Aheadworks\AdvancedReports\Model\Config;

/**
 * Test for \Aheadworks\AdvancedReports\Model\Source\Period
 */
class PeriodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Period
     */
    private $model;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var ListsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $localeListsMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->configMock = $this->getMock(Config::class, ['getLocaleFirstday', 'getLocaleWeekend'], [], '', false);
        $this->localeListsMock = $this->getMockForAbstractClass(ListsInterface::class);

        $this->model = $objectManager->getObject(
            Period::class,
            [
                'config' => $this->configMock,
                'localeLists' => $this->localeListsMock
            ]
        );
    }

    /**
     * Testing of getOptions method
     */
    public function testGetOptions()
    {
        $localeFirstday = 0;
        $localeWeekend = '0,6';

        $this->configMock->expects($this->exactly(2))
            ->method('getLocaleFirstday')
            ->willReturn($localeFirstday);
        $this->configMock->expects($this->once())
            ->method('getLocaleWeekend')
            ->willReturn($localeWeekend);
        $this->localeListsMock->expects($this->exactly(2))
            ->method('getOptionWeekdays')
            ->willReturn([]);

        $this->assertTrue(is_array($this->model->getOptions()));
    }

    /**
     * Testing of getRangeList method
     */
    public function testGetRangeList()
    {
        $localeFirstday = 0;
        $localeTimezone = new \DateTimeZone('Europe/Minsk');

        $this->configMock->expects($this->exactly(2))
            ->method('getLocaleFirstday')
            ->willReturn($localeFirstday);

        $this->assertTrue(is_array($this->model->getRangeList($localeTimezone)));
    }
}
