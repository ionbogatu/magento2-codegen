<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 9/7/2018
 * Time: 10:57 AM
 */

namespace Ibg\Codegen\Controller\Adminhtml\Helper;

use Ibg\Codegen\Controller\Adminhtml\AbstractAjaxAction;
use Ibg\Codegen\Helper\ModuleGenerator as ModuleGeneratorHelper;
use Ibg\Codegen\Logger\Logger as CodegenLogger;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Ibg\Codegen\Helper\HelperGenerator as HelperGeneratorHelper;

class CreateHelper extends AbstractAjaxAction
{
    /**
     * @var ModuleGeneratorHelper
     */
    private $moduleGeneratorHelper;
    /**
     * @var HelperGeneratorHelper
     */
    private $helperGeneratorHelper;

    /**
     * CreateHelper constructor.
     * @param Action\Context $context
     * @param CodegenLogger $codegenLogger
     * @param ModuleGeneratorHelper $moduleGeneratorHelper
     * @param HelperGeneratorHelper $helperGeneratorHelper
     */
    public function __construct(
        Action\Context $context,
        CodegenLogger $codegenLogger,
        ModuleGeneratorHelper $moduleGeneratorHelper,
        HelperGeneratorHelper $helperGeneratorHelper
    )
    {
        parent::__construct($context, $codegenLogger);

        $this->moduleGeneratorHelper = $moduleGeneratorHelper;
        $this->helperGeneratorHelper = $helperGeneratorHelper;
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

            $resultData = $this->createHelper();
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
                throw new \Exception(__('You did not select any module to put new helper.'));
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
            throw new \Exception(__('Area should be selected when creating new helper.'));
        }

        // validate helper name
        if(empty($params['helper_name'])){
            throw new \Exception(__('Helper name cannot be empty when creating new helper.'));
        }

        $pattern = $this->helperGeneratorHelper->getHelperNameRegEx();
        $pregMatchResult = preg_match($pattern, $params['helper_name'], $matches);
        if($pregMatchResult === 0){
            throw new \Exception(sprintf(__('%s is an invalid name for helper. Please choose another one that match the regular expression %s'), $params['helper_name'], $pattern));
        }else if($pregMatchResult === false){
            throw new \Exception(__('Cannot validate helper name.'));
        }
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Exception
     */
    private function createHelper(){
        $filesToGenerate = $this->helperGeneratorHelper->getFilesToGenerate();

        $module_name = $this->moduleGeneratorHelper->getCurrentlySelectedModule();

        foreach($filesToGenerate as $file){

            if(is_array($file)){
                $copyPath = $file['copy_path'];
                $pastePath = $file['paste_path'];
            }else{
                $copyPath = $pastePath = $file;
            }

            $this->helperGeneratorHelper->copyFileToLocation(
                $copyPath,
                $this->helperGeneratorHelper->buildLocation($module_name, $pastePath),
                [
                    'modulePath' => trim(str_replace('_', '\\', $module_name)),
                    'helperName'=> $this->_request->getParam('helper_name')
                ],
                $pastePath
            );
        }

        return ['success' => true];
    }
}
