<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Test\Unit\Model\Filter;

use Aheadworks\AdvancedReports\Model\Filter\Period;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Aheadworks\AdvancedReports\Model\Source\Period as PeriodSource;

/**
 * Test for \Aheadworks\AdvancedReports\Model\Filter\Period
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PeriodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Period
     */
    private $model;

    /**
     * @var PeriodSource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $periodSourceMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var SessionManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionMock;

    /**
     * @var TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $localeDateMock;

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
        $this->periodSourceMock = $this->getMock(PeriodSource::class, ['getRangeList'], [], '', false);
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->sessionMock = $this->getMockForAbstractClass(
            SessionManagerInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['setData', 'getData']
        );
        $this->localeDateMock = $this->getMockForAbstractClass(TimezoneInterface::class);
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->model = $objectManager->getObject(
            Period::class,
            [
                'periodSource' => $this->periodSourceMock,
                'request' => $this->requestMock,
                'session' => $this->sessionMock,
                'localeDate' => $this->localeDateMock,
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }

    /**
     * Testing of getLocaleTimezone method
     */
    public function testGetLocaleTimezone()
    {
        $timezonePath = 'timezone_path';
        $localeTimezone = 'Europe/Minsk';

        $this->localeDateMock->expects($this->once())
            ->method('getDefaultTimezonePath')
            ->willReturn($timezonePath);
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with($timezonePath, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn($localeTimezone);

        $this->assertEquals(new \DateTimeZone($localeTimezone), $this->model->getLocaleTimezone());
    }

    /**
     * Testing of getPeriod method from request
     */
    public function testGetPeriodFromRequest()
    {
        $params = [
            ['period_type', null, Period::PERIOD_TYPE_CUSTOM],
            ['period_from', null, '2016-12-01'],
            ['period_to', null, '2016-12-01']
        ];
        $timezonePath = 'timezone_path';
        $localeTimezone = 'Europe/Minsk';

        $this->requestMock->expects($this->exactly(3))
            ->method('getParam')
            ->willReturnMap($params);
        $this->sessionMock->expects($this->at(0))
            ->method('setData')
            ->with(Period::SESSION_PERIOD_FROM_KEY, $params[1][2])
            ->willReturnSelf();
        $this->sessionMock->expects($this->at(1))
            ->method('setData')
            ->with(Period::SESSION_PERIOD_TO_KEY, $params[2][2])
            ->willReturnSelf();

        $this->localeDateMock->expects($this->once())
            ->method('getDefaultTimezonePath')
            ->willReturn($timezonePath);
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with($timezonePath, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn($localeTimezone);

        $this->sessionMock->expects($this->at(2))
            ->method('setData')
            ->with(Period::SESSION_KEY, $params[0][2])
            ->willReturnSelf();

        $this->assertTrue(is_array($this->model->getPeriod()));
    }

    /**
     * Testing of getPeriod method from session
     */
    public function testGetPeriodFromSession()
    {
        $params = [
            ['period_type', null, null],
            ['period_from', null, null],
            ['period_to', null, null]
        ];
        $periodType = Period::PERIOD_TYPE_CUSTOM;
        $periodFrom = '2016-12-01';
        $periodTo = '2016-12-01';
        $timezonePath = 'timezone_path';
        $localeTimezone = 'Europe/Minsk';

        $this->requestMock->expects($this->exactly(3))
            ->method('getParam')
            ->willReturnMap($params);
        $this->sessionMock->expects($this->at(0))
            ->method('getData')
            ->with(Period::SESSION_KEY)
            ->willReturn($periodType);
        $this->sessionMock->expects($this->at(1))
            ->method('getData')
            ->with(Period::SESSION_PERIOD_FROM_KEY)
            ->willReturn($periodFrom);
        $this->sessionMock->expects($this->at(2))
            ->method('getData')
            ->with(Period::SESSION_PERIOD_TO_KEY)
            ->willReturn($periodTo);
        $this->sessionMock->expects($this->at(3))
            ->method('setData')
            ->with(Period::SESSION_PERIOD_FROM_KEY, $periodFrom)
            ->willReturnSelf();
        $this->sessionMock->expects($this->at(4))
            ->method('setData')
            ->with(Period::SESSION_PERIOD_TO_KEY, $periodTo)
            ->willReturnSelf();

        $this->localeDateMock->expects($this->once())
            ->method('getDefaultTimezonePath')
            ->willReturn($timezonePath);
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with($timezonePath, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn($localeTimezone);

        $this->sessionMock->expects($this->at(5))
            ->method('setData')
            ->with(Period::SESSION_KEY, $periodType)
            ->willReturnSelf();

        $this->assertTrue(is_array($this->model->getPeriod()));
    }

    /**
     * Testing of getPeriod method from default
     */
    public function testGetPeriodFromDfault()
    {
        $params = [
            ['period_type', null, null],
            ['period_from', null, null],
            ['period_to', null, null]
        ];
        $localeTimezone = 'Europe/Minsk';
        $rangeList[PeriodSource::TYPE_THIS_MONTH] = [
            'from' => new \DateTime('now', new \DateTimeZone($localeTimezone)),
            'to' => new \DateTime('now', new \DateTimeZone($localeTimezone))
        ];
        $timezonePath = 'timezone_path';

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->willReturnMap($params);
        $this->sessionMock->expects($this->at(0))
            ->method('getData')
            ->with(Period::SESSION_KEY)
            ->willReturn(null);
        $this->periodSourceMock->expects($this->once())
            ->method('getRangeList')
            ->willReturn($rangeList);

        $this->localeDateMock->expects($this->once())
            ->method('getDefaultTimezonePath')
            ->willReturn($timezonePath);
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with($timezonePath, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn($localeTimezone);

        $this->sessionMock->expects($this->at(1))
            ->method('setData')
            ->with(Period::SESSION_KEY, Period::DEFAULT_PERIOD_TYPE)
            ->willReturnSelf();

        $this->assertTrue(is_array($this->model->getPeriod()));
    }
}
