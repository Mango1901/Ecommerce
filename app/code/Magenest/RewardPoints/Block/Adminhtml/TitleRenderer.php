<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_RewardPoints extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_RewardPoints
 */

namespace Magenest\RewardPoints\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;

/**
 * Class TitleRenderer
 * @package Magenest\RewardPoints\Block\Adminhtml
 */
class TitleRenderer extends AbstractRenderer
{
    /**
     * @var \Magenest\RewardPoints\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * TitleRenderer constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param \Magenest\RewardPoints\Model\RuleFactory $ruleFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magenest\RewardPoints\Model\RuleFactory $ruleFactory,
        array $data = []
    )
    {
        $this->ruleFactory = $ruleFactory;
        parent::__construct($context, $data);
    }

    /**
	 * @param \Magento\Framework\DataObject $row
	 *
	 * @return string
	 */
	public function render(DataObject $row)
	{
		$title = '';
		if ($row->getRuleId() > 0) {
			$ruleModel = $this->ruleFactory->create()->load($row->getRuleId());
			$title = $ruleModel->getTitle();
		} else {
			switch ($row->getRuleId()) {
				case 0:
					$title = 'Redeem points';
					break;
				case -1:
					$title = 'Points from admin';
					break;
				case -2:
					$title = 'Referral code points';
					break;
				case -3:
					$title = 'Points Expired';
					break;
                case -4:
                    $item['title'] = __("Deduct received points");
                    break;
                case -5:
                    $item['title'] = __("Return applied points");
                    break;
			}
		}
		if (!$row->getComment() AND $title) {
			$row->setComment($title);
		}

		return $title;
	}
}
