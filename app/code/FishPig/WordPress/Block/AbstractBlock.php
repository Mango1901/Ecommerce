<?php
/**
 *
 */
namespace FishPig\WordPress\Block;

abstract class AbstractBlock extends \Magento\Framework\View\Element\Template
{
    const PATH_IMAGE_DEFAULT_CONFIG = 'wordpress/general/default_blog_image';

    /**
     * @var
     */
    protected $wpContext;

    /**
     * @var OptionManager
     */
    protected $optionManager;

    /**
     * @var ShortcodeManager
     */
    protected $shortcodeManager;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Url
     */
    protected $url;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @param Context $context
     * @param App
     * @param array   $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \FishPig\WordPress\Model\Context $wpContext,
        array $data = []
    ) {
        $this->wpContext = $wpContext;
        $this->optionManager = $wpContext->getOptionManager();
        $this->shortcodeManager = $wpContext->getShortcodeManager();
        $this->registry = $wpContext->getRegistry();
        $this->url = $wpContext->getUrl();
        $this->factory = $wpContext->getFactory();

        parent::__construct($context, $data);
    }

    /**
     * Parse and render a shortcode
     *
     * @param  string $shortcode
     * @param  mixed  $object    = null
     * @return string
     */
    public function renderShortcode($shortcode, $object = null)
    {
        return $this->shortcodeManager->renderShortcode($shortcode, ['object' => $object]);
    }

    /**
     *
     * @return string
     */
    public function doShortcode($shortcode, $object = null)
    {
        return $this->renderShortcode($shortcode, $object);
    }

    /**
     *
     * @return Factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Catch and log any excep§tions to var/log/wordpress.log
     */
    public function toHtml()
    {
        try {
            return parent::toHtml();
        } catch (\Exception $e) {
            $this->wpContext->getLogger()->error($e);

            throw $e;
        }
    }

    /**
     *
     */
    public function getWpUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getDefaultImage()
    {
        return $this->getUrl('media/wordpress') . ($this->_scopeConfig->getValue(self::PATH_IMAGE_DEFAULT_CONFIG));
    }

    /**
     * @param $dateString
     * @return array|string|string[]|null
     */
    public function formatDateVietNam($dateString)
    {
        return preg_replace('/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/', '$1 tháng $2, $3', $dateString);
    }
}
