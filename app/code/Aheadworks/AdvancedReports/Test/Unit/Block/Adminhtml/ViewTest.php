<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Test\Unit\Block\Adminhtml;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReports\Block\Adminhtml\View;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Aheadworks\AdvancedReports\Model\Flag;
use Magento\Framework\View\LayoutInterface;
use Aheadworks\AdvancedReports\Block\Adminhtml\View\Breadcrumbs;

/**
 * Test for \Aheadworks\AdvancedReports\Block\Adminhtml\View
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    const NAME_IN_LAYOUT = 'aw_arep.view_container';

    /**
     * @var View
     */
    private $block;

    /**
     * @var TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $localeDateMock;

    /**
     * @var Flag|\PHPUnit_Framework_MockObject_MockObject
     */
    private $flagMock;

    /**
     * @var LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layoutMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->localeDateMock = $this->getMockForAbstractClass(TimezoneInterface::class);
        $this->flagMock = $this->getMock(
            Flag::class,
            ['setReportFlagCode', 'loadSelf', 'hasData', 'getLastUpdate'],
            [],
            '',
            false
        );
        $this->layoutMock = $this->getMockForAbstractClass(LayoutInterface::class);

        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'localeDate' => $this->localeDateMock,
                'layout' => $this->layoutMock
            ]
        );
        $this->block = $objectManager->getObject(
            View::class,
            [
                'context' => $contextMock,
                'flag' => $this->flagMock
            ]
        );
        $this->block->setNameInLayout(self::NAME_IN_LAYOUT);
    }

    /**
     * Testing of showLastIndexUpdate method
     * @dataProvider showLastIndexUpdateDataProvider
     *
     * @param bool $hasData
     * @param string $lastUpdate
     * @param string $updatedAt
     */
    public function testShowLastIndexUpdate($hasData, $lastUpdate, $updatedAt)
    {
        $expected = __('The latest Advanced Reports index was updated on %1', $updatedAt);

        $this->flagMock->expects($this->once())
            ->method('setReportFlagCode')
            ->with(Flag::AW_AREP_STATISTICS_FLAG_CODE)
            ->willReturnSelf();
        $this->flagMock->expects($this->once())
            ->method('loadSelf')
            ->willReturnSelf();
        $this->flagMock->expects($this->once())
            ->method('hasData')
            ->willReturn($hasData);
        if ($hasData) {
            $this->flagMock->expects($this->once())
                ->method('getLastUpdate')
                ->willReturn($lastUpdate);

            $this->localeDateMock->expects($this->once())
                ->method('formatDate')
                ->with($lastUpdate, \IntlDateFormatter::MEDIUM, true)
                ->willReturn($updatedAt);
        }

        $this->assertEquals($expected, $this->block->showLastIndexUpdate());
    }

    /**
     * Data provider for testShowLastIndexUpdate method
     *
     * @return array
     */
    public function showLastIndexUpdateDataProvider()
    {
        return [
            [true, '2016-12-21 09:30:25', 'Dec 21, 2016 12:30:25 PM'],
            [false, '', 'undefined']
        ];
    }

    /**
     * Testing of getBreadcrumbs method
     */
    public function testGetBreadcrumbs()
    {
        $breadcrumbsBlockName = 'aw_arep.view_container.breadcrumbs';

        $breadcrumbsBlockMock = $this->getMock(Breadcrumbs::class, [], [], '', false);
        $this->layoutMock->expects($this->once())
            ->method('getChildName')
            ->with(self::NAME_IN_LAYOUT, 'breadcrumbs')
            ->willReturn($breadcrumbsBlockName);
        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->with($breadcrumbsBlockName)
            ->willReturn($breadcrumbsBlockMock);

        $this->assertSame($breadcrumbsBlockMock, $this->block->getBreadcrumbs());
    }
}
