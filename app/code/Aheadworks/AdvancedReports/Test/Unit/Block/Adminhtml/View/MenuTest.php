<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Test\Unit\Block\Adminhtml\View;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\LayoutInterface;
use Aheadworks\AdvancedReports\Block\Adminhtml\View;
use Aheadworks\AdvancedReports\Block\Adminhtml\View\Breadcrumbs;
use Aheadworks\AdvancedReports\Block\Adminhtml\View\Menu;

/**
 * Test for \Aheadworks\AdvancedReports\Block\Adminhtml\View\Menu
 */
class MenuTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    const NAME_IN_LAYOUT = 'aw_arep.view_container.menu';

    /**
     * @var Menu
     */
    private $block;

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
        $this->layoutMock = $this->getMockForAbstractClass(LayoutInterface::class);

        $contextMock = $objectManager->getObject(
            Context::class,
            ['layout' => $this->layoutMock]
        );
        $this->block = $objectManager->getObject(
            Menu::class,
            ['context' => $contextMock]
        );
        $this->block->setNameInLayout(self::NAME_IN_LAYOUT);
    }

    /**
     * Testing of getFirstCrumb method
     */
    public function testGetFirstCrumb()
    {
        $parentName = 'aw_arep.view_container';
        $firstCrumb = 'salesoverview';

        $breadcrumbsBlockMock = $this->getMock(Breadcrumbs::class, ['getFirstLastCrumb'], [], '', false);
        $breadcrumbsBlockMock->expects($this->once())
            ->method('getFirstLastCrumb')
            ->willReturn($firstCrumb);

        $viewBlockMock = $this->getMock(View::class, ['getBreadcrumbs'], [], '', false);
        $viewBlockMock->expects($this->once())
            ->method('getBreadcrumbs')
            ->willReturn($breadcrumbsBlockMock);

        $this->layoutMock->expects($this->any())
            ->method('getParentName')
            ->with(self::NAME_IN_LAYOUT)
            ->willReturn($parentName);
        $this->layoutMock->expects($this->any())
            ->method('getBlock')
            ->with($parentName)
            ->willReturn($viewBlockMock);

        $this->assertSame($firstCrumb, $this->block->getFirstCrumb());
    }
}
