$(function () {
    $("form").on("submit", function () {
        var $form = $(this);
        var $submit = $form.find('button[type="submit"]');
        $submit.attr("disabled", true);
    });
});
