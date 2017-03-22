<?php

namespace App\Http\Middleware;

use Closure;
use Neo\CodeMarker\CodeMarker;

class CodeMarkerMiddleware {

    /**
     * Inject the codemarker code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $content = $response->getContent();

        if (config('options.code_marker')) {
            $markers  = CodeMarker::instance()->profileCode(false, false);

            $total = 0;

            $html = '<div id="code-marker"><div class="list"><ul>';

            foreach ($markers as $key => $value) {
                $total += (int) $value;
                $html .= "<li><strong>{$key}</strong> took "
                      .  "<strong>{$value}</strong> seconds to execute.</li>";
            }

            $html .= '</ul></div>'
                  .  '<div class="indicator">'
                  .  '<a href="#">'
                  .  'Page took <span class="seconds">'.$total.'</span> seconds to load'
                  .  '<div class="expand">Click to expand</div>'
                  .  '<div class="contract">Click to contract</div>'
                  .  '</a>'
                  .  '</div>'
                  .  '</div>';

            $html .= '<style type="text/css">'.$this->getCSS().'</style>';
            $html .= '<script type="text/javascript">'.$this->getJS().'</script>';

            $content = str_replace('</body>', "{$html}\n</body>", $content);
        }

        $response->setContent($content);

        return $response;
    }

     protected function getJS()
    {
        $jsfile = self::getFileContents("getJSContent.js");  
        return $jsfile;
    }

    protected function getCSS()
    {
      $cssfile = self::getFileContents("getCSSContent.css");
      return $cssfile;
    }

    public static function getFileContents($filename)
    {  
      if(file_exists($filename))
      {
        return file_get_contents($filename);
      }
      
     return "File not Found!! 😭";
   }
}