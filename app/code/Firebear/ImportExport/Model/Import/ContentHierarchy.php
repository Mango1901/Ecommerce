<?php
/**
 * @copyright: Copyright © 2019 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Import;

use Exception;
use Firebear\ImportExport\Model\IntegrationFactory;
use Firebear\ImportExport\Model\ResourceModel\Import\Data as ImportData;
use Firebear\ImportExport\Traits\Import\Entity as ImportTrait;
use InvalidArgumentException;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\ResourceModel\Page as PageResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\ImportExport\Helper\Data as ImportExportData;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Magento\ImportExport\Model\ImportFactory;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\VersionsCms\Api\Data\HierarchyNodeInterface as NodeInterface;
use Magento\VersionsCms\Helper\Hierarchy as HierarchyHelper;
use Magento\VersionsCms\Model\Hierarchy\Node;
use Magento\VersionsCms\Model\Hierarchy\NodeRepository;
use Magento\VersionsCms\Model\ResourceModel\Hierarchy\Node as NodeResource;
use Magento\VersionsCms\Model\ResourceModel\Hierarchy\Node\Collection as NodeCollection;
use Zend\Serializer\Serializer;

/**
 * Class ContentHierarchy
 *
 * @package Firebear\ImportExport\Model\Import
 */
class ContentHierarchy extends AbstractEntity implements ImportAdapterInterface
{
    use ImportTrait;

    /**
     * Entity type code
     */
    const ENTITY_TYPE_CODE = 'content_hierarchy';

    /**
     * List of available behaviors
     *
     * @var string[]
     */
    protected $_availableBehaviors = [
        Import::BEHAVIOR_APPEND,
        Import::BEHAVIOR_REPLACE,
        Import::BEHAVIOR_DELETE,
    ];

    /**
     * Import export data
     *
     * @var ImportExportData
     */
    protected $_importExportData;

    /**
     * @var Helper
     */
    protected $_resourceHelper;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var HierarchyHelper
     */
    public $hierarchyHelper;

    /**
     * @var NodeResource
     */
    private $nodeResource;

    /**
     * @var NodeRepository
     */
    private $nodeRepository;

    /**
     * @var IntegrationFactory
     */
    private $integrationFactory;

    /**
     * @var PageResource
     */
    private $pageResource;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     *
     * @var array
     */
    private $contentHierarchy;

    /**
     * @var array
     */
    private $contentHierarchyOld;

    /**
     * Json Serializer
     *
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var array
     */
    private $nodeScopes = [
        Node::NODE_SCOPE_DEFAULT,
        Node::NODE_SCOPE_STORE,
        Node::NODE_SCOPE_WEBSITE,
    ];

    /**
     * @var array
     */
    private $nodeFields = [
        NodeInterface::NODE_ID,
        NodeInterface::PARENT_NODE_ID,
        NodeInterface::PAGE_ID,
        NodeInterface::IDENTIFIER,
        NodeInterface::LABEL,
        NodeInterface::LEVEL,
        NodeInterface::SORT_ORDER,
        NodeInterface::REQUEST_URL,
        NodeInterface::XPATH,
        NodeInterface::SCOPE,
        NodeInterface::SCOPE_ID,
    ];

    /**
     * @var array
     */
    private $metadataFields = [];

    /**
     * Error codes
     */
    const ERROR_IDENTIFIERS_IS_EMPTY = 'idAndRequestUrlIsEmpty';
    const ERROR_CMS_PAGE_NOT_FOUND = 'cmsPageNotFound';
    const ERROR_PARENT_NOT_FOUND = 'parentNodeNotFound';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        self::ERROR_IDENTIFIERS_IS_EMPTY => 'Columns %s is empty',
        self::ERROR_CMS_PAGE_NOT_FOUND => 'The CMS page with the \'%s\' ID doesn\'t exist',
        self::ERROR_PARENT_NOT_FOUND => 'The CMS node with the \'%s\' ID doesn\'t exist',
    ];

    /**
     * ContentHierarchy constructor.
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param ImportFactory $importFactory
     * @param ImportExportData $importExportData
     * @param ImportData $importData
     * @param PageResource $pageResource
     * @param PageRepositoryInterface $pageRepository
     * @param StoreRepositoryInterface $storeRepository
     * @param IntegrationFactory $integrationFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        ImportFactory $importFactory,
        ImportExportData $importExportData,
        ImportData $importData,
        PageResource $pageResource,
        PageRepositoryInterface $pageRepository,
        StoreRepositoryInterface $storeRepository,
        IntegrationFactory $integrationFactory,
        array $data = []
    ) {
        parent::__construct(
            $context->getStringUtils(),
            $scopeConfig,
            $importFactory,
            $context->getResourceHelper(),
            $context->getResource(),
            $context->getErrorAggregator(),
            $data
        );
        $this->integrationFactory = $integrationFactory;
        $this->hierarchyHelper = $data['hierarchyHelper'];
        $this->nodeResource = $data['nodeResource'];
        $this->nodeRepository = $data['nodeRepository'];
        $this->pageResource = $pageResource;
        $this->pageRepository = $pageRepository;
        $this->storeRepository = $storeRepository;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $context->getResourceHelper();
        $this->_dataSourceModel = $importData;
        $this->resource = $context->getResource();
        $this->output = $context->getOutput();
        $this->serializer = $context->getSerializer();
        $this->metadataFields = $this->hierarchyHelper->getMetadataFields();

        foreach ($this->_messageTemplates as $errorCode => $message) {
            $this->getErrorAggregator()->addErrorMessageTemplate($errorCode, $message);
        }
        $this->initContentHierarchy();
    }

    /**
     * Initialize Content Hierarchy
     */
    private function initContentHierarchy()
    {
        if (empty($this->contentHierarchy)) {
            /** @var NodeCollection $nodeCollection */
            $nodeCollection = $this->integrationFactory->create(NodeCollection::class);
            foreach ($nodeCollection as $node) {
                $requestUrl = $node->getRequestUrl();
                $scope = $node->getScope();
                $scopeId = $node->getScopeId();
                if (!empty($requestUrl)) {
                    $this->contentHierarchy[$requestUrl][$scope][$scopeId] = $node->getId();
                } else {
                    $this->contentHierarchy[$scope][$scopeId] = $node->getId();
                }
            }
        }
    }

    /**
     * Imported entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return self::ENTITY_TYPE_CODE;
    }

    /**
     * Retrieve All Fields Source
     *
     * @return array
     */
    public function getAllFields()
    {
        return array_unique($this->nodeFields + $this->metadataFields);
    }

    /**
     * Import data rows
     *
     * @return boolean
     * @throws LocalizedException
     */
    protected function _importData()
    {
        $this->_validatedRows = null;
        if (Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->delete();
        } elseif (Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            $this->replaceProcess();
        } else {
            $this->save();
        }

        return true;
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    protected function replaceProcess()
    {
        if (empty($this->contentHierarchy)) {
            $this->addLogWriteln(
                __('Node can\'t be replaced. Firstly add before replace'),
                $this->output,
                'error'
            );
        } else {
            $this->delete();
            $this->save();
        }

        return $this;
    }

    /**
     * Delete Content Hierarchy if delete behaviour is selected
     *
     * @return $this
     */
    private function delete()
    {
        $idsToDelete = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                if ($this->validateRow($rowData, $rowNum)) {
                    $idsToDelete = array_merge($idsToDelete, $this->getProcessedIds($rowData));
                }
                if (!$rowData || $this->validateRow($rowData, $rowNum)) {
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
            }

            if ($idsToDelete) {
                try {
                    $this->nodeResource->dropNodes($idsToDelete);
                    $this->countItemsDeleted += count($idsToDelete);
                    if (Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
                        $this->contentHierarchyOld = $this->contentHierarchy;
                    }
                    foreach ($this->contentHierarchy as $requestUrl => $hierarchyByUrl) {
                        foreach ($hierarchyByUrl as $scope => $hierarchyByScope) {
                            if (is_array($hierarchyByScope)) {
                                foreach ($hierarchyByScope as $scopeId => $id) {
                                    if (in_array($id, $idsToDelete)) {
                                        unset($this->contentHierarchy[$requestUrl][$scope][$scopeId]);
                                    }
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                    $this->addLogWriteln(__('Nodes can\'t be deleted.'), $this->output, 'error');
                }
            }
        }

        if ($this->getDeletedItemsCount()) {
            $this->addLogWriteln(__('Deleted: %1 nodes.', $this->getDeletedItemsCount()), $this->output, 'info');
        }

        return $this;
    }

    /**
     * Gather and save information of Hierarchy nodes
     *
     * @return $this
     * @throws LocalizedException
     */
    public function save()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->_processedRowsCount++;
                $rowData = $this->joinIdenticalyData($rowData);
                $rowData = $this->customChangeData($rowData);
                $rowData = $this->prepareRowData($rowData);
                if (!$this->validateRow($rowData, $rowNum)) {
                    continue;
                }

                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                if (Import::BEHAVIOR_REPLACE == $this->getBehavior()
                    && !$this->isDataForReplace($rowData)) {
                    continue;
                }

                if ($rowData) {
                    try {
                        $nodeData = $this->prepareNodeData($rowData);
                        $this->createNode($nodeData);
                        $this->countItemsUpdated++;
                    } catch (Exception $e) {
                        $this->getErrorAggregator()->addError(
                            $e->getCode(),
                            ProcessingError::ERROR_LEVEL_NOT_CRITICAL,
                            $this->getProcessedRowsCount(),
                            null,
                            $e->getMessage()
                        );
                    }
                }
            }

            if ($this->getUpdatedItemsCount()) {
                $this->addLogWriteln(__('Imported: %1 Nodes.', $this->getUpdatedItemsCount()), $this->output);
            }
        }

        return $this;
    }

    /**
     * @param array $rowData
     * @return bool
     */
    private function isDataForReplace(array $rowData)
    {
        $requestUrl = $rowData[NodeInterface::REQUEST_URL];
        $scope = $rowData[NodeInterface::SCOPE];
        $scopeId = $rowData[NodeInterface::SCOPE_ID];
        if (isset($this->contentHierarchyOld[$requestUrl][$scope][$scopeId])) {
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Retrieve Node id's for delete
     *
     * @param array $rowData
     * @return array
     */
    private function getProcessedIds(array $rowData)
    {
        $processedIds = [];
        $requestUrl = $rowData[NodeInterface::REQUEST_URL] ?? null;
        $scope = $rowData[NodeInterface::SCOPE] ?? null;
        $scopeId = $rowData[NodeInterface::SCOPE_ID] ?? null;
        $nodeId = $rowData[NodeInterface::NODE_ID] ?? null;

        if ($requestUrl && $scope && $scopeId && isset($this->contentHierarchy[$requestUrl][$scope][$scopeId])) {
            $processedIds[] = $this->contentHierarchy[$requestUrl][$scope][$scopeId];
        } elseif ($requestUrl && $scope && isset($this->contentHierarchy[$requestUrl][$scope])) {
            foreach ($this->contentHierarchy[$requestUrl][$scope] as $id) {
                $processedIds[] = $id;
            }
        } elseif ($requestUrl && isset($this->contentHierarchy[$requestUrl])) {
            foreach ($this->contentHierarchy[$requestUrl] as $data) {
                foreach ($data as $id) {
                    $processedIds[] = $id;
                }
            }
        } elseif ($nodeId) {
            $processedIds[] = $nodeId;
        }

        return $processedIds;
    }

    /**
     * Create Content Hierarchy Node
     *
     * @param $rowData
     * @return NodeInterface
     * @throws LocalizedException
     */
    private function createNode($rowData)
    {
        $node = $this->integrationFactory->create(Node::class)->setData($rowData);
        $newNode = $this->nodeRepository->save($node);
        $newNodeId = $newNode->getId();
        $newNodeRequestUrl = $newNode->getRequestUrl();
        $newNodeScope = $newNode->getScope();
        $newNodeScopeId = $newNode->getScopeId();
        $this->contentHierarchy[$newNodeRequestUrl][$newNodeScope][$newNodeScopeId] = $newNodeId;
        return $newNode;
    }

    /**
     * Validate data row
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return bool
     */
    public function validateRow(array $rowData, $rowNumber)
    {
        if (isset($this->_validatedRows[$rowNumber])) {
            /* check that row is already validated */
            return !$this->getErrorAggregator()->isRowInvalid($rowNumber);
        }

        $this->_validatedRows[$rowNumber] = true;
        $this->_processedEntitiesCount++;

        /* behavior selector */
        switch ($this->getBehavior()) {
            case Import::BEHAVIOR_DELETE:
                break;
            case Import::BEHAVIOR_REPLACE:
            case Import::BEHAVIOR_APPEND:
            case AbstractEntity::getDefaultBehavior():
                if (empty($rowData[NodeInterface::NODE_ID]) && empty($rowData[NodeInterface::REQUEST_URL])) {
                    $colName = NodeInterface::NODE_ID . __(' and ') . NodeInterface::REQUEST_URL;
                    $this->addRowError(self::ERROR_IDENTIFIERS_IS_EMPTY, $rowNumber, $colName);
                }

                if (!empty($rowData[NodeInterface::NODE_ID]) && empty($rowData[NodeInterface::REQUEST_URL])) {
                    if (!empty($rowData[NodeInterface::PARENT_NODE_ID])) {
                        $parentNodeId = $rowData[NodeInterface::PARENT_NODE_ID];
                        try {
                            $this->nodeRepository->getById($parentNodeId);
                        } catch (NoSuchEntityException $e) {
                            $this->addRowError(self::ERROR_PARENT_NOT_FOUND, $rowNumber, $parentNodeId);
                        }
                    }
                }

                if (!empty($rowData[NodeInterface::PAGE_ID])) {
                    $pageId = $rowData[NodeInterface::PAGE_ID];
                    try {
                        $this->pageRepository->getById($pageId);
                    } catch (LocalizedException $e) {
                        $this->addRowError(self::ERROR_CMS_PAGE_NOT_FOUND, $rowNumber, $pageId);
                    }
                }
                break;
        }
        return !$this->getErrorAggregator()->isRowInvalid($rowNumber);
    }

    /**
     * Prepare row data for update/replace behaviour
     *
     * @param array $rowData
     * @return array
     * @throws LocalizedException
     */
    private function prepareRowData(array $rowData)
    {
        if (!empty($rowData[NodeInterface::IDENTIFIER]) && empty($rowData[NodeInterface::REQUEST_URL])) {
            $rowData[NodeInterface::REQUEST_URL] = $rowData[NodeInterface::IDENTIFIER];
        }

        if (empty($rowData[NodeInterface::SCOPE]) || !in_array($rowData[NodeInterface::SCOPE], $this->nodeScopes)) {
            $rowData[NodeInterface::SCOPE] = Node::NODE_SCOPE_DEFAULT;
            $rowData[NodeInterface::SCOPE_ID] = Node::NODE_SCOPE_DEFAULT_ID;
        } elseif (!isset($rowData[NodeInterface::SCOPE_ID]) || !is_numeric($rowData[NodeInterface::SCOPE_ID])) {
            $rowData[NodeInterface::SCOPE_ID] = Node::NODE_SCOPE_DEFAULT_ID;
        }

        $pageId = null;
        $identifier = null;
        if (!empty($rowData[NodeInterface::REQUEST_URL])) {
            $identifiers = explode("/", $rowData[NodeInterface::REQUEST_URL]);
            if (empty($rowData[NodeInterface::LEVEL])) {
                $rowData[NodeInterface::LEVEL] = count($identifiers);
            }
            $identifier = array_pop($identifiers);
            $scope = $rowData[NodeInterface::SCOPE];
            $scopeId = $rowData[NodeInterface::SCOPE_ID];
            $storeId = ($scope == Node::NODE_SCOPE_STORE) ? $scopeId : 0;
            $pageId = $this->pageResource->checkIdentifier($identifier, $storeId);
            if ($pageId) {
                $rowData[NodeInterface::PAGE_ID] = $pageId;
            }
        } elseif (!empty($rowData[NodeInterface::PAGE_ID])) {
            $pageId = $rowData[NodeInterface::PAGE_ID];
            $page = $this->pageRepository->getById($pageId);
            if ($page && $page->getId()) {
                $rowData[NodeInterface::REQUEST_URL] = $page->getIdentifier();
            }
        }

        if (!empty($rowData[NodeInterface::PAGE_ID])) {
            $rowData[NodeInterface::IDENTIFIER] = null;
            $rowData[NodeInterface::LABEL] = null;
        } else {
            $rowData[NodeInterface::PAGE_ID] = null;
            if (empty($rowData[NodeInterface::IDENTIFIER])) {
                $rowData[NodeInterface::IDENTIFIER] = $identifier;
            }
            $identifier = $rowData[NodeInterface::IDENTIFIER];
            if (empty($rowData[NodeInterface::LABEL]) && !empty($identifier)) {
                $rowData[NodeInterface::LABEL] = ucwords(str_replace(['-', '_'], ' ', $identifier));
            }
        }

        return $rowData;
    }

    /**
     * Prepare node data for save
     *
     * @param $rowData
     * @return array
     * @throws LocalizedException
     */
    private function prepareNodeData($rowData)
    {
        $scope = $rowData[NodeInterface::SCOPE];
        $scopeId = $rowData[NodeInterface::SCOPE_ID];
        $requestUrl = null;
        $xpath = null;
        $parentNodeId = null;
        $nodeData = [];

        if (!empty($rowData[NodeInterface::REQUEST_URL])) {
            $identifiers = explode("/", $rowData[NodeInterface::REQUEST_URL]);
            foreach ($identifiers as $identifier) {
                $requestUrl = !empty($requestUrl) ? $requestUrl . "/" . $identifier : $identifier;
                $xpath = !empty($xpath) ? $xpath . "/" : '';
                if (!isset($this->contentHierarchy[$requestUrl][$scope][$scopeId])) {
                    $nodeData = [
                        NodeInterface::NODE_ID => null,
                        NodeInterface::PARENT_NODE_ID => $parentNodeId,
                        NodeInterface::XPATH => $xpath,
                        NodeInterface::SCOPE => $scope,
                        NodeInterface::SCOPE_ID => $scopeId,
                    ];
                    if ($rowData[NodeInterface::REQUEST_URL] != $requestUrl) {
                        $storeId = ($scope == Node::NODE_SCOPE_STORE) ? $scopeId : 0;
                        $pageId = $this->pageResource->checkIdentifier($identifier, $storeId);
                        $label = ucwords(str_replace(['-', '_'], ' ', $identifier));
                        $nodeData[NodeInterface::PAGE_ID] = $pageId ? $pageId : null;
                        $nodeData[NodeInterface::IDENTIFIER] = $pageId ? null : $identifier;
                        $nodeData[NodeInterface::LABEL] = $pageId ? null : $label;
                        $nodeData[NodeInterface::LEVEL] = count(explode("/", $requestUrl));
                        $nodeData[NodeInterface::SORT_ORDER] = 0;
                        $nodeData[NodeInterface::REQUEST_URL] = $requestUrl;
                        $newNode = $this->createNode($nodeData);
                        $parentNodeId = $newNode->getId();
                        $xpath = $newNode->getXpath();
                    } else {
                        $nodeData[NodeInterface::PAGE_ID] = $rowData[NodeInterface::PAGE_ID];
                        $nodeData[NodeInterface::IDENTIFIER] = $rowData[NodeInterface::IDENTIFIER];
                        $nodeData[NodeInterface::LABEL] = $rowData[NodeInterface::LABEL];
                        $nodeData[NodeInterface::LEVEL] = $rowData[NodeInterface::LEVEL];
                        $nodeData[NodeInterface::SORT_ORDER] = $rowData[NodeInterface::SORT_ORDER] ?? 0;
                        $nodeData[NodeInterface::REQUEST_URL] = $rowData[NodeInterface::REQUEST_URL];
                        $rowData = $this->hierarchyHelper->copyMetaData($rowData, $nodeData);
                    }
                } else {
                    /** @var Node $existNode */
                    $existNode = $this->integrationFactory
                        ->create(
                            Node::class,
                            ['data' => [NodeInterface::SCOPE => $scope, NodeInterface::SCOPE_ID => $scopeId]]
                        )->loadByRequestUrl($requestUrl);
                    if ($rowData[NodeInterface::REQUEST_URL] != $requestUrl) {
                        $parentNodeId = $existNode->getId();
                        $xpath = $existNode->getXpath();
                    } else {
                        unset($this->contentHierarchy[$requestUrl][$scope][$scopeId]);
                        unset($rowData[NodeInterface::NODE_ID]);
                        $nodeData = $existNode->getData();
                        foreach ($this->nodeFields as $field) {
                            if (isset($rowData[$field])) {
                                $nodeData[$field] = $rowData[$field];
                                if ($field == NodeInterface::PARENT_NODE_ID) {
                                    $nodeData[$field] = $parentNodeId;
                                }
                                if ($field == NodeInterface::XPATH) {
                                    $nodeData[$field] = $xpath;
                                }
                            }
                        }
                        $rowData = $this->hierarchyHelper->copyMetaData($rowData, $nodeData);
                    }
                }
            }
        } elseif (!empty($scope) && !empty($scopeId)) {
            if (!isset($this->contentHierarchy[$scope][$scopeId])) {
                unset($rowData[NodeInterface::NODE_ID]);
            }
            $nodeField = $this->getAllFields();
            foreach ($nodeField as $field) {
                if (!empty($rowData[$field])) {
                    $nodeData[$field] = $rowData[$field];
                }
            }
            $rowData = $nodeData;
        } else {
            $rowData = [];
        }

        return $rowData;
    }

    /**
     * Save Validated Bunches
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _saveValidatedBunches()
    {
        $source = $this->getSource();
        $currentDataSize = 0;
        $bunchRows = [];
        $startNewBunch = false;
        $nextRowBackup = [];
        $maxDataSize = $this->_resourceHelper->getMaxDataSize();
        $bunchSize = $this->_importExportData->getBunchSize();

        $source->rewind();
        $this->_dataSourceModel->cleanBunches();
        $file = null;
        $jobId = null;
        if (isset($this->_parameters['file'])) {
            $file = $this->_parameters['file'];
        }
        if (isset($this->_parameters['job_id'])) {
            $jobId = $this->_parameters['job_id'];
        }

        while ($source->valid() || $bunchRows) {
            if ($startNewBunch || !$source->valid()) {
                $this->_dataSourceModel->saveBunches(
                    $this->getEntityTypeCode(),
                    $this->getBehavior(),
                    $jobId,
                    $file,
                    $bunchRows
                );
                $bunchRows = $nextRowBackup;
                $currentDataSize = strlen(Serializer::serialize($bunchRows));
                $startNewBunch = false;
                $nextRowBackup = [];
            }

            if ($source->valid()) {
                try {
                    $rowData = $source->current();
                } catch (InvalidArgumentException $e) {
                    $this->addRowError($e->getMessage(), $this->getProcessedRowsCount());
                    $this->_processedRowsCount++;
                    $source->next();
                    continue;
                }
                $rowData = $this->customBunchesData($rowData);
                $this->_processedRowsCount++;

                $rowSize = strlen($this->serializer->serialize($rowData));

                $isBunchSizeExceeded = $bunchSize > 0 && count($bunchRows) >= $bunchSize;

                if ($currentDataSize + $rowSize >= $maxDataSize || $isBunchSizeExceeded) {
                    $startNewBunch = true;
                    $nextRowBackup = [$source->key() => $rowData];
                } else {
                    $bunchRows[$source->key()] = $rowData;
                    $currentDataSize += $rowSize;
                }

                $source->next();
            }
        }
        return $this;
    }
}
