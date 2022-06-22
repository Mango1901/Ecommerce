<?php

namespace Magenest\SocialLogin\Model;

use Magento\Config\Model\Config\CommentInterface;
use Magento\Framework\View\Element\AbstractBlock;

class CommentConfirmationRequired extends AbstractBlock implements CommentInterface
{
    /**
     * @inheritDoc
     */
    public function getCommentText($elementValue)
    {
        $url = $this->_urlBuilder->getUrl('adminhtml/system_config/edit/section/customer/create_account');
        return "You must enable <a href='$url'>Customer Configuration -> Create New Account Options -> Require Emails Confirmation</a> for the module to work correctly";
    }
}
