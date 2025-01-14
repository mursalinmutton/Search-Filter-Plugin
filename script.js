jQuery(document).ready(function($) {
    $('#sfp-filter-form').on('submit', function(event) {
        event.preventDefault(); 

        let filterData = $(this).serialize();

        $.ajax({
            url: sfp_ajax_obj.ajax_url, 
            type: 'GET',
            data: filterData + '&action=sfp_filter_stories', 
            success: function(response) {
                $('#sfp-filter-results').html(response); 
            },
            error: function(error) {
                console.log('Error:', error);
            }
        });
    });
});
