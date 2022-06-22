<?php

namespace Magenest\NotificationBox\Observer;

use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Filesystem\DirectoryList;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\App\RequestInterface;
use Magenest\NotificationBox\Helper\Helper;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList as PathDirectoryList;

class SaveConfig implements ObserverInterface
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param Logger $logger
     */

    /** @var RequestInterface  */
    protected $requestInterface;

    /**
     * @var DirectoryList
     */
    protected $dir;

    /**
     * @var ComponentRegistrarInterface
     */
    protected $path;

    /** @var Helper  */
    protected $helper;

    /** @var Filesystem  */
    protected $filesystem;

    /**
     * @param Logger $logger
     * @param RequestInterface $requestInterface
     * @param DirectoryList $dir
     * @param ComponentRegistrarInterface $path
     * @param Helper $helper
     * @param Filesystem $filesystem
     */
    public function __construct(
        Logger $logger,
        RequestInterface $requestInterface,
        DirectoryList $dir,
        ComponentRegistrarInterface $path,
        Helper $helper,
        Filesystem $filesystem
    )
    {
        $this->filesystem = $filesystem;
        $this->helper = $helper;
        $this->dir = $dir;
        $this->path = $path;
        $this->logger = $logger;
        $this->requestInterface = $requestInterface;
    }

    public function execute(EventObserver $observer)
    {
        $params = $this->requestInterface->getParam('groups');
        if(isset($params['general']['fields']['api_key']['value']) && isset($params['general']['fields']['sender_id']['value'])){
            $senderId = $params['general']['fields']['sender_id']['value'];
            $rootPub = $this->dir->getPath('pub');
            try {
                $media = $this->filesystem->getDirectoryWrite(PathDirectoryList::MEDIA);
                //add content for firebase-messaging-sw file
                $contents = "importScripts('https://www.gstatic.com/firebasejs/4.1.3/firebase-app.js');\r\nimportScripts('https://www.gstatic.com/firebasejs/4.1.3/firebase-messaging.js');\r\n\r\nconst firebaseConfig = {\r\n    messagingSenderId: \"".$senderId."\"\r\n};\r\n//init\r\nfirebase.initializeApp(firebaseConfig);\r\nconst messaging = firebase.messaging();\r\n\r\n//Handle messages when your web app is in the background\r\nmessaging.setBackgroundMessageHandler(function (payload) {\r\n    payload.data['data'] = payload.data;\r\n    registration.showNotification(payload.data.title, payload.data['data']);\r\n});\r\n\r\n\r\n//handle when click to notification\r\nself.addEventListener('notificationclick', function(event) {\r\n \r\n    var click_action = event.notification;\r\n    event.notification.close()\r\n    event.waitUntil(clients.matchAll({type: \"window\"})\r\n        .then(function(clientList) {\r\n            return clients.openWindow(\"\");\r\n        }));\r\n});\r\n\r\nconst showMessage = function(payload){\r\n \r\n    const notificationTitle = payload.data.title;\r\n    const notificationOptions = {\r\n        body: payload.data.body,\r\n        icon: payload.data.icon,\r\n        image: payload.data.image,\r\n        click_action: payload.data.click_action,\r\n        data:payload.data.click_action\r\n    };\r\n\r\n\r\n    return self.registration.showNotification(notificationTitle,notificationOptions);\r\n}\r\n";
                $media->writeFile("firebase-messaging-sw.js",$contents);

                $firebasePath = $this->dir->getPath('media');
                $this->helper->copyFile($firebasePath, $rootPub);

            } catch(\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }

}
