<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * NOTICE OF LICENSE
 *
 * @category Magenest
 */

namespace Magenest\RewardPoints\Api;


interface ProcessPointInterface
{
    /**
     * @return string
     */
    public function calculateMaxAppliedPoint();

    /**
     * Returns url
     *
     * @api
     * @return string url
     */
    public function loadPoint();


    /** Returns Transaction Reference ID
     *
     * @param int $point
     * @return string|null
     */
    public function addPoint($point);

    /**
     * Returns url
     *
     * @api
     * @return string url
     */
    public function cancelPoint();
}
