<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Test\Unit\Model;

use Aheadworks\AdvancedReports\Model\Url;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Aheadworks\AdvancedReports\Model\Filter;
use Aheadworks\AdvancedReports\Model\Source\Groupby as GroupbySource;

/**
 * Test for \Aheadworks\AdvancedReports\Model\Url
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Url
     */
    private $model;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * @var Filter\Period|\PHPUnit_Framework_MockObject_MockObject
     */
    private $periodFilterMock;

    /**
     * @var Filter\Groupby|\PHPUnit_Framework_MockObject_MockObject
     */
    private $groupbyFilterMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $this->periodFilterMock = $this->getMock(
            Filter\Period::class,
            ['getPeriodFrom', 'getPeriodTo', 'getLocaleTimezone'],
            [],
            '',
            false
        );
        $this->groupbyFilterMock = $this->getMock(Filter\Groupby::class, ['getCurrentGroupByKey'], [], '', false);

        $this->model = $objectManager->getObject(
            Url::class,
            [
                'request' => $this->requestMock,
                'urlBuilder' => $this->urlBuilderMock,
                'periodFilter' => $this->periodFilterMock,
                'groupbyFilter' => $this->groupbyFilterMock
            ]
        );
    }

    /**
     * Testing of getUrl method
     */
    public function testGetUrl()
    {
        $report = 'salesoverview';
        $reportTo = 'productperformance';
        $periodFrom = $periodTo = new \DateTime('2016-12-12');
        $url = 'http://mydomain.com';

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('brc')
            ->willReturn(null);
        $this->periodFilterMock->expects($this->once())
            ->method('getPeriodFrom')
            ->willReturn($periodFrom);
        $this->periodFilterMock->expects($this->once())
            ->method('getPeriodTo')
            ->willReturn($periodTo);
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->willReturn($url);

        $this->assertSame($url, $this->model->getUrl($report, $reportTo));
    }

    /**
     * Testing of getUrlByPeriod method
     * @dataProvider getUrlByPeriodDataProvider
     *
     * @param [] $item
     * @param string $groupbyType
     */
    public function testGetUrlByPeriod($item, $groupbyType)
    {
        $report = 'salesoverview';
        $reportTo = 'productperformance';
        $periodFrom = $periodTo = new \DateTime('2016-12-12');
        $url = 'http://mydomain.com';

        $this->groupbyFilterMock->expects($this->once())
            ->method('getCurrentGroupByKey')
            ->willReturn($groupbyType);
        $this->periodFilterMock->expects($this->once())
            ->method('getPeriodFrom')
            ->willReturn($periodFrom);
        $this->periodFilterMock->expects($this->once())
            ->method('getPeriodTo')
            ->willReturn($periodTo);
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('brc')
            ->willReturn(null);
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->willReturn($url);

        $this->assertTrue(is_array($this->model->getUrlByPeriod($item, $report, $reportTo)));
    }

    /**
     * Data provider for testGetUrlByPeriod method
     *
     * @return array
     */
    public function getUrlByPeriodDataProvider()
    {
        return [
            [['date' => '2016-12-12'], GroupbySource::TYPE_DAY],
            [['start_date' => '2016-06-01', 'end_date' => '2016-06-04'], GroupbySource::TYPE_WEEK],
            [['start_date' => '2016-12-12', 'end_date' => '2016-12-30'], GroupbySource::TYPE_MONTH],
            [['start_date' => '2016-07-01', 'end_date' => '2016-09-30'], GroupbySource::TYPE_QUARTER],
            [['start_date' => '2016-12-12', 'end_date' => '2016-12-12'], GroupbySource::TYPE_YEAR]
        ];
    }

    /**
     * Testing of getBrcParam method
     * @dataProvider getBrcParamDataProvider
     *
     * @param [] $item
     * @param string $groupbyType
     */
    public function testGetBrcParam($report, $reportTo, $brc)
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('brc')
            ->willReturn($brc);

        $class = new \ReflectionClass($this->model);
        $method = $class->getMethod('getBrcParam');
        $method->setAccessible(true);

        $expected = ($brc ?: $report) . '-' . $reportTo;

        $this->assertEquals(
            $expected,
            $method->invokeArgs($this->model, [$report, $reportTo])
        );
    }

    /**
     * Data provider for testGetUrlByPeriod method
     *
     * @return array
     */
    public function getBrcParamDataProvider()
    {
        return [
            ['salesoverview', 'productperformance', ''],
            ['productperformance', 'productperformance_variantperformance', 'couponcode-salesoverview'],
        ];
    }
}
