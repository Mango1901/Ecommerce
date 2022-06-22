<?php
namespace Magenest\MegaMenu\Model\Import;

use Magenest\MegaMenu\Model\Import\MegaMenu\RowValidatorInterface as ValidatorInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Model\AbstractModel;

class MegaMenu extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    const TABLE_Entity = 'magenest_mega_menu';

    const ID = 'menu_id';
    const MENU_NAME = 'menu_name';
    const STORE_ID = 'store_id';
    const MENU_TEMPLATE = 'menu_template';
    const ID_TEMP = 'id_temp';
    const CUSTOM_CSS = 'custom_css';
    const MENU_TOP = 'menu_top';
    const MENU_ALIAS = 'menu_alias';
    const EVENT = 'event';
    const SCROLL_TO_FIXED = 'scroll_to_fixed';
    const MOBILE_TEMPLALE = 'mobile_template';
    const DISABLE_IBLOCKS = 'disable_iblocks';
    const CHILD_ID_MENU = 'child_menu_id';
    const TITLE = 'title';
    const LEVEL = 'level';
    const SORT = 'sort';
    const LABEL = 'label';
    const MAIN_CONTENT_TYPE = 'mainContentType';
    const MAIN_COLUMN = 'mainColumn';
    const MAIN_CONTENT_HTML = 'mainContentHtml';
    const MAIN_CONTENT_WIDTH = 'mainContentWidth';
    const CSS_CLASS = 'cssClass';
    const LEFT_CLASS = 'leftClass';
    const LEFT_WIDTH = 'leftWidth';
    const LEFT_CONTENT_HTML = 'leftContentHtml';
    const RIGHT_CLASS = 'rightClass';
    const RIGHT_WIDTH = 'rightWidth';
    const RIGHT_CONTENT_HTML = 'rightContentHtml';
    const TEXT_COLOR = 'textColor';
    const HOVER_TEXT_COLOR = 'hoverTextColor';
    const HOVER_ICON_COLOR = 'hoverIconColor';
    const MAIN_ENABLE = 'mainEnable';
    const LEFT_ENABLE = 'leftEnable';
    const RIGHT_ENABLE = 'rightEnable';
    const LINK = 'link';
    const CSS_INLINE = 'cssInline';
    const ICON = 'icon';
    const FOOTER_ENABLE = 'footerEnable';
    const ROOTER_CONTENT_HTML = 'footerContentHtml';
    const FOOTER_CLASS = 'footerClass';
    const HEADER_ENABLE = 'headerEnable';
    const HEADER_CLASS = 'headerClass';
    const HEADER_CONTENT_HTML = 'headerContentHtml';
    const COLOR = 'color';
    const BACKGROUND_COLOR = 'backgroundColor';
    const BACKGROUND_SIZE = 'backgroundSize';
    const BACKGROUND_OPACITY = 'backgroundOpacity';
    const BACKGROUND_POSITION_X = 'backgroundPositionX';
    const BACKGROUND_POSITION_Y = 'backgroundPositionY';
    const BACKGROUND_IMAGE = 'backgroundImage';
    const BACKGROUND_REPEAT = 'backgroundRepeat';
    const ANIMATION_DELAY_TIME = 'animationDelayTime';
    const ANIMATION_SPEED = 'animationSpeed';
    const ANIMATION_IN = 'animationIn';
    const MAIN_PARENT_CATEGORY = 'mainParentCategory';
    const ITEM_ENABLE = 'itemEnable';
    const LINK_TARGET = 'linkTarget';
    const HIDE_TEXT = 'hideText';
    const HAS_CHILD = 'hasChild';
    const ANIMATE_DELAY_TIME = 'animateDelayTime';
    const ANIMATE_SPEED = 'animateSpeed';

    protected $_specialAttributes = [
        self::CHILD_ID_MENU,
        self::TITLE,
        self::LEVEL,
        self::SORT,
        self::LABEL,
        self::MAIN_CONTENT_TYPE,
        self::MAIN_COLUMN,
        self::MAIN_CONTENT_HTML,
        self::MAIN_CONTENT_WIDTH,
        self::CSS_CLASS,
        self::LEFT_CLASS,
        self::LEFT_WIDTH,
        self::LEFT_CONTENT_HTML,
        self::RIGHT_CLASS,
        self:: RIGHT_WIDTH,
        self:: RIGHT_CONTENT_HTML,
        self::TEXT_COLOR,
        self::HOVER_TEXT_COLOR,
        self::HOVER_ICON_COLOR,
        self::MAIN_ENABLE,
        self::LEFT_ENABLE,
        self::RIGHT_ENABLE,
        self::LINK,
        self::CSS_INLINE,
        self::ICON,
        self::FOOTER_ENABLE,
        self::ROOTER_CONTENT_HTML,
        self::FOOTER_CLASS,
        self::HEADER_ENABLE,
        self::HEADER_CLASS,
        self::HEADER_CONTENT_HTML,
        self::COLOR,
        self::BACKGROUND_COLOR,
        self::BACKGROUND_SIZE,
        self::BACKGROUND_OPACITY,
        self::BACKGROUND_POSITION_X,
        self::BACKGROUND_POSITION_Y,
        self::BACKGROUND_IMAGE,
        self::BACKGROUND_REPEAT,
        self::ANIMATION_DELAY_TIME,
        self::ANIMATION_SPEED,
        self::ANIMATION_IN,
        self::MAIN_PARENT_CATEGORY,
        self::ITEM_ENABLE,
        self::LINK_TARGET,
        self::HIDE_TEXT,
        self::HAS_CHILD,
        self::ANIMATE_DELAY_TIME,
        self::ANIMATE_SPEED,
    ];

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        ValidatorInterface::ERROR_MESSAGE_IS_EMPTY => 'Magenest Mega Menu Is Empty',
    ];
    protected $_permanentAttributes = [self::ID];


    protected $megaMenuCollection;
    protected $menuEntity;
    protected $needColumnCheck = true;
    protected $_menuItemFactory;


    protected $validColumnNames = [
        self::ID,
        self::MENU_NAME,
        self::STORE_ID,
        self::MENU_TEMPLATE,
        self::ID_TEMP,
        self::CUSTOM_CSS,
        self::MENU_TOP,
        self::MENU_ALIAS,
        self::EVENT,
        self::SCROLL_TO_FIXED,
        self::MOBILE_TEMPLALE,
        self::DISABLE_IBLOCKS,
        self::CHILD_ID_MENU,
        self::TITLE,
        self::LEVEL,
        self::SORT,
        self::LABEL,
        self::MAIN_CONTENT_TYPE,
        self::MAIN_COLUMN,
        self::MAIN_CONTENT_HTML,
        self::MAIN_CONTENT_WIDTH,
        self::CSS_CLASS,
        self::LEFT_CLASS,
        self::LEFT_WIDTH,
        self::LEFT_CONTENT_HTML,
        self::RIGHT_CLASS,
        self:: RIGHT_WIDTH,
        self:: RIGHT_CONTENT_HTML,
        self::TEXT_COLOR,
        self::HOVER_TEXT_COLOR,
        self::HOVER_ICON_COLOR,
        self::MAIN_ENABLE,
        self::LEFT_ENABLE,
        self::RIGHT_ENABLE,
        self::LINK,
        self::CSS_INLINE,
        self::ICON,
        self::FOOTER_ENABLE,
        self::ROOTER_CONTENT_HTML,
        self::FOOTER_CLASS,
        self::HEADER_ENABLE,
        self::HEADER_CLASS,
        self::HEADER_CONTENT_HTML,
        self::COLOR,
        self::BACKGROUND_COLOR,
        self::BACKGROUND_SIZE,
        self::BACKGROUND_OPACITY,
        self::BACKGROUND_POSITION_X,
        self::BACKGROUND_POSITION_Y,
        self::BACKGROUND_IMAGE,
        self::BACKGROUND_REPEAT,
        self::ANIMATION_DELAY_TIME,
        self::ANIMATION_SPEED,
        self::ANIMATION_IN,
        self::MAIN_PARENT_CATEGORY,
        self::ITEM_ENABLE,
        self::LINK_TARGET,
        self::HIDE_TEXT,
        self::HAS_CHILD,
        self::ANIMATE_DELAY_TIME,
        self::ANIMATE_SPEED,
    ];
    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;
    protected $_validators = [];
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_connection;
    protected $_resource;
    protected $logger;


    /**
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Psr\Log\LoggerInterface $logger,
        \Magenest\MegaMenu\Model\ResourceModel\MegaMenu\CollectionFactory $megaMenuCollection,
        \Magenest\MegaMenu\Model\MenuEntity $menuEntity,
        \Magenest\MegaMenu\Model\MenuEntityFactory $menuEntityFactory,
        array $data = []
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_resource = $resource;
        $this->_connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->megaMenuCollection = $megaMenuCollection;
        $this->_menuItemFactory = $menuEntityFactory;
        $this->menuEntity = $menuEntity;
        $this->logger = $logger;
    }

    public function getValidColumnNames()
    {
        return $this->validColumnNames;
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'magenest_mega_menu';
    }

    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        $title = false;
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
        $this->_validatedRows[$rowNum] = true;
        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * Create Advanced magenest_mega_menu data from raw data.
     *
     * @return bool Result of operation.
     * @throws \Exception
     */
    protected function _importData()
    {
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->deleteEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            $this->replaceEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->saveEntity();
        }
        return true;
    }

    /**
     * Save Message
     *
     * @return $this
     */
    public function saveEntity()
    {
        $this->saveAndReplaceEntity();
        $this->saveMenuData();
        return $this;
    }


    /**
     * Replace newsletter subscriber
     *
     * @return $this
     */
    public function replaceEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }

    /**
     * Deletes newsletter subscriber data from raw data.
     *
     * @return $this
     */
    public function deleteEntity()
    {
        $listTitle = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);
                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $rowTtile = $rowData[self::ID];
                    $listTitle[] = $rowTtile;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }
        if ($listTitle) {
            $this->deleteEntityFinish(array_unique($listTitle), self::TABLE_Entity);
        }
        return $this;
    }

    /**
     * Save and replace data message
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function saveAndReplaceEntity()
    {
        $behavior = $this->getBehavior();
        $listTitle = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_MESSAGE_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
                if ($this->_isChildMenuEntity($rowData)) {
                    continue;
                }
                $rowTitle = $rowData[self::ID];
                $listTitle[] = $rowTitle;
                $entityList[$rowTitle][] = [
                    self::ID => $rowData[self::ID],
                    self::MENU_NAME => $rowData[self::MENU_NAME],
                    self::STORE_ID => $rowData[self::STORE_ID],
                    self::MENU_TEMPLATE => $rowData[self::MENU_TEMPLATE],
                    self::ID_TEMP => $rowData[self::ID_TEMP],
                    self::CUSTOM_CSS => $rowData[self::CUSTOM_CSS],
                    self::MENU_TOP => $rowData[self::MENU_TOP],
                    self::MENU_ALIAS => $rowData[self::MENU_ALIAS],
                    self::EVENT => $rowData[self::EVENT],
                    self::SCROLL_TO_FIXED => $rowData[self::SCROLL_TO_FIXED],
                    self::MOBILE_TEMPLALE => $rowData[self::MOBILE_TEMPLALE],
                    self::DISABLE_IBLOCKS => $rowData[self::DISABLE_IBLOCKS],
                ];
            }
            if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
                if ($listTitle) {
                    if ($this->deleteEntityFinish(array_unique($listTitle), self::TABLE_Entity)) {
                        $this->saveEntityFinish($entityList, self::TABLE_Entity);
                    }
                }
            } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $behavior) {
                $this->saveEntityFinish($entityList, self::TABLE_Entity);
            }
        }
        return $this;
    }

    protected function saveEntityFinish(array $entityData, $table)
    {
        if ($entityData) {
            $tableName = $this->_connection->getTableName($table);
            $entityIn = [];
            foreach ($entityData as $id => $entityRows) {
                foreach ($entityRows as $row) {
                    $entityIn[] = $row;
                }
            }
            if ($entityIn) {
                $this->_connection->insertOnDuplicate($tableName, $entityIn, [
                    self::ID,
                    self::MENU_NAME,
                    self::STORE_ID,
                    self::MENU_TEMPLATE,
                    self::ID_TEMP,
                    self::CUSTOM_CSS,
                    self::MENU_TOP,
                    self::MENU_ALIAS,
                    self::EVENT,
                    self::SCROLL_TO_FIXED,
                    self::MOBILE_TEMPLALE,
                    self::DISABLE_IBLOCKS,
                ]);
            }
        }
        return $this;
    }

    protected function deleteEntityFinish(array $listTitle, $table)
    {
        if ($table && $listTitle) {
            try {
                $this->countItemsDeleted += $this->_connection->delete(
                    $this->_connection->getTableName($table),
                    $this->_connection->quoteInto('menu_id IN (?)', $listTitle)
                );
                return true;
            } catch (\Exception $e) {
                $this->logger->info($e);
            }
        }
        return false;
    }

    protected function saveMenuData()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                try {
                    if (!$this->validateRow($rowData, $rowNum)) {
                        $this->addRowError(ValidatorInterface::ERROR_MESSAGE_IS_EMPTY, $rowNum);
                        continue;
                    }
                    if ($this->getErrorAggregator()->hasToBeTerminated()) {
                        $this->getErrorAggregator()->addRowToSkip($rowNum);
                        continue;
                    }
                    $this->_setMenuEntity($rowData);
                }
                catch (\Exception $e) {
                    $this->logger->error($e->getMessage(), $rowData);
                    throw new \Exception($e);
                }
            }
        }
    }

    protected function _setMenuEntity($rowData){
        $menuData = $this->_menuItemFactory->create();
        $menuData->addData($rowData);
        $menuData->save();
    }


    protected function _isChildMenuEntity($rowData)
    {
        if (is_array($rowData) || array_key_exists(self::CHILD_ID_MENU, $rowData)) {
            $isChild = !is_null($rowData[self::CHILD_ID_MENU]) && !empty($rowData[self::CHILD_ID_MENU]);
            return $isChild && !empty($rowData[self::ID]);
        }
        return false;
    }
}
