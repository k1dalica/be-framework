<?php

use Common\Util\Redirect;
use Common\Util\JsonView;
use Common\Util\PDFView;

function jsonViewRaw() {
  return new JsonView();
}

function jsonView($data=[], $code) {
  $view=new JsonView();
  return $view->render($data, $code);
}

function pdfViewRaw($template) {
  return new PDFView($template);
}

function pdfView($template, $data=[]) {
  $view=new PDFView($template);
  return $view->render($data);
}

function pdfSave($template, $path, $data=[]) {
  $view = new PDFView($template);
  return $view->save($path, $data);
}

function redirect($url) {
  return (new Redirect($url))->render();
}

function _e($text) {
  return nl2br(htmlspecialchars($text));
}
