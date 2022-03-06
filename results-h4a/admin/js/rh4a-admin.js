(function ($) {
    'use strict';
    $(document).ready(function () {
        // DELETE ITEM
        $('.rh4a-delete-item-link').on('click', (oEvent) => {
            if(confirm("Are you sure?")) {
                let oBtn = $(oEvent.target);
                let oTr = oBtn.parents("tr");
                $.post(oRH4A.ajax_url, {
                    _ajax_nonce: oRH4A.nonce,
                    action: "delete_item",
                    item_type: oBtn.data("item-type"),
                    item_id: oBtn.data("item-id") // 8888
                }).done((oData) => {
                    if(oData !== false && oData > 0) {
                        // Item was deleted -> remove row
                        oTr.fadeOut();
                    } else {
                        // Item was not deleted
                        if(!oTr.hasClass("rh4a-transition")) {
                            oTr.addClass("rh4a-transition");
                        }
                        oTr.addClass("rh4a-background-error");
                        setTimeout(function() {
                            oTr.removeClass("rh4a-background-error");
                        }, 300);
                    }
                });
            }
        });

        // CHANGE STATUS OF ITEM
        $('.rh4a-change-status-link').on('click', (oEvent) => {
            let oBtn = $(oEvent.currentTarget); // currentTarget because span with dashicon is clicked initially
            let oTr = oBtn.parents("tr");
            let iNewStatus = oBtn.data("item-new-status");
            $.post(oRH4A.ajax_url, {
                _ajax_nonce: oRH4A.nonce,
                action: "change_status",
                item_type: oBtn.data("item-type"),
                item_new_status: iNewStatus,
                item_id: oBtn.data("item-id") // 8888
            }).done((oData) => {
                if(oData !== false && oData > 0) {
                    // Item was edited -> highlight row
                    if(!oTr.hasClass("rh4a-transition")) {
                        oTr.addClass("rh4a-transition");
                    }
                    oTr.addClass("rh4a-background-success");
                    setTimeout(function() {
                        oTr.removeClass("rh4a-background-success");
                    }, 300);
                    oBtn.data("item-new-status", iNewStatus == 1 ? 0 : 1);
                    oBtn.children("span.dashicons").toggleClass(["dashicons-hidden", "dashicons-visibility"]);
                    oBtn.siblings(".rh4a-status-text").text(oBtn.data(iNewStatus == 1 ? "item-status-text-active" : "item-status-text-inactive"));
                } else {
                    // Item was not edited
                    if(!oTr.hasClass("rh4a-transition")) {
                        oTr.addClass("rh4a-transition");
                    }
                    oTr.addClass("rh4a-background-error");
                    setTimeout(function() {
                        oTr.removeClass("rh4a-background-error");
                    }, 300);
                }
            });
        });

    });
})(jQuery);
