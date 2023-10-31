const btn_list = document.querySelectorAll('.nav-item');

const form_list = document.querySelectorAll('form');

const clear_btn = document.querySelector('.clear-btn');

form_list.forEach(function(form_elem) {
    form_elem.style.display = 'none';
});

// keep first form active
document.querySelector('.add_user').style.display = 'flex';

let mapping = {
    'Add User' : 'add_user',
    'Show User' : 'show_user',
    'Remove User' : 'remove_user',
    'Update User' : 'update_user'
};


btn_list.forEach(function (btn) {
    
    btn.addEventListener('click', function (e) {
        
        document.querySelector('.active').classList.toggle('active');        

        // disable every form first
        btn.classList.add('active');
        form_list.forEach(function(form_elem) {
            form_elem.style.display = 'none';
        });

        // grab target form
        let target_class = mapping[btn.textContent];
        let form_elem = document.querySelector('.'+target_class);

        form_elem.style.display = 'flex';
    });
});



// make clear btn funcitonal
clear_btn.addEventListener('click', function(e) {
    let all_msgs = document.querySelectorAll('.output-msg');

    all_msgs.forEach(function (elem) {
        elem.parentNode.removeChild(elem);
        console.log(elem);
    });
});


// function to render a message element in the output panel
function render_message(data, color='') {
    let terminal = document.querySelector('.output_terminal');

    let div_elem = document.createElement('div');
    div_elem.classList.add('output-msg');
    if (color)
        div_elem.classList.add(`${color}`);
    div_elem.innerHTML = data;

    let input_fields = document.querySelectorAll('input');
    input_fields.forEach(function(elem) {
        elem.value = '';
    })

    terminal.appendChild(div_elem);
}


// adduser form
let adduserForm = document.querySelector('.add_user');

adduserForm.addEventListener('submit', function(e) {
    e.preventDefault();

    let username = document.querySelector('.add_user > input[name=username]').value;
    let email = document.querySelector('.add_user > input[name=email]').value;
    let password = document.querySelector('.add_user > input[name=password]').value;

    fetch("/crud_app/add_user", {
        method: "POST",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `username=${username}&email=${email}&password=${password}`
    }).then((reslt) => {
        return reslt.text();
    }).then((text) => {
        if (text == "OK") {
            render_message("Successfully added the user", "success");
        }
        else {
            render_message("Couldnt add the user for some reason", "danger");
        }
    });


});


let showuserForm = document.querySelector('.show_user');

showuserForm.addEventListener('submit', function(e) {
    e.preventDefault();

    let email = document.querySelector('.show_user > input[name=email]').value;

    if (email == '')
        email = '%';

    fetch("/crud_app/show_user", {
        method: "POST",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `email=${email}`
    }).then((reslt) => {
        return reslt.text();
    }).then((text) => {
        let data = JSON.parse(text);
        console.log(data);
        if (data != [] && email == '%') {
            for (let d of data) {
                let message = "<p>ID : " + d['id'] + "</p>";
                message += "<p>Username : " + d['username'] + "</p>";
                message += "<p>Email : " + d['email'] + "</p>";
                message += "<p>Password Hash : " + d['password'] + "</p>";
                render_message(message);
            }
        }
        else if (data != []) {
            let message = "<p>ID : " + data['id'] + "</p>";
            message += "<p>Username : " + data['username'] + "</p>";
            message += "<p>Email : " + data['email'] + "</p>";
            message += "<p>Password Hash : " + data['password'] + "</p>";
            render_message(message);
        }
        else {
            render_message("Couldnt find the user with specified email", "danger");
        }
    });
});


let removeuserForm = document.querySelector('.remove_user');

removeuserForm.addEventListener('submit', function(e) {
    e.preventDefault();

    let email = document.querySelector('.remove_user > input[name=email]').value;

    fetch("/crud_app/remove_user", {
        method: "POST",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `email=${email}`
    }).then((reslt) => {
        return reslt.text();
    }).then((text) => {
        if (text == "OK") {
            render_message("Successfully removed the user", "success");
        }
        else {
            render_message("Could'nt find a user with given email", "danger");
        }
    });
});


let updateuserForm = document.querySelector('.update_user');

updateuserForm.addEventListener('submit', function(e) {
    e.preventDefault();

    let target_email = document.querySelector('.update_user > input[name=target_email]').value;
    let email = document.querySelector('.update_user > input[name=email]').value;
    let username = document.querySelector('.update_user > input[name=username]').value;
    let password = document.querySelector('.update_user > input[name=password]').value;

    fetch("/crud_app/update_user", {
        method: "POST",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `target_email=${target_email}&email=${email}&username=${username}&password=${password}`
    }).then((reslt) => {
        return reslt.text();
    }).then((text) => {
        if (text == "OK") {
            render_message("Successfully updated the user", "success");
        }
        else {
            render_message("Could'nt find a user with given email", "danger");
        }
    });
});