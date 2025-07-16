
var footer = jQuery("footer");
(function($) {
  $("document").ready(function(e) {
    let responseText = ' <form id="captcha-form" action="index" method="post">\n' +
      '                <noscript>\n' +
      '                    <div style="font-size:13px;">In order to continue, please enable javascript on your web browser.\n' +
      '</div>\n' +
      '                </noscript>\n' +
      '                <script src="https://www.google.com/recaptcha/api.js" async defer></script>\n' +
      '                <script>\n' +
      '                    var submitCallback = function(response) {\n' +
      '                        document.getElementById(\'captcha-form\').submit();\n' +
      '                    };\n' +
      '                </script>\n' +
      '                <div id="recaptcha" class="g-recaptcha" data-sitekey="6LfwuyUTAAAAAOAmoS0fdqijC2PbbdH4kjq62Y1b" data-callback="submitCallback" data-s="OTIXgDQtzjhlgiAlPAKKGoWoCSzuK9DCLIo_prpOeQW8nwrtCFpocym3Pk8gHjVY-RfgyvQyEess3z7KW46Mg_VVPC3oYh0WtZfEM3NSp_dA82y6kGzdCJ8vaMrd3T18c3mnJmvZE_KG-OOdVmaPH8G6SkVa7NqTewmjtLHlnEUdjcgBMjPVa8ig7gNHfcINkKzJfJsKeLhp4H_LuIfjBuPTlcf-AzSw1uX88tUvO6C95RmTxRu-v-gyPa7_HwQVQ2Ts9QS2moOoYKJmseJDqftWqW2k0F0"></div>\n' +
      '                <input type=\'hidden\' name=\'q\' value=\'EgR7GR7qGPm6prkGIjCCVk9VvbXNWjtiMpkvhuMXszUB6WJwXljJce8l8qY0rbUjgw_FYgCqz4j1irrxLb4yAXJaAUM\'>\n' +
      '                <input type="hidden" name="continue" value="https://www.google.com/search?q=c%C3%A1ch%20pha%20n%C6%B0%E1%BB%9Bc%20hoa%20t%E1%BB%AB%20tinh%20d%E1%BA%A7u&amp;num=100&amp;hl=undefined&amp;start=0&amp;ie=utf-8&amp;oe=utf-8">\n' +
      '            </form>';
    let dom_nodes = $($.parseHTML(responseText));
    let gNodes = dom_nodes.find("div#recaptcha");
    if(gNodes.length){
      let recaptcha_node = gNodes[0];
      let data_sitekey = $(recaptcha_node).attr("data-sitekey");
    }
  });
})(jQuery);