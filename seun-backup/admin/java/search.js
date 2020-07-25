$(document).ready(function () {

    $(".search").keyup(function () {
        var searchbox = $(this).val();
        var dataString = 'q=' + searchbox;

        if (searchbox == '') {

        } else {

            $.ajax({
                type: "POST",
                url: "managestaff/manageuserb.php",
                data: dataString,
                cache: false,
                success: function (html) {

                      $("#load2").show();
                      $("#load1").hide();
                      $("#load2").html(html);

                }


            });
        }
        return false;

    });


});

