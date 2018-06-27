<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/7/2018
 * Time: 9:11 PM
 */

namespace Ibg\Codegen\Helper;

class BlockGenerator extends GeneratorHelper
{
    public function getMenuItemHtml()
    {
        return '<li data-ui-id="menu-ibg-codegen-block" class="item-block level-2" role="menu-item">
                    <a href="#" onclick="return false;" class=""><span>Block</span></a>
                </li>';
    }

    public function getBlockClassNameRegEx()
    {
        return '/[A-Z]+[a-zA-Z]*(\\[A-Z]+[a-zA-Z]*)*/';
    }

    public function getBlockNameInLayoutRegEx()
    {
        return '/[a-z]+[._a-zA-Z]*/';
    }
}