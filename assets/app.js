/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
import '@nobleclem/jquery-multiselect/jquery.multiselect.css';

const $ = require('jquery');

require('bootstrap');
require('@nobleclem/jquery-multiselect');

import '@fortawesome/fontawesome-free/js/fontawesome'
import '@fortawesome/fontawesome-free/js/solid'
import '@fortawesome/fontawesome-free/js/regular'
import '@fortawesome/fontawesome-free/js/brands'

let $loadingContent = $('#loading');
let $formContent = $('#main-filter-form');

let $form = $('#filter-form');
let $cityFormField = $('#filter_form_cities');
let $schoolYearButtons = $("input[name='filter_form[schoolYear]']");
let $typeButtons = $("input[name='filter_form[type]']");
let $levelButtons = $("input[name='filter_form[level]']");

let $schoolLevelFormField = $("select[name='filter_form[schoolLevel]']");
let $schoolTypeFormField = $("select[name='filter_form[schoolType]']");
let $schoolPhaseFormField = $("select[name='filter_form[schoolPhase]']");
let $schoolGradeFormField = $("select[name='filter_form[schoolGrade]']");
let $educationFormField = $("select[name='filter_form[education]']");

let $noResetFields = [
    'filter_form[schoolYear]',
    'filter_form[type]',
    'filter_form[level]',
];

if ($cityFormField) {
    let $cityFormChanged = false;

    $cityFormField.on("change", function() {
      $cityFormChanged = true;
    });

    $cityFormField.multiselect({
        placeholder: 'Selecteer gemeentes',
        maxWidth: '100%',
        maxHeight: '235px',
        maxPlaceholderOpts: 0,
        search: true,
        selectGroup: true,
        texts: {
            placeholder: 'Selecteer gemeentes',
            search: 'Typ hier je gemeente om te zoeken'
        },
        searchOptions: {
          searchValue: true,
          searchText: false,
        },
        onControlClose: function( element ){
          if($cityFormChanged){
            formSubmit();
          }
        }
    });

    $schoolYearButtons.on("change", function () {
        formChanged(this.name);
    });

    $typeButtons.on("change", function () {
        formChanged(this.name);
    });

    $levelButtons.on("change", function () {
        formChanged(this.name);
    });

    $schoolLevelFormField.on("change", function () {
        formChanged(this.name);
    });

    $schoolTypeFormField.on("change", function () {
        formChanged(this.name);
    });

    $schoolPhaseFormField.on("change", function () {
        formChanged(this.name);
    });

    $schoolGradeFormField.on("change", function () {
        formChanged(this.name);
    });

    $educationFormField.on("change", function () {
        formChanged(this.name);
    });

    function formChanged(fieldName) {
        var elements = document.getElementById("filter-form").elements;
        var setNull = false;

        for (var i = 0, element; element = elements[i++];) {
            if (setNull === true && element.name !== fieldName && $noResetFields.indexOf(element.name) === -1) {
                element.value = null;
            }
            if (element.name === fieldName) {
                setNull = true;
            }
        }

       formSubmit();
    }

    function formSubmit() {
      if(
        !isFilled($schoolYearButtons) ||
        !isFilled($typeButtons) ||
        !isFilled($levelButtons) ||
        $('#filter_form_cities option:selected').length === 0
      ){
        return;
      }

      showForm(false);
      $form.submit();
    }

    $( document ).ready(function() {
        showForm(true);
    });

    function showForm($showForm){
        $formContent.attr("hidden",!$showForm);
        $loadingContent.attr("hidden",$showForm);
    }
}

function isFilled($input) {
  var filled = false;

  $input.each(function()  {
    if ($(this).is(':checked')) {
      filled = true;
    }
  });

  return filled;
}


var mobileTriggerFilter = $('.toggle-filter');
var appFilter = $('.app-filter');
var resultFilter = $('.app-results');
mobileTriggerFilter.on('click', function(e) {
    e.preventDefault();
    appFilter.toggleClass('active');
    resultFilter.toggleClass('active', !appFilter.is('.active'));
    mobileTriggerFilter.toggleClass('invisible');
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
});

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })
