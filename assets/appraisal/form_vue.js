import Vue from 'vue';
import axios from 'axios';
import BaseForm from '../vue/appraisal/version_1/BaseForm';

$(document).ready(function(){
    window.apiPath = Param.apiPath;
    window.form = new Vue({
        el: "#appraisal-wrapper",
        render: h => h(BaseForm, {
            props: {
                apiPath: apiPath
            }
        })
    });
});
