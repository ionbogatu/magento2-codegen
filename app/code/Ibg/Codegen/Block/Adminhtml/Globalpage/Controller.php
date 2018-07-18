<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 7/9/2018
 * Time: 6:35 PM
 */

namespace Ibg\Codegen\Block\Adminhtml\Globalpage;

use Magento\Backend\Block\Template;
use Magento\Framework\App\AreaList;

class Controller extends Template
{
    /**
     * @var AreaList
     */
    private $areaList;

    /**
     * Controller constructor.
     * @param Template\Context $context
     * @param AreaList $areaList
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        AreaList $areaList,
        array $data = []
    )
    {
        parent::__construct($context, $data);

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
}