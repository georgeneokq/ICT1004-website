<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <ul>

    </ul>

    <script>
        let url = '/api/users';
        let followUrl = '/api/users/follow';
        let listener = async function(e) {
                let button = e.target;
                let userToFollow = button.dataset.userId;
                let formData = new FormData();
                formData.append('user_to_follow', userToFollow);
                let response = await fetch(followUrl, {
                    method: 'POST',
                    headers: {_token: localStorage._token},
                    body: formData
                });
                let data = await response.json();
                if(data.err) {
                    window.alert('Not logged in');
                } else {
                    window.alert('Followed');
                }
        };
            (async function() {
                let response = await fetch(url);
                let users = await response.json();
                let ul = document.querySelector('ul');
                for (let user of users) {
                    let html = `<li>${user.email}</li> <button id="follow" data-user-id="${user.id}">Follow</button>`;
                    ul.innerHTML += html;
                }
                let buttons = document.querySelectorAll('button');
                for (let i = 0; i < buttons.length; i++) {
                    buttons[i].addEventListener('click', listener);
                }
            })();
    </script>
</body>

</html>