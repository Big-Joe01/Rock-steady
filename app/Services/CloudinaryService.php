<?php

declare(strict_types=1);

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;

class CloudinaryService
{
    private Cloudinary $cloudinary;
    private string $folder;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => CLOUDINARY_CLOUD_NAME,
                'api_key' => CLOUDINARY_API_KEY,
                'api_secret' => CLOUDINARY_API_SECRET,
            ],
        ]);
        
        $this->folder = CLOUDINARY_FOLDER;
    }

    public function upload(string $filePath, array $options = []): array
    {
        $defaultOptions = [
            'folder' => $this->folder,
            'resource_type' => 'auto',
            'transformation' => [
                'quality' => 'auto',
                'fetch_format' => 'auto',
            ],
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        try {
            $result = $this->cloudinary->uploadApi()->upload($filePath, $options);
            
            return [
                'success' => true,
                'public_id' => $result->getPublicId(),
                'url' => $result->getSecurePath(),
                'secure_url' => $result->getSecurePath(),
                'format' => $result->getFormat(),
                'width' => $result->getWidth(),
                'height' => $result->getHeight(),
                'bytes' => $result->getBytes(),
            ];
        } catch (\Exception $e) {
            Logger::error('Cloudinary upload failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function uploadBase64(string $base64Data, array $options = []): array
    {
        $defaultOptions = [
            'folder' => $this->folder,
            'resource_type' => 'auto',
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        try {
            $result = $this->cloudinary->uploadApi()->upload($base64Data, $options);
            
            return [
                'success' => true,
                'public_id' => $result->getPublicId(),
                'url' => $result->getSecurePath(),
                'secure_url' => $result->getSecurePath(),
                'format' => $result->getFormat(),
                'width' => $result->getWidth(),
                'height' => $result->getHeight(),
                'bytes' => $result->getBytes(),
            ];
        } catch (\Exception $e) {
            Logger::error('Cloudinary base64 upload failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function delete(string $publicId): bool
    {
        try {
            $this->cloudinary->uploadApi()->destroy($publicId);
            return true;
        } catch (\Exception $e) {
            Logger::error('Cloudinary delete failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getUrl(string $publicId, array $transformations = []): string
    {
        $config = [
            'cloud' => [
                'cloud_name' => CLOUDINARY_CLOUD_NAME,
            ],
        ];
        
        $cloudinary = new Cloudinary($config);
        
        $options = ['cloud_name' => CLOUDINARY_CLOUD_NAME];
        
        if (!empty($transformations)) {
            $options['transformation'] = $transformations;
        }
        
        return $cloudinary->url($publicId)->toUrl();
    }

    public function getOptimizedUrl(string $publicId, int $width = 800, int $height = null): string
    {
        $transformations = [
            'width' => $width,
            'quality' => 'auto',
            'fetch_format' => 'auto',
            'crop' => 'limit',
        ];
        
        if ($height) {
            $transformations['height'] = $height;
        }
        
        return $this->getUrl($publicId, $transformations);
    }

    public function getThumbnailUrl(string $publicId, int $width = 300, int $height = 300): string
    {
        return $this->getUrl($publicId, [
            'width' => $width,
            'height' => $height,
            'crop' => 'fill',
            'gravity' => 'auto',
            'quality' => 'auto',
            'fetch_format' => 'auto',
        ]);
    }

    public function generateSignature(array $params, bool $timestamp = null): array
    {
        $timestamp = $timestamp ?? time();
        
        $params['timestamp'] = $timestamp;
        $params['folder'] = $this->folder;
        
        ksort($params);
        
        $toSign = [];
        foreach ($params as $key => $value) {
            $toSign[] = $key . '=' . $value;
        }
        
        $signature = hash_hmac('sha256', implode('&', $toSign), CLOUDINARY_API_SECRET);
        
        return [
            'signature' => $signature,
            'timestamp' => $timestamp,
            'api_key' => CLOUDINARY_API_KEY,
            'cloud_name' => CLOUDINARY_CLOUD_NAME,
        ];
    }
}
