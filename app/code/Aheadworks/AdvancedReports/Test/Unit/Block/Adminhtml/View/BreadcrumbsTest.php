<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Test\Unit\Block\Adminhtml\View;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReports\Block\Adminhtml\View\Breadcrumbs;
use Magento\Backend\Block\Template\Context;
use Aheadworks\AdvancedReports\Model\Filter;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\Generic;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Page\Title as PageTitle;

/**
 * Test for \Aheadworks\AdvancedReports\Block\Adminhtml\View\Breadcrumbs
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BreadcrumbsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Breadcrumbs
     */
    private $block;

    /**
     * @var Filter\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeFilterMock;

    /**
     * @var Filter\Groupby|\PHPUnit_Framework_MockObject_MockObject
     */
    private $groupbyFilterMock;

    /**
     * @var Filter\Period|\PHPUnit_Framework_MockObject_MockObject
     */
    private $periodFilterMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * @var Generic|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionMock;

    /**
     * @var PageConfig
     */
    private $pageConfigMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->storeFilterMock = $this->getMock(Filter\Store::class, [], [], '', false);
        $this->groupbyFilterMock = $this->getMock(Filter\Groupby::class, ['getCurrentGroupByKey'], [], '', false);
        $this->periodFilterMock = $this->getMock(
            Filter\Period::class,
            ['getPeriodType', 'getPeriodFrom', 'getPeriodTo'],
            [],
            '',
            false
        );
        $this->requestMock = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['getControllerName', 'getQueryValue']
        );
        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $this->sessionMock = $this->getMock(Generic::class, ['getData', 'setData'], [], '', false);
        $this->pageConfigMock = $this->getMock(PageConfig::class, ['getTitle'], [], '', false);

        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('brc')
            ->willReturn('salesoverview-productperformance');

        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
                'urlBuilder' => $this->urlBuilderMock,
                'session' => $this->sessionMock,
                'pageConfig' => $this->pageConfigMock
            ]
        );
        $this->block = $objectManager->getObject(
            Breadcrumbs::class,
            [
                'context' => $contextMock,
                'storeFilter' => $this->storeFilterMock,
                'groupbyFilter' => $this->groupbyFilterMock,
                'periodFilter' => $this->periodFilterMock
            ]
        );
    }

    /**
     * Testing of addCrumb method
     */
    public function testAddCrumb()
    {
        $key = 'key';
        $alias = 'alias';
        $label = 'label';
        $url = 'http://mydomain.com';

        $this->sessionMock->expects($this->once())
            ->method('getData')
            ->with(Breadcrumbs::SESSION_KEY)
            ->willReturn([]);
        $this->sessionMock->expects($this->once())
            ->method('setData')
            ->willReturnSelf();

        $this->assertSame($this->block, $this->block->addCrumb($key, $alias, $label, $url));
    }

    /**
     * Testing of getCrumbs method
     */
    public function testGetCrumbs()
    {
        $sessionCrumbs['salesoverview'] = [
            'salesoverview' => ['label' => 'label', 'url' => 'http://mydomain.com', 'last' => false],
            'productperformance' => ['label' => 'label', 'url' => 'http://mydomain.com', 'last' => true]
        ];

        $this->requestMock->expects($this->exactly(2))
            ->method('getControllerName')
            ->willReturn('productperformance');
        $this->sessionMock->expects($this->once())
            ->method('getData')
            ->with(Breadcrumbs::SESSION_KEY)
            ->willReturn($sessionCrumbs);

        $this->assertEquals($sessionCrumbs['salesoverview'], $this->block->getCrumbs());
    }

    /**
     * Testing of getFirstLastCrumb method
     * @dataProvider getFirstLastCrumbDataProvider
     *
     * @param bool $key
     * @param string $expected
     */
    public function testGetFirstLastCrumb($key, $expected)
    {
        $this->requestMock->expects($this->once())
            ->method('getControllerName')
            ->willReturn('productperformance');

        $this->assertEquals($expected, $this->block->getFirstLastCrumb($key));
    }

    /**
     * Data provider for testGetFirstLastCrumb method
     *
     * @return array
     */
    public function getFirstLastCrumbDataProvider()
    {
        return [
            [true, 'salesoverview'],
            [false, 'productperformance'],
        ];
    }

    /**
     * Testing of _beforeToHtml method for data from session
     */
    public function testBeforeToHtml()
    {
        $sessionCrumbs['salesoverview'] = [
            'salesoverview' => ['label' => 'label', 'url' => 'http://mydomain.com', 'last' => false],
            'productperformance' => ['label' => 'label', 'url' => 'http://mydomain.com', 'last' => true]
        ];

        $this->requestMock->expects($this->exactly(3))
            ->method('getControllerName')
            ->willReturn('productperformance');
        $this->sessionMock->expects($this->exactly(2))
            ->method('getData')
            ->with(Breadcrumbs::SESSION_KEY)
            ->willReturn($sessionCrumbs);
        $this->sessionMock->expects($this->once())
            ->method('setData')
            ->willReturnSelf();

        $this->periodFilterMock->expects($this->once())
            ->method('getPeriodType')
            ->willReturn('period_type');
        $this->periodFilterMock->expects($this->once())
            ->method('getPeriodFrom')
            ->willReturn(new \DateTime());
        $this->periodFilterMock->expects($this->once())
            ->method('getPeriodTo')
            ->willReturn(new \DateTime());
        $this->requestMock->expects($this->once())
            ->method('getQueryValue')
            ->willReturn([]);

        $titleMock = $this->getMock(PageTitle::class, ['getShort'], [], '', false);
        $this->pageConfigMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($titleMock);
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->willReturn('http://mydomain.com');

        $class = new \ReflectionClass($this->block);
        $method = $class->getMethod('_beforeToHtml');
        $method->setAccessible(true);

        $this->assertSame($this->block, $method->invoke($this->block));
    }

    /**
     * Testing of getUrlLabelByDefaultCrumb method for data from session
     * @dataProvider getUrlLabelByDefaultCrumbDataProvider
     *
     * @param string $crumb
     * @param string $firstCrumb
     */
    public function testGetUrlLabelByDefaultCrumb($crumb, $firstCrumb)
    {
        $url = 'http://mydomain.com';
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->willReturn($url);

        if ($firstCrumb != $crumb) {
            $this->requestMock->expects($this->once())
                ->method('getQueryValue')
                ->willReturn([]);
        }

        $class = new \ReflectionClass($this->block);
        $method = $class->getMethod('getUrlLabelByDefaultCrumb');
        $method->setAccessible(true);

        $this->assertTrue(is_array($method->invokeArgs($this->block, [$crumb, $firstCrumb])));
    }

    /**
     * Data provider for testGetFirstLastCrumb method
     *
     * @return array
     */
    public function getUrlLabelByDefaultCrumbDataProvider()
    {
        return [
            ['salesoverview', 'salesoverview'],
            ['productperformance', 'salesoverview'],
        ];
    }

    /**
     * Testing of getLabelByQueryParam method for data from session
     * @dataProvider getLabelByQueryParamDataProvider
     *
     * @param stirng $key
     * @param stirng $label
     * @param stirng $expValue
     */
    public function testGetLabelByQueryParam($key, $label, $expValue)
    {
        switch ($key) {
            case 'payment_name':
            case 'product_name':
            case 'category_name':
                $this->requestMock->expects($this->once())
                    ->method('getParam')
                    ->with($key)
                    ->willReturn($expValue);
                break;
        }

        $class = new \ReflectionClass($this->block);
        $method = $class->getMethod('getLabelByQueryParam');
        $method->setAccessible(true);

        $this->assertEquals(
            __($label . ' (%1)', base64_decode($expValue)),
            $method->invokeArgs($this->block, [$key, $expValue, $label])
        );
    }

    /**
     * Data provider for testGetFirstLastCrumb method
     *
     * @return array
     */
    public function getLabelByQueryParamDataProvider()
    {
        return [
            ['payment_name', 'label', base64_encode('Check Money Order')],
            ['product_name', 'label', base64_encode('Product 1')],
            ['category_name', 'label', base64_encode('Category 1')],
            ['manufacturer', 'label', base64_encode('Manufacturer 1')],
        ];
    }
}
