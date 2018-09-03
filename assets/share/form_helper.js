function bindDOMElement() {
    $(document).on("click", "*[data-collection-remove]", function(evt) {
        $(evt.target).closest("[data-index]").remove();
    });
    $(document).on("click", "*[data-collection-prototype]", function(evt) {
        let prototype = $(evt.target).data("collection-prototype");
        let container = $(evt.target).data("collection-container");
        let index = $(evt.target).data("index");
        if (!index) {
            index = $(container).children().length;
        }
        index = index + 1;
        prototype = prototype.replace(/__name__/g, index);
        $(evt.target).data("index", index);
        $(container).append(prototype);
    });
}

export default {
    bindDOMElement
}