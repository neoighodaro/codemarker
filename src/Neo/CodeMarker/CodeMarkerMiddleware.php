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
        return '!function(){function e(){var e=($("#code-marker"),$("#code-marker .indicator a")),a=$("#code-marker .list"),c=$("#code-marker .expand"),o=$("#code-marker .contract");e.off().on("click",function(){var e="none"==a.css("display");e?a.show():a.hide(),e?(c.hide(),o.show()):(c.show(),o.hide())})}if("undefined"==typeof jQuery){var a=document.getElementsByTagName("head")[0],c=document.createElement("script");c.type="text/javascript",c.src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js",c.onload=e,a.appendChild(c)}else e()}();';
    }

    protected function getCSS()
    {
        return "#code-marker{width:250px;position:absolute;bottom:0;right:0;margin:0 20px 20px 0;z-index:9999;border-radius:5px;border:1px solid rgba(136,136,136,.5);background:rgba(170,170,170,.5);font-size:12px;font-family:Roboto,'Helvetica Neue',sans-serif;padding:7px 10px;-webkit-transition:all .3s ease;transition:all .3s ease}#code-marker:hover{background:rgba(170,170,170,.9)}#code-marker:hover .indicator a{background:rgba(152,152,152,.9)}#code-marker .indicator a{display:block;font-size:13px;text-align:center;text-decoration:none;padding:7px 10px;margin:-7px -10px;color:#333;-webkit-transition:all .3s ease;transition:all .3s ease;background:rgba(170,170,170,.5)}#code-marker .indicator a:hover{background:rgba(152,152,152,.9)}#code-marker .indicator .seconds{font-weight:700}#code-marker .indicator .contract,#code-marker .indicator .expand{display:block;font-size:10px;color:#333;margin-top:3px}#code-marker .indicator .contract,#code-marker .list{display:none}#code-marker .list ul{margin:10px 0 27px;padding-left:15px}#code-marker .list ul li{margin-bottom:7px}";
    }
}