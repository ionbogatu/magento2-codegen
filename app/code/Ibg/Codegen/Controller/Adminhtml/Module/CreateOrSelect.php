<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/8/2018
 * Time: 5:41 PM
 */

namespace Ibg\Codegen\Controller\Adminhtml\Module;

use Ibg\Codegen\Controller\Adminhtml\AbstractAjaxAction;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Ibg\Codegen\Helper\ModuleGenerator as ModuleGeneratorHelper;
use Ibg\Codegen\Helper\ControllerAndRouteGenerator as ControllerAndRouteGeneratorHelper;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem;
use Ibg\Codegen\Logger\Logger as CodegenLogger;

class CreateOrSelect extends AbstractAjaxAction
{
    /**
     * @var DirectoryList
     */
    private $directoryList;
    /**
     * @var ModuleGeneratorHelper
     */
    private $moduleGeneratorHelper;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var ControllerAndRouteGeneratorHelper
     */
    private $controllerAndRouteGeneratorHelper;

    /**
     * CreateOrSelect constructor.
     * @param Action\Context $context
     * @param DirectoryList $directoryList
     * @param ModuleGeneratorHelper $moduleGeneratorHelper
     * @param ControllerAndRouteGeneratorHelper $controllerAndRouteGeneratorHelper
     * @param Filesystem $filesystem
     * @param CodegenLogger $codegenLogger
     */
    public function __construct(
        Action\Context $context,
        DirectoryList $directoryList,
        ModuleGeneratorHelper $moduleGeneratorHelper,
        ControllerAndRouteGeneratorHelper $controllerAndRouteGeneratorHelper,
        Filesystem $filesystem,
        CodegenLogger $codegenLogger
    )
    {
        parent::__construct($context, $codegenLogger);
        $this->logTime('CreateOrSelect action started.');

        $this->directoryList = $directoryList;
        $this->moduleGeneratorHelper = $moduleGeneratorHelper;
        $this->controllerAndRouteGeneratorHelper = $controllerAndRouteGeneratorHelper;
        $this->filesystem = $filesystem;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $this->isAjaxAndPost();

            if($params['destination'] === 'create'){
                $resultData = $this->moduleGeneratorHelper->createModule();
            }else if($params['destination'] === 'select'){
                $resultData = $this->moduleGeneratorHelper->selectModule();
            }else{
                throw new \Exception('Undefined destination action.');
            }
        }catch (\Exception $e){
            $result->setData(['success' => false, 'message' => $e->getMessage()]);
            return $result;
        }

        $this->logTime('CreateOrSelect action ended.');

        $result->setData($resultData);
        return $result;
    }

    /**
     * @throws \Exception
     */
    protected function validateParams()
    {
        $params = $this->getRequest()->getParams();

        if (empty($params['module_name'])) {
            throw new \Exception(__('Module name cannot be empty when creating new module.'));
        }

        if (empty($params['destination'])) {
            throw new \Exception(__('Cannot determine whether to create a new module or select an existing one.'));
        }
    }
}
