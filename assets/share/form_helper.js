function bindDOMElement() {
    $(document).on("click", "*[data-collection-remove]", function(evt) {
        $(evt.target).closest(".row").remove();
    });
    $(document).on("click", "*[data-collection-prototype]", function(evt) {
        let prototype = $(evt.target).data("collection-prototype");
        let container = $(evt.target).data("collection-container");
        let index = $(evt.target).data("index");
        if (!index) {
            index = $(container).children().length;
        }
        prototype = prototype.replace(/__name__/g, index);
        $(evt.target).data("index", index + 1);
        $(container).append(prototype);
    });
}

export default {
    bindDOMElement
}