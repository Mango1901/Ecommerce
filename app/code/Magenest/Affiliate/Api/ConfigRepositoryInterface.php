<?php


namespace Magenest\Affiliate\Api;

/**
 * Interface ConfigRepositoryInterface
 * @api
 */
interface ConfigRepositoryInterface
{
    /**
     * Get all affiliate configs
     *
     * @param int|null $storeId
     * @return \Magenest\Affiliate\Api\Data\ConfigInterface Config.
     */
    public function get($storeId = null);
}
