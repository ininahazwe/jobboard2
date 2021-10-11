$(function(){
    console.log('Validation - Ready !');
    $('form[name="registration_form"]').validate({
        rules: {
            'registration_form[firstname]': {
                required: true
            },
            'registration_form[lastname]': {
                required: true
            },
            'registration_form[email]': {
                required: true,
                email: true
            },
            userName: {
                required: true,
                minlength: 6
            },
            'registration_form[password][first]': {
                required: true,
                minlength: 8
            },
            'registration_form[password][second]': {
                required: true,
                minlength: 8,
                equalTo: '#registration_form_password_first'
            }
        },
        messages: {
            'registration_form[firstname]': 'Veuillez saisir votre prénom',
            'registration_form[lastname]': 'Veuillez saisir votre nom',
            'registration_form[email]': 'Veuillez saisir votre email',
            userName: {
                required: 'Please enter a User Name',
                minlength: 'Your User Name must consist of at least 6 characters'
            },
            'registration_form[password][first]': {
                required: 'Veuillez saisir un mot de passe',
                minlength: 'Le mot de passe doit comporter au moins 8 lettres'
            },
            'registration_form[password][second]': {
                required: 'Veuillez saisir un mot de passe',
                minlength: 'Le mot de passe doit comporter au moins 8 lettres',
                equalTo: 'Les mots de passe doivent être identiques'
            }
        },
        errorElement: 'em',
        errorPlacement: function (error, element) {
            error.addClass('registration-invalid-feedback');

            if (element.prop('type') === 'checkbox') {
                error.insertAfter( element.next('label'));
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid').removeClass('is-valid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).addClass('is-valid').removeClass('is-invalid');
        }
    });
});