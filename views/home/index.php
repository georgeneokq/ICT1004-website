<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Responsive sidebar template with sliding effect and dropdown menu based on bootstrap 3">
    <title>Sidebar template</title>

    <!-- Redirect if not logged in -->
    <script>
        if (!localStorage._token) window.location.href = '/';
    </script>

    <!-- using online links -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

    <link rel="stylesheet" href="/css/home/profile.css">
    <link rel="stylesheet" href="/css/home/posts-section.css">
    <link rel="stylesheet" href="/css/home/index.css">

    <link rel="shortcut icon" type="image/png" href="img/favicon.png" />

    <script src="js/libs/sweetalert2.all.min.js"></script>
    <script src="js/util.js"></script>
    <script src="js/home/index.js" defer></script>
    <!--<script src='https://cloud.tinymce.com/stable/tinymce.min.js'></script> -->

</head>

<body>
    <div class="page-wrapper default-theme sidebar-bg bg1 toggled">
        <a id="show-sidebar" class="btn btn-L btn-dark sticky" href="#">
            <i class="fas fa-bars"></i>
        </a>
        <nav id="sidebar" class="sidebar-wrapper">
            <div class="sidebar-content">
                <!-- sidebar-brand  -->
                <div class="sidebar-item sidebar-brand">
                    <a href="#">Pet$tonks</a>
                    <div id="close-sidebar">
                        <i class="fas fa-times"></i>
                    </div>
                </div>
                <!-- sidebar-header  -->
                <div class="sidebar-item sidebar-header d-flex flex-nowrap">
                    <div data-toggle="modal" data-target="#uploadModal" class="user-pic">
                        <img id="profile-image-main" class="img-responsive img-rounded" src="/img/icons/icon-user.jpg" alt="User picture">
                    </div>
                    <div class="user-info">
                        <span class="user-name">Ben
                            <strong>Dover</strong>
                        </span>
                        <span class="user-email"></span>
                        <span class="user-status">
                            <i class="fa fa-circle"></i>
                            <span>Online</span>
                        </span>
                    </div>
                </div>
                <!-- sidebar-search  -->
                <div class="sidebar-item sidebar-search">
                    <div>
                        <div class="input-group">
                            <input type="text" class="form-control search-menu" placeholder="Search...">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- sidebar-menu  -->
                <div class=" sidebar-item sidebar-menu">
                    <ul>
                        <li class="header-menu">
                            <span>Functions</span>
                        </li>
                        <li>
                            <a data-toggle="modal" data-target="#myModal" href="#">
                                <i class="fa fa-book"></i>
                                <span class="menu-text">Edit Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa fa-calendar"></i>
                                <span data-toggle="modal" data-target="#uploadModal" class="menu-text">Change Profile Picture</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa fa-folder"></i>
                                <span data-toggle="modal" data-target="#createpostmodal" class="menu-text">Create Post</span>
                            </a>
                        </li>
                        <li>
                            <a href="/follow">
                                <i class="fa fa-folder"></i>
                                <span class="menu-text">Follow</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="logout">
                    <button type="submit" id="btn-logout" class="logoutbtn">Logout</button>
                </div>
                <!-- sidebar menu -->
            </div>
            <!-- sidebar footer -->
        </nav>
        <!-- page-content  -->
        <main class="page-content">
            <div id="overlay" class="overlay"></div>
            <div class="container-fluid">
                <!-- News feed -->
                <div id="posts"></div>
            </div>
        </main>
        <!-- page-content" -->
    </div>
    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Profile</h4>
                </div>
                <div class="modal-body">
                    <form id="updateprofile">
                        <div class="form-group">
                            <label for="firstnamechange">First Name</label>
                            <input type="text" class="form-control" name="firstnamechange" id="firstnamechange" aria-describedby="firstnamechange" placeholder="Change First Name" value="">
                        </div>
                        <div class="form-group">
                            <label for="lastnamechange">last Name</label>
                            <input type="text" class="form-control" name="lastnamechange" id="lastnamechange" aria-describedby="lastnamechange" placeholder="Change Last Name" value="">
                        </div>
                        <div class="form-group">
                            <label for="biographychange">Biography</label>
                            <input type="text" class="form-control" name="biographychange" id="biographychange" aria-describedby="biographychange" placeholder="Change Biography" value="">
                        </div>
                        <p id="upderror"></p>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div id="uploadModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Change profile image</h4>
                </div>
                <div class="modal-body">
                    <!-- Form -->
                    <form id="changeimg">
                        <label for="file">Select file :</label>
                        <input type="file" name="profile_image" id="file" aria-label="Change Profile Img" class="form-control">
                        <br>
                        <img id="preview" class="previewimg" src="#" style="display: none;" alt="imagepreview" />
                        <br>
                        <input type="submit" class="btn btn-info" value="Upload" id="btn_upload">
                        <p id="imgerror"></p>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="createpostmodal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create Post</h4>
                </div>
                <div class="modal-body">
                    <form id="createpost">
                        <div class="form-group form-group-lg">
                            <label for="content">Content</label>
                            <!--<input type="text" class="form-control inputlg" name="content" id="content" aria-describedby="content" placeholder="Content" required>-->
                            <textarea class="form-control inputlg" name="content" id="content" aria-describedby="content" placeholder="Content" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="post_image">Select file :</label>
                            <input type="file" name="images[]" multiple id="post_image" class="form-control">
                            <img id="postpreview" class="previewimg" src="#" style="display: none;" alt="imagepreview " />
                        </div>
                        <div class="form-group">
                            <label for="category-select">Pet Category:</label>
                            <select id="category-select" class="form-select form-select-lg mb-3" name="category" aria-label="Category" required>
                                <option value="">--Select category--</option>
                                <option value="Others">Others</option>
                                <option value="Cat">Cat</option>
                                <option value="Dog">Dog</option>
                                <option value="Bird">Bird</option>
                                <option value="Fish">Fish</option>
                                <option value="Hamsters">Hamsters</option>
                                <option value="Rabbits">Rabbits</option>
                                <option value="Terrapins">Terrapins</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <p id="posterror"></p>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- page-wrapper -->

    <!-- using online scripts -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js "></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js " integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut " crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
    <script src="//malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js "></script>

    <!-- using local scripts -->
    <!-- <script src="../node_modules/jquery/dist/jquery.min.js "></script>
    <script src="../node_modules/popper.js/dist/umd/popper.min.js "></script>
    <script src="../node_modules/bootstrap/dist/js/bootstrap.min.js "></script>
    <script src="../node_modules/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js "></script> -->

</body>

</html>