/*--------------------------------------------------------------
# Karrierecheck Scripts
--------------------------------------------------------------*/

jQuery(document).ready(function () {

  /*--------------------------------------------------------------
   Answers - Global functionality
   --------------------------------------------------------------*/
  jQuery('.tx-rkwwebcheck .radio-group .radio').on(
    "click",
    function () {
      var dataId = jQuery(this).find('input[data-id]').attr('data-id');
      if (dataId) {
        jQuery('.tx-rkwwebcheck .answer-tip').show();
        jQuery('.tx-rkwwebcheck .answer-tip .answer').hide();
        jQuery('.tx-rkwwebcheck .answer-tip #' + dataId).show();
      }

    }
  );

  var checkedFields = jQuery('.tx-rkwwebcheck .radio-group .radio input:checked');
  if (checkedFields.length) {
    checkedFields.each(function() {
      jQuery(this).closest('.radio').addClass('selected');
      if (jQuery(this).attr('data-id')) {
        var dataId = jQuery(this).attr('data-id');
        jQuery('.tx-rkwwebcheck .answer-tip').show();
        jQuery('.tx-rkwwebcheck .answer-tip #' + dataId).show();
      }
    });
  }

  /*--------------------------------------------------------------
   Answers - Single select
   -------------------------------------------------------------- */
  jQuery('.tx-rkwwebcheck .radio-group--single .radio').on(
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
  jQuery('.tx-rkwwebcheck .radio-group--multiple .radio').on(
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
  jQuery('.tx-rkwwebcheck .question .more-info').click(function(){
    jQuery('.question-overlay').fadeIn();
  });

  jQuery('.tx-rkwwebcheck .question-overlay .close-trigger').click(function () {
    jQuery('.question-overlay').fadeOut();
  });


  /*--------------------------------------------------------------
   Functionality for benchmarks
   --------------------------------------------------------------*/
  jQuery('.tx-rkwwebcheck #mainCheckResult').on('change', function() {
    var $form = jQuery(this).closest('.tx-rkwwebcheck form');
    $form.submit();
  });

  jQuery('.tx-rkwwebcheck #compareCheckResult').on('change', function() {
    var $form = jQuery(this).closest('.tx-rkwwebcheck form');
    $form.submit();
  });

});
