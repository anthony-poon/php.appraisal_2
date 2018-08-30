function bindDOMElement() {
    $(document).on("click", "*[data-remove]", function(evt) {
        $(evt.target).closest(".row").remove();
    });
    $(document).on("click", "*[data-prototype]", function(evt) {
        $(evt.target).closest(".row").before($(evt.target).data("prototype"));
    });
}

export default {
    bindDOMElement
}