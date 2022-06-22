<?php
namespace Magenest\Affiliate\Model\ResourceModel;

use Magento\Cms\Model\Wysiwyg\Images\Storage;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Magenest\Affiliate\Helper\Data;
use Magenest\Affiliate\Model\Account\Status;
use Psr\Log\LoggerInterface;

class Account extends AbstractDb
{
    /** @type Data */
    protected $_helper;

    /** @var Filesystem */
    private $filesystem;

    /** @var Storage */
    private $imageStorage;

    /** @var File */
    private $file;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * Account constructor.
     *
     * @param File $file
     * @param Data $helper
     * @param Context $context
     * @param Storage $imageStorage
     * @param Filesystem $filesystem
     * @param LoggerInterface $logger
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        File $file,
        Data $helper,
        Context $context,
        Storage $imageStorage,
        Filesystem $filesystem,
        LoggerInterface $logger,
        ManagerInterface $eventManager
    ) {
        $this->file = $file;
        $this->logger = $logger;
        $this->_helper = $helper;
        $this->filesystem = $filesystem;
        $this->imageStorage = $imageStorage;
        $this->eventManager = $eventManager;

        parent::__construct($context);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_affiliate_account', 'account_id');
    }

    /**
     * @inheritdoc
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setUpdatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
        if ($object->isObjectNew()) {
            $object->setCreatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
            if (!$object->hasData('group_id')) {
                $object->setGroupId($this->_helper->getDefaultGroup());
            }
        }
        if (!$object->hasData('status')) {
            $status = $this->_helper->isAdminApproved() ? Status::NEED_APPROVED : Status::ACTIVE;
            $object->setStatus($status);
        }
        if (!$object->hasData('code')) {
            $object->setCode($this->generateAffiliateCode());
        }

        return parent::_beforeSave($object);
    }

    /**
     * @inheritdoc
     */
    protected function _afterSave(AbstractModel $object)
    {
        parent::_afterSave($object);
        if ($object->isObjectNew()) {
            $account = $this->_helper->getAffiliateAccount($object->getId());
            if ($parentId = $object->getParent()) {
                $parent = $this->_helper->getAffiliateAccount($parentId);
                if ($parent && $parent->getId()) {
                    $account->setTree($parent->getTree() . '/' . $account->getId())->save();
                }
            } else {
                $account->setTree($account->getId())->save();
            }
        }

        if ($object->dataHasChangedFor('id_front')) {
            $this->deleteImage($object->getOrigData('id_front'));
        }
        if ($object->dataHasChangedFor('id_back')) {
            $this->deleteImage($object->getOrigData('id_back'));
        }

        if ($object->dataHasChangedFor('status') && $object->getStatus() == 1) {
            $this->eventManager->dispatch('affiliate_account_change_status', ['object' => $object]);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function generateAffiliateCode()
    {
        $flag = true;
        do {
            $code    = substr(str_shuffle(hash('md5', microtime())), 0, $this->_helper->getCodeLength());
            $account = $this->_helper->getAffiliateAccount($code, 'code');
            if (!$account->getId()) {
                $flag = false;
            }
        } while ($flag);

        return $code;
    }

    /**
     * @param AbstractModel $object
     * @return Account
     */
    public function _afterDelete(AbstractModel $object)
    {
        foreach (\Magenest\Affiliate\Model\Account::IMAGE_FIELDS as $field) {
            $this->deleteImage($object->getData($field));
        }
        return parent::_afterDelete($object);
    }

    /**
     * @param $path
     */
    public function deleteImage($path)
    {
        try {
            $filePath = $this->filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath("affiliate" . $path);
            if ($this->file->fileExists($filePath)) {
                $this->imageStorage->deleteFile($filePath);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }
}
