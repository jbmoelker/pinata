<?php

namespace Pinata\Keyword;
use Symfony\Component\Process\Process;

class KeywordGenerator {
    private $cacheDir;
    private $appFile;
    public function __construct($cacheDir, $app)
    {
        $this->cacheDir = $cacheDir;
        $this->appFile = $app;
        
    }
    
    public function GenerateKeywords ($content)
    {
        var_dump($_ENV['PATH']);
        $file = \tempnam($this->cacheDir, "keyword");
        \file_put_contents($file, $content);
        $filePlain = $file.".plain";
        $command = sprintf("pandoc --from html --to plain %s -o %s", $file, $filePlain);
        echo $command . "<br>";
        //shell_exec($command);
        $process = new Process($command);
        $process->run();
        unlink($file);
        
        
        // this is where it gets reaaal pretty.
        $command = sprintf("java -jar %s %s", $this->appFile, $filePlain);
        echo $command . "<br>";
        $output = shell_exec($command);
        //$process = new Process($command);
        //$process->run();
        echo 'output:' . $output;
        
        $stuff = $output; //$process->getOutput();
        $stuff = str_replace("'","\"", $stuff);
        
        //unlink($filePlain);
        
        return json_decode($stuff, true);
    }
}