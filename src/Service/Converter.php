<?php

namespace App\Service;

use App\Entity\ConvertCriteria;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Converter
{
    private $durationLimit;

    private $tmpDirPath;
    private $audioFilePath;
    private $outputFilePath;
    private $frameRate;
    /** @var UploadedFile */
    private $imageFile;
    private $imageFilePath;
    private $imageResolution;
    private $imageColor;

    public function __construct(string $durationLimit)
    {
        $this->durationLimit = $durationLimit;
    }

    public function setCriteria(ConvertCriteria $criteria)
    {
        $this->tmpDirPath = sprintf('%s_%s', sys_get_temp_dir() . '/audio2video', preg_replace('/[.\s]/', '', microtime()));
        $this->audioFilePath = $criteria->audioFile->getFileInfo()->getPathname();
        $this->outputFilePath = $this->tmpDirPath.'/output.' . ltrim($criteria->outputFormat, '.');
        $this->frameRate = $criteria->frameRate;
        $this->imageFile = $criteria->imageFile;
        $this->imageFilePath = $criteria->imageFile ? $criteria->imageFile->getFileInfo()->getPathname() : null;
        $this->imageResolution = $criteria->imageResolution;
        $this->imageColor = '#' . $criteria->imageColor;

        mkdir($this->tmpDirPath);

        return $this;
    }

    public function convert()
    {
        // get the duration of the input audio.
        $process = new Process("ffprobe -show_streams -print_format json '{$this->audioFilePath}' 2>/dev/null");
        $process->setTimeout(60);
        $process->run();
        $this->ensureSuccessful($process);
        $duration = floatval(json_decode($process->getOutput())->streams[0]->duration);

        if ($duration > $this->durationLimit) {
            throw new \Exception('exception_too_long_duration');
        }

        // get the required number of images.
        $frameNum = intval(ceil($duration * $this->frameRate));

        // place uploaded image or new-created image as origin of copies.
        if ($this->imageFile) {
            $imageExt = $this->imageFile->getClientOriginalExtension();
            rename($this->imageFilePath, $this->tmpDirPath . "/origin.{$imageExt}");
        } else {
            // create blank image.
            $image = new \Imagick();
            list($w, $h) = explode('x', $this->imageResolution);
            $image->newImage($w, $h, $this->imageColor, 'jpg');
            $image->writeImage($this->tmpDirPath . '/origin.jpg');
            $imageExt = 'jpg';
        }

        // generate sequential numbered images.
        for ($i = 0; $i < $frameNum; $i++) {
            copy($this->tmpDirPath."/origin.{$imageExt}", sprintf('%s/%06d.%s', $this->tmpDirPath, $i, $imageExt));
        }

        // generate video file.
        $process = new Process("ffmpeg -r {$this->frameRate} -i '{$this->tmpDirPath}/%06d.{$imageExt}' -i '{$this->audioFilePath}' -r 30 -vcodec libx264 -pix_fmt yuv420p '{$this->outputFilePath}'");
        $process->setTimeout(60);
        $process->run();
        $this->ensureSuccessful($process);

        return $this->outputFilePath;
    }

    private function ensureSuccessful(Process $process)
    {
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
