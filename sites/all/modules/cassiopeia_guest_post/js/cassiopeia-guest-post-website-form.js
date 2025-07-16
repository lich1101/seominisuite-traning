if (typeof (window.cassiopeia_tagifies) == 'undefined') {
    window.cassiopeia_tagifies = {};
}
(function ($) {
    Drupal.behaviors.cassiopeia_guest_post_website_form = {
        attach: function (context, settings) {
            $('.cassiopeia-guest-post-website-form', context).once('cassiopeia-guest-post-website-form',function () {
                var typingTimer;
                $("document").ready(function (e) {
                    let str = "<div class=\"loading-block active\">\n" +
                        "    <div class=\"loading-block-container\">\n" +
                        "        <div class=\"lds-css ng-scope\">\n" +
                        "            <div class=\"lds-spin\" style=\"width:100%;height:100%\"><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div></div>\n" +
                        "        </div>\n" +
                        "    </div>\n" +
                        "</div>";
                    $("input[name='tagify-agent']").each(function( index, element ) {
                        let _application_country_controller_; // for aborting the call
                        let _application_country_whitelist_ = $('input[name="agent"]').val()? JSON.parse($('input[name="agent"]').val()):[];
                        cassiopeia_tagifies['tagify_agent'] = $(element).tagify({
                            whitelist: _application_country_whitelist_,
                            // maxTags: 1,
                            enforceWhitelist : true,
                            editTags: 0,
                            pasteAsTags: false,
                            dropdown: {
                                maxItems: 300,
                                classname: "tags-look",
                                enabled: 1,
                                closeOnSelect: false,
                                searchKeys: ["code", "name"],
                            },
                            templates : {
                                tag : function(tagData){
                                    try{
                                        return `<tag title='${tagData.name} (${tagData.code})' contenteditable='false' spellcheck="false" class='tagify__tag ${tagData.class ? tagData.class : ""}' ${this.getAttributes(tagData)}>
                    <x title='remove tag' class='tagify__tag__removeBtn'></x>
                    <div>
                        <div class="country-autocomplete-item">
                          ${tagData.code ? `<span onerror="this.style.visibility='hidden'" class="fi fi-${tagData.code.toLowerCase()}"></span>` : ''}
                          <span class='tagify__tag-text'>${tagData.name} (${tagData.code})</span>
                        </div>
                    </div>
                    </tag>`
                                    }
                                    catch(err){}
                                },

                                dropdownItem : function(tagData){
                                    try{
                                        return `<div class='tagify__dropdown__item ${tagData.class ? tagData.class : ""}' tagifySuggestionIdx="${tagData.tagifySuggestionIdx}" style="width: 100% !important;">
                        <div class="country-autocomplete-item">
                          <span onerror="this.style.visibility = 'hidden'" class="fi fi-${tagData.code.toLowerCase()}"></span>
                          <span>${tagData.name} (${tagData.code})</span>
                        </div>
                    </div>`
                                    }
                                    catch(err){}
                                }
                            },
                            hooks: {
                                beforePaste: function (ClipboardEvent ,tagify) {
                                    return new Promise(function(resolve, reject){
                                        _application_country_controller_ && _application_country_controller_.abort();
                                        _application_country_controller_ = new AbortController();
                                        cassiopeia_tagifies['tagify_agent'].data('tagify').loading(true).dropdown.hide();
                                        fetch(location.protocol+'//'+location.host +'/cassiopeia/agent/ajax/autocomplete?string=' + tagify.pastedText +'&type=tagify', {signal:_application_country_controller_.signal})
                                            .then(RES => RES.json())
                                            .then(function(applicationCountryNewWhitelist){
                                                cassiopeia_tagifies['tagify_agent'].data('tagify').whitelist = applicationCountryNewWhitelist; // update whitelist Array in-place
                                                cassiopeia_tagifies['tagify_agent'].data('tagify').loading(false).dropdown.show(tagify.pastedText); // render the suggestions dropdown
                                            });
                                        resolve();
                                    });
                                }
                            }
                        });
                        cassiopeia_tagifies['tagify_agent'].on('input', function(e, data){
                            cassiopeia_tagifies['tagify_agent'].data('tagify').whitelist = null; // reset the whitelist
                            console.log("cassiopeia_tagifies['tagify_agent'].data('tagify')",cassiopeia_tagifies['tagify_agent'].data('tagify').loading(true).dropdown);
                            clearTimeout(typingTimer);
                            typingTimer = setTimeout(function() {
                                let _value_ = data.value;
                                _application_country_controller_ && _application_country_controller_.abort();
                                _application_country_controller_ = new AbortController();
                                cassiopeia_tagifies['tagify_agent'].data('tagify').loading(true).dropdown.hide();
                                fetch(location.protocol+'//'+location.host +'/cassiopeia/agent/ajax/autocomplete?string=' + _value_+'&type=tagify', {signal:_application_country_controller_.signal})
                                    .then(RES => RES.json())
                                    .then(function(applicationCountryNewWhitelist){
                                        cassiopeia_tagifies['tagify_agent'].data('tagify').whitelist = applicationCountryNewWhitelist; // update whitelist Array in-place
                                        cassiopeia_tagifies['tagify_agent'].data('tagify').loading(false).dropdown.show(_value_); // render the suggestions dropdown
                                    })
                            },300);
                        });
                        // cassiopeia_tagifies['tagify_agent'].on('focus', function(e, data){
                        //     let _value_ = data.value;
                        //     cassiopeia_tagifies['tagify_agent'].data('tagify').whitelist = null; // reset the whitelist
                        //     _application_country_controller_ && _application_country_controller_.abort();
                        //     _application_country_controller_ = new AbortController();
                        //     cassiopeia_tagifies['tagify_agent'].data('tagify').loading(true).dropdown.hide();
                        //     fetch(location.protocol+'//'+location.host +'/cassiopeia/agent/ajax/autocomplete?string=' + _value_+'&type=tagify', {signal:_application_country_controller_.signal})
                        //         .then(RES => RES.json())
                        //         .then(function(applicationCountryNewWhitelist){
                        //             cassiopeia_tagifies['tagify_agent'].data('tagify').whitelist = applicationCountryNewWhitelist; // update whitelist Array in-place
                        //             cassiopeia_tagifies['tagify_agent'].data('tagify').loading(false).dropdown.show(_value_); // render the suggestions dropdown
                        //         })
                        // });
                        cassiopeia_tagifies['tagify_agent'].on('change', function (e,data) {
                            let form = '.cassiopeia-guest-post-website-form';
                            let field_name = 'agent';
                            if (typeof (data) != 'undefined') {
                                $(form + ' input[name="'+field_name+'"]').val(data);
                                setTimeout(function () {
                                    $(form + ' input[name="'+field_name+'"]').trigger('blur');
                                }, 100);
                            }else {
                                $(form + ' input[name="'+field_name+'"]').val('');
                                setTimeout(function () {
                                    $(form + ' input[name="'+field_name+'"]').trigger('blur');
                                }, 100);
                            }
                        });
                    });
                });

            });
        },
        detach: function(context, settings, trigger) {
            $('.cassiopeia-guest-post-website-form', context).removeOnce('cassiopeia-guest-post-website-form', function() {});
        }
    };
})(jQuery);