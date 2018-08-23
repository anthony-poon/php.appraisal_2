import _ from 'underscore';

function ajaxToFieldName(prefix, ajax) {
    let rtn = {};
    _.map(ajax, function(v, k) {
        if (_.isArray(v)) {
            rtn[prefix + "[" + k + "]"] = ajaxToFieldName(prefix + "[" + k + "]", v)
        } else if (_.isObject(v)){
            rtn[prefix + "[" + k + "]"] = ajaxToFieldName(prefix + "[" + k + "]", v)
        } else {
            rtn[prefix + "[" + k + "]"] = v;
        }
    });
    return rtn;
}

export default {
    ajaxToFieldName
}