<?php

namespace App\Model\Upload;

use App\Model\Orm;
use App\Model\Vehicle;
use App\Model\VehicleImage;
use Nette\Utils\Image;
use Nette\Utils\Random;
use Nette\Utils\Strings;
use Tracy\Debugger;
use Zet\FileUpload\Model\IUploadModel;

class UploadCarService implements IUploadModel
{

    public Orm $orm;

    public const PRODUCT_IMAGES_DIR_ABSOLUTE = WWW_DIR . '/images/vehicles';
    public const PRODUCT_IMAGES_DIR = '/images/vehicles';

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function save(\Nette\Http\FileUpload $file, array $params = [])
    {
        $vehicle = $this->orm->vehicles->getById($params['vehicleId']);

        if ($file->isImage() and $file->isOk() && $vehicle) {

            $image = $this->saveOriginal($file, $vehicle);

            $img = Image::fromFile(self::PRODUCT_IMAGES_DIR_ABSOLUTE . '/' . $image->fileName);
            $this->savePhoto($img, VehicleImage::SIZE_XS, $image->fileName, $vehicle, $image);
            $this->savePhoto($img, VehicleImage::SIZE_S, $image->fileName, $vehicle, $image);
            $this->savePhoto($img, VehicleImage::SIZE_MD, $image->fileName, $vehicle, $image);
            $this->savePhoto($img, VehicleImage::SIZE_LG, $image->fileName, $vehicle, $image);

        }
    }

    private function saveOriginal(\Nette\Http\FileUpload $file, Vehicle $vehicle): VehicleImage
    {
        $fileExt = strtolower(mb_substr($file->getSanitizedName(), strrpos($file->getSanitizedName(), ".")));
        $image = new VehicleImage();
        $image->vehicle = $vehicle;
        $image->originalName = $file->getName();
        $image->fileName = Random::generate(40);
        $image->filePath = '';
        $image->size = VehicleImage::SIZE_ORIGINAL;
        $this->orm->persistAndFlush($image);
        $image->fileName = Strings::webalize($image->vehicle->name) . '-' . $image->id . VehicleImage::SIZE_ORIGINAL . $fileExt;
        $image->filePath = self::PRODUCT_IMAGES_DIR . '/' . $image->fileName;
        $this->orm->persistAndFlush($image);

        $file->move(self::PRODUCT_IMAGES_DIR_ABSOLUTE . '/' . $image->fileName);

        return $image;
    }

    private function savePhoto(Image $image, string $size, string $fileName, Vehicle $vehicle, VehicleImage $originalImage): void
    {
        $imageSize = null;

        if ($size === VehicleImage::SIZE_XS) {
            $imageSize = new ImageSize(80, 80);
        } elseif ($size === VehicleImage::SIZE_S) {
            $imageSize = new ImageSize(250, 250);
        } elseif ($size === VehicleImage::SIZE_MD) {
            $imageSize = new ImageSize(500, 500);
        } elseif ($size === VehicleImage::SIZE_LG) {
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

            $vehicleImage = new VehicleImage();
            $vehicleImage->vehicle = $vehicle;
            $vehicleImage->originalName = $originalImage->originalName;
            $vehicleImage->fileName = Random::generate(40);
            $vehicleImage->filePath = '';
            $vehicleImage->size = $size;
            $this->orm->persistAndFlush($vehicleImage);
            $vehicleImage->fileName = Strings::webalize($vehicleImage->vehicle->name) . '-' . $originalImage->id . $size . $fileExt;
            $vehicleImage->filePath = self::PRODUCT_IMAGES_DIR . '/' . $vehicleImage->fileName;
            $this->orm->persistAndFlush($vehicleImage);

            $image->save(self::PRODUCT_IMAGES_DIR_ABSOLUTE . '/' . $vehicleImage->fileName);
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