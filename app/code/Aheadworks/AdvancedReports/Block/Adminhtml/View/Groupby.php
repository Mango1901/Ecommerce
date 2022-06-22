<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Block\Adminhtml\View;

use Magento\Backend\Block\Template\Context;
use Aheadworks\AdvancedReports\Model\Source\Groupby as GroupbySource;
use Aheadworks\AdvancedReports\Model\Filter\Groupby as GroupbyFilter;

/**
 * Class Groupby
 *
 * @package Aheadworks\AdvancedReports\Block\Adminhtml\View
 */
class Groupby extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_AdvancedReports::view/groupby.phtml';

    /**
     * @var GroupbySource
     */
    private $groupbySource;

    /**
     * @var  GroupbyFilter
     */
    private $groupbyFilter;

    /**
     * @param Context $context
     * @param GroupbySource $groupbySource
     * @param GroupbyFilter $groupbyFilter
     * @param [] $data
     */
    public function __construct(
        Context $context,
        GroupbySource $groupbySource,
        GroupbyFilter $groupbyFilter,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->groupbySource = $groupbySource;
        $this->groupbyFilter = $groupbyFilter;
    }

    /**
     * Retrieve group by types
     *
     * @return []
     */
    public function getOptions()
    {
        return $this->groupbySource->getOptions();
    }

    /**
     * Retrieve current group by type
     *
     * @return string
     */
    public function getCurrentGroupByKey()
    {
        return $this->groupbyFilter->getCurrentGroupByKey();
    }

    /**
     * Retrieve group by url
     *
     * @param string $key
     * @return string
     */
    public function getGroupbyUrl($key)
    {
        return $this->getUrl('*/*/*', ['_query' => ['group_by' => $key], '_current' => true]);
    }
}
