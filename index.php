<?php
session_start(); // start php session
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!--- Main --->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ToDo Application</title>
    <!--- Jquery --->
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!--- Bootstrap --->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
    <!--- Icon --->
    <link rel="icon" type="image/x-icon" href="/static/img/todo_icon.ico">
    <!--- Static --->
    <script type="module" src="/static/js/index.js"></script>
    <link rel="stylesheet" href="/static/css/styles.css">
    <!--- Fonts --->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap" rel="stylesheet">
    <!--- Vue --->
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-vue/2.22.0/bootstrap-vue.min.css" integrity="sha512-YUqiEWiqDbBHeBWsrh+VDjFU6cMhVVmgjpBaoDhjMDhsfmfKVuU68KC0bFjWCgyD8B4LbcNc2p+1+NFtdYU9bw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-vue/2.22.0/bootstrap-vue.min.js" integrity="sha512-fpl6VxrVL83pzi0dMBPknsykT+mf3+TLzBigOtNKp1cPi2oEpooeOzTb+tOku1YhL7/0eDfe9nnzCPzuAwvtog==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!--- FontAwesome --->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <!--- Main app div --->
    <div id="todo-app" class="container-fluid align-middle d-flex flex-column">
        <!--- First row for title and login area --->
        <div class="row justify-content-center app-header">
            <div class="col-12 container align-middle">
                <div class="row">
                    <!--- Page header --->
                    <div class="col-8 col-lg-9 title-div">
                        Personal ToDo Application
                    </div>
                    <!--- Login/User button --->
                    <div class="col-2 col-lg-2 d-flex justify-content-center user-div">
                        <div id="user-icon" class="user-icon">
                            <!--- Vue conditional rendering --->
                            <div class="dropdown" v-if="acronym">
                                <button class="btn btn-secondary dropdown-toggle h-100 w-100" type="button" id="userDropDown" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ acronym }}
                                </button>
                                <!--- Bootstrap dropdown for user actions --->
                                <ul class="dropdown-menu user-dropdown" aria-labelledby="userDropDown">
                                    <li><a v-on:click="logoutUser" class="dropdown-item user-dropdown-option" href="#">Logout</a></li>
                                </ul>
                            </div>
                            <!--- Opening the login modal --->
                            <div onclick="document.getElementById('login_modal').style.display='block'" v-else="acronym">Login</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--- Second row for task display --->
        <div class="row justify-content-center flex-grow-1 todo_container" v-if="acronym">
            <div class="col-12 container-fluid align-middle">
                <div class="row justify-content-between">
                    <!--- Left side of the page--->
                    <div class="col-12 col-lg-7 todo_display">
                        <!--- Using vue to submit form --->
                        <form class="container-fluid" v-on:submit.prevent="addTask" autocomplete="off">
                            <div class="row flex-column flex-lg-row justify-content-center align-items-center">
                                <span class="col-4 col-lg-1"><b>Task:</b></span>
                                <input class="col-12 col-lg-3" type="text" v-model="task_name" placeholder="Enter task name..." name="task_name" required></input>
                                <input class="col-12 col-lg-4" type="text" v-model="task_description" placeholder="Enter description..." name="task_description" optional></input>
                                <input class="col-8 col-lg-2 ms-lg-auto" type="submit" value="Add task"></input>
                            </div>
                        </form>
                        <!--- Vue conditional rendering --->
                        <template v-if="running_tasks">
                            <div class="d-flex flex-column align-content-between overflow-auto mt-3">
                                <!--- BootstrapVue table for dynamic loading -->
                                <b-table class="task-table" label-sort-asc="" label-sort-desc="" label-sort-clear="" striped :fields="fields_running" id="task-table" :items="running_tasks" :per-page="per_page_running" :current-page="current_page_running" small>
                                    <template #cell(task_name)="row">
                                        <!--- Inputs/buttons use onchange/click event and custom function calls to pass task ID to the server --->
                                        <b-form-input @change="updateTask($event, 'task_name', row.item.id)" type="text" :value="row.item.task_name"></b-form-input>
                                    </template>
                                    <template #cell(description)="row">
                                        <b-form-input @change="updateTask($event, 'description', row.item.id)" type="text" :value="row.item.description"></b-form-input>
                                    </template>
                                    <template #cell(completed)="row">
                                        <b-btn class="custom-button" @click="updateTask($event, 'completed', row.item.id)">
                                            <!--- Font Awesome icon --->
                                            <i class="fa fa-check" aria-hidden="true"></i>
                                        </b-btn>
                                    </template>
                                    <template v-slot:cell(delete)="row">
                                        <span>
                                            <!--- Button uses custom click event to pass task ID to the server --->
                                            <b-btn class="custom-button" @click="deleteTask(row.item.id)">
                                                <!--- Font Awesome icon --->
                                                <i class="fa-solid fa-trash-can"></i>
                                            </b-btn>
                                        </span>
                                    </template>
                                </b-table>
                                <!--- BootstrapVue pagination for dynamic loading --->
                                <b-pagination class="custom-pagination" v-model="current_page_running" pills :per-page="per_page_running" :total-rows="rowsRunning" align="center" aria-controls="my-table"></b-pagination>
                            </div>
                        </template>
                    </div>
                    <!--- Right side of the page --->
                    <div class="mt-2 mt-lg-0 col-12 col-lg-4 todo_display">
                        <div class="d-flex justify-content-start">
                            <span><b>Completed tasks:</b></span>
                        </div>
                        <!--- Vue conditional rendering --->
                        <template v-if="completed_tasks">
                            <div class="overflow-auto mt-3">
                                <!--- BootstrapVue table for dynamic loading --->
                                <b-table class="task-table" label-sort-asc="" label-sort-desc="" label-sort-clear="" striped :fields="fields_completed" id="completed-table" :items="completed_tasks" :per-page="per_page_completed" :current-page="current_page_completed" small>
                                    <template v-slot:cell(delete)="row">
                                        <span>
                                            <!--- Button uses custom click event to pass task ID to the server --->
                                            <b-btn class="custom-button" class="delete-button" @click="deleteTask(row.item.id)">
                                                <!--- Font Awesome icon --->
                                                <i class="fa-solid fa-trash-can trash-can"></i>
                                            </b-btn>
                                        </span>
                                    </template>
                                </b-table>
                                <!--- BootstrapVue pagination for dynamic loading --->
                                <b-pagination class="custom-pagination" v-model="current_page_completed" pills :per-page="per_page_completed" :total-rows="rowsCompleted" align="center" aria-controls="my-table"></b-pagination>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <!--- MODAL AREA --->
        <!--- Login form --->
        <div id="login_modal" class="modal h-100 w-100">
            <form class="modal-content container-fluid align-middle d-flex justify-content-center login-form" v-on:submit.prevent="loginUser">
                <!--- Button to close modal --->
                <span onclick="document.getElementById('login_modal').style.display='none'" class="close" title="Close Modal">&times;</span>
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <!--- Username input with HTMl validation --->
                        <div class="col-9 text-center">
                            <label for="login_username"><b>Username</b></label>
                            <input type="text" v-model="login_username" maxlength="60" placeholder="username" name="login_username" required autofocus>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <!--- Password input with HTMl validation --->
                        <div class="col-9 text-center">
                            <label for="login_password"><b>Password</b></label>
                            <input type="password" v-model="login_password" minlength="8" placeholder="**********" name="login_password" required>
                        </div>
                    </div>
                    <!--- Status for errors while logging in --->
                    <div class="row justify-content-center">
                        <div class="col-12 text-center">
                            <span class="LS">{{login_status}}</span>
                        </div>
                    </div>
                    <!--- Link to open register modal --->
                    <div class="row justify-content-center">
                        <div class="col-12 text-center">
                            <span class="NYR" onclick="
                            document.getElementById('register_modal').style.display='block'">Haven't yet registered? Click here.</span>
                        </div>
                    </div>
                    <!--- Button area --->
                    <div class="row justify-content-center button-div">
                        <!--- Confirm form --->
                        <div class="col-4 text-center">
                            <input type="submit" value="Login"></input>
                        </div>
                        <!--- Cancel login --->
                        <div class="col-4 text-center">
                            <input type="button" onclick="document.getElementById('login_modal').style.display='none'" class="cancel_login" value="Cancel"></button>
                        </div>
                    </div>
            </form>
        </div>
        <!--- Register form --->
        <div id="register_modal" class="modal h-100 w-100">
            <form class="modal-content container-fluid align-middle d-flex justify-content-center register-form" v-on:submit.prevent="registerUser">
                <!--- Button to close modal --->
                <span onclick="document.getElementById('register_modal').style.display='none'" class="close" title="Close Modal">&times;</span>
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <!--- Username input with HTMl validation --->
                        <div class="col-9 text-center">
                            <label for="username"><b>Username</b></label>
                            <input type="text" v-model="register_usernames" maxlength="60" placeholder="username" name="username" required autofocus>
                        </div>
                    </div>
                    <!--- Password input with HTMl validation --->
                    <div class="row justify-content-center">
                        <div class="col-9 text-center">
                            <label for="password"><b>Password</b></label>
                            <input type="password" v-model="register_password" minlength="8" placeholder="**********" name="password" required>
                        </div>
                    </div>
                    <!--- Input to repeat the password input with HTMl validation --->
                    <div class="row justify-content-center">
                        <div class="col-9 text-center">
                            <label for="repeat-password"><b>Repeat Password</b></label>
                            <input type="password" v-model="repeat_password" minlength="8" placeholder="**********" name="repeat-password" required>
                        </div>
                    </div>
                    <!--- Status area to update the user during registration --->
                    <div class="row justify-content-center">
                        <div class="col-12 text-center">
                            <span class="LS">{{register_status}}</span>
                        </div>
                    </div>
                    <!--- Button area --->
                    <div class="row justify-content-center button-div">
                        <!--- Submit button --->
                        <div class="col-4 text-center">
                            <input type="submit" value="Register"></input>
                        </div>
                        <!--- Cancellation button --->
                        <div class="col-4 text-center">
                            <input type="button" onclick="document.getElementById('register_modal').style.display='none'" class="cancel_register" value="Cancel"></input>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</body>

</html>