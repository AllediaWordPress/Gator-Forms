if (typeof jQuery !== "undefined") jQuery(document).ready(function ($) {
    $(document).on('click', '.pweb-repeatable-item > .pweb-repeatable-add', function (e) {
        e.preventDefault(); //Do not scroll up (due to the pound sign in href)
        var item = $(this).parent();
        item.clone().appendTo(item.parent());
        item.parent().trigger('pweb-repeatable:update');
    });

    $(document).on('click', '.pweb-repeatable-item > .pweb-repeatable-remove', function (e) {
        e.preventDefault(); //Do not scroll up (due to the pound sign in href)
        var item = $(this).parent(),
            left = item.siblings().length,
            container = item.parent();
        if (left > 0) {
            item.remove();
            container.trigger('pweb-repeatable:update');
        }
    });

    $('.pweb-repeatable-container').on('pweb-repeatable:update', function (e) {
        //Recalculate indices here, probably.
        var container = $(this);
        container.children().each(function (i, v) {
            var item = $(v);
            item.find("input,textarea,select").each(function () {
                this.disabled = false;
                $(this)
                    .attr("id", $(this).attr("id").replace(/_Y_/g, "_" + i + "_"))
                    .attr("name", $(this).attr("name").replace(/\[Y\]/g, "[" + i + "]"));
            });
        });
    });
});


