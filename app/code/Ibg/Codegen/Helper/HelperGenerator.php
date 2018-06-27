<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/7/2018
 * Time: 9:11 PM
 */

namespace Ibg\Codegen\Helper;

class HelperGenerator extends GeneratorHelper
{
    public function getMenuItemHtml()
    {
        return '<li data-ui-id="menu-ibg-codegen-helper" class="item-block level-2" role="menu-item">
                    <a href="#" onclick="return false;" class=""><span>Helper</span></a>
                </li>';
    }
}