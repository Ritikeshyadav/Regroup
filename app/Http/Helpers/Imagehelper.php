<?php

use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Image as thumbimage;

/**
 *   Created by : Pradyumn Dwivedi
 *   Created On : 06 November 2023
 *   Uses :  To upload single image after crop
 */
if (!function_exists('singleImageUpload')) {
    function singleImageUpload($image, $path, $image_db = null)
    {
        $thumbnail = '';
        if (!empty($image)) {
            $folderPath = 'storage/app/public/uploads/' . $path . '/';
            $image_parts = explode(";base64,", $image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $imageName = uniqid() . '.png';
            $imageFullPath = $folderPath . $imageName;
            if (isset($image_db) && !empty($image_db)) {
                unlink($folderPath . '/' . $image_db);
            }
            file_put_contents($imageFullPath, $image_base64);
            $thumbnail = $imageName;
        } else {
            $thumbnail = $image_db;
        }
        return $thumbnail;
    }
}

/**
 * Created By : Pradyumn Dwivedi
 * Created at : 06 November 2023
 * Use : Function for listing image url
 */

if (!function_exists('ListingImageUrl')) {
    function ListingImageUrl($type, $imageName)
    {
        $src = '';
        $defaultImagePath = "";

        if (!empty($imageName) && file_exists('storage/app/public/uploads/' . $type . '/' . $imageName)) {
            $src =  'storage/app/public/uploads/' . $type . '/' . $imageName . '?d=' . time();
        } else {
            $src = "storage/app/public/uploads/" . $type . '/' . $imageName;
        }
        return url($src);
    }
}

/**
 * Created By : Pradyumn Dwivedi
 * Created at : 13 March 2023
 * Use : To upload multiple image without crop
 */
if (!function_exists('saveMultipleImage')) {
    function saveMultipleImage($files, $type = "", $id = "", $sub_id = "")
    {
        //sort images array by name
        // sort($files);
        foreach ($files as $file) {
            $actualImagePath = 'uploads/' . $type;
            $extension = $file->extension();
            $name = time() . '.' . $file->getClientOriginalExtension();
            $originalImageName = $type . '_' . $id . '_' . $sub_id . '_' . $name;
            //if image already exist unlink that image
            // $path = public_path() . '/uploads/' . $type . '/' . $originalImageName;
            // if(File::exists($path)) {
            //     unlink($path);
            // }
            $path = $file->storeAs($actualImagePath, $originalImageName, 'public');

            // Create the full path to the uploaded image
            $folderPath = storage_path('app/public/uploads/' . $type . '/' . $originalImageName);
            // the image will be replaced with an optimized version which should be smaller
            optimizeImage($folderPath);

            $sub_id++;
            $imagePath[] = $originalImageName;
        }
        return $imagePath;
    }
}

/**
 * Created By : Pradyumn Dwivedi
 * Created at : 06 November 2023
 * Use : To upload single image without crop
 */
if (!function_exists('saveSingleImageWithoutCrop')) {
    function saveSingleImageWithoutCrop($image, $path, $image_db = null)
    {
        $thumbnail = '';

        if (!empty($image)) {
            // Define the folder path where the image will be stored
            $folderPath = storage_path('app/public/uploads/' . $path . '/');

            // Generate a unique image name
            // $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension(); //hritik has updated this on 22-07-2024

            // Move the uploaded image to the specified folder
            $image->move($folderPath, $imageName);

            // the image will be replaced with an optimized version which should be smaller
            $pathToImage = $folderPath . $imageName;
            optimizeImage($pathToImage);
            // ImageOptimizer::optimize($pathToImage);

            // If there was a previous image, delete it
            if (!empty($image_db)) {
                $previousImagePath = $folderPath . $image_db;
                if (file_exists($previousImagePath)) {
                    unlink($previousImagePath);
                }
            }

            $thumbnail = $imageName;
        } elseif (!empty($image_db)) {
            $thumbnail = $image_db;
        }
        return $thumbnail;
    }
}

/**
 * Created By: Pradyumn Dwivedi
 * Created at: 02 feb 2024
 * Use: To optimize image
 */
if (!function_exists('optimizeImage')) {
    function optimizeImage($pathToImage)
    {
        // Log the size before optimization
        $originalSize = filesize($pathToImage);
        Log::info("Original Image Size: {$originalSize} bytes");

        // Optimize the uploaded image using the ImageOptimizer package
        ImageOptimizer::optimize($pathToImage, null, ['debug' => true]);

        // Log the size after optimization
        $optimizedSize = filesize($pathToImage);
        Log::info("Optimized Image Size: {$optimizedSize} bytes");
    }
}