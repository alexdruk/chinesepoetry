$(function () {
  $(".tip").each(function () {
    $(this).tooltip({
      html: true,
      title: $("#" + $(this).data("tip")).html(),
    });
  });
});
