<?php
namespace Tch\A2V;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Converter
{
    private $tmpDirPath;
    private $audioFilePath;
    private $outputFilePath;
    private $frameRate;
    private $imageFile;
    private $imageFilePath;
    private $imageResolution;
    private $imageColor;

    public function __construct(UploadedFile $audioFile, $outputFormat, $frameRate, UploadedFile $imageFile = null, $imageResolution, $imageColor)
    {
        $this->tmpDirPath = sprintf('%s_%s', sys_get_temp_dir().'/audio2video', preg_replace('/[.\s]/', '', microtime()));
        $this->audioFilePath = $audioFile->getFileInfo()->getPathname();
        $this->outputFilePath = $this->tmpDirPath.'/output.'.ltrim($outputFormat, '.');
        $this->frameRate = $frameRate;
        $this->imageFile = $imageFile;
        $this->imageFilePath = $imageFile ? $imageFile->getFileInfo()->getPathname() : null;
        $this->imageResolution = $imageResolution;
        $this->imageColor = '#'.$imageColor;

        mkdir($this->tmpDirPath);
    }

    public function __invoke()
    {
        // get the duration of the input audio.
        $process = new Process("ffprobe -show_streams -print_format json '{$this->audioFilePath}' 2>/dev/null");
        $process->setTimeout(300);
        $process->run();
        $this->ensureSuccessful($process);
        $duration = floatval(json_decode($process->getOutput())->streams[0]->duration);

        // get the required number of images.
        $frameNum = intval(ceil($duration * $this->frameRate));

        // place uploaded image or new-created image as origin of copies.
        if ($this->imageFile) {
            $imageExt = $this->imageFile->getClientOriginalExtension();
            rename($this->imageFilePath, $this->tmpDirPath."/origin.{$imageExt}");
        } else {
            // create blank image.
            $image = new \Imagick();
            list($w, $h) = explode('x', $this->imageResolution);
            $image->newImage($w, $h, $this->imageColor, 'jpg');
            $image->writeImage($this->tmpDirPath.'/origin.jpg');
            $imageExt = 'jpg';
        }

        // generate sequential numbered images.
        for ($i = 0; $i < $frameNum; $i++) {
            copy($this->tmpDirPath."/origin.{$imageExt}", sprintf('%s/%06d.%s', $this->tmpDirPath, $i, $imageExt));
        }

        // generate video file.
        $process = new Process("ffmpeg -r {$this->frameRate} -i '{$this->tmpDirPath}/%06d.{$imageExt}' -i '{$this->audioFilePath}' -r 30 -vcodec libx264 -pix_fmt yuv420p '{$this->outputFilePath}'");
        $process->setTimeout(300);
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
