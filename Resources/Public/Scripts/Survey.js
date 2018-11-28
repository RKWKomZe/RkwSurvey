/*--------------------------------------------------------------
# Karrierecheck Scripts
--------------------------------------------------------------*/

jQuery(document).ready(function () {

    /*--------------------------------------------------------------
     Answers - Global functionality
     --------------------------------------------------------------*/
    jQuery('.tx-rkwsurvey .radio-group .radio').on(
        "click",
        function () {
            var dataId = jQuery(this).find('input[data-id]').attr('data-id');
            if (dataId) {
                jQuery('.tx-rkwsurvey .answer-tip').show();
                jQuery('.tx-rkwsurvey .answer-tip .answer').hide();
                jQuery('.tx-rkwsurvey .answer-tip #' + dataId).show();
            }

        }
    );

    var checkedFields = jQuery('.tx-rkwsurvey .radio-group .radio input:checked');
    if (checkedFields.length) {
        checkedFields.each(function() {
            jQuery(this).closest('.radio').addClass('selected');
            if (jQuery(this).attr('data-id')) {
                var dataId = jQuery(this).attr('data-id');
                jQuery('.tx-rkwsurvey .answer-tip').show();
                jQuery('.tx-rkwsurvey .answer-tip #' + dataId).show();
            }
        });
    }

    /*--------------------------------------------------------------
     Answers - Single select
     -------------------------------------------------------------- */
    jQuery('.tx-rkwsurvey .radio-group--single .radio').on(
        "click",
        function () {
            // here we need prop() to have it working correctly
            jQuery(this).parent().find('input[type=radio]').prop('checked', false);
            jQuery(this).find('input[type=radio]').prop('checked', true);
            jQuery(this).parent().find('.radio').removeClass('selected');
            jQuery(this).addClass('selected');
        }
    );

    /*--------------------------------------------------------------
    Answers - Mulitple select
    -------------------------------------------------------------- */
    jQuery('.tx-rkwsurvey .radio-group--multiple .radio').on(
        "click",
        function (event) {
            event.preventDefault();
            var $checkbox = jQuery(this).find('input[type=checkbox]');
            if ($checkbox.attr( 'checked' )) {
                jQuery(this).removeClass('selected');
                $checkbox.removeAttr('checked');
            } else {
                jQuery(this).addClass('selected');
                $checkbox.attr('checked', 'checked');
            }
        }
    );


    /*--------------------------------------------------------------
     More Info Overlay
     --------------------------------------------------------------*/
    jQuery('.tx-rkwsurvey .question .more-info').click(function(){
        jQuery('.question-overlay').fadeIn();
    });

    jQuery('.tx-rkwsurvey .question-overlay .close-trigger').click(function () {
        jQuery('.question-overlay').fadeOut();
    });


});




