import axios from 'axios';
import transformer from '../share/ajax_transformer';
import _ from 'underscore';
$(document).ready(function(){
    let appraisal = {};

    let bindAjaxData = function(option) {
        let el = option.el;
        let data = option.data;
        let parsed = transformer.ajaxToFieldName("appraisal", data);
        console.log(parsed);
        _.each(parsed, function(val, fName) {
            if (_.isObject(val)) {
                let prototype = $("[data-prototype='" + fName + "']");
                _.each(val, function(child, key){
                    let clone = prototype.clone();
                    prototype.before(clone);
                    clone.data("prototype", null);
                    clone.attr("data-prototype", null);
                    clone.data("index", key);
                    _.each(clone.find("[data-proto-name]"), function(el){
                        $(el).attr("name", key + $(el).data("proto-name"));
                    });
                    _.each(child, function(cVal, cKey){
                        let inputEls = $(el).find("[name='" + cKey + "']")
                        _.each(inputEls, function(inputEl) {
                            $(inputEl).val(cVal);
                        })
                    });
                });

            } else {
                let inputEls = $(el).find("[name='" + fName + "']");
                _.each(inputEls, function(inputEl){
                    if (inputEl.type === "select-one" || inputEl.type === "select-multiple") {
                        let isOptionExist = $(inputEl).find("option[value='" + val + "']").length > 0;
                        console.log(isOptionExist);
                        if (!isOptionExist) {
                            $(inputEl).append("<option value='" + val + "' hidden disabled>" + val + "</option>")
                        }
                        $(inputEl).val(val);
                    } else {
                        $(inputEl).val(val);
                    }
                });
            }
        });

    };
    let apiPath = Param.apiPath;
    axios.get(apiPath).then(function(ajax){
        bindAjaxData({
            el: "#appraisal",
            data: ajax.data
        })
    })
});
