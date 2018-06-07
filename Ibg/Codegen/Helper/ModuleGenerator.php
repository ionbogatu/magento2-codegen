<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/7/2018
 * Time: 9:11 PM
 */

namespace Ibg\Codegen\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Backend\Model\Session;
use Magento\Framework\App\Helper\Context;

class ModuleGenerator extends AbstractHelper
{
    /**
     * @var Session
     */
    private $session;

    /**
     * ModuleGenerator constructor.
     * @param Context $context
     * @param Session $session
     */
    public function __construct(
        Context $context,
        Session $session
    )
    {
        parent::__construct($context);
        $this->session = $session;
    }

    public function getCurrentlySelectedModule()
    {
        return $this->session->getCurrentlySelectedModule();
    }
}