<?php

namespace App\Model\Upload;

use App\Model\Orm;
use App\Model\Product;
use App\Model\ProductImage;
use Nette\Utils\Image;
use Nette\Utils\Random;
use Nette\Utils\Strings;
use Tracy\Debugger;
use Zet\FileUpload\Model\IUploadModel;

class UploadService implements IUploadModel
{

    public Orm $orm;

    public const PRODUCT_IMAGES_DIR_ABSOLUTE = WWW_DIR . '/images/products';
    public const PRODUCT_IMAGES_DIR = '/images/products';

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function save(\Nette\Http\FileUpload $file, array $params = [])
    {
        $product = $this->orm->products->getById($params['productId']);

        if ($file->isImage() and $file->isOk() && $product) {

            $image = $this->saveOriginal($file, $product);

            $img = Image::fromFile(self::PRODUCT_IMAGES_DIR_ABSOLUTE . '/' . $image->fileName);
            $this->savePhoto($img, ProductImage::SIZE_XS, $image->fileName, $product, $image);
            $this->savePhoto($img, ProductImage::SIZE_S, $image->fileName, $product, $image);
            $this->savePhoto($img, ProductImage::SIZE_MD, $image->fileName, $product, $image);
            $this->savePhoto($img, ProductImage::SIZE_LG, $image->fileName, $product, $image);

        }
    }

    private function saveOriginal(\Nette\Http\FileUpload $file, Product $product): ProductImage
    {
        $fileExt = strtolower(mb_substr($file->getSanitizedName(), strrpos($file->getSanitizedName(), ".")));
        $image = new ProductImage();
        $image->product = $product;
        $image->originalName = $file->getName();
        $image->fileName = Random::generate(40);
        $image->filePath = '';
        $image->size = ProductImage::SIZE_ORIGINAL;
        $this->orm->persistAndFlush($image);
        $image->fileName = Strings::webalize($image->product->productName) . '-' . $image->id . ProductImage::SIZE_ORIGINAL . $fileExt;
        $image->filePath = self::PRODUCT_IMAGES_DIR . '/' . $image->fileName;
        $this->orm->persistAndFlush($image);

        $file->move(self::PRODUCT_IMAGES_DIR_ABSOLUTE . '/' . $image->fileName);

        return $image;
    }

    private function savePhoto(Image $image, string $size, string $fileName, Product $product, ProductImage $originalImage): void
    {
        $imageSize = null;

        if ($size === ProductImage::SIZE_XS) {
            $imageSize = new ImageSize(80, 80);
        } elseif ($size === ProductImage::SIZE_S) {
            $imageSize = new ImageSize(250, 250);
        } elseif ($size === ProductImage::SIZE_MD) {
            $imageSize = new ImageSize(500, 500);
        } elseif ($size === ProductImage::SIZE_LG) {
            if ($image->getWidth() > 1000 || $image->getHeight() > 1000) {
                $imageSize = new ImageSize(1000, 1000);
            } else {
                $imageSize = new ImageSize($image->getWidth(), $image->getHeight());
            }
        }

        if ($imageSize) {
            if ($image->getWidth() || $image->getHeight()) {
                $image->resize($imageSize->getWidth(), $imageSize->getHeight());
                //$image->alphaBlending(false);
               // $image->saveAlpha(true);
            }
            //$image->sharpen();

            $fileExt = strtolower(mb_substr($fileName, strrpos($fileName, ".")));

            $productImage = new ProductImage();
            $productImage->product = $product;
            $productImage->originalName = $originalImage->originalName;
            $productImage->fileName = Random::generate(40);
            $productImage->filePath = '';
            $productImage->size = $size;
            $this->orm->persistAndFlush($productImage);
            $productImage->fileName = Strings::webalize($productImage->product->productName) . '-' . $originalImage->id . $size . $fileExt;
            $productImage->filePath = self::PRODUCT_IMAGES_DIR . '/' . $productImage->fileName;
            $this->orm->persistAndFlush($productImage);

            $image->save(self::PRODUCT_IMAGES_DIR_ABSOLUTE . '/' . $productImage->fileName);
        }
    }

    public function rename($upload, $newName)
    {
        // TODO: Implement rename() method.
    }

    public function remove($uploaded)
    {
        // TODO: Implement remove() method.
    }
}