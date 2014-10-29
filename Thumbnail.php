<?php

namespace omnilight\thumbnails;

use yii\base\Component;


/**
 * Class Thumbnail
 */
class Thumbnail extends Component
{
    /**
     * @var string Cache path for thumbnails
     */
    public $cachePath = '@webroot/media/thumbnails';
    /**
     * @var string Cache url for thumbnails
     */
    public $cacheUrl = '@web/media/thumbnails';

    /**
     * @param string $image
     * @param callable $callable
     * @param string $name
     * @return string
     */
    public function url($image, $callable, $name = '')
    {
        $cacheImageName = md5(serialize([$image, $name])) . '.' . pathinfo($image, PATHINFO_EXTENSION);
        $cacheFile = \Yii::getAlias($this->cachePath) . '/' . $cacheImageName;
        $cacheUrl = \Yii::getAlias($this->cacheUrl) . '/' . $cacheImageName;

        $createThumbnail = !file_exists($cacheFile) || (filemtime($cacheFile) < filemtime($image));

        if ($createThumbnail) {

        }

        return $cacheUrl;
    }
} 