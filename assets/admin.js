import Sortable from 'sortablejs';
import 'jquery-sortablejs';

const $ = require('jquery');

require('@nobleclem/jquery-multiselect');
import '@nobleclem/jquery-multiselect/jquery.multiselect.css';
import './styles/admin.scss';

var cityFormField = $('#user_schools');
if(cityFormField){
    cityFormField.multiselect({
        placeholder: 'Selecteer gemeentes',
        maxWidth: '300px',
        position: 'relative',
        maxHeight: '235px',
        maxPlaceholderOpts: 0,
        search   : true,
        selectGroup: true,
        texts    : {
            placeholder: 'Selecteer gemeentes',
            search     : 'Typ hier je gemeente om te zoeken'
        }
    });
}

$('.js-sortable').each(function (key, sortable) {
  $(sortable).sortable({
    handle: '.js-sortable-handle',
    ghostClass: 'ghost',
  });
})

$('#school_educations').on('submit', function () {
  $('.js-sortable').each(function (key, sortable) {
    var inputs = $(sortable).find('.js-position');
    inputs.each(function (k, row) {
      $(row).val(k);
    })
  });
})
