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

class Helper extends Template
{
    /**
     * @var ModuleGeneratorHelper
     */
    private $moduleGeneratorHelper;

    /**
     * Block constructor.
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

    public function getCurrentlySelectedModule()
    {
        return $this->moduleGeneratorHelper->getCurrentlySelectedModule();
    }
}