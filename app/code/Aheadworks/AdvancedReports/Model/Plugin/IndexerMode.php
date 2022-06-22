<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\Plugin;

use Aheadworks\AdvancedReports\Model\Flag;
use Magento\Framework\Mview\View\StateInterface;

/**
 * Class IndexerMode
 *
 * @package Aheadworks\AdvancedReports\Model\Plugin
 */
class IndexerMode
{
    /**
     * Disable UPDATE ON SAVE mode
     *
     * @param \Magento\Indexer\Model\Mview\View\State $mode
     * @return \Magento\Indexer\Model\Mview\View\State\Interceptor
     */
    public function afterSetMode(
        \Magento\Indexer\Model\Mview\View\State\Interceptor $mode
    ) {
//        if (StateInterface::MODE_DISABLED == $mode->getMode() && $mode->getViewId() == Flag::AW_AREP_STATISTICS_FLAG_CODE) {
//            $mode->setMode(StateInterface::MODE_ENABLED);
//        }
        return $mode;
    }
}
