$(function() {
      "use strict";
      //Datepicker embedded
      var picker = $('#date_booking').daterangepicker({
        parentEl: '#daterangepicker-embedded-container',
        autoUpdateInput: false,
        autoApply :true,
        alwaysShowCalendars:true,
        locale: {
                separator:' > ',
                direction: 'ltr',
                format: 'DD-MM-YY',
                applyLabel: 'Valider',
                cancelLabel: 'Annuler',
                fromLabel: 'De',
                toLabel: 'à',
                daysOfWeek: [
                    'Dim',
                    'Lun',
                    'Mar',
                    'Mer',
                    'Jeu',
                    'Ven',
                    'Sam'
                ],
                monthNames: [
                    'Janvier',
                    'Février',
                    'Mars',
                    'Avril',
                    'Mai',
                    'Juin',
                    'Juillet',
                    'Août',
                    'Septembre',
                    'Octobre',
                    'Novembre',
                    'Décembre'
                ]
            },
      });
      // range update listener
      picker.on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YY') + ' to ' + picker.endDate.format('DD-MM-YY'));
      });
      // prevent hide after range selection
      picker.data('daterangepicker').hide = function () {};
      // show picker on load
      picker.data('daterangepicker').show();
  });