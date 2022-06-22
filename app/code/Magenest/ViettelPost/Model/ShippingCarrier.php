<?php
/**
 * Created by Magenest JSC.
 * Author: Jacob
 * Date: 07/11/2019
 * Time: 10:54
 */
namespace Magenest\ViettelPost\Model;

use Magenest\ViettelPost\Model\ShippingCarrierInterface;
use Magenest\ViettelPost\Helper\Data;
use Magento\Framework\App\RequestInterface;

class ShippingCarrier implements ShippingCarrierInterface
{
    const CARRIER_NAME = 'viettel_post';

    protected $request;
    protected $viettelPostHelper;
    protected $viettelOrderFactory;

    private $dataObj;
    private $orderId;

    public function __construct(
        RequestInterface $request,
        \Magenest\ViettelPost\Helper\Data $viettelPostHelper,
        \Magenest\ViettelPost\Model\ViettelOrderFactory $viettelOrderFactory
    )
    {
        $this->request = $request;
        $this->viettelPostHelper = $viettelPostHelper;
        $this->viettelOrderFactory = $viettelOrderFactory;
    }

    /**
     * @deprecated
     * @param $order
     * @return mixed|void
     * @throws \Exception
     */
    public function createOrder($order)
    {
        $this->viettelPostHelper->login();
        $dataReq = $this->parseShipmentRequest($order);
        $response = $this->viettelPostHelper->sendOrder($dataReq);
        $respBody = isset($response['body']) ? $response['body'] : "";
        $respBody = json_decode($respBody, true);
        $respData = isset($respBody['data'])?$respBody['data']:[];
        $viettelOrder = $this->viettelOrderFactory->create();
        $data = array_change_key_case($respData,CASE_LOWER);
        $data['order_id'] = $order->getId();
        $viettelOrder->addData($data);
        $viettelOrder->isObjectNew(true);
        $viettelOrder->save();
    }

    public function createShipment($order, $shipment)
    {
        $this->viettelPostHelper->login(true);
        $dataReq = $this->parseShipmentRequest($order, $shipment);
        $response = $this->viettelPostHelper->sendOrder($dataReq);
        $respBody = isset($response['body']) ? $response['body'] : "";
        $respBody = json_decode($respBody, true);
        $respData = isset($respBody['data'])?$respBody['data']:[];
        $viettelOrderShipment = $this->viettelOrderFactory->create();
        $data = array_change_key_case($respData,CASE_LOWER);
        $data['shipment_id'] = $shipment->getId();
        $viettelOrderShipment->addData($data);
        $viettelOrderShipment->isObjectNew(true);
        $viettelOrderShipment->save();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     */
    private function parseShipmentRequest($order, $shipment){
        $orderPrefix = $this->viettelPostHelper->getVTOrderPrefix();
//        $cityName = $shippingAddress->getCity();
//        $districtName = $shippingAddress->getDistrict();
//        $wardsName = $shippingAddress->getWard();
        $vtProvinceId = $this->request->getParam('province_id');
        $vtDistrictId = $this->request->getParam('district_id');
        $vtWardsId = $this->request->getParam('wards_id');
        if(!$vtProvinceId || !$vtDistrictId || !$vtWardsId){
            throw new \Exception("Please validate shipping address fields");
        }

        $orderNote = $this->request->getParam('order_note');
        $orderItems = $order->getAllVisibleItems();
        $firstItem = $orderItems[0];
        $listItem = [];
        foreach ($orderItems as $item){
            $listItem[] = [
                'PRODUCT_NAME' => $item->getName(),
                'PRODUCT_QUANTITY' => round($item->getQtyShipped()),
                'PRODUCT_PRICE' => round($item->getPrice()),
                'PRODUCT_WEIGHT' => round($item->getWeight()*1000)?:'0',
            ];
        }
        $shippingAddress = $order->getShippingAddress();

        $data = [];
        $data['ORDER_NUMBER'] = $orderPrefix.$order->getIncrementId()."-".$shipment->getIncrementId();
        $data['GROUPADDRESS_ID'] = '0';
        $data['CUS_ID'] = '0';
        $data['DELIVERY_DATE'] = '';

        $data['SENDER_FULLNAME'] = $this->viettelPostHelper->getSenderFullName();
        $data['SENDER_ADDRESS'] = $this->viettelPostHelper->getSenderAddress();
        $data['SENDER_PHONE'] = $this->viettelPostHelper->getSenderPhone();
        $data['SENDER_EMAIL'] = $this->viettelPostHelper->getSenderEmail();
        $data['SENDER_WARD'] = $this->viettelPostHelper->getSenderWards();
        $data['SENDER_DISTRICT'] = $this->viettelPostHelper->getSenderDistrict();
        $data['SENDER_PROVINCE'] = $this->viettelPostHelper->getSenderProvince();
        $data['SENDER_LATITUDE'] = '0';
        $data['SENDER_LONGITUDE'] = '0';

        $data['RECEIVER_FULLNAME'] = $order->getCustomerName();
        $data['RECEIVER_ADDRESS'] = $this->viettelPostHelper->getFormattedAddress($shippingAddress);
        $data['RECEIVER_PHONE'] = $shippingAddress->getTelephone();
        $data['RECEIVER_EMAIL'] = $order->getCustomerEmail();
        $data['RECEIVER_WARD'] = $vtWardsId;
        $data['RECEIVER_DISTRICT'] = $vtDistrictId;
        $data['RECEIVER_PROVINCE'] = $vtProvinceId;
        $data['RECEIVER_LATITUDE'] = '0';
        $data['RECEIVER_LONGITUDE'] = '0';

        $data['PRODUCT_NAME'] = $firstItem->getName();
        $data['PRODUCT_DESCRIPTION'] = $firstItem->getSku();
        $data['PRODUCT_QUANTITY'] = round($firstItem->getQtyShipped());
        $data['PRODUCT_PRICE'] = round($firstItem->getPrice());
        $data['PRODUCT_WEIGHT'] = $this->request->getParam('product_weight');
        $data['PRODUCT_LENGTH'] =  $this->request->getParam('product_length');
        $data['PRODUCT_WIDTH'] = $this->request->getParam('product_width');
        $data['PRODUCT_HEIGHT'] = $this->request->getParam('product_height');
        $data['PRODUCT_TYPE'] = 'HH';

// Loại vận đơn/ Oder type
// 1: Không thu tiền/ Uncollect money
// 2: Thu hộ tiền cước và tiền hàng/ Collect express fee and price of goods.
// 3: Thu hộ tiền hàng/ Collect price of goods
// 4: Thu hộ tiền cước/ Collect express fee
        $data['ORDER_PAYMENT'] = $this->viettelPostHelper->isCOD($order)?"2":"1";
        $data['ORDER_SERVICE'] = $this->viettelPostHelper->getOrderService();
        $data['ORDER_SERVICE_ADD'] = $this->request->getParam('order_service_add', '');
        $data['ORDER_VOUCHER'] = '';
        $data['ORDER_NOTE'] = $orderNote;
        $data['MONEY_COLLECTION'] = $this->viettelPostHelper->isCOD($order)?round($order->getBaseGrandTotal()):0;
        $data['MONEY_TOTALFEE'] = 0;
        $data['MONEY_FEECOD'] = 0;
        $data['MONEY_FEEVAS'] = 0;
        $data['MONEY_FEEINSURRANCE'] = 0;
        $data['MONEY_FEE'] = 0;
        $data['MONEY_FEEOTHER'] = 0;
        $data['MONEY_TOTALVAT'] = 0;
        $data['MONEY_TOTAL'] = 0;
        $data['LIST_ITEM'] = $listItem;
        return $data;
    }

    public function handleShippingStatusResponse($response)
    {
        // TODO: Implement handleShippingStatusResponse() method.
    }

    /**
     * @param array|string $orderNumber
     */
    public function requestShippingLabel($orderNumber){
        if(!is_array($orderNumber)){
            $orderArr = [$orderNumber];
        }else{
            $orderArr = $orderNumber;
        }
        $request = $this->buildRequestShippingLabel($orderArr);
        $token = $this->viettelPostHelper->login(true);
        $resp = $this->viettelPostHelper->sendRequest(Data::PRINT_SHIPPING_LABEL_URL,
            "POST",
            json_encode($request),
            $token
        );
        return $resp;
    }

    private function buildRequestShippingLabel($orderArr){
        $request = [
            'TYPE' => '1',
            'ORDER_ARRAY' => $orderArr,
            'EXPIRY_TIME' => strtotime('+1 year')."000",
            'PRINT_TOKEN' => $this->viettelPostHelper->getPrintToken()
        ];
        return $request;
    }

    public function getDataObj($orderId){
        if(!$this->dataObj){
            $dataObj = $this->viettelOrderFactory->create()->load($orderId);
            $this->dataObj = $dataObj;
        }
        return $this->dataObj;
    }

    public function getShipmentData($shipmentId, $field = null)
    {
        $viettelOrder = $this->getDataObj($shipmentId);
        if(!$field){
            return $viettelOrder->getData();
        }else{
            return $viettelOrder->getData($field);
        }
    }

    public function getShippingLabelUrl($shipmentId){
        return $this->getShipmentData($shipmentId, 'shipping_label_url');
    }

    public function getOrderNumber($shipmentId){
        return $this->getShipmentData($shipmentId, 'order_number');
    }
}