jQuery(document).ready(function($) {
    // Handle form submission
    $('#registration-form').submit(function(e) {
        e.preventDefault(); // Prevent default form submission

        // Get form data
        var formData = $(this).serialize();

        // Send AJAX request
        $.ajax({
            url: registration_ajax_object.ajax_url, // WordPress AJAX URL
            type: 'POST',
            data: formData,
            success: function(response) {
                // Handle the response from the server
                console.log(response.data);
                console.log( "Status code" );
                // Add your own logic here
            },
            error: function(xhr, status, error) {
                // Handle error
                console.error(error);
                console.log( "Error status code" );
            }
        });
    });
});
