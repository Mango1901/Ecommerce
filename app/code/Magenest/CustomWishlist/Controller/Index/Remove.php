<?php
namespace Magenest\CustomWishlist\Controller\Index;

use Magento\Framework\App\Action;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\Product\AttributeValueProvider;

/**
 * Wishlist Remove Controller
 */
class Remove extends \Magento\Wishlist\Controller\Index\Remove
{
    /**
     * @var AttributeValueProvider
     */
    private $attributeValueProvider;

    /**
     * @var ItemFactory
     */
    private $itemFactory;

    /**
     * @var Data
     */
    private $wishlistHelper;

    /**
     * @param Action\Context $context
     * @param Data $wishlistHelper
     * @param WishlistProviderInterface $wishlistProvider
     * @param Validator $formKeyValidator
     * @param ItemFactory $itemFactory
     * @param AttributeValueProvider|null $attributeValueProvider
     */
    public function __construct(
        Action\Context            $context,
        Data                      $wishlistHelper,
        WishlistProviderInterface $wishlistProvider,
        Validator                 $formKeyValidator,
        ItemFactory               $itemFactory,
        AttributeValueProvider    $attributeValueProvider
    ) {
        $this->itemFactory            = $itemFactory;
        $this->wishlistHelper         = $wishlistHelper;
        $this->attributeValueProvider = $attributeValueProvider;
        parent::__construct($context, $wishlistProvider, $formKeyValidator);
    }

    /**
     * Remove item
     * @return ResultInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setPath('*/*/');
        }

        $id   = (int)$this->getRequest()->getParam('item');
        $item = $this->itemFactory->create()->load($id);
        if (!$item->getId()) {
            throw new NotFoundException(__('Item not found.'));
        }
        $wishlist = $this->wishlistProvider->getWishlist($item->getWishlistId());
        if (!$wishlist) {
            throw new NotFoundException(__('Wishlist not found.'));
        }
        try {
            $item->delete();
            $wishlist->save();
            $productName = $this->attributeValueProvider
                ->getRawAttributeValue($item->getProductId(), 'name');
            $this->messageManager->addComplexSuccessMessage(
                'removeWishlistItemSuccessMessage',
                [
                    'product_name' => $productName,
                ]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t delete the item from Wish List right now because of an error: %1.', $e->getMessage())
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We can\'t delete the item from the Wish List right now.'));
        }

        $this->wishlistHelper->calculate();

        if ($this->getRequest()->isAjax()) {
            return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData(['id' => $wishlist->getId()]);
        } else {
            $refererUrl = $this->_redirect->getRefererUrl();
            if ($refererUrl) {
                $redirectUrl = $refererUrl;
            } else {
                $redirectUrl = $this->_redirect->getRedirectUrl($this->_url->getUrl('*/*'));
            }
            $resultRedirect->setUrl($redirectUrl);
            return $resultRedirect;
        }
    }
}
