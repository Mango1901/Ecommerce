<?php

namespace Magenest\SellOnInstagram\Controller\Adminhtml\Mapping;

use Magento\Framework\Controller\ResultFactory;

class Save extends AbstractMapping
{
    public function execute()
    {
        $controllerResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $params = $this->getRequest()->getParams();
            $mappingModel = $this->mappingFactory->create();
            $id = $params['id'] ?? null;
            if ($id) {
                $this->mappingResource->load($mappingModel, $id);
                unset($params['id']);
            }
            $templateContent = $params['template_mapping'] ?? [];
            if (isset($params['template_name']) && $params['template_name'] != "") {
                $templateCollection = $this->mappingCollectionFactory->create();
                $templateCollection->addFieldToFilter('name', $params['template_name'])
                    ->addFieldToFilter('id', ['neq' => $id]);
                if ($templateCollection->getSize() > 0) {
                    throw new \Exception('The template name has already exited');
                } else {
                    $mappingModel->setName($params['template_name']);
                }
            } else {
                throw new \Exception('The template name is required');
            }
            $mappingModel->setContentTemplate($this->jsonFramework->serialize($templateContent));
            $this->mappingResource->save($mappingModel);
            $urlRedirect = $this->getUrl(
                'instagramshop/mapping/edit',
                [
                    'id' => $mappingModel->getId()
                ]
            );
            $attributesMapped = $this->mappingResource->getAllMagentoMappedFields($id);
            $attributesNew = [];
            $attributesFbNew = [];
            if ($templateContent != '') {
                foreach ($templateContent as $content) {
                    $content['template_id'] = $mappingModel->getId();
                    $this->mappingResource->saveTemplateContent($content);
                    $attributesNew[] = $content['magento_attribute'];
                    $attributesFbNew[] = $content['fb_attribute'];
                }
            }
            $diffArray = array_diff($attributesMapped, $attributesNew);
            $diffMagentoArray = array_unique(array_diff_assoc($attributesNew, array_unique($attributesNew)));
            $diffFbArray = array_unique(array_diff_assoc($attributesFbNew, array_unique($attributesFbNew)));
            $hasMagentoDuplicates = count($attributesNew) > count(array_unique($attributesNew));
            $hasFbDuplicates = count($attributesFbNew) > count(array_unique($attributesFbNew));
            if ($hasMagentoDuplicates) {
                $this->mappingResource->deleteTemplateContent($id, $diffMagentoArray);
                throw new \Exception('DUPLICATE: magento attribute fields is duplicated. We auto replace them.');
            }
            if ($hasFbDuplicates) {
                $this->mappingResource->deleteFbTemplateContent($id, $diffFbArray);
                throw new \Exception('DUPLICATE: facebook attribute fields is duplicated. We auto replace them.');
            }
            $this->mappingResource->deleteTemplateContent($id, $diffArray);
            $result = [
                'flag' => true,
                'message' => __("You saved the Mapping Template"),
                'url' => $urlRedirect
            ];
            $this->messageManager->addSuccessMessage(__("You saved the Mapping Template"));

        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $result = [
                'flag' => false,
                'message' => $exception->getMessage(),
                'url' => $this->getUrl(
                    'instagramshop/mapping/edit',
                    [
                        'id' => $mappingModel->getId()
                    ]
                ),
            ];
        }
        $controllerResult->setData($result);
        return $controllerResult;
    }
}
