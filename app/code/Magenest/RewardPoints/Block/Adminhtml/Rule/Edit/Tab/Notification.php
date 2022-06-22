<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 06/11/2020 14:32
 */

namespace Magenest\RewardPoints\Block\Adminhtml\Rule\Edit\Tab;

use Magenest\RewardPoints\Api\Data\NotificationInterface;
use Magenest\RewardPoints\Model\NotificationFactory as NotificationModel;
use Magenest\RewardPoints\Model\ResourceModel\Notification as NotificationResource;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Notification extends Generic implements TabInterface
{
    /**
     * @var NotificationResource
     */
    protected $_notificationResource;

    /**
     * @var NotificationModel
     */
    protected $_notificationModel;

    /**
     * Notification constructor.
     * @param NotificationModel $notificationModel
     * @param NotificationResource $notificationResource
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        NotificationModel $notificationModel,
        NotificationResource $notificationResource,
        \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory, array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_notificationModel = $notificationModel;
        $this->_notificationResource = $notificationResource;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('rewardpoints_rule');
        $notificationModel = $this->_notificationModel->create();
        $this->_notificationResource->load($notificationModel, $model->getId(), NotificationInterface::RULE_ID);

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');
        $htmlIdPrefix = $form->getHtmlIdPrefix();

        $fieldset = $form->addFieldset(
            'notification_fieldset',
            [
                'legend' => __('Notification Settings'),
                'class' => 'fieldset-wide'
            ]
        );

        if ($notificationModel->getData(NotificationInterface::ENTITY_ID)) {
            $fieldset->addField(
                'notification_id',
                'hidden',
                ['name' => 'notification_id']
            )->setData($notificationModel->getData(NotificationInterface::ENTITY_ID));
        }

        $notificationStatus = $fieldset->addField(
            'notification_status',
            'select',
            [
                'name' => 'notification_status',
                'label' => __('Notification Status'),
                'title' => __('Notification Status'),
                'options' => [
                    NotificationInterface::NOTIFICATION_STATUS_ENABLE => __('Active'),
                    NotificationInterface::NOTIFICATION_STATUS_DISABLE => __('Inactive')
                ],
                'required' => true
            ]
        );
        $notificationContent = $fieldset->addField(
            'notification_content',
            'text',
            [
                'label' => __('Content'),
                'title' => __('Content'),
                'name' => 'notification_content',
                'required' => true
            ]
        );
        $notificationPosition = $fieldset->addField(
            'notification_display_position',
            'select',
            [
                'label' => __('Display Position'),
                'title' => __('Display Position'),
                'name' => 'notification_display_position',
                'options' => [
                    NotificationInterface::NOTIFICATION_DISPLAY_POSITION_CUSTOMER => __('Customer Page'),
                    NotificationInterface::NOTIFICATION_DISPLAY_POSITION_CART => __('Cart Page')
                ]
            ]
        );

        $notificationForGuest = $fieldset->addField(
            'notification_display_for_guest',
            'select',
            [
                'label' => __('Display for Guest'),
                'title' => __('Display for Guest'),
                'name' => 'notification_display_for_guest',
                'options' => [
                    NotificationInterface::NOTIFICATION_DISPLAY_FOR_GUEST_YES => __('Yes'),
                    NotificationInterface::NOTIFICATION_DISPLAY_FOR_GUEST_NO => __('No')
                ]
            ]
        );

        $form->setValues([
            'notification_id' => $notificationModel->getData(NotificationInterface::ENTITY_ID),
            'notification_status' => $notificationModel->getData(NotificationInterface::NOTIFICATION_STATUS),
            'notification_content' => $notificationModel->getData(NotificationInterface::NOTIFICATION_CONTENT),
            'notification_display_position' => $notificationModel->getData(NotificationInterface::NOTIFICATION_DISPLAY_POSITION),
            'notification_display_for_guest' => $notificationModel->getData(NotificationInterface::NOTIFICATION_DISPLAY_FOR_GUEST)
        ]);
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )->addFieldMap(
                $notificationStatus->getHtmlId(),
                $notificationStatus->getName()
            )->addFieldMap(
                $notificationContent->getHtmlId(),
                $notificationContent->getName()
            )->addFieldMap(
                $notificationPosition->getHtmlId(),
                $notificationPosition->getName()
            )->addFieldMap(
                $notificationForGuest->getHtmlId(),
                $notificationForGuest->getName()
            )->addFieldDependence(
                $notificationContent->getName(),
                $notificationStatus->getName(),
                NotificationInterface::NOTIFICATION_STATUS_ENABLE
            )->addFieldDependence(
                $notificationPosition->getName(),
                $notificationStatus->getName(),
                NotificationInterface::NOTIFICATION_STATUS_ENABLE
            )->addFieldDependence(
                $notificationForGuest->getName(),
                $notificationStatus->getName(),
                NotificationInterface::NOTIFICATION_STATUS_ENABLE
            )
        );
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @inheritDoc
     */
    public function getTabLabel()
    {
        return __('Notification Settings');
    }

    /**
     * @inheritDoc
     */
    public function getTabTitle()
    {
        return __('Notification Settings');
    }

    /**
     * @inheritDoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isHidden()
    {
        return false;
    }
}