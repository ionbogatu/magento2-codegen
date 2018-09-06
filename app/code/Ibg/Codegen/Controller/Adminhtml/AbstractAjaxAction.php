<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/8/2018
 * Time: 5:47 PM
 */

namespace Ibg\Codegen\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Ibg\Codegen\Logger\Logger as CodegenLogger;

abstract class AbstractAjaxAction extends Action
{
    /**
     * @var CodegenLogger
     */
    protected $codegenLogger;

    /**
     * AbstractAjaxAction constructor.
     * @param Action\Context $context
     * @param CodegenLogger $codegenLogger
     */
    public function __construct(
        Action\Context $context,
        CodegenLogger $codegenLogger
    )
    {
        parent::__construct($context);

        $this->codegenLogger = $codegenLogger;
    }

    /**
     * @throws \Exception
     */
    public function isAjaxAndPost()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $errorMessages = [];
        if(!$this->getRequest()->isPost()){
            $errorMessages[] = __('Unsupported HTTP Verb.');
        }

        if(!$this->getRequest()->isAjax()){
            $errorMessages[] = __('Only Ajax Requests are available');
        }

        if(!empty($errorMessages)){
            throw new \Exception($errorMessages);
        }

        $this->validateParams();
    }

    public function logTime($message){
        $t = microtime(true);
        $micro = sprintf("%06d",($t - floor($t)) * 1000000);
        $d = new \DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
        $this->codegenLogger->info('[' . $d->format('Y-m-d H:i:s.u') . ']: ' . $message);
    }

    /**
     * @return void
     * @throws \Exception
     */
    abstract protected function validateParams();
}
