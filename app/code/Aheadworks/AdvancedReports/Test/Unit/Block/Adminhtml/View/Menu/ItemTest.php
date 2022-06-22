<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Test\Unit\Block\Adminhtml\View\Menu;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReports\Block\Adminhtml\View\Menu\Item;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\UrlInterface;
use Aheadworks\AdvancedReports\Block\Adminhtml\View\Menu;
use Magento\Framework\View\LayoutInterface;

/**
 * Test for \Aheadworks\AdvancedReports\Block\Adminhtml\View\Menu\Item
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Item
     */
    private $item;

    /**
     * @var Http|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * @var AuthorizationInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $authorizationMock;

    /**
     * @var Menu|\PHPUnit_Framework_MockObject_MockObject
     */
    private $menuBlockMock;

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
        $this->requestMock = $this->getMock(Http::class, ['getControllerName'], [], '', false);
        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $this->authorizationMock = $this->getMockForAbstractClass(AuthorizationInterface::class);
        $this->menuBlockMock = $this->getMock(Menu::class, ['getFirstCrumb'], [], '', false);
        $this->layoutMock = $this->getMockForAbstractClass(LayoutInterface::class);
        $this->layoutMock->expects($this->any())
            ->method('getParentName')
            ->with('aw_arep_menu_item.salesoverview')
            ->willReturn('aw_arep.view_container.menu');
        $this->layoutMock->expects($this->any())
            ->method('getBlock')
            ->with('aw_arep.view_container.menu')
            ->willReturn($this->menuBlockMock);
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
                'urlBuilder' => $this->urlBuilderMock,
                'authorization' => $this->authorizationMock,
                'layout' => $this->layoutMock
            ]
        );
        $this->item = $objectManager->getObject(
            Item::class,
            ['context' => $contextMock]
        );
        $this->item->setNameInLayout('aw_arep_menu_item.salesoverview');
    }

    /**
     * Testing of prepareLinkAttributes method for the use getUrl method
     */
    public function testPrepareLinkAttributes()
    {
        $linkAttributes = [
            'class' => 'separator',
        ];
        $path = '*/rule/index';

        $this->item->setLinkAttributes($linkAttributes);
        $this->item->setPath($path);

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with($path);

        $class = new \ReflectionClass($this->item);
        $method = $class->getMethod('prepareLinkAttributes');
        $method->setAccessible(true);

        $method->invoke($this->item);
    }

    /**
     * Testing of serializeLinkAttributes method
     */
    public function testSerializeLinkAttributes()
    {
        $linkAttributes = [
            'attr' => 'attr_value',
            'attr_1' => 'attr_value_1',
        ];
        $expected = 'attr="attr_value" attr_1="attr_value_1"';
        $this->item->setLinkAttributes($linkAttributes);

        $this->assertEquals($expected, $this->item->serializeLinkAttributes());
    }

    /**
     * Testing of _toHtml method, resource is not allowed
     */
    public function testToHtml()
    {
        $resource = 'test';
        $expected = '';

        $this->authorizationMock->expects($this->once())
            ->method('isAllowed')
            ->with($resource)
            ->willReturn(false);
        $this->item->setResource($resource);

        $class = new \ReflectionClass($this->item);
        $method = $class->getMethod('_toHtml');
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invoke($this->item));
    }

    /**
     * Testing of isCurrent method
     *
     * @param string $controllerName
     * @param string $requestControllerName
     * @param bool $expected
     * @dataProvider isCurrentDataProvider
     */
    public function testIsCurrent($controllerName, $firstCrumb, $expected)
    {
        $this->item->setController($controllerName);
        $this->menuBlockMock->expects($this->once())
            ->method('getFirstCrumb')
            ->willReturn($firstCrumb);

        $class = new \ReflectionClass($this->item);
        $method = $class->getMethod('isCurrent');
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invoke($this->item));
    }

    /**
     * Data provider for testIsCurrent method
     *
     * @return array
     */
    public function isCurrentDataProvider()
    {
        return [
            ['test', 'test_test', false],
            ['test', 'test', true]
        ];
    }
}
