<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon Developer Tools.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\DevTools\Elements\Menu;

use Phalcon\DevTools\Elements\Element;

class SidebarMenu extends Element
{
    protected $menuItems = [];

    public function __construct(array $menuItems)
    {
        foreach ($menuItems as $className => $menuData) {
            $this->addMenuItems($className, $menuData);
        }
    }

    public function addMenuItems($className, array $menuData): void
    {
        $this->menuItems[$className] = $menuData;
    }

    protected function renderItems($items): string
    {
        if (empty($items) || !is_array($items)) {
            return '';
        }

        $html = '';

        foreach ($items as $item) {
            $html .= sprintf('<li class="%s">', (isset($item['class']) ? $item['class'] : ''));

            if (isset($item['text'])) {
                $html .= $item['text'];
            }

            if (isset($item['link'])) {
                $html .= $this->createLink($item['link']);
            }

            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $submenu = $item['submenu'];
                if (isset($submenu['link'])) {
                    $html .= $this->createLink($submenu['link']);
                }

                $class = isset($submenu['class']) ? $submenu['class'] : '';
                $html .= sprintf('<ul class="%s">', $class);

                if (isset($submenu['items']) && is_array($submenu['items'])) {
                    $html .= $this->renderItems($submenu['items']);
                }

                $html .= '</ul>';
            }

            $html .= '</li>';
        }


        return $html;
    }

    public function render(): string
    {
        $menu = '';

        foreach ($this->menuItems as $className => $menuData) {
            $menu .= sprintf('<ul class="%s">', $className);
            $menu .= $this->renderItems($menuData);
            $menu .= '</ul>';
        }

        return $menu;
    }
}
