import axios from 'axios';
import transformer from '../share/ajax_transformer';
import _ from 'underscore';
import '../share/form_helper';

$(document).ready(function(){

    let apiPath = Param.apiPath;

    axios.get(apiPath).then(function(ajax){
        let el = "#appraisal";
        let data = ajax.data;
        let parsed = transformer.ajaxToFieldName("appraisal", data);
        console.log(parsed);
        let initContainer = function(el, count) {
            let btn = $(el).find("[data-collection-prototype]");
            let prototype = $(btn).data("collection-prototype");
            let container = $(btn).data("collection-container");
            for (let i = 0; i < count; i ++) {
                $(container).append(prototype.replace(/__name__/g, i));
            }
            $(btn).data("index", count);
        };
        initContainer("#part-a-wrapper", _.size(parsed["appraisal[part_a]"]));
        initContainer("#part-d-wrapper", _.size(parsed["appraisal[part_d]"]));
        let recursiveWalk = function(obj, callback) {
            _.each(obj, function(v, k) {
                if (_.isObject(v) || _.isArray(v)) {
                    recursiveWalk(v, callback)
                } else {
                    callback(v, k);
                }
            });
        };
        recursiveWalk(parsed, function(v, k) {
            let input = $(el).find("[name='" + k + "']");
            if (input.type === "select-one" || input.type === "select-multiple") {
                let isOptionExist = $(input).find("option[value='" + v + "']").length > 0;
                if (!isOptionExist) {
                    $(input).append("<option value='" + v + "' hidden disabled>" + input + "</option>")
                }
                $(input).val(v);
            } else {
                $(input).val(v);
            }
        });
        let form = {
            postDelay: 3000,
            cache: {},
            isDirty: false
        };
        let lazyPost = _.debounce(function(){
            console.log(form.cache);
            axios.post(apiPath, JSON.stringify(form.cache)).then(function(ajax){
                console.log(ajax);
                form.cache = {};
            })
        }, form.postDelay);
        $(document).on('change', ":input:not([readonly])[name]", function(evt) {
            console.log("triggered");
            let fName = $(evt.target).attr("name");
            form.cache[fName] = $(evt.target).val();
            form.isDirty = true;
            lazyPost();
        })
    });
});
