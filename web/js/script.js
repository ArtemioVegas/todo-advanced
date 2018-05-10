'use strict';

var expandControls = document.querySelectorAll('.expand-control');

var hidePopups = function() {
  [].forEach.call(document.querySelectorAll('.expand-list'), function(item) {
    item.classList.add('hidden');
  });
};

document.body.addEventListener('click', hidePopups, true);

/*
[].forEach.call(expandControls, function(item) {
  item.addEventListener('click', function() {
    item.nextElementSibling.classList.toggle('hidden');
  });
});
*/

$(document).on('click','.expand-control',function(){//do something}
    $(this).next().toggleClass('hidden',false);
 }
);

var $checkbox = document.getElementsByClassName('checkbox__input')[0];

$checkbox.addEventListener('change', function(event) {
  var is_checked = +event.target.checked;

  window.location = '?show_completed=' + is_checked;
});


(function ($) {
        $(document).on('submit', 'form[data-confirmation]', function (event) {
                var $form = $(this),
                        $confirm = $('#confirmationModal');

                    if ($confirm.data('result') !== 'yes') {
                        //cancel submit event
                            event.preventDefault();

                            $confirm
                            .off('click', '#btnYes')
                            .on('click', '#btnYes', function () {
                                    $confirm.data('result', 'yes');
                                    $form.find('input[type="submit"]').attr('disabled', 'disabled');
                                    $form.submit();
                                })
                            .modal('show');
                    }
            });
    })(window.jQuery);