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

    public function getCurrentlySelectedModule()
    {
        return $this->moduleGeneratorHelper->getCurrentlySelectedModule();
    }

    public function getBlockClassNameRegEx()
    {
        return $this->blockGeneratorHelper->getBlockClassNameRegEx();
    }

    public function getBlockNameInLayoutRegEx()
    {
        return $this->blockGeneratorHelper->getBlockNameInLayoutRegEx();
    }

    public function getAreas()
    {
        $result = [];

        $areaCodes = $this->areaList->getCodes();

        foreach($areaCodes as $areaCode){

            if(in_array($areaCode, ['crontab', 'webapi_rest', 'webapi_soap']))
                continue;

            $result[] = [
                'label' => $areaCode,
                'value' => $areaCode,
            ];
        }

        return $result;
    }
}