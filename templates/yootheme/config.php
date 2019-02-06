<?php

return array_merge([

    'name' => 'YOOtheme',

    'main' => 'YOOtheme\\Theme',

    'version' => '1.9.1',

    'require' => 'yootheme/theme',

    'include' => 'vendor/yootheme/theme/index.php',

    'inject' => [

        'view' => 'app.view',
        'image' => 'app.image',
        'styles' => 'app.styles',
        'scripts' => 'app.scripts',
        'locator' => 'app.locator',
        'modules' => 'app.modules',
        'builder' => 'app.builder',

    ],

    'menus' => [

        'navbar' => 'Navbar',
        'mobile' => 'Mobile',

    ],

    'positions' => [

        'toolbar-left' => 'Toolbar Left',
        'toolbar-right' => 'Toolbar Right',
        'navbar' => 'Navbar',
        'header' => 'Header',
        'top' => 'Top',
        'sidebar' => 'Sidebar',
        'bottom' => 'Bottom',
        'mobile' => 'Mobile',
        'builder-1' => 'Builder 1',
        'builder-2' => 'Builder 2',
        'builder-3' => 'Builder 3',
        'builder-4' => 'Builder 4',
        'builder-5' => 'Builder 5',
        'builder-6' => 'Builder 6',

    ],

    'styles' => [

        'imports' => [
            'vendor/assets/uikit/src/images/backgrounds/*.svg',
            'vendor/assets/uikit-themes/*/images/*.svg',
        ],

    ],

    'config' => [

        'menu' => [
            'positions' => [
                'navbar' => '',
                'mobile' => '',
            ]
        ]

    ],

    'events' => [

        'theme.init' => [function () {

            // Deprecated
            if ($this->get('header.layout') == 'toggle-offcanvas') {
                $this->set('header.layout', 'offcanvas-top-a');
            }

            // Deprecated
            if ($this->get('header.layout') == 'toggle-modal') {
                $this->set('header.layout', 'modal-center-a');
                $this->set('navbar.toggle_menu_style', 'primary');
                $this->set('navbar.toggle_menu_center', true);
            }

            // Deprecated
            if ($this->get('mobile.animation') == 'modal' && !$this->has('mobile.menu_center')) {
                $this->set('mobile.menu_style', 'primary');
                $this->set('mobile.menu_center', true);
                $this->set('mobile.menu_center_vertical', true);
            }

        }, -10],

        'theme.site' => function () {

            $this->styles->add('theme-style', 'css/theme'.($this->get('direction') === 'rtl' ? '.rtl' : '').'.css', 'highlight', [
                'version' => $css = @filemtime("{$this->path}/css/theme.css")
            ]);

            if (filemtime(__FILE__) >= $css) {
                $this->styles->add('theme-style-update', 'css/theme.update.css');
            }

            $icons = "{$this->path}/vendor/assets/uikit/dist/js/uikit-icons";
            $style = explode(':', $this->get('style', ''));
            $style = "{$icons}-{$style[0]}.min.js";

            $this->scripts
                ->add('theme-uikit', 'vendor/assets/uikit/dist/js/uikit.min.js')
                ->add('theme-uikit-icons', file_exists($style) ? $style : "{$icons}.min.js")
                ->add('theme-script', 'js/theme.js', 'theme-uikit');

            if ($custom = $this->locator->find('@assets/css/custom.css')) {
                $this->styles->add('theme-custom', $custom, 'theme-style');
            }

        },

        'content' => function ($content) {

            if ($style = $this->get('highlight') and strpos($content, '</code>')) {
                $this->styles->add('highlight', "vendor/assets/highlightjs/styles/{$style}.css", '', ['defer' => true]);
                $this->scripts
                    ->add('highlight', 'vendor/assets/highlightjs/highlight.min.js', 'theme-script', ['defer' => true])
                    ->add('highlight-init', 'jQuery(function() {hljs.initHighlightingOnLoad()});', 'highlight', ['type' => 'string', 'defer' => true]);
            }

        }

    ],

    'yootheme/layout' => require 'config/layout.php',
    'yootheme/settings' => require 'config/settings.php',
    'yootheme/styler' => require 'config/styler.php',

], require 'config/platform.php');
