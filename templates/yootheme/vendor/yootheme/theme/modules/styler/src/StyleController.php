<?php

namespace YOOtheme\Theme;

use YOOtheme\ContainerTrait;
use YOOtheme\Util\File;

class StyleController
{
    use ContainerTrait;

    public $inject = [
        'locator' => 'app.locator',
        'themes' => 'yootheme/styler.themes',
    ];

    public function index($request, $response)
    {
        $styles = [];
        $imports = [];

        $resolve = function ($file, $replace = []) use (&$imports, &$resolve) {

            if (!file_exists($file)) {
                return;
            }

            $imports[File::normalizePath($this->app->url($file))] = $contents = @file_get_contents($file) ?: '';

            if (preg_match_all('/^@import.*"(.*)";/m', $contents, $matches)) {
                foreach ($matches[1] as $path) {
                    $resolve(dirname($file).'/'.str_replace(array_keys($replace), array_values($replace), $path), $replace);
                }
            }

        };

        // add styles
        foreach ($this->themes as $id => $theme) {

            $file = $theme['file'];

            $styles[$id] = [
                'filename' => $this->app->url($file),
                'contents' => file_get_contents($file)
            ];

            // add theme imports
            $resolve($file, ['@{internal-theme}' => $id]);

            if (isset($theme['styles'])) {
                foreach (array_keys($theme['styles']) as $style) {
                    $resolve($file, ['@{internal-theme}' => $id, '@{internal-style}' => $style]);
                }
            }

        }

        // add imports
        if (isset($this->theme->options['styles']['imports'])) {
            foreach ((array) $this->theme->options['styles']['imports'] as $path) {
                foreach ($this->locator->findAll("@theme/{$path}") as $file) {
                    $resolve($file);
                }
            }
        }

        return $response->withJson(compact('styles', 'imports'));
    }

    public function save($request, $response)
    {
        $upload = $request->getUploadedFile('files');

        if (!$upload || $upload->getError()) {
            $this->app->abort(400, 'Invalid file upload.');
        }

        if (!$contents = (string) $upload->getStream()) {
            $this->app->abort(400, 'Unable to read contents file.');
        }

        if (!$contents = @base64_decode($contents)) {
            $this->app->abort(400, 'Base64 Decode failed.');
        }

        if (!$files = @json_decode($contents, true)) {
            $this->app->abort(400, 'Unable to decode JSON from temporary file.');
        }

        foreach ($files as $file => $data) {

            $file = new File("@theme/$file");

            if (!$file->isFile()) {
                continue;
            }

            if ($file->putContents($data) === false) {
                $this->app->abort(400, sprintf('Unable to write file (%s).', (string) $file));
            }
        }

        return $response->withJson(['message' => 'success']);
    }

}
