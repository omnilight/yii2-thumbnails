<?php

namespace omnilight\thumbnails;

use Imagine\Image\ImageInterface;
use yii\base\Component;
use yii\helpers\FileHelper;


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
        $cachePath = \Yii::getAlias($this->cachePath);
        $cacheFile = $cachePath . '/' . $cacheImageName;
        $cacheUrl = \Yii::getAlias($this->cacheUrl) . '/' . $cacheImageName;

        $createThumbnail = !file_exists($cacheFile) || (filemtime($cacheFile) < filemtime($image));

        if ($createThumbnail) {
            /** @var ImageInterface $imaging */
            $imaging = call_user_func($callable, $image);
            if (!is_dir($cachePath))
                FileHelper::createDirectory($cachePath);
            $imaging->save($cacheFile);
        }

        return $cacheUrl;
    }
} 