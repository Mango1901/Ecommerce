<?php
/**
 * Created by PhpStorm.
 * User: hahoang
 * Date: 23/01/2019
 * Time: 11:49
 */

namespace Magenest\MapList\Controller\Adminhtml\Export;

use Magenest\MapList\Controller\Adminhtml\Location;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\LocalizedException;

class Export extends Location
{


    protected $fileFactory;
    protected $csvProcessor;
    protected $directoryList;
    public function __construct(
        Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magenest\MapList\Model\LocationFactory $locationFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        LoggerInterface $logger,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->fileFactory = $fileFactory;
        $this->csvProcessor = $csvProcessor;
        $this->directoryList = $directoryList;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $locationFactory, $logger);
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return string
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        try {
            $fileName = 'store_locator_and_maplist.csv';
            $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)
                . "/" . $fileName;

            $storeData = $this->getStoreData();


            $this->csvProcessor
                ->setDelimiter(',')
                ->setEnclosure('"')
                ->saveData(
                    $filePath,
                    $storeData
                );
            return $this->fileFactory->create(
                $fileName,
                array(
                    'type' => "filename",
                    'value' => $fileName,
                    'rm' => true,
                ),
                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                'application/octet-stream'
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            $this->messageManager->addError(__('Can\'t export data to file csv !'));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $this->messageManager->addNotice("Export store and maplist successful!");
        $resultRedirect->setPath('*/*/');
        return $resultRedirect;
    }
    protected function getStoreData()
    {
        $storeCollection = $this->locationFactory->create()->getCollection();
        $index = 1;
        $result = array();
        $result[] = array(
            'title',
            'description',
            'latitude',
            'longitude',
            'short_description',
            'address',
            'website',
            'email',
            'phone_number',
            'is_active',
            'created_at',
            'updated_at',
            'country',
            'state_province',
            'city',
            'zip',
            'is_use_default_opening_hours',
            'opening_hours',
            'special_date',
            'gallery',
            'meta_title',
            'meta_keywords',
            'meta_description',
            'parking',
            'atm',
            'new_arrivals',
            'payment_methods',
            'icon',
            'store'
        );
        foreach ($storeCollection as $store) {
            $data = $store->getData();
            unset($data['location_id']);
            unset($data['big_image']);
            unset($data['brands']);
            unset($data['enable_seo']);
            $result[$index] = $data;
            $result[$index]['icon'] = $result[$index]['small_image'];
            unset($result[$index]['small_image']);
            $gallery = str_replace(';', ',',$result[$index]['gallery']);
            $result[$index]['gallery'] = $gallery;
            $storeTmp = $result[$index]['store'];
            unset($result[$index]['store']);
            $result[$index]['store'] = $storeTmp;
            $index++;
        }
        return $result;
    }
}
