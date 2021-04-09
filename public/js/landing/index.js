let production = true;
let BASE_URL;

/*
 * Do variables setup
 */
(function() {
    if (production) {
        /* For production when deployed to server */
        BASE_URL = '';
    } else {
        /* To ease local development */
        BASE_URL = 'https://petstonks.ml';
    }
})();

/* Login form */
const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById('form-container');

signUpButton.addEventListener('click', () => {
    container.classList.add("right-panel-active");
});

signInButton.addEventListener('click', _ => {
    container.classList.remove("right-panel-active");
});

async function login(e) {
    e.preventDefault();
    /* get inputs */
    let form = e.target;
    let email = form.querySelector('[name=email]').value;
    let password = form.querySelector('[name=password]').value;

    let url = BASE_URL + '/api/users/login';

    /* Send to API */
    let body = toFormData({
        email,
        password
    });

    let response = await fetch(url, {
        body,
        method: 'POST'
    });
    let data = await response.json();

    if (!data.err) {
        /* Save token to localStorage */
        localStorage._token = data._token;
        /* Redirect to posts page */
        window.location.href = '/home';
    } else {
        Swal.fire({
            text: data.msg
        });
    }
}

async function signup(e) {
    e.preventDefault();

    let form = e.target;
    let nameField = form.querySelector('[name=name]');
    let emailField = form.querySelector('[name=email]');
    let passwordField = form.querySelector('[name=password]');
    let name = nameField.value;
    let email = emailField.value;
    let password = passwordField.value;

    let body = toFormData({
        email,
        password,
        first_name: name
    });

    let url = BASE_URL + '/api/users/signup';

    let response = await fetch(url, {
        body,
        method: 'POST'
    });

    let data = await response.json();

    /* Signup is successful, show success message and clear inputs */
    if (!data.err) {
        Swal.fire({
            title: 'Success',
            html: 'You have successfully registered your account.<br>Please check your email for a verification link.',
            icon: 'success'
        });
        nameField.value = '';
        emailField.value = '';
        passwordField.value = '';

        signInButton.click();

    } else {
        Swal.fire(data.msg);
    }
}

/* Add event listeners for login and sign up */
let loginForm = document.querySelector('.sign-in-container form');
let signupForm = document.querySelector('.sign-up-container form');
let mobileLoginForm = document.querySelector('.sign-in-container-mobile form');
let mobileSignupForm = document.querySelector('.sign-up-container-mobile form');

loginForm.addEventListener('submit', login);
mobileLoginForm.addEventListener('submit', login);
signupForm.addEventListener('submit', signup);
mobileSignupForm.addEventListener('submit', signup);

let mobileLoginContainer = document.querySelector('.sign-in-container-mobile');
let mobileChangeToSignupBtn = mobileLoginContainer.querySelector('a');
let mobileSignupContainer = document.querySelector('.sign-up-container-mobile');
let mobileChangeToLoginBtn = mobileSignupContainer.querySelector('a');

mobileChangeToSignupBtn.addEventListener('click', function(e) {
    e.preventDefault();
    $(mobileSignupContainer).css('display', 'block');
    $(mobileLoginContainer).css('display', 'none');
});

mobileChangeToLoginBtn.addEventListener('click', function(e) {
    e.preventDefault();
    $(mobileLoginContainer).css('display', 'block');
    $(mobileSignupContainer).css('display', 'none');
});