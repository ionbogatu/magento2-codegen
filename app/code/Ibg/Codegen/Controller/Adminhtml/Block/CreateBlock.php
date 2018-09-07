<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 9/7/2018
 * Time: 10:57 AM
 */

namespace Ibg\Codegen\Controller\Adminhtml\Block;

use Ibg\Codegen\Controller\Adminhtml\AbstractAjaxAction;
use Ibg\Codegen\Helper\ModuleGenerator as ModuleGeneratorHelper;
use Ibg\Codegen\Logger\Logger as CodegenLogger;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Ibg\Codegen\Helper\BlockGenerator as BlockGeneratorHelper;

class CreateBlock extends AbstractAjaxAction
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
     * CreateBlock constructor.
     * @param Action\Context $context
     * @param CodegenLogger $codegenLogger
     * @param ModuleGeneratorHelper $moduleGeneratorHelper
     * @param BlockGeneratorHelper $blockGeneratorHelper
     */
    public function __construct(
        Action\Context $context,
        CodegenLogger $codegenLogger,
        ModuleGeneratorHelper $moduleGeneratorHelper,
        BlockGeneratorHelper $blockGeneratorHelper
    )
    {
        parent::__construct($context, $codegenLogger);

        $this->moduleGeneratorHelper = $moduleGeneratorHelper;
        $this->blockGeneratorHelper = $blockGeneratorHelper;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try{
            $this->isAjaxAndPost();

            if(!empty($params['destination'])){
                if($params['destination'] === 'create'){
                    $this->moduleGeneratorHelper->createModule();
                }else if($params['destination'] === 'select'){
                    $this->moduleGeneratorHelper->selectModule();
                }
            }

            $resultData = $this->createBlock();
        }catch(\Exception $e){
            $result->setData(['success' => false, 'message' => $e->getMessage()]);
            return $result;
        }

        $result->setData($resultData);
        return $result;
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function validateParams()
    {
        $params = $this->getRequest()->getParams();

        // validate module creation
        if(!empty($params['destination'])){
            if(empty($params['module_name'])){
                throw new \Exception(__('You did not select any module to put new block.'));
            }

            $pattern = $this->moduleGeneratorHelper->getModuleNameRegEx();
            $pregMatchResult = preg_match($pattern, $params['module_name'], $matches);
            if($pregMatchResult === 0){
                throw new \Exception(sprintf(__('%s is an invalid name for module name. Please choose another one that match the regular expression %s'), $params['module_name'], $pattern));
            }else if($pregMatchResult === false){
                throw new \Exception(__('Cannot validate module name.'));
            }
        }else{
            $params['module_name'] = $this->moduleGeneratorHelper->getCurrentlySelectedModule();
            $this->getRequest()->setParams($params);
        }

        // validate area
        if(empty($params['area'])){
            throw new \Exception(__('Area should be selected when creating new block.'));
        }

        // validate block name
        if(empty($params['block_name'])){
            throw new \Exception(__('Block name cannot be empty when creating new block.'));
        }

        $pattern = $this->blockGeneratorHelper->getBlockNameRegEx();
        $pregMatchResult = preg_match($pattern, $params['block_name'], $matches);
        if($pregMatchResult === 0){
            throw new \Exception(sprintf(__('%s is an invalid name for block. Please choose another one that match the regular expression %s'), $params['block_name'], $pattern));
        }else if($pregMatchResult === false){
            throw new \Exception(__('Cannot validate block name.'));
        }
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Exception
     */
    private function createBlock(){
        $filesToGenerate = $this->blockGeneratorHelper->getFilesToGenerate();

        $module_name = $this->moduleGeneratorHelper->getCurrentlySelectedModule();

        foreach($filesToGenerate as $file){

            if(is_array($file)){
                $copyPath = $file['copy_path'];
                $pastePath = $file['paste_path'];
            }else{
                $copyPath = $pastePath = $file;
            }

            $this->blockGeneratorHelper->copyFileToLocation(
                $copyPath,
                $this->blockGeneratorHelper->buildLocation($module_name, $pastePath),
                [
                    'modulePath' => trim(str_replace('_', '\\', $module_name)),
                    'blockName'=> $this->_request->getParam('block_name')
                ],
                $pastePath
            );
        }

        return ['success' => true];
    }
}
