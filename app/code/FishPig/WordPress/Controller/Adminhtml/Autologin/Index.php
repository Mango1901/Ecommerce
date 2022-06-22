<?php
/**
 *
 */
declare(strict_types=1);

namespace FishPig\WordPress\Controller\Adminhtml\Autologin;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Url;

/**
 * Class Index
 * @package FishPig\WordPress\Controller\Adminhtml\Autologin
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * @const string
     */
    const ADMIN_RESOURCE = 'FishPig_WordPress::wp';

    /**
     * @var Url
     */
    protected $url;

    /**
     * Index constructor.
     * @param Url $url
     * @param Context $context
     */
    public function __construct(
        Url $url,
        Context $context
    ) {
        $this->url = $url;
        parent::__construct($context);
    }

    /**
     *
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(
            $this->resultFactory::TYPE_PAGE
        );

        $resultPage->setActiveMenu('FishPig_WordPress::wordpress');
        $resultPage->getConfig()->getTitle()->prepend(__('WordPress Admin - Auto Login'));

        return header('Location: '. $this->url->getBaseUrl().'/wp/wp-admin');
    }
}
