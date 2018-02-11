<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class ConvertCriteria
{
    /**
     * @var UploadedFile
     *
     * @Assert\NotBlank()
     * @Assert\File()
     */
    public $audioFile;

    /**
     * @var string
     *
     * @Assert\Choice({"mp4", "m4v", "avi", "wmv"})
     */
    public $outputFormat;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(1)
     * @Assert\LessThanOrEqual(60)
     */
    public $frameRate;

    /**
     * @var UploadedFile
     *
     * @Assert\Image()
     */
    public $imageFile;

    /**
     * @var string
     *
     * @Assert\Regex("/^\d+x\d+$/")
     */
    public $imageResolution;

    /**
     * @var string
     *
     * @Assert\Regex("/^([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/")
     */
    public $imageColor;

    public function __construct()
    {
        $this->outputFormat = 'mp4';
        $this->frameRate = 30;
        $this->imageResolution = '800x450';
        $this->imageColor = '000';
    }
}
