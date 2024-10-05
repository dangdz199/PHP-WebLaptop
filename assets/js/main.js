$(document).ready(function () {
  $("#cartModal").on("show.bs.modal", function () {
    $.ajax({
      url: "controllers/get_cart.php",
      type: "GET",
      dataType: "html",
      success: function (response) {
        console.log("AJAX Success:", response);
        $("#cart-content").html(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error("AJAX Error:", textStatus, errorThrown);
        $("#cart-content").html(
          "<p>Không thể tải giỏ hàng. Vui lòng thử lại sau.</p>"
        );
      },
    });
  });

  // Initialize autocomplete
  $("#search").autocomplete({
    source: function (request, response) {
      $.ajax({
        url: "controllers/autocomplete.php",
        type: "GET",
        dataType: "json",
        data: {
          term: request.term,
        },
        success: function (data) {
          response(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.error("Autocomplete AJAX Error:", textStatus, errorThrown);
        },
      });
    },
    minLength: 1, // số ký tự tối thiểu để bắt đầu tìm kiếm
  });
});
