import axios from 'axios';
import _ from 'underscore';
import '../share/form_helper';

$(document).ready(function(){
    let btmBar = {
        _el: $("#status-bar"),
        setText: function(option) {
            let cls = option.cls || "bg-success text-white";
            let text = option.text || "";
            this._el.removeClass();
            this._el.html(text);
            this._el.addClass(cls);
        }
    };
    let form = {
        el: $("form"),
        url: $("form").data("ajax"),
        postDelay: 3000,
        _isDirty: false,
        setIsDirty: function (val) {
            this._isDirty = val;
            btmBar.setText({
                text: "Change not saved.",
                cls: "bg-danger text-white"
            });
        }
    };

    let lazyPost = _.debounce(function(){
        btmBar.setText({
            text: "Saving...",
            cls: "bg-secondary text-white"
        });
        $(form.el).find(".is-invalid").removeClass("is-invalid");
        $(form.el).find(".invalid-feedback").remove();
        axios({
            url: form.url,
            method: 'post',
            data: $(form.el).serialize(),
            responseType: 'json'
        }).then(function(ajax){
            form.setIsDirty(false);
            btmBar.setText({
                text: "Saved.",
                cls: "bg-success text-white"
            });
            let rtn = ajax.data;
            _.each(rtn.data, function(v, k){
                $("[name$='[" + k + "]']").val(v);
            })
        }).catch(function(ajax) {
            btmBar.setText({
                text: "Error when saving.",
                cls: "bg-danger text-white"
            });
            if (ajax.response) {
                let errors = ajax.response.data;
                _.each(errors.error, function(e) {
                    console.log(e);
                    let src = "[name='" + e.source + "']";
                    let msg = e.msg;
                    $(form.el).find(".is-invalid").removeClass("is-invalid");
                    $(src).addClass("is-invalid");
                    $(src).after("<div class='invalid-feedback'>" + msg + "</div>");
                })
            }
        });
    }, form.postDelay);

    $(document).on('keydown', ":input:not([readonly])[name]", function(evt) {
        form.setIsDirty(true);
        lazyPost();
    });

    $(document).on('delete', "[data-collection-name]", function(evt, args) {
        let data = new FormData();
        data.set("name", args.collectionName);
        data.set("index", args.index);
        axios({
            url: form.url,
            method: 'delete',
            data: JSON.stringify({
                name: args.collectionName,
                index: args.index
            }),
            responseType: 'json'
        })
    });
});
