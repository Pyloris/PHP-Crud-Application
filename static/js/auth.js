const form_elem = document.querySelector('form');

const pass_checks = document.querySelector('.pass-checks');

const cpass_check = document.querySelector('.cpass-check');

const pass_field = document.querySelector('input[name=password]');

const c_pass = document.querySelector('input[name=cpassword]');

// get hide/show buttons
const toggle_btn = document.querySelector('.pass-group > span');
const toggle_c_pass = document.querySelector('.c-pass-group > span');

// get password and confirmpassword elements
const pass_elem = document.querySelector('input[type=password]');
const c_pass_elem = document.querySelector('input[name=cpassword]');


// add the toggle functions
toggle_btn.addEventListener('click', function(e) {
    if (this.classList.contains('active')) {
        // remove active class
        this.classList.remove('active');

        // change type back to password
        pass_elem.setAttribute('type', 'password');

        // change icon to hidden
        toggle_btn.firstElementChild.setAttribute('src', '/crud_app/static/images/hidden.png');
    }
    else {
        this.classList.add('active');
        
        // change type to text
        pass_elem.setAttribute('type', 'text');

        // change icon to show
        toggle_btn.firstElementChild.setAttribute('src', '/crud_app/static/images/eye.png');
    }
});

// add the toggle functions
toggle_c_pass.addEventListener('click', function(e) {
    if (this.classList.contains('active')) {
        // remove active class
        this.classList.remove('active');

        // change type back to password
        c_pass_elem.setAttribute('type', 'password');

        // change icon to hidden
        toggle_c_pass.firstElementChild.setAttribute('src', '/crud_app/static/images/hidden.png');
    }
    else {
        this.classList.add('active');
        
        // change type to text
        c_pass_elem.setAttribute('type', 'text');

        // change icon to show
        toggle_c_pass.firstElementChild.setAttribute('src', '/crud_app/static/images/eye.png');
    }
});

// hide the checks
pass_checks.style.display = 'none';
cpass_check.style.display = 'none';

// list for entire form
form_elem.addEventListener('input', function (e) {
    let pass_value = pass_field.value;

    if (pass_value == '') {
        pass_checks.style.display = 'none';
    }
});

// make checks visible only if something is written in the field.
pass_field.addEventListener('input', function (e) {
    let pass_val = this.value;
    if (pass_val != '')
        pass_checks.style.display = 'inline';


    // checks to verify
    let checks = {
        'uppercase' : /[A-Z]+/,
        'numbers' : /[0-9]+/,
        'symbols' : /[@\/\.\-\!\^&\*\(\)\]\[\%\$#]+/,
        'length' : /.{8,}/
    };

    for(let key in checks) {
        // get corresponding element
        let elem = document.querySelector('.' + key + ' > span');

        if (!checks[key].test(pass_val)) {
            // set the picture
            elem.innerHTML = '<img src="/crud_app/static/images/cross.png" width="15px">';
        }
        else {
            elem.innerHTML = '<img src="/crud_app/static/images/check.png" width="15px">'; 
        }
    }
});


c_pass.addEventListener('input', function(e) {
    let cpass_val = this.value;
    let msg_elem = document.querySelector('.cpass-check > li');

    if (cpass_val != '')
        cpass_check.style.display = 'inline';
    else
        cpass_check.style.display = 'none';

    if (pass_field.value == cpass_val) {
        msg_elem.innerHTML = '<img src="/crud_app/static/images/check.png" width="15px"> Passwords Match';
    }
    else {
        msg_elem.innerHTML = '<img src="/crud_app/static/images/cross.png" width="15px"> Passwords do not Match';
    }
});

