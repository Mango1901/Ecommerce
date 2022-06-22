<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Test\Unit\Model;

use Aheadworks\AdvancedReports\Model\Period;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\CacheInterface;
use Aheadworks\AdvancedReports\Model\Filter;
use Aheadworks\AdvancedReports\Model\Url as UrlModel;
use Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping\Factory as DatesGroupingFactory;
use Aheadworks\AdvancedReports\Model\Source\Groupby;
use Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping;

/**
 * Test for \Aheadworks\AdvancedReports\Model\Period
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PeriodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Period
     */
    private $model;

    /**
     * @var TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $localeDateMock;

    /**
     * @var CacheInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cacheMock;

    /**
     * @var DatesGroupingFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $datesGroupingFactoryMock;

    /**
     * @var Filter\Groupby|\PHPUnit_Framework_MockObject_MockObject
     */
    private $groupbyFilterMock;

    /**
     * @var UrlModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlModelMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->localeDateMock = $this->getMockForAbstractClass(TimezoneInterface::class);
        $this->cacheMock = $this->getMockForAbstractClass(CacheInterface::class);
        $this->datesGroupingFactoryMock = $this->getMock(DatesGroupingFactory::class, ['create'], [], '', false);
        $this->groupbyFilterMock = $this->getMock(Filter\Groupby::class, ['getCurrentGroupByKey'], [], '', false);
        $this->urlModelMock = $this->getMock(UrlModel::class, ['getUrlByPeriod'], [], '', false);

        $this->model = $objectManager->getObject(
            Period::class,
            [
                'localeDate' => $this->localeDateMock,
                'cache' => $this->cacheMock,
                'datesGroupingFactory' => $this->datesGroupingFactoryMock,
                'groupbyFilter' => $this->groupbyFilterMock,
                'urlModel' => $this->urlModelMock
            ]
        );
    }

    /**
     * Testing of getFirstAvailableDateAsString method
     */
    public function testGetFirstAvailableDateAsString()
    {
        $minDate = '2016-10-01';

        $this->cacheMock->expects($this->once())
            ->method('load')
            ->with(Period::MIN_DATE_CACHE_KEY)
            ->willReturn(null);
        $dayMock = $this->getMock(DatesGrouping\Day::class, ['getMinDate'], [], '', false);
        $dayMock->expects($this->once())
            ->method('getMinDate')
            ->willReturn($minDate);
        $this->datesGroupingFactoryMock->expects($this->once())
            ->method('create')
            ->with(DatesGrouping\Day::KEY)
            ->willReturn($dayMock);
        $this->cacheMock->expects($this->once())
            ->method('save')
            ->with($minDate, Period::MIN_DATE_CACHE_KEY, [], null)
            ->willReturnSelf();

        $this->assertEquals($minDate, $this->model->getFirstAvailableDateAsString());
    }

    /**
     * Testing of getPeriod method
     */
    public function testGetPeriod()
    {
        $item = [];
        $periodLabel = 'Dec 12, 2016';
        $period = [
            'start_date' => new \DateTime('2016-12-12'),
            'end_date' => new \DateTime('2016-12-12'),
            'url' => 'http://mydomain.com'
        ];
        $expected = ['period_url' => $period['url'], 'period_label' => $periodLabel];

        $this->urlModelMock->expects($this->once())
            ->method('getUrlByPeriod')
            ->willReturn($period);
        $this->groupbyFilterMock->expects($this->once())
            ->method('getCurrentGroupByKey')
            ->willReturn(Groupby::TYPE_DAY);
        $this->localeDateMock->expects($this->once())
            ->method('formatDateTime')
            ->willReturn($periodLabel);

        $this->assertEquals($expected, $this->model->getPeriod($item));
    }

    /**
     * Testing of getPeriodAsString method
     * @dataProvider getPeriodAsStringDataProvider
     *
     * @param \DateTime $from
     * @param \DateTime $to
     * @param string $groupType
     * @param string $expected
     */
    public function testGetPeriodAsString($from, $to, $groupType, $expected)
    {
        if (Groupby::TYPE_DAY == $groupType) {
            $this->localeDateMock->expects($this->once())
                ->method('formatDateTime')
                ->willReturn($expected);
        }
        if (Groupby::TYPE_WEEK == $groupType) {
            $dates = explode(' - ', $expected);
            $this->localeDateMock->expects($this->at(0))
                ->method('formatDateTime')
                ->with($from)
                ->willReturn($dates[0]);
            $this->localeDateMock->expects($this->at(1))
                ->method('formatDateTime')
                ->with($to)
                ->willReturn($dates[1]);
        }

        $class = new \ReflectionClass($this->model);
        $method = $class->getMethod('getPeriodAsString');
        $method->setAccessible(true);

        $this->assertEquals(
            $expected,
            $method->invokeArgs($this->model, [$from, $to, $groupType])
        );
    }

    /**
     * Data provider for testGetPeriodAsString method
     *
     * @return array
     */
    public function getPeriodAsStringDataProvider()
    {
        return [
            [new \DateTime('2016-12-12'), new \DateTime('2016-12-12'), Groupby::TYPE_DAY, 'Dec 12, 2016'],
            [new \DateTime('2016-06-01'), new \DateTime('2016-06-04'), Groupby::TYPE_WEEK, 'Jun 1, 2016 - Jun 4, 2016'],
            [new \DateTime('2016-12-12'), new \DateTime('2016-12-30'), Groupby::TYPE_MONTH, 'Dec 2016'],
            [new \DateTime('2016-07-01'), new \DateTime('2016-09-30'), Groupby::TYPE_QUARTER, 'Q3 2016'],
            [new \DateTime('2016-12-12'), new \DateTime('2016-12-12'), Groupby::TYPE_YEAR, '2016']
        ];
    }
}
