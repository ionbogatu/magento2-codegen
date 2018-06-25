<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/7/2018
 * Time: 7:41 PM
 */

namespace Ibg\Codegen\Block\Adminhtml\Globalpage;

use Magento\Backend\Block\Template;
use Ibg\Codegen\Helper\ModuleGenerator as ModuleGeneratorHelper;
use Magento\Framework\Module\ModuleListInterface;

class CurrentlySelectedModule extends Template
{
    /**
     * @var ModuleGeneratorHelper
     */
    private $moduleGeneratorHelper;
    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * CurrentlySelectedModule constructor.
     * @param Template\Context $context
     * @param ModuleGeneratorHelper $moduleGeneratorHelper
     * @param ModuleListInterface $moduleList
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ModuleGeneratorHelper $moduleGeneratorHelper,
        ModuleListInterface $moduleList,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->moduleGeneratorHelper = $moduleGeneratorHelper;
        $this->moduleList = $moduleList;
    }

    /**
     * @return mixed
     */
    public function getCurrentlySelectedModule()
    {
        return $this->moduleGeneratorHelper->getCurrentlySelectedModule();
    }

    /**
     * @return string[]
     */
    public function getModuleList()
    {
        $result = $this->moduleList->getNames();
        sort($result, SORT_STRING);

        return $result;
    }

    public function getModuleNameRegEx()
    {
        return $this->moduleGeneratorHelper->getModuleNameRegEx();
    }
}