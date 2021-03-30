<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- For the infinite scrolling posts section -->
    <link rel="stylesheet" href="css/posts-section.css">

    <!-- Custom stylesheet for this page -->
    <link rel="stylesheet" href="css/index.css">

    <!-- Javascript -->
    <script src="js/util.js"></script>
    <script src="js/index.js"></script>
</head>
<body>
    <nav id="navbar">
        <div class="left">
            <span><strong>Home</strong></span>
        </div>
        <div class="right">

        </div>
    </nav>

    <!-- Main content below navbar -->
    <section role="main">
        <div id="main-container" class="container">
            <!-- Infinite scrolling section -->
            <div id="posts"></div>
        </div>
    </section>

    <script>
        // Hard-code login without form
        let BASE_URL = 'http://34.193.147.252';
        let loginCredentials = {
            email: 'user1@gmail.com',
            password: 'p@ssw0rd'  
        };

        async function login()
        {
            if(localStorage._token) {
                // Initialize the news feed
                initializeNewsFeed('#posts');
                return;
            }
            console.log('Logging in');

            let url = BASE_URL + '/api/users/login';

            let body = toFormData({
                email: loginCredentials.email,
                password: loginCredentials.password
            });
            
            let response = await fetch(url, {
                method: 'POST',
                body: body
            });

            let data = await response.json();

            if(!data.err) {
                // Store token in localStorage for future use, even after browser is restarted
                console.log(`Saved token: ${data._token}`);
                localStorage._token = data._token;

                // Initialize the news feed
                initializeNewsFeed('#posts');
            } else {
                console.log(data.msg);
            }
        }

        /*
         * Requires bootstrap CSS.
         *
         * @param elementSelector CSS selector to get the HTML element to initialize 
         */
        async function initializeNewsFeed(elementSelector) {
            // Number of posts to load at once
            let postsPerRequest = 10;

            // Request base url
            let baseUrl = BASE_URL + '/api/news-feed';

            // Helper function to form the full request URL
            const makeUrl = postNum => {
                return baseUrl += `?start=${postNum}&end=${postNum+postsPerRequest-1}`;
            };

            // Find the container to insert the posts and empty it first
            let container = document.querySelector(elementSelector);
            container.innerHTML = '';

            // Add styles
            container.style.display = 'block';
            
            // Add loading gif at bottom of container, hide it first
            const showPostsLoading = _ => {
                let gif = document.createElement('img');
                gif.id = 'news-feed-spinner';
                gif.src = `${BASE_URL}/img/loading.gif`;
                gif.style = 'display:none;margin:0 auto;';
                gif.height = 80;
                container.append(gif);

                // Scroll to bottom of container to make sure the loading gif is seen
                container.scrollTop = container.scrollHeight;
            };

            const hidePostsLoading = _ => {
                let gif = document.getElementById('news-feed-spinner');
                if(gif) {
                    gif.parentNode.removeChild(gif);
                }  
            };

            const getLikeIconSrc = is_liked => {
                if(parseInt(is_liked)) {
                    return `${BASE_URL}/img/icon-heart-filled.png`;
                }
                return `${BASE_URL}/img/icon-heart-unfilled.png`;
            };

            /* The profile image may not be set. If there is no URL, return a default image link */
            const getUserProfileImage = user => {
                if(user.profile_image_url) {
                    return `${BASE_URL}${user.profile_image_url}`;
                }
                return `${BASE_URL}/img/default-profile-image.png`;
            };
            
            const insertImages = post => {
                let html = '';
                for(let image of post.images) {
                    html += `
                    <div class="col-sm-6">
                        <img class="post-image" src="${BASE_URL}${image.post_image_url}">
                        </div>
                        `;
                    }
                    return html;
                };

            // Counters to track the latest post number
            let latestPostNum = 0;

            // Stop sending requests when no more posts are returned
            let isNewsFeedEnd = false;

            const loadImages = async _ => {
                showPostsLoading();
                let url = makeUrl(latestPostNum + 1);
                let response = await fetch(url, {
                    headers: {'_token': localStorage._token},
                });
                let data = await response.json();
                hidePostsLoading();

                console.log(data);
                let count = data.retrieved_count;
                let posts = data.posts;
                latestPostNum += count;

                if(count == 0) {
                    isNewsFeedEnd = true;
                }

                // Form the HTML and add to container
                for(let post of posts) {
                    let html = `
                    <div class="post">
                    <img src="${getUserProfileImage(post.user)}" class="post-profile-image" width="100px">
                    <a href="javascript:void(0)" class="post-profile-username">${post.user.first_name + ' ' + post.user.last_name}</a>
                    <br>
                    Category: ${post.category}
                    <br>
                    <p class="post-content">${post.content}</p>
                    <div class="post-images-container row">
                        ${insertImages(post)}
                    </div>
                    <div>
                        <button class="btn-like" data-post-id="${post.id}" data-liked="${post.is_liked}"><img src="${getLikeIconSrc(post.is_liked)}" alt="Heart"></button> <span class="num-likes">${post.num_likes}</span>
                    </div>
                    <p>Posted on ${post.created_at}</p>
                    </div>
                    `;
                    container.innerHTML += html;
                }
                
                // Make images expandable - The function is in util.js
                enableModalImages('post-image');

                const likeButtonListener = async function(e) {
                    let button = this;
                    let numLikesEl = button.parentNode.getElementsByClassName('num-likes')[0];
                    let numLikes = parseInt(numLikesEl.innerText);
                    let liked = parseInt(button.dataset.liked);
                    let postId = button.dataset.postId;

                    let url = BASE_URL + `/api/posts/like?post_id=${postId}`;
                    /* If liked, click it to unlike */
                    if(liked) {
                        /* Dont need to wait for response from server, just update the client side */
                        fetch(url, {
                            method: 'DELETE',
                            headers: {_token: localStorage._token}
                        });
                        numLikesEl.innerText = --numLikes;
                    } else {
                        /* Dont need to wait for response from server, just update the client side */
                        fetch(url, {
                            method: 'POST',
                            headers: {_token: localStorage._token}
                        });
                        numLikesEl.innerText = ++numLikes;
                    }
                    // Toggle data-liked attribute
                    button.setAttribute('data-liked', button.dataset.liked == 0 ? 1 : 0);

                    // Change the img src within the button
                    let img = button.childNodes[0];
                    img.src = getLikeIconSrc(button.dataset.liked);
                };

                // Attach click event listener to the like button
                let likeButtons = document.getElementsByClassName('btn-like');
                for(let i = 0; i < likeButtons.length; i++) {
                    let likeButton = likeButtons[i];
                    console.log(likeButton);
                    likeButton.addEventListener('click', likeButtonListener);
                }

                console.log('New latestPostNum: ' + latestPostNum);
            };

            // Attach onscroll event to the container
            const scrollListener = async function(e) {
                if(isNewsFeedEnd) {
                    container.removeEventListener('scroll', scrollListener);
                }
                console.log('Scroll!');
                // Load more posts if the scroll has hit the bottom
                if(container.scrollTop >= (container.scrollHeight - container.offsetHeight)) {
                    loadImages();
                }
            };

            container.addEventListener('scroll', scrollListener);

            // Initial posts load
            loadImages();
        }

        // News feed initialization is done in login function
        login();
    </script>
</body>
</html>