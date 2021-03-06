<?php
namespace Nethgui\Module;

/*
 * Copyright (C) 2011 Nethesis S.r.l.
 * 
 * This script is part of NethServer.
 * 
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

use Nethgui\System\PlatformInterface as Validate;

/**
 *
 */
class Menu extends \Nethgui\Controller\AbstractController
{

    /**
     *
     * @var string Current menu item identifier
     * @return Menu
     */
    private $currentItem;

    /**
     *
     * @var \Nethgui\Module\ModuleSetInterface
     */
    private $moduleSet;

    public function initialize()
    {
        parent::initialize();
        $this->declareParameter('search', Validate::ANYTHING);
    }

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        $attributes = new SystemModuleAttributesProvider();
        $attributes->initializeFromModule($this);
        return $attributes;
    }    
    
    /**
     *
     * @param string $currentModuleIdentifier
     * @return Menu
     */
    public function setCurrentModuleIdentifier($currentModuleIdentifier)
    {
        $this->currentItem = $currentModuleIdentifier;
        return $this;
    }

    /**
     *
     * @param \Nethgui\Module\ModuleSetInterface $moduleSet
     * @return Menu
     */
    public function setModuleSet(\Nethgui\Module\ModuleSetInterface $moduleSet)
    {
        $this->moduleSet = $moduleSet;
        return $this;
    }

    private function searchTags(\Nethgui\View\ViewInterface $view, $query)
    {
        if (is_null($this->moduleSet)) {
            return array();
        }

        $translator = $view->getTranslator();
        $results = array();

        foreach ($this->moduleSet as $module) {
            if ( ! $module instanceof \Nethgui\Module\ModuleInterface) {
                continue;
            }

            if ( ! $module->isInitialized()) {
                $module->setPlatform($this->getPlatform());
                $module->initialize();
            }

            $tags = array_map('trim', explode(' ', $module->getAttributesProvider()->getTags()));

            foreach ($tags as $tag) {
                $tagTranslated = $translator->translate($module, $tag);
                if (stripos($tagTranslated, $query) !== FALSE) {
                    $results[] = $view->getModuleUrl('/' . $module->getIdentifier());
                }
            }
        }

        return $results;
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        if ($view->getTargetFormat() === $view::TARGET_JSON) {
            if ($this->getRequest()->isEmpty()) {
                return;
            }
            $view['tags'] = $this->searchTags($view, $this->parameters['search']);
            return;
        }

        parent::prepareView($view);
        $view->setTemplate(array($this, 'renderModuleMenu'));

        $categories = array();
        $translator = $view->getTranslator();

        $categoryOrder = array_flip(array_map('trim', explode(',', $view->translate('Category_Order'))));

        foreach ($this->moduleSet as $moduleIdentifier => $moduleInstance) {
            if ( ! $moduleInstance instanceof \Nethgui\Module\ModuleInterface) {
                continue;
            }

            $attributes = $moduleInstance->getAttributesProvider();

            $category = $attributes->getCategory();
            $title = $translator->translate($moduleInstance, $attributes->getTitle());
            $tags = $translator->translate($moduleInstance, $attributes->getTags());
            $description = $translator->translate($moduleInstance, $attributes->getDescription());
            $href = $view->spawnView($moduleInstance)->getModuleUrl();
            $position = $attributes->getMenuPosition();

            // skip elements without any category
            if (is_null($category)) {
                continue;
            }

            // initialize category:
            if ( ! isset($categories[$category])) {
                $categories[$category] = array(
                    'key' => $category,
                    'title' => $translator->translate($moduleInstance, $category),
                    'items' => array()
                );
            }

            // add item to category
            if ( ! isset($categories[$category]['items'][$moduleIdentifier])) {
                $categories[$category]['items'][$moduleIdentifier] = array(
                    'identifier' => $moduleIdentifier,
                    'title' => $title,
                    'description' => $description,
                    'href' => $href,
                    'tags' => $tags,
                    'position' => $position
                );
            }
        }


        foreach ($categories as &$category) {
            usort($category['items'], array($this, 'sortItems'));
        }

        usort($categories, function($c, $d) use ($categoryOrder) {

                if (isset($categoryOrder[$c['key']], $categoryOrder[$d['key']])) {
                    return $categoryOrder[$c['key']] - $categoryOrder[$d['key']];
                }

                return strcmp($c['title'], $d['title']);
            });

        $view['categories'] = $categories;
    }

    public function sortItems($a, $b)
    {
        return strcmp($a['title'], $b['title']);
    }

    public function renderModuleMenu(\Nethgui\Renderer\Xhtml $view)
    {
        $view->includeFile('Nethgui/Js/jquery.nethgui.controller.js');
        $view->includeFile('Nethgui/Js/jquery.nethgui.navigation.js');

        $rootList = $view->elementList()->setAttribute('wrap', '/');
        foreach ($view['categories'] as $category) {
            // Add category title with fake module
            $rootList->insert(
                $view->panel()
                    ->setAttribute('class', 'category')
                    ->insert($view->literal($category['title'])->setAttribute('hsc', TRUE))
            );

            // Add category contents:
            $el = $view->elementList()->setAttribute('class', FALSE);

            foreach ($category['items'] as $item) {
                $el->insert($this->renderMenuItem($view, $item));
            }

            $rootList->insert($el);
        }

        $searchPanel = $view->panel()
            ->setAttribute('class', 'searchPanel ui-corner-all')
            ->insert($view->textInput("search", $view::LABEL_NONE)->setAttribute('placeholder', $view->translate('Search') . "..."))
            ->insert($view->button("Find", $view::BUTTON_SUBMIT)->setAttribute('class', 'Button search'));

        return $view->panel()
                ->setAttribute('class', 'Navigation Flat ' . $view->getClientEventTarget("tags"))
                ->insert($view->form()
                    ->setAttribute('method', 'get')
                    ->insert($searchPanel)
                    ->insert($rootList)
                )
        ;
    }

    protected function renderMenuItem(\Nethgui\Renderer\Xhtml $view, $item)
    {
        $placeholders = array(
            '%HREF' => htmlspecialchars($item['href']),
            '%CONTENT' => htmlspecialchars($item['title']),
            '%TITLE' => htmlspecialchars($item['description']),
        );

        if ($item['identifier'] === $this->currentItem) {
            $tpl = '<a href="%HREF" title="%TITLE" class="currentMenuItem">%CONTENT</a>';
        } else {
            $tpl = '<a href="%HREF" title="%TITLE">%CONTENT</a>';
        }

        return $view->literal(strtr($tpl, $placeholders))->setAttribute('hsc', FALSE);
    }

}
