LessPHP CakePHP Plugin
======================

This is a CakePHP Plugin for [LessPHP](http://leafo.net/lessphp)

It includes the following functionality (even a bit more):
* __Helper__: `$this->Lesscss->style('filename')` for stylesheets in _webroot/css/filename.less_ or _.css_
* __Component__: `$this->Lesscss->compileFile($src, $dst = null, $options = array());`
* __Console__: `cake LessPHP lesscss build` to build all _.less_ files in _webroot/css/_
* __Dispatcher__: Will serve directly compiled version (when no such file exists) of _webroot/css/filename.less_ when accessing _webroot/compiled_css/filename.css_

