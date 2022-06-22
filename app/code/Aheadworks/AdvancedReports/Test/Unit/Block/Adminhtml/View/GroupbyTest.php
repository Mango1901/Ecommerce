<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Test\Unit\Block\Adminhtml\View;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReports\Block\Adminhtml\View\Groupby;
use Magento\Backend\Block\Template\Context;
use Aheadworks\AdvancedReports\Model\Source\Groupby as GroupbySource;
use Aheadworks\AdvancedReports\Model\Filter\Groupby as GroupbyFilter;
use Magento\Framework\UrlInterface;

/**
 * Test for \Aheadworks\AdvancedReports\Block\Adminhtml\View\Groupby
 */
class GroupbyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Groupby
     */
    private $block;

    /**
     * @var GroupbySource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $groupbySourceMock;

    /**
     * @var GroupbyFilter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $groupbyFilterMock;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->groupbySourceMock = $this->getMock(GroupbySource::class, ['getOptions'], [], '', false);
        $this->groupbyFilterMock = $this->getMock(GroupbyFilter::class, ['getCurrentGroupByKey'], [], '', false);
        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);

        $contextMock = $objectManager->getObject(
            Context::class,
            ['urlBuilder' => $this->urlBuilderMock]
        );
        $this->block = $objectManager->getObject(
            Groupby::class,
            [
                'context' => $contextMock,
                'groupbySource' => $this->groupbySourceMock,
                'groupbyFilter' => $this->groupbyFilterMock,
            ]
        );
    }

    /**
     * Testing of getOptions method
     */
    public function testGetOptions()
    {
        $this->groupbySourceMock->expects($this->once())
            ->method('getOptions')
            ->willReturn([]);

        $this->assertTrue(is_array($this->block->getOptions()));
    }

    /**
     * Testing of getCurrentGroupByKey method
     */
    public function testGetCurrentGroupByKey()
    {
        $groupBy = 'day';

        $this->groupbyFilterMock->expects($this->once())
            ->method('getCurrentGroupByKey')
            ->willReturn($groupBy);

        $this->assertEquals($groupBy, $this->block->getCurrentGroupByKey());
    }

    /**
     * Testing of getGroupbyUrl method
     */
    public function testGetGroupbyUrl()
    {
        $groupBy = 'day';
        $expected = 'http://mydomen.com/index.php/admin/advancedreports/salesoverview/index/?group_by=day';

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('*/*/*', ['_query' => ['group_by' => $groupBy], '_current' => true])
            ->willReturn($expected);

        $this->assertEquals($expected, $this->block->getGroupbyUrl($groupBy));
    }
}
