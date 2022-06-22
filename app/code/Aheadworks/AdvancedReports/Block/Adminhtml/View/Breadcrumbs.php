<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Block\Adminhtml\View;

use Magento\Backend\Block\Template\Context;
use Aheadworks\AdvancedReports\Model\Filter;

/**
 * Class Breadcrumbs
 *
 * @package Aheadworks\AdvancedReports\Block\Adminhtml\View
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Breadcrumbs extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    const SESSION_KEY = 'aw_arep_breadcrumbs';

    /**
     * @var string
     */
    protected $_template = 'Aheadworks_AdvancedReports::view/breadcrumbs.phtml';

    /**
     * @var string
     */
    private $className = 'aw-arep-breadcrumbs';

    /**
     * @var bool
     */
    private $isValidCrumbs = true;

    /**
     * @var []
     */
    private $crumbsFromUrl = [];

    /**
     * @var string
     */
    private $brcParam;

    /**
     * @var []
     */
    private $mapCrumbs = [
        'salesoverview-productperformance',
        'salesoverview-productperformance-productperformance_variantperformance',
        'productperformance-productperformance_variantperformance',
        'category-productperformance',
        'category-productperformance-productperformance_variantperformance',
        'couponcode-salesoverview',
        'couponcode-salesoverview-productperformance',
        'couponcode-salesoverview-productperformance-productperformance_variantperformance',
        'manufacturer-productperformance',
        'manufacturer-productperformance-productperformance_variantperformance',
        'paymenttype-salesoverview',
        'paymenttype-salesoverview-productperformance',
        'paymenttype-salesoverview-productperformance-productperformance_variantperformance',
    ];

    /**
     * @var []
     */
    private $defaultCrumbs = [
        'salesoverview' => [
            'label' => 'Sales Overview',
            'url' => '*/salesoverview/index',
            'last' => false,
            'allowed_url_param' => [
                'coupon_code' => 'label',
                'payment_name' => 'label',
                'payment_type' => 'param'
            ]
        ],
        'productperformance' => [
            'label' => 'Product Performance',
            'url' => '*/productperformance/index',
            'last' => false,
            'allowed_url_param' => [
                'coupon_code' => 'label',
                'payment_name' => 'label',
                'payment_type' => 'param',
                'manufacturer' => 'label',
                'category_name' => 'label',
                'category_id' => 'param'
            ]
        ],
        'productperformance_variantperformance' => [
            'label' => 'Product Variation Performance',
            'url' => '*/productperformance_variantperformance/index',
            'last' => false,
            'allowed_url_param' => [
                'product_name' => 'label',
                'product_id' => 'param'
            ]
        ],
        'category' => [
            'label' => 'Sales by Category',
            'url' => '*/category/index',
            'last' => false
        ],
        'couponcode' => [
            'label' => 'Sales by Coupon Code',
            'url' => '*/couponcode/index',
            'last' => false
        ],
        'paymenttype' => [
            'label' => 'Sales by Payment Type',
            'url' => '*/paymenttype/index',
            'last' => false
        ],
        'manufacturer' => [
            'label' => 'Sales by Manufacturer',
            'url' => '*/manufacturer/index',
            'last' => false
        ],
    ];

    /**
     * @var Filter\Store
     */
    private $storeFilter;

    /**
     * @var Filter\Groupby
     */
    private $groupbyFilter;

    /**
     * @var Filter\Period
     */
    private $periodFilter;

    /**
     * Breadcrumbs constructor.
     * @param Context $context
     * @param Filter\Store $storeFilter
     * @param Filter\Groupby $groupbyFilter
     * @param Filter\Period $periodFilter
     * @param array $data
     */
    public function __construct(
        Context $context,
        Filter\Store $storeFilter,
        Filter\Groupby $groupbyFilter,
        Filter\Period $periodFilter,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeFilter = $storeFilter;
        $this->groupbyFilter = $groupbyFilter;
        $this->periodFilter = $periodFilter;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        if ($crumbsFromUrl = $this->getRequest()->getParam('brc')) {
            $this->crumbsFromUrl = explode('-', $crumbsFromUrl);
            if (!in_array($crumbsFromUrl, $this->mapCrumbs)) {
                $this->isValidCrumbs = false;
            }
        }
    }

    /**
     * Get breadcrumbs container class name
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Add crumb
     *
     * @param string $key
     * @param string $alias
     * @param string $label
     * @param string $url
     * @return $this
     */
    public function addCrumb($key, $alias, $label, $url)
    {
        $sessionCrumbs = $this->_session->getData(self::SESSION_KEY) ?: [];
        $sessionCrumbs[$key][$alias] = [
            'label' => $label,
            'url' => $url,
            'last' => false
        ];
        $this->_session->setData(self::SESSION_KEY, $sessionCrumbs);
        return $this;
    }

    /**
     * Retrieve crumbs
     *
     * @return []
     */
    public function getCrumbs()
    {
        $crumbs = [];
        if (!$this->isValidCrumbs) {
            return $crumbs;
        }

        if ($sessionCrumbs = $this->_session->getData(self::SESSION_KEY)) {
            $lastCrumb = $this->getFirstLastCrumb(false);
            foreach ($sessionCrumbs[$this->getFirstLastCrumb()] as $key => $value) {
                $crumbs[$key] = $value;
                if ($lastCrumb == $key) {
                    $crumbs[$key]['last'] = true;
                    break;
                }
            }
        }
        return count($crumbs) > 1 ? $crumbs : [];
    }

    /**
     * Retrieve the first|last key crumb
     *
     * @param bool $crumbs
     * @return string
     */
    public function getFirstLastCrumb($first = true)
    {
        $key = $this->getRequest()->getControllerName();
        if ($crumbs = $this->crumbsFromUrl) {
            $key = $first ? reset($crumbs) : end($crumbs);
        }
        return $key;
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        if (!$this->isValidCrumbs) {
            return parent::_beforeToHtml();
        }

        $addCrumb = false;
        $firstCrumb = $this->getFirstLastCrumb();
        $sessionCrumbs = $this->_session->getData(self::SESSION_KEY);
        if ($sessionCrumbs && array_key_exists($firstCrumb, $sessionCrumbs)) {
            $addCrumb = true;
        } else {
            if ($this->crumbsFromUrl) {
                $this->brcParam = '';
                foreach ($this->crumbsFromUrl as $crumb) {
                    list($url, $label) = $this->getUrlLabelByDefaultCrumb($crumb, $firstCrumb);
                    $this->addCrumb($firstCrumb, $crumb, $label, $url);
                }
            } else {
                $addCrumb = true;
            }
        }

        if ($addCrumb) {
            $query = [
                'period_type' => $this->periodFilter->getPeriodType(),
                'group_by' => $this->groupbyFilter->getCurrentGroupByKey(),
                'period_from' => $this->periodFilter->getPeriodFrom()->format('Y-m-d'),
                'period_to' => $this->periodFilter->getPeriodTo()->format('Y-m-d')
            ];
            $currentQuery = $this->getRequest()->getQueryValue();
            $query = array_merge($query, $currentQuery);

            $this->addCrumb(
                $this->getFirstLastCrumb(),
                $this->getRequest()->getControllerName(),
                $this->pageConfig->getTitle()->getShort(),
                $this->getUrl('*/*/*', ['_current' => true, '_query' => $query])
            );
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve url and label by default crumb
     *
     * @param string $crumb
     * @param string $firstCrumb
     * @return []
     */
    private function getUrlLabelByDefaultCrumb($crumb, $firstCrumb)
    {
        $url = '';
        if ($firstCrumb == $crumb) {
            $url = $this->getUrl($this->defaultCrumbs[$crumb]['url']);
            $label = __($this->defaultCrumbs[$crumb]['label']);
            $this->brcParam .= $crumb;
        } else {
            $this->brcParam .= '-' . $crumb;
            $query = ['brc' => $this->brcParam];
            $label = __($this->defaultCrumbs[$crumb]['label']);
            foreach ($this->getRequest()->getQueryValue() as $key => $value) {
                if (array_key_exists($key, $this->defaultCrumbs[$crumb]['allowed_url_param'])) {
                    $query[$key] = $value;
                    if ($this->defaultCrumbs[$crumb]['allowed_url_param'][$key] == 'label') {
                        $label = $this->getLabelByQueryParam($key, $value, $this->defaultCrumbs[$crumb]['label']);
                    }
                }
            }
            $url = $this->getUrl($this->defaultCrumbs[$crumb]['url'], ['_query' => $query]);
        }
        return [$url, $label];
    }

    /**
     * Retrieve label by query param
     *
     * @param stirng $key
     * @param mixed $value
     * @param stirng $label
     * @return \Magento\Framework\Phrase
     */
    private function getLabelByQueryParam($key, $value, $label)
    {
        switch ($key) {
            case 'payment_name':
            case 'product_name':
            case 'category_name':
                $value = $this->_request->getParam($key);
                break;
        }
        if ($value) {
            $label = __($label . ' (%1)', base64_decode($value));
        } else {
            $label = __($label);
        }
        return $label;
    }
}
