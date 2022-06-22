<?php

namespace Magenest\MobileApi\Setup\Patch;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\State;

abstract class AbstractAddCmsBlockPatch implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /** @var \Magento\Cms\Model\BlockFactory */
    protected $blockFactory;

    /** @var \Magento\Cms\Api\BlockRepositoryInterface */
    protected $blockRepository;

    /** @var ModuleDataSetupInterface */
    protected $dataSetup;

    /** @var State */
    protected $state;

    /**
     * AbstractAddCmsBlockPatch constructor.
     *
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     * @param \Magento\Cms\Api\BlockRepositoryInterface $blockRepository
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param State $state
     */
    public function __construct(
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        ModuleDataSetupInterface $moduleDataSetup,
        State $state
    ) {
        $this->dataSetup = $moduleDataSetup;
        $this->blockFactory = $blockFactory;
        $this->blockRepository = $blockRepository;
        $this->state = $state;
    }

    /**
     * Get dependencies
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Update cms block
     *
     * @param mixed $identifier
     * @param null $title
     * @param int[] $storeId
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateCmsBlock($identifier, $title = null, $storeId = [0])
    {
        if (empty($title)) {
            $title = $identifier;
        }

        $data = [
            'title' => $title,
            'identifier' => $identifier,
            'content' => '',
            'is_active' => 1,
            'stores' => $storeId,
            'sort_order' => 0
        ];
        try {
            $cmsBlock = $this->blockRepository->getById($identifier);
        } catch (\Exception $e) {
            $cmsBlock = $this->blockFactory->create();
        }
        $cmsBlock->addData($data);
        $this->blockRepository->save($cmsBlock);
    }

    /**
     * Create cms block
     *
     * @param mixed $identifier
     * @param mixed $templatePath
     * @param null $title
     * @param int[] $storeId
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createCmsBlock($identifier, $title = null, $storeId = [0])
    {
        if (empty($title)) {
            $title = $identifier;
        }

        $data = [
            'title' => $title,
            'identifier' => $identifier,
            'content' => '',
            'is_active' => 1,
            'stores' => $storeId,
            'store_id' => $storeId[0],
            'sort_order' => 0
        ];
        $cmsBlock = $this->blockFactory->create();
        $cmsBlock->addData($data);
        $this->blockRepository->save($cmsBlock);
    }

    /**
     * Get Aliases
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Apply
     */
    public function apply()
    {
        $this->dataSetup->startSetup();
        $this->doUpdate();
        $this->dataSetup->endSetup();
    }

    /**
     * Update cms block
     *
     * @return mixed
     */
    public abstract function doUpdate();
}
