<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/7/2018
 * Time: 9:11 PM
 */

namespace Ibg\Codegen\Helper;

use Magento\Backend\Model\Session;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class ModuleGenerator extends GeneratorHelper
{
    /**
     * @var Session
     */
    private $session;

    /**
     * ModuleGenerator constructor.
     * @param Context $context
     * @param Session $session
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Context $context,
        DirectoryList $directoryList,
        Session $session
    )
    {
        parent::__construct($context, $directoryList);

        $this->session = $session;
    }

    public function getCurrentlySelectedModule()
    {
        return $this->session->getCurrentlySelectedModule();
    }

    public function getModuleNameRegEx(){

        return '/([A-Z]+[a-z]*)_[A-Za-z]+/';
    }

    public function getModuleFilesToGenerate(){

        $codegenModulePath = $this->getCodegenModulePath();

        return [
            $this->directoryList->getPath(DirectoryList::APP) . '/code/' . $codegenModulePath . '/_Files/ModuleEthalone/etc/module.xml.sample',
            $this->directoryList->getPath(DirectoryList::APP) . '/code/' . $codegenModulePath . '/_Files/ModuleEthalone/registration.php.sample'
        ];
    }

    public function regenerateConfigFile($config)
    {
        $configFilePath = $this->directoryList->getPath(DirectoryList::APP) . '/etc/config.php';

        $fd = fopen($configFilePath, 'w');

        fwrite($fd, "<?php\n");
        fwrite($fd, "return [\n");
        fwrite($fd, "  'modules' => [\n");

        $arrayKeys = array_keys($config['modules']);
        $lastKey = end($arrayKeys);

        foreach($config['modules'] as $key => $module){
            if ($lastKey === $key){
                fwrite($fd, "    '" . $key . "' => " . $module . "\n");
            }else{
                fwrite($fd, "    '" . $key . "' => $module,\n");
            }
        }

        fwrite($fd, "  ]\n");
        fwrite($fd, "];");

        fclose($fd);
    }
}