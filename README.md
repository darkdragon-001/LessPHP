LessPHP CakePHP Plugin
======================

This is a CakePHP Plugin for [LessPHP](http://leafo.net/lessphp)

It includes the following functionality:
* __Helper__: `$this->Lesscss->style('filename')` for stylesheets in webroot/css/filename.less or .css
* __Component__: `$this->Lesscss->compileFile($src, $dst = null, $options = array());`
* __Console__: `cake LessPHP lesscss build` to build all .less files in webroot/css/
* __Dispatcher__: Will serve directly compiled version (when no such file exists) of webroot/css/filename.less when accessing webroot/compiled_css/filename.css