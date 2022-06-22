<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model;

/**
 * Class Flag
 *
 * @package Aheadworks\AdvancedReports\Model
 */
class Flag extends \Magento\Framework\Flag
{
    /**
     * @var string
     */
    const AW_AREP_STATISTICS_FLAG_CODE = 'aw_arep_statistics';

    /**
     * Setter for flag code
     * @codeCoverageIgnore
     *
     * @param string $code
     * @return $this
     */
    public function setReportFlagCode($code)
    {
        $this->_flagCode = $code;
        return $this;
    }
}
