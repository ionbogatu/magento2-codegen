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

class CurrentlySelectedModule extends Template
{
    /**
     * @var ModuleGeneratorHelper
     */
    private $moduleGeneratorHelper;

    /**
     * CurrentlySelectedModule constructor.
     * @param Template\Context $context
     * @param ModuleGeneratorHelper $moduleGeneratorHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ModuleGeneratorHelper $moduleGeneratorHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->moduleGeneratorHelper = $moduleGeneratorHelper;
    }

    /**
     * @return mixed
     */
    public function getCurrentlySelectedModule()
    {
        return $this->moduleGeneratorHelper->getCurrentlySelectedModule();
    }
}