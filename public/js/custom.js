$(window).ready(function(){
window.loadCronJobScript = function () {
    $(function () {

        let currentRunAt = $('.run-at-cron-job').data('current-value');
        let currentCleanAt = $('.clean-at-cron-job').data('current-value');


        console.log(currentCleanAt, currentRunAt);

        $('.run-at-cron-job').jqCron(
            {
                enabled_minute: true,
                default_period: 'week',
                no_reset_button: false,
                numeric_zero_pad: true,
                default_value: currentRunAt,
                lang: 'en',
                bind_to: $('.run-at-cron-input'),
                bind_method: {
                    set: function ($element, value) {

                        console.log(value);
                        $element.val(value);
                    }
                },
            }
        );
        $('.run-at-cron-job').parent().css('z-index', 100000);


        $('.clean-at-cron-job').jqCron(
            {
                enabled_minute: true,
                default_period: 'week',
                no_reset_button: false,
                numeric_zero_pad: true,
                lang: 'en',
                default_value: currentCleanAt,
                bind_to: $('.clean-at-cron-input'),
                bind_method: {
                    set: function ($element, value) {

                        console.log($element);
                        $element.val(value);
                    }
                },
            }
        );
        $('.clean-at-cron-job').parent().css('z-index', 99999);


    });

}});
