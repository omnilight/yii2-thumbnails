<?php

namespace omnilight\thumbnails;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ManipulatorInterface;
use yii\base\Component;
use yii\base\InvalidParamException;
use yii\helpers\FileHelper;
use yii\imagine\Image;


/**
 * Class Thumbnail
 */
class Thumbnail extends Component
{
    /**
     * Will keep original ratio of the image
     */
    const SIZE_MODE_ORIGINAL = 'original';
    /**
     * Will use ratio of the desired image
     */
    const SIZE_MODE_DESIRED = 'desired';

    /**
     * @var string Cache path for thumbnails
     */
    public $cachePath = '@webroot/assets';
    /**
     * @var string Cache url for thumbnails
     */
    public $cacheUrl = '@web/assets';

    /**
     * @param string $image Image file to be processed
     * @param callable $callable Function that will process transformations.
     * @param string $name Name of the cache, that will help to differentiate this thumbnail from the others
     * @return string Url of the thumbnail
     */
    public function url($image, $callable, $name = '')
    {
        if (!file_exists($image))
            return '';

        $dir = md5(serialize([dirname($image), $name]));
        $fileName = basename($image);
        $cacheDir = \Yii::getAlias($this->cachePath) . DIRECTORY_SEPARATOR . $dir;
        $cacheFile = $cacheDir .DIRECTORY_SEPARATOR . $fileName;

        $createThumbnail = !file_exists($cacheFile) || (filemtime($cacheFile) < filemtime($image));

        if ($createThumbnail) {
            /** @var ImageInterface $imaging */
            $imaging = call_user_func($callable, $image);
            if (!is_dir($cacheDir))
                FileHelper::createDirectory($cacheDir);
            $imaging->save($cacheFile);
        }

        return \Yii::getAlias($this->cacheUrl . "/{$dir}/{$fileName}");
    }

    /**
     * Creates thumbnail function callback for [[url]] function. Example:
     * ```php
     *  (new Thumbnail())->url($image, Thumbnail::thumb(100, 100), '100x100');
     * ```
     * @param integer $width
     * @param integer $height
     * @param string $sizeMode
     * @return callable
     */
    public static function thumb($width, $height, $sizeMode = self::SIZE_MODE_ORIGINAL)
    {
        if ($sizeMode == self::SIZE_MODE_DESIRED) {
            return function($image) use ($width, $height) {
                return Image::thumbnail($image, $width, $height, ManipulatorInterface::THUMBNAIL_INSET);
            };
        }

        if ($sizeMode == self::SIZE_MODE_ORIGINAL) {
            return function($image) use ($width, $height) {
                return Image::getImagine()->open($image)->thumbnail(new Box($width, $height));
            };
        }

        throw new InvalidParamException('Unknown $sizeMode');

    }
} 