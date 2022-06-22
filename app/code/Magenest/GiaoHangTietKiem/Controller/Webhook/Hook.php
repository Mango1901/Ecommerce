<?php

namespace Magenest\GiaoHangTietKiem\Controller\Webhook;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\AlreadyExistsException;

class Hook extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface {
	/**
	 * @var \Magento\Framework\App\Request\Http
	 */
	protected $_request;

	/**
	 * @var \Magenest\GiaoHangTietKiem\Model\Carrier\GiaoHangTietKiem
	 */
	protected $_helper;
	/**
	 * @var \Psr\Log\LoggerInterface
	 */
	protected $_logger;

	/**
	 * Constructor
	 *
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\App\Request\Http $request
	 * @param \Psr\Log\LoggerInterface $logger
	 * @param \Magenest\GiaoHangTietKiem\Model\Carrier\GiaoHangTietKiem $helper
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\App\Request\Http $request,
		\Psr\Log\LoggerInterface $logger,
		\Magenest\GiaoHangTietKiem\Model\Carrier\GiaoHangTietKiem $helper
	) {
		$this->_request = $request;
		$this->_helper  = $helper;
		$this->_logger  = $logger;
		parent::__construct( $context );
	}


	public function execute() {
		$content = json_decode( urldecode( $this->_request->getContent() ), true );
		if ( is_array( $content ) ) {
			try {
				$this->_logger->debug( json_encode( $content ) );
				$this->_helper->updateShipmentStatus( $content );
			} catch ( AlreadyExistsException $e ) {
			}
		}
	}

	public function createCsrfValidationException( RequestInterface $request ): ?InvalidRequestException {
		return null;
	}

	public function validateForCsrf( RequestInterface $request ): ?bool {
		return true;
	}
}