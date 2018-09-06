<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 7/9/2018
 * Time: 6:35 PM
 */

namespace Ibg\Codegen\Block\Adminhtml\Globalpage;

use Magento\Backend\Block\Template;
use Magento\Framework\App\AreaList;
use Ibg\Codegen\Helper\ModuleGenerator as ModuleGeneratorHelper;
use Ibg\Codegen\Helper\ControllerAndRouteGenerator as ControllerAndRouteGeneratorHelper;

class Controller extends Template
{
    /**
     * @var AreaList
     */
    private $areaList;
    /**
     * @var ModuleGeneratorHelper
     */
    private $moduleGeneratorHelper;
    /**
     * @var ControllerAndRouteGeneratorHelper
     */
    private $controllerAndRouteGeneratorHelper;

    /**
     * Controller constructor.
     * @param Template\Context $context
     * @param AreaList $areaList
     * @param ModuleGeneratorHelper $moduleGeneratorHelper
     * @param ControllerAndRouteGeneratorHelper $controllerAndRouteGeneratorHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        AreaList $areaList,
        ModuleGeneratorHelper $moduleGeneratorHelper,
        ControllerAndRouteGeneratorHelper $controllerAndRouteGeneratorHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->areaList = $areaList;
        $this->moduleGeneratorHelper = $moduleGeneratorHelper;
        $this->controllerAndRouteGeneratorHelper = $controllerAndRouteGeneratorHelper;
    }

    public function getApplicationAreas()
    {
        $areaCodes = $this->areaList->getCodes();

        unset($areaCodes[array_search('crontab', $areaCodes)]);
        unset($areaCodes[array_search('webapi_rest', $areaCodes)]);
        unset($areaCodes[array_search('webapi_soap', $areaCodes)]);

        return $areaCodes;
    }

    public function getAdditionalSlides()
    {
        $blocks = [];

        if(!$this->moduleGeneratorHelper->getCurrentlySelectedModule()){
            /**
             * @var \Magento\Backend\Block\Template $block
             */
            $moduleBlock = $this->getLayout()->createBlock('\Magento\Backend\Block\Template', 'codegen.controller.module');
            $moduleBlock->setData('additional_class', 'module');
            $moduleBlock->setData('module_name_reg_ex', $this->moduleGeneratorHelper->getModuleNameRegEx());
            $moduleBlock->setTemplate('Ibg_Codegen::globalpage/parts/module.phtml');
            $blocks[] = $moduleBlock;
        }

        $result = '';

        foreach($blocks as $block){
            $result .= $block->toHtml();
        }

        return $result;
    }

    public function getFrontNameRegEx()
    {
        return $this->controllerAndRouteGeneratorHelper->getFrontNameRegEx();
    }

    public function getControllerNameRegEx()
    {
        return $this->controllerAndRouteGeneratorHelper->getControllerNameRegEx();
    }

    public function getActionNameRegEx()
    {
        return $this->controllerAndRouteGeneratorHelper->getActionNameRegEx();
    }
}
