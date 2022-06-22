<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Test\Unit\Model\Filter;

use Aheadworks\AdvancedReports\Model\Filter\Store;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Website;
use Magento\Store\Model\Group;
use Magento\Store\Model\Store as StoreModel;

/**
 * Test for \Aheadworks\AdvancedReports\Model\Filter\Store
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Period
     */
    private $model;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var SessionManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
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
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $this->model = $objectManager->getObject(
            Store::class,
            [
                'request' => $this->requestMock,
                'session' => $this->sessionMock,
                'storeManager' => $this->storeManagerMock,
                'urlBuilder' => $this->urlBuilderMock
            ]
        );
    }

    /**
     * Testing of getItems method
     */
    public function testGetItems()
    {
        $url = 'http://mydomain.com';
        $storeId = 1;
        $storeName = 'Store 1';
        $groupId = 1;
        $groupName = 'Group 1';
        $websiteId = 1;
        $websiteName = 'Website 1';

        $storeModelMock = $this->getMock(StoreModel::class, ['getId', 'getName'], [], '', false);
        $storeModelMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($storeId);
        $storeModelMock->expects($this->once())
            ->method('getName')
            ->willReturn($storeName);

        $groupModelMock = $this->getMock(Group::class, ['getId', 'getName', 'getStores'], [], '', false);
        $groupModelMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($groupId);
        $groupModelMock->expects($this->once())
            ->method('getName')
            ->willReturn($groupName);
        $groupModelMock->expects($this->once())
            ->method('getStores')
            ->willReturn([$storeModelMock]);

        $websiteModelMock = $this->getMock(Website::class, ['getId', 'getName', 'getGroups'], [], '', false);
        $websiteModelMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($websiteId);
        $websiteModelMock->expects($this->once())
            ->method('getName')
            ->willReturn($websiteName);
        $websiteModelMock->expects($this->once())
            ->method('getGroups')
            ->willReturn([$groupModelMock]);
        $this->storeManagerMock->expects($this->once())
            ->method('getWebsites')
            ->willReturn([$websiteModelMock]);

        $this->urlBuilderMock->expects($this->at(0))
            ->method('getUrl')
            ->with(
                '*/*/*',
                ['_query' => ['website_id' => -1, 'group_id' => '', 'store_id' => ''], '_current' => true]
            )->willReturn($url);
        $this->urlBuilderMock->expects($this->at(1))
            ->method('getUrl')
            ->with(
                '*/*/*',
                ['_query' => ['website_id' => $websiteId, 'group_id' => '', 'store_id' => ''], '_current' => true]
            )->willReturn($url);
        $this->urlBuilderMock->expects($this->at(2))
            ->method('getUrl')
            ->with(
                '*/*/*',
                ['_query' => ['website_id' => '', 'group_id' => $groupId, 'store_id' => ''], '_current' => true]
            )->willReturn($url);
        $this->urlBuilderMock->expects($this->at(3))
            ->method('getUrl')
            ->with(
                '*/*/*',
                ['_query' => ['website_id' => '', 'group_id' => '', 'store_id' => $storeId], '_current' => true]
            )->willReturn($url);

        $this->assertTrue(is_array($this->model->getItems()));
    }

    /**
     * Testing of getCurrentItemKey method from request
     * @dataProvider getCurrentItemKeyFromRequestDataProvider
     *
     * @param [] $params
     * @param string $expected
     */
    public function testGetCurrentItemKeyFromRequest($params, $expected)
    {
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->willReturnMap($params);
        $this->sessionMock->expects($this->once())
            ->method('setData')
            ->with(Store::SESSION_KEY, $expected)
            ->willReturnSelf();

        $this->assertEquals($expected, $this->model->getCurrentItemKey());
    }

    /**
     * Data provider for testGetCurrentItemKeyFromRequest method
     *
     * @return array
     */
    public function getCurrentItemKeyFromRequestDataProvider()
    {
        return [
            [
                [['website_id', null, -1], ['group_id', null, null], ['store_id', null, null]],
                Store::DEFAULT_TYPE
            ],
            [
                [['website_id', null, 1], ['group_id', null, null], ['store_id', null, null]],
                Store::WEBSITE_TYPE . '_1'
            ],
            [
                [['website_id', null, null], ['group_id', null, 1], ['store_id', null, null]],
                Store::GROUP_TYPE . '_1'
            ],
            [
                [['website_id', null, null], ['group_id', null, null], ['store_id', null, 1]],
                Store::STORE_TYPE . '_1'
            ]
        ];
    }

    /**
     * Testing of getCurrentItemKey method from session
     */
    public function testGetCurrentItemKeyFromSession()
    {
        $params = [
            ['website_id', null, null],
            ['group_id', null, null],
            ['store_id', null, null]
        ];
        $value = Store::STORE_TYPE . '_1';

        $this->requestMock->expects($this->exactly(3))
            ->method('getParam')
            ->willReturnMap($params);
        $this->sessionMock->expects($this->once())
            ->method('getData')
            ->with(Store::SESSION_KEY)
            ->willReturn($value);

        $this->assertEquals($value, $this->model->getCurrentItemKey());
    }

    /**
     * Testing of getStoreIds method from session
     */
    public function testGetStoreIds()
    {
        $params = [
            ['website_id', null, null],
            ['group_id', null, null],
            ['store_id', null, null]
        ];
        $storeId = 1;
        $value = Store::STORE_TYPE . '_' . $storeId;

        $this->requestMock->expects($this->exactly(3))
            ->method('getParam')
            ->willReturnMap($params);
        $this->sessionMock->expects($this->once())
            ->method('getData')
            ->with(Store::SESSION_KEY)
            ->willReturn($value);

        $storeModelMock = $this->getMock(StoreModel::class, ['getId'], [], '', false);
        $storeModelMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($storeModelMock);

        $this->assertEquals([$storeId], $this->model->getStoreIds());
    }
}
