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
use Ibg\Codegen\Helper\ControllerAndRouteGenerator as ControllerAndRouteGeneratorHelper;

class ControllerAndRoute extends Template
{
    /**
     * @var ModuleGeneratorHelper
     */
    private $moduleGeneratorHelper;
    /**
     * @var ControllerAndRouteGeneratorHelper
     */
    private $controllerAndRouteGeneratorHelper;

    /**
     * ControllerAndRoute constructor.
     * @param Template\Context $context
     * @param ModuleGeneratorHelper $moduleGeneratorHelper
     * @param ControllerAndRouteGeneratorHelper $controllerAndRouteGeneratorHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ModuleGeneratorHelper $moduleGeneratorHelper,
        ControllerAndRouteGeneratorHelper $controllerAndRouteGeneratorHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->moduleGeneratorHelper = $moduleGeneratorHelper;
        $this->controllerAndRouteGeneratorHelper = $controllerAndRouteGeneratorHelper;
    }

    public function getCurrentlySelectedModule()
    {
        return $this->moduleGeneratorHelper->getCurrentlySelectedModule();
    }

    public function getFrontNameRegEx(){
        return $this->controllerAndRouteGeneratorHelper->getFrontNameRegEx();
    }
}