$(function() {
    
    // Отправка формы
    $('.ajax-form').on('submit', function(e){
        let form = $(this);
        let data = form.serialize();
        let metrikaId = form.data('counter');
        let goal = $('.ajax-form input[name="goal"]').val();
        $.ajax({
            url: 'php/main.php',
            type: 'POST',
            data: data,
            success: function() {
                $('.ajax-form')[0].reset();
                ym(metrikaId, 'reachGoal', goal);
                $('#recallModal').modal('hide');
                $('#thankyouModal').modal('show');
            }
        })
        e.preventDefault();
    });

    //Подстановка заголовков и текста кнопок в модальное окно
    $('.button').on('click', function() {
        let title = $(this).data('title');
        let button = $(this).data('button');
        let goal = $(this).data('goal');
        $('.modal-title').text(title);
        $('.submit-form').val(button);
        $('.ajax-form input[name="goal"]').val(goal);
        $('.ajax-form input[name="formname"]').val(title);
    });

    // Экранирование номера
    $('input[type="phone"]').inputmask('+7 (999) 999-99-99');

    //Плавный скролл к якорям
    $('a[href^="#"]').on('click', function(event) {
        event.preventDefault();
        var sc = $(this).attr("href"),
            dn = $(sc).offset().top;

        $('html, body').animate({scrollTop: dn}, 1000);
    });

    // Получение get из url
    let getUrlParams = window
    .location
    .search
    .replace('?','')
    .split('&')
    .reduce(
        function(p,e){
            var a = e.split('=');
            p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
            return p;
        },
        {}
    );

    // Подстановка UTM-меток
    $('.ajax-form input[name="utm_source"]').val( getUrlParams['utm_source'] );
    $('.ajax-form input[name="utm_medium"]').val( getUrlParams['utm_medium'] );
    $('.ajax-form input[name="utm_campaign"]').val( getUrlParams['utm_campaign'] );
    $('.ajax-form input[name="utm_content"]').val( getUrlParams['utm_content'] );
    $('.ajax-form input[name="utm_term"]').val( getUrlParams['utm_term'] );
    $('.ajax-form input[name="yclid"]').val( getUrlParams['yclid'] );

});
