<?php

namespace Magenest\SellOnInstagram\Block\Adminhtml\Feed\Edit\Tab;

use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Serialize\Serializer\Json;
use Magenest\SellOnInstagram\Model\InstagramFeed;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Rule\Block\Conditions as BlockConditions;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Framework\Stdlib\DateTime\TimezoneInterfaceFactory;
use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory as ScheduleCollectionFactory;
use Psr\Log\LoggerInterface;

class Cron extends Generic implements TabInterface
{
    const ENABLE_CRON = 1;
    const DISABLE_CRON = 0;
    /**
     * @var Fieldset
     */
    protected $_rendererFieldset;

    /**
     * @var BlockConditions
     */
    protected $_conditions;

    /**
     * @var Json
     */
    protected $_jsonFramework;

    protected $_feedModel = null;

    /**
     * @var RuleFactory
     */
    protected $_ruleFactory;
    /**
     * @var \Magenest\SellOnInstagram\Model\Config\Source\Day
     */
    protected $sourceDay;
    /**
     * @var \Magenest\SellOnInstagram\Model\Config\Source\Time
     */
    protected $sourceTime;
    /**
     * @var \Magenest\SellOnInstagram\Model\Config\Source\ActionType
     */
    protected $actionType;
    /**
     * @var TimezoneInterfaceFactory
     */
    protected $timezoneFactory;
    /**
     * @var ScheduleCollectionFactory
     */
    protected $scheduleCollectionFactory;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Conditions constructor.
     *
     * @param Fieldset $rendererFieldset
     * @param BlockConditions $conditions
     * @param RuleFactory $ruleFactory
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Json $jsonFramework
     * @param array $data
     */
    public function __construct(
        Fieldset $rendererFieldset,
        BlockConditions $conditions,
        RuleFactory $ruleFactory,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Json $jsonFramework,
        \Magenest\SellOnInstagram\Model\Config\Source\Day $sourceDay,
        \Magenest\SellOnInstagram\Model\Config\Source\Time $sourceTime,
        \Magenest\SellOnInstagram\Model\Config\Source\ActionType $actionType,
        TimezoneInterfaceFactory $timezoneFactory,
        ScheduleCollectionFactory $scheduleCollectionFactory,
        LoggerInterface $logger,
        array $data = []
    )
    {
        $this->_rendererFieldset = $rendererFieldset;
        $this->_conditions = $conditions;
        $this->_ruleFactory = $ruleFactory;
        $this->_jsonFramework = $jsonFramework;
        $this->sourceDay = $sourceDay;
        $this->sourceTime = $sourceTime;
        $this->actionType = $actionType;
        $this->timezoneFactory = $timezoneFactory;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->logger = $logger;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Phrase|string
     */
    public function getTabLabel()
    {
        return __("Scheduled Task");
    }

    /**
     * @return Phrase|string
     */
    public function getTabTitle()
    {
        return __("Scheduled Task");
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function canShowTab()
    {
        return $this->getFeedModel()->getId();
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Tab class getter
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * @return Conditions
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry(InstagramFeed::REGISTER);
        $form = $this->addTabToForm($model);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param        $model
     * @param string $fieldsetId
     * @param string $formName
     *
     * @return Form
     * @throws LocalizedException
     */
    protected function addTabToForm($model, $fieldsetId = 'scheduled_task', $formName = 'instagram_feed_form')
    {
        $form = $this->_formFactory->create();
        $form->setFieldNameSuffix('feed');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            $fieldsetId, ['legend' => __('Scheduled Task')]);
        $fieldStatus = $fieldset->addField('cron_enable', 'select', [
            'required' => false,
            'name'   => 'cron_enable',
            'label'  => __('Enabled'),
            'values' => [self::DISABLE_CRON => __('No'), self::ENABLE_CRON => __('Yes')],
            'data-form-part' => $formName,
            'note'   => __(
                'If enabled, the extension will generate a feed by schedule. To generate feed by schedule, magento cron must be configured.'
            )
        ]);
        $fieldDays = $fieldset->addField('cron_day', 'multiselect', [
            'label'    => __('Days of the week'),
            'required' => true,
            'name'     => 'cron_day',
            'values'   => $this->sourceDay->toOptionArray(),
            'data-form-part' => $formName,
        ]);
        $timeNow          = $this->timezoneFactory->create()->date()->format('h:i A');

        $lastFeedCron = '-';
        $cron = $this->scheduleCollectionFactory->create();
        $cron->addFieldToFilter('job_code', 'sync_product')
            ->addFieldToFilter('status', 'success')
            ->setOrder('executed_at', 'desc')
            ->getFirstItem()
            ->setPageSize(1);

        if ($cron->getSize()) {
            $timezone     = $this->timezoneFactory->create();
            $lastFeedCron = $timezone->date($cron->fetchItem()->getExecutedAt())->format('d.m.Y h:i A');
        }

        $message = '
            <table>
                <tr>
                    <th align="left">Current Time </th>
                    <td>'. $timeNow.'</td>
                </tr>
                <tr>
                    <th>Last Feed Cron Run&nbsp;</th>
                    <td>'. $lastFeedCron.'</td>
                </tr>
            </table>';
        $fieldTimes = $fieldset->addField('cron_time', 'multiselect', [
            'label'    => __('Time of the day'),
            'required' => true,
            'name'     => 'cron_time',
            'values'   => $this->sourceTime->toOptionArray(),
            'data-form-part' => $formName,
            'note' => $message,
        ]);
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('\Magento\Backend\Block\Widget\Form\Element\Dependence')
                ->addFieldMap($fieldStatus->getHtmlId(), $fieldStatus->getName())
                ->addFieldMap($fieldDays->getHtmlId(), $fieldDays->getName())
                ->addFieldMap($fieldStatus->getHtmlId(), $fieldStatus->getName())
                ->addFieldMap($fieldTimes->getHtmlId(), $fieldTimes->getName())
                ->addFieldDependence($fieldDays->getName(), $fieldStatus->getName(), self::ENABLE_CRON)
                ->addFieldDependence($fieldTimes->getName(), $fieldStatus->getName(), self::ENABLE_CRON)
        );

        if ($this->getRequest()->getParam('id')) {
            $feedModel = $this->getFeedModel();
            if ($feedModel->getCronDay()) {
                try {
                    $feedModel->setCronDay($this->_jsonFramework->unserialize($feedModel->getCronDay()));
                } catch (\InvalidArgumentException $e) {
                    $this->logger->debug($e->getMessage());
                }
            }
            if ($feedModel->getCronTime()) {
                try {
                    $feedModel->setCronTime($this->_jsonFramework->unserialize($feedModel->getCronTime()));
                } catch (\InvalidArgumentException $e) {
                    $this->logger->debug($e->getMessage());
                }
            }
            $feedModel->setId($this->getRequest()->getParam('id'));
            $form->setValues($feedModel->getData());
        }
        return $form;
    }

    public function getFeedModel()
    {
        if ($this->_feedModel == null) {
            $this->_feedModel = $this->_coreRegistry->registry(InstagramFeed::REGISTER);
        }

        return $this->_feedModel;
    }
}
