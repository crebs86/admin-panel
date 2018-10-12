<?php
/**
 * Created by PhpStorm.
 * User: crebs
 * Date: 13/07/18
 * Time: 18:48
 */

namespace Crebs86\Acl\Controllers\Util;

class Misc
{

    private $breadCrumbItens = [];

    /**
     * @param array $itens
     * @return $this
     */
    public function add(array $itens)
    {
        $this->breadCrumbItens = $itens;
        return $this;
    }

    /**
     * @param string $text
     * @param string $link
     * @return $this
     */
    public function addItem(string $text, string $link = "#")
    {
        $arr = [$text => $link];
        $this->breadCrumbItens = array_merge($this->breadCrumbItens, $arr);
        return $this;
    }

    /**
     * @param string $activeItem
     * @return string
     */
    public function active(string $activeItem = '#')
    {
        return Util::buildBreadCumbs($this->breadCrumbItens, $activeItem);
    }
}