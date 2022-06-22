<?php

namespace Magenest\SellOnInstagram\Controller\Adminhtml\Feed;

class Save extends AbstractFeed
{
    public function execute()
    {
        try {
            $params = $this->getRequest()->getParams();
            $instagramFeedModel = $this->instagramFeedFactory->create();
            $id = $params['general']['id'] ?? null;
            if ($id) {
                $this->instagramFeedResource->load($instagramFeedModel, $id);
                unset($params['id']);
            }
            $dataRaw = $params['general'];
            if (!isset($params['rule']) && isset($params['general']['conditions_serialized'])) {
                $dataRaw['conditions_serialized'] = $params['general']['conditions_serialized'];
            } else {
                $conditions = $params['rule'] ?? [];
                $dataRaw['conditions_serialized'] = $this->saveConditions($conditions);
            }
            if (isset($params['feed'])) {
                $cronDay = $params['feed']['cron_day'] ?? [];
                $dataRaw['cron_day'] = $this->jsonFramework->serialize($cronDay);
                $cronTime = $params['feed']['cron_time'] ?? [];
                $dataRaw['cron_time'] = $this->jsonFramework->serialize($cronTime);
                $dataRaw['cron_enable'] = $params['feed']['cron_enable'] ?? 0;
            }
            $instagramFeedModel->addData($dataRaw);
            $this->instagramFeedResource->save($instagramFeedModel);
            $instagramFeedModel->syncByFeedId();
            $this->messageManager->addSuccessMessage(__("You saved the Feed."));
            if (isset($params['back']) && $params['back'] == 'edit') {
                return $this->_redirect('*/*/edit', ['id' => $instagramFeedModel->getId()]);
            } else {
                return $this->_redirect('*/*');
            }
        } catch (\InvalidArgumentException | \Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->logger->debug($exception->getMessage());
            return $this->_redirect('*/*');
        }
    }

    /**
     * @param $conditions
     *
     * @return bool|string
     */
    private function saveConditions($conditions)
    {
        $salesRuleModel = $this->ruleFactory->create();
        $salesRuleModel->loadPost($conditions);
        $asArray = $salesRuleModel->getConditions()->asArray();
        return $this->jsonFramework->serialize($asArray);
    }
}
