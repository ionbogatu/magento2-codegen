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
use Ibg\Codegen\Helper\HelperGenerator as HelperGeneratorHelper;
use Magento\Framework\App\AreaList;

class Helper extends Template
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
     * @var HelperGeneratorHelper
     */
    private $helperGeneratorHelper;

    /**
     * Helper constructor.
     * @param Template\Context $context
     * @param AreaList $areaList
     * @param ModuleGeneratorHelper $moduleGeneratorHelper
     * @param HelperGeneratorHelper $helperGeneratorHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        AreaList $areaList,
        ModuleGeneratorHelper $moduleGeneratorHelper,
        HelperGeneratorHelper $helperGeneratorHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->areaList = $areaList;
        $this->moduleGeneratorHelper = $moduleGeneratorHelper;
        $this->helperGeneratorHelper = $helperGeneratorHelper;
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
            $moduleBlock = $this->getLayout()->createBlock('\Magento\Backend\Block\Template', 'codegen.helper.module');
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

    public function getHelperNameRegEx()
    {
        return $this->helperGeneratorHelper->getHelperNameRegEx();
    }
}
