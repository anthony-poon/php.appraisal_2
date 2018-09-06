import axios from 'axios';
import transformer from '../share/ajax_transformer';
import _ from 'underscore';
import '../share/form_helper';

$(document).ready(function(){

    let apiPath = Param.apiPath;
    let form = {
        postDelay: 3000,
        isDirty: false
    };
    let lazyPost = _.debounce(function(){
        let form = $("form");
        axios.post($(form).attr("action"), $(form).serialize()).then(function(ajax){
            console.log(ajax);
        })
    }, form.postDelay);
    $(document).on('change', ":input:not([readonly])[name]", function(evt) {
        form.isDirty = true;
        lazyPost();
    });
});
