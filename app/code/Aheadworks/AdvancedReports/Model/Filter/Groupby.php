<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\Filter;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Aheadworks\AdvancedReports\Model\Source\Groupby as GroupbySource;

/**
 * Class Groupby
 *
 * @package Aheadworks\AdvancedReports\Model\Filter
 */
class Groupby
{
    /**
     * @var string
     */
    const SESSION_KEY = 'aw_arep_groupby';

    /**
     * @var string
     */
    const DEFAULT_GROUP_BY = GroupbySource::TYPE_MONTH;

    /**
     * @var string
     */
    private $groupbyKey;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @param RequestInterface $request
     * @param SessionManagerInterface $session
     */
    public function __construct(
        RequestInterface $request,
        SessionManagerInterface $session
    ) {
        $this->request = $request;
        $this->session = $session;
    }

    /**
     * Retrieve current group by type
     *
     * @return string
     */
    public function getCurrentGroupByKey()
    {
        if (!$this->groupbyKey) {
            $this->groupbyKey = $this->request->getParam('group_by');
            if (!$this->groupbyKey) {
                $this->groupbyKey = $this->session->getData(self::SESSION_KEY);
            }
            if (!$this->groupbyKey) {
                $this->groupbyKey = self::DEFAULT_GROUP_BY;
            }
            $this->session->setData(self::SESSION_KEY, $this->groupbyKey);
        }
        return $this->groupbyKey;
    }
}
