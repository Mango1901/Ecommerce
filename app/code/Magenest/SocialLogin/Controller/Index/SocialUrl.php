<?php
namespace Magenest\SocialLogin\Controller\Index;

use Magenest\SocialLogin\Model\Facebook\Client as ClientFacebook;
use Magenest\SocialLogin\Model\Google\Client as ClientGoogle;
use Magenest\SocialLogin\Model\Apple\Client as ClientApple;

use Magento\Framework\App\Action\Context;

class SocialUrl extends \Magento\Framework\App\Action\Action
{
    /** @var ClientFacebook  */
    protected $clientFacebook;

    /** @var ClientGoogle  */
    protected $clientGoogle;

    /* @var ClientApple  */
    protected $clientApple;

    /**
     * @param ClientFacebook $clientFacebook
     * @param ClientGoogle $clientGoogle
     * @param ClientApple $clientApple
     * @param Context $context
     */
    public function __construct(
        ClientFacebook $clientFacebook,
        ClientGoogle $clientGoogle,
        ClientApple $clientApple,
        Context $context
    ) {
        parent::__construct($context);
        $this->clientGoogle = $clientGoogle;
        $this->clientFacebook = $clientFacebook;
        $this->clientApple = $clientApple;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $request = $this->getRequest()->getParam('social');
        $response = $this->getResponse();
        switch ($request){
            case "facebook":
                $response->setRedirect($this->clientFacebook->createAuthUrl());
                break;
            case "google":
                $response->setRedirect($this->clientGoogle->createAuthUrl());
                break;
            case "apple":
                $response->setRedirect($this->clientApple->createAuthUrl());
                break;
            default:
                break;
        }

        return $this->getResponse();
    }
}
