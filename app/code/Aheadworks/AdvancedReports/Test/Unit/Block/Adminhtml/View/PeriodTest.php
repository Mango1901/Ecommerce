<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Test\Unit\Block\Adminhtml\View;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReports\Block\Adminhtml\View\Period;
use Magento\Backend\Block\Template\Context;
use Aheadworks\AdvancedReports\Model\Source\Period as PeriodSource;
use Aheadworks\AdvancedReports\Model\Filter\Period as PeriodFilter;
use Aheadworks\AdvancedReports\Model\Config;
use Aheadworks\AdvancedReports\Model\Period as PeriodModel;

/**
 * Test for \Aheadworks\AdvancedReports\Block\Adminhtml\View\Period
 */
class PeriodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Period
     */
    private $block;

    /**
     * @var PeriodSource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $periodSourceMock;

    /**
     * @var PeriodFilter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $periodFilterMock;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var PeriodModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $periodModelMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->periodSourceMock = $this->getMock(PeriodSource::class, ['getOptions', 'getRangeList'], [], '', false);
        $this->periodFilterMock = $this->getMock(
            PeriodFilter::class,
            ['getLocaleTimezone', 'getPeriod'],
            [],
            '',
            false
        );
        $this->configMock = $this->getMock(Config::class, ['getLocaleFirstday'], [], '', false);
        $this->periodModelMock = $this->getMock(PeriodModel::class, ['getFirstAvailableDateAsString'], [], '', false);

        $contextMock = $objectManager->getObject(
            Context::class
        );
        $this->block = $objectManager->getObject(
            Period::class,
            [
                'context' => $contextMock,
                'periodSource' => $this->periodSourceMock,
                'periodFilter' => $this->periodFilterMock,
                'config' => $this->configMock,
                'periodModel' => $this->periodModelMock,
            ]
        );
    }

    /**
     * Testing of getOptions method
     */
    public function testGetOptions()
    {
        $this->periodSourceMock->expects($this->once())
            ->method('getOptions')
            ->willReturn([]);

        $this->assertTrue(is_array($this->block->getOptions()));
    }

    /**
     * Testing of getRanges method
     */
    public function testGetRanges()
    {
        $localeTimezone = new \DateTimeZone('Europe/Minsk');
        $rangeList[PeriodSource::TYPE_TODAY] = [
            'from' => new \DateTime('now', $localeTimezone),
            'to' => new \DateTime('now', $localeTimezone)
        ];

        $this->periodFilterMock->expects($this->once())
            ->method('getLocaleTimezone')
            ->willReturn($localeTimezone);
        $this->periodSourceMock->expects($this->once())
            ->method('getRangeList')
            ->willReturn($rangeList);

        $this->assertTrue(is_array($this->block->getRanges()));
    }

    /**
     * Testing of getPeriod method
     */
    public function testGetPeriod()
    {
        $localeTimezone = new \DateTimeZone('Europe/Minsk');
        $period = [
            'type' => PeriodSource::TYPE_TODAY,
            'from' => new \DateTime('now', $localeTimezone),
            'to' => new \DateTime('now', $localeTimezone)
        ];

        $this->periodFilterMock->expects($this->once())
            ->method('getPeriod')
            ->willReturn($period);

        $this->assertTrue(is_array($this->block->getPeriod()));
    }

    /**
     * Testing of getEarliestCalendarDateAsString method
     */
    public function testGetEarliestCalendarDateAsString()
    {
        $localeTimezone = new \DateTimeZone('Europe/Minsk');
        $firstAvailableDate = '2012-08-30';

        $this->periodFilterMock->expects($this->once())
            ->method('getLocaleTimezone')
            ->willReturn($localeTimezone);
        $this->periodModelMock->expects($this->once())
            ->method('getFirstAvailableDateAsString')
            ->willReturn($firstAvailableDate);

        $this->assertEquals($firstAvailableDate, $this->block->getEarliestCalendarDateAsString());
    }

    /**
     * Testing of getLatestCalendarDateAsString method
     */
    public function testGetLatestCalendarDateAsString()
    {
        $localeTimezone = new \DateTimeZone('Europe/Minsk');
        $date = new \DateTime('now', $localeTimezone);

        $this->periodFilterMock->expects($this->once())
            ->method('getLocaleTimezone')
            ->willReturn($localeTimezone);

        $this->assertEquals($date->format('Y-m-d'), $this->block->getLatestCalendarDateAsString());
    }

    /**
     * Testing of getLocaleTimezone method
     */
    public function testGetLocaleTimezone()
    {
        $localeTimezone = new \DateTimeZone('Europe/Minsk');

        $this->periodFilterMock->expects($this->once())
            ->method('getLocaleTimezone')
            ->willReturn($localeTimezone);

        $this->assertEquals($localeTimezone, $this->block->getLocaleTimezone());
    }

    /**
     * Testing of getFirstDayOfWeek method
     */
    public function testGetFirstDayOfWeek()
    {
        $firstday = 0;

        $this->configMock->expects($this->once())
            ->method('getLocaleFirstday')
            ->willReturn($firstday);

        $this->assertEquals($firstday, $this->block->getFirstDayOfWeek());
    }
}
