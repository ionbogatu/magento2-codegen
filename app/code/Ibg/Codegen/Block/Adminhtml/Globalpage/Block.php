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
use Ibg\Codegen\Helper\BlockGenerator as BlockGeneratorHelper;
use Magento\Framework\App\AreaList;

class Block extends Template
{
    /**
     * @var ModuleGeneratorHelper
     */
    private $moduleGeneratorHelper;
    /**
     * @var BlockGeneratorHelper
     */
    private $blockGeneratorHelper;
    /**
     * @var AreaList
     */
    private $areaList;

    /**
     * Block constructor.
     * @param Template\Context $context
     * @param ModuleGeneratorHelper $moduleGeneratorHelper
     * @param BlockGeneratorHelper $blockGeneratorHelper
     * @param AreaList $areaList
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ModuleGeneratorHelper $moduleGeneratorHelper,
        BlockGeneratorHelper $blockGeneratorHelper,
        AreaList $areaList,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->moduleGeneratorHelper = $moduleGeneratorHelper;
        $this->blockGeneratorHelper = $blockGeneratorHelper;
        $this->areaList = $areaList;
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
            $moduleBlock = $this->getLayout()->createBlock('\Magento\Backend\Block\Template', 'codegen.block.module');
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

    public function getBlockNameRegEx()
    {
        return $this->blockGeneratorHelper->getBlockNameRegEx();
    }
}
