<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;

class UploadService {
    const PARTNER_IMAGE_UPLOAD_PATH = './partner_images';
    const PRODUCT_IMAGE_UPLOAD_PATH = './product_images';
    const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png'];

    //todo: merge these two methdos into one

    public function uploadPartnerImage(?UploadedFile $image) {
        if ($image == null) {
            return null;
        }

        $this->checkFileType($image->getMimeType());

        $file_name = $this->randomFileName() . '.' . $image->getClientOriginalExtension();
        $image->move('./partner_images', $file_name);

        return $file_name;
    }

    public function uploadProductImage(?UploadedFile $image) {
        if ($image == null) {
            return null;
        }

        $this->checkFileType($image->getMimeType());

        $file_name = $this->randomFileName() . '.' . $image->getClientOriginalExtension();
        $image->move('./product_images', $file_name);

        return $file_name;
    }

    private function randomFileName() {
        return time() . '_' . bin2hex(random_bytes(10));
    }

    private function checkFileType($mime_type) {
        
        if (!in_array($mime_type, self::ALLOWED_IMAGE_TYPES)) {
            throw new \Exception('Only PNG and JPEG files are allowed to upload');
        }
    }
}