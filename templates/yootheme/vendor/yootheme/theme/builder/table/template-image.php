<?php

$attrs_image = [];

// Image
if ($item['image']) {

    $src = $item['image'];

    $attrs_image['class'][] = 'el-image uk-preserve-width';
    $attrs_image['class'][] = $element['image_border'] ? "uk-border-{$element['image_border']}" : '';
    $attrs_image['class'][] = $element['image_box_shadow'] ? "uk-box-shadow-{$element['image_box_shadow']}" : '';
    $attrs_image['alt'] = $item['image_alt'];

    $ext = $this->isImage($item['image']);

    if ($ext == 'gif') {
        $attrs_image['uk-gif'] = true;
    }

    if ($ext == 'svg') {
        $item['image'] = $this->image($src, array_merge($attrs_image, ['width' => $element['image_width'], 'height' => $element['image_height']]));
    } elseif ($element['image_width'] || $element['image_height']) {
        $item['image'] = $this->image([$src, 'thumbnail' => [$element['image_width'], $element['image_height']], 'sizes' => '80%,200%'], $attrs_image);
    } else {
        $item['image'] = $this->image($src, $attrs_image);
    }

}

?>

<?= $item['image'] ?>
