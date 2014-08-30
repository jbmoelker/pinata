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
        $file = \tempnam(sys_get_temp_dir(), "keyword");
        \file_put_contents($file, $content);
        $filePlain = $file.".plain";
        
        $process = new Process(sprintf("pandoc --from html --to plain %s -o %s", $file, $filePlain) );
        $process->run();
        unlink($file);
        
        
        // this is where it gets reaaal pretty.
        $process = new Process(sprintf("java -jar %s %s", $this->appFile, $filePlain));
        $process->run();
        
        $stuff = $process->getOutput();
        $stuff = str_replace("'","\"", $stuff);
        
        unlink($filePlain);
        
        return json_decode($stuff, true);
    }
}