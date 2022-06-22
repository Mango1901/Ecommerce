<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_tn233 extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_tn233
 */

namespace Magenest\CustomWordPress\Plugin\Model;
/**
 * Class Search
 * @package Magenest\CustomWordPress\Plugin\Model
 */
class Search
{
    /**
     * @param \FishPig\WordPress\Model\Search $subject
     * @param $result
     * @return \Magento\Framework\Phrase|string|void
     */
    public function afterGetName(\FishPig\WordPress\Model\Search $subject, $result)
    {
        return __('Search results for %1', $subject->getSearchTerm());
    }
}
