<?php

namespace Magenest\AffiliateOpt\Model;

use Magenest\AffiliateOpt\Api\CreditmemoRepositoryInterface;
use Magenest\AffiliateOpt\Model\AffiliateCreditmemoFactory;
use Magenest\AffiliateOpt\Model\AffiliateCreditmemoItemFactory;

/**
 * Class CreditmemoRepository
 * @package Magenest\AffiliateOpt\Model
 */
class CreditmemoRepository implements CreditmemoRepositoryInterface
{
    /**
     * @var \Magenest\AffiliateOpt\Model\AffiliateCreditmemoFactory
     */
    protected $affiliateCreditmemoFactory;

    /**
     * @var \Magenest\AffiliateOpt\Model\AffiliateCreditmemoItemFactory
     */
    protected $affiliateCreditmemoItemFactory;

    /**
     * CreditmemoRepository constructor.
     *
     * @param \Magenest\AffiliateOpt\Model\AffiliateCreditmemoFactory $affiliateCreditmemoFactory
     * @param \Magenest\AffiliateOpt\Model\AffiliateCreditmemoItemFactory $affiliateCreditmemoItemFactory
     */
    public function __construct(
        AffiliateCreditmemoFactory $affiliateCreditmemoFactory,
        AffiliateCreditmemoItemFactory $affiliateCreditmemoItemFactory
    ) {
        $this->affiliateCreditmemoFactory = $affiliateCreditmemoFactory;
        $this->affiliateCreditmemoItemFactory = $affiliateCreditmemoItemFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function get($invoiceId)
    {
        return $this->affiliateCreditmemoFactory->create()->load($invoiceId);
    }

    /**
     * {@inheritDoc}
     */
    public function getItemById($itemId)
    {
        return $this->affiliateCreditmemoItemFactory->create()->load($itemId);
    }
}
