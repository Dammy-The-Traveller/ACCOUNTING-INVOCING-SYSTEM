function showInputErrors(e) {
    void 0 !== e.errors &&
    $.each(e.errors, function (e, t) {
        $("#" + e + "-error").remove();
        let s = '<label id="' + e + '-error" class="error" for="' + e + '">' + t + "</label>";
        $("#" + e)
            .parents()
            .hasClass("form-check")
            ? $("#" + e)
                .parents()
                .find(".form-check")
                .append(s)
            : $("#" + e)
                .addClass("error")
                .after(s);
    });
}
if (
    (!(function (e) {
        "use strict";
        (e.fn.initValidate = function () {
            e(this).validate({ errorClass: "error text-danger", validClass: "" });
        }),
            e(".checkAll").on("click", function () {
                e("input:checkbox").not(this).prop("checked", this.checked);
            }),
            e("#is_period").on("change", function () {
                1 == e(this).val() ? e(".period_duration").removeClass("d-none") : e(".period_duration").addClass("d-none");
            }),
            e(".anna-dismiss").on("click", function () {
                e(".top-header-area").fadeOut();
            }),
            (e.fn.initFormValidation = function () {
                var t = e(this).validate({
                    errorClass: "error",
                    highlight: function (t, s) {
                        var a = e(t);
                        a.hasClass("select2-hidden-accessible")
                            ? e("#select2-" + a.attr("id") + "-container")
                                .parent()
                                .addClass(s)
                            : a.hasClass("input-group")
                                ? e("#" + a.add("id"))
                                    .parents(".input-group")
                                    .append(s)
                                : a.parents().hasClass("image-checkbox")
                                    ? Notify("error", null, a.parent().data("required"))
                                    : a.addClass(s);
                    },
                    unhighlight: function (t, s) {
                        var a = e(t);
                        a.hasClass("select2-hidden-accessible")
                            ? e("#select2-" + a.attr("id") + "-container")
                                .parent()
                                .removeClass(s)
                            : a.removeClass(s);
                    },
                    errorPlacement: function (t, s) {
                        var a = e(s);
                        a.hasClass("select2-hidden-accessible")
                            ? ((s = e("#select2-" + a.attr("id") + "-container").parent()), t.insertAfter(s))
                            : a.parents().hasClass("image-checkbox") ||
                            (a.parent().hasClass("form-floating") ? t.insertAfter(s.parent().css("color", "text-danger")) : a.parent().hasClass("input-group") ? t.insertAfter(s.parent()) : t.insertAfter(s));
                    },
                });
                e(this).on("select2:select", function () {
                    e.isEmptyObject(t.submitted) || t.form();
                });
            });
        var t = !1;
        "undefined" != typeof jQuery &&
        void 0 !== e.fn.select2 &&
        ([].slice.call(document.querySelectorAll('[data-control="select2"]')).map(function (t) {
            var s = { dir: document.body.getAttribute("direction") };
            "true" == t.getAttribute("data-hide-search") && (s.minimumResultsForSearch = 1 / 0),
            t.hasAttribute("data-placeholder") && (s.placeholder = t.getAttribute("data-placeholder")),
                e(document).ready(function () {
                    e(t).select2(s);
                });
        }),
        !1 === t &&
        ((t = !0),
            e(document).on("select2:open", function (e) {
                var t = document.querySelectorAll(".select2-container--open .select2-search__field");
                t.length > 0 && t[t.length - 1].focus();
            })));
    })(jQuery),
        $(document).ready(function () {
            "true" === window.sessionStorage.hasPreviousMessage && (Notify("success", null, window.sessionStorage.previousMessage, window.sessionStorage.redirect), (window.sessionStorage.hasPreviousMessage = !1));
        }),
    $("#selectAll").length > 0)
) {
    let e = document.querySelector("#selectAll"),
        t = document.querySelectorAll('[type="checkbox"]');
    e.addEventListener("change", (e) => {
        t.forEach((t) => {
            t.checked = e.target.checked;
        });
    });
}
