<?php

namespace Common\Util;
use Common\Exception\InternalException;
use Common\Util\Response;
use Dompdf\Dompdf;


class PDFView extends View {

  protected $template;
  protected $bundle;
  public $extras;
  public $extendsView;
  
  public function __construct($template) {
    parent::__construct($template);
    $this->extras["headers"]["Content-Type"]="application/pdf";
    
  }
  
  public function render($data=null) {
    $contents=$this->doRender($data);
    return new Response($contents, $this->extras);
  }

  public function save($path, $data = null) {
    $output = $this->doRender($data);
    $parts = explode('/', $path);
    if (count($parts) > 1) {
      $file = array_pop($parts);
      $dir = '';
      foreach($parts as $part) {
        if(!is_dir($dir .= $dir === '' ? $part : "/$part")) {
          mkdir($dir);
        }
      }
      file_put_contents("$dir/$file", $output);
    } else {
      file_put_contents($dir, $output);
    }
    return '/' . $path;
  }
  
  public function doRender($_data=null) {
    $_contents=parent::doRender($_data);
    $dompdf=new Dompdf();
    $dompdf->loadHtml($_contents);
    $dompdf->set_option('isHtml5ParserEnabled', true);
    $dompdf->setPaper('Letter', 'portrait');
    $dompdf->render();
    return $dompdf->output();
  }

}
