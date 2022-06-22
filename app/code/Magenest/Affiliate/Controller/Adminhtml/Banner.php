<?php


namespace Magenest\Affiliate\Controller\Adminhtml;

/**
 * Class Banner
 * @package Magenest\Affiliate\Controller\Adminhtml
 */
abstract class Banner extends AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magenest_Affiliate::banner';

    /**
     * @return \Magenest\Affiliate\Model\Banner
     */
    protected function _initBanner()
    {
        $bannerId = (int)$this->getRequest()->getParam('id');
        /** @var \Magenest\Affiliate\Model\Banner $banner */
        $banner = $this->_objectManager->create('Magenest\Affiliate\Model\Banner');
        if ($bannerId) {
            $banner->load($bannerId);
        }
        if (!$this->_coreRegistry->registry('current_banner')) {
            $this->_coreRegistry->register('current_banner', $banner);
        }

        return $banner;
    }
}
