// Vue2 app declaration
let App = new Vue({
    el: '#todo-app', // div for app
    data: {
        // specific user data used for login queries and rendering
        user_id: '',
        login_username: '',
        login_password: '',
        acronym: '',
        login_status: '',
        register_usernames: '',
        register_password: '',
        repeat_password: '',
        register_status: '',

        // specific user data used for task queries and rendering
        task_name: '',
        task_description: '',
        completed_tasks: [],
        running_tasks: [],

        // validation errors
        errors: [],

        // global settings for tasks in progress
        per_page_running: 16,
        current_page_running: 1,
        fields_running: [
            { key: "task_name", sortable: true, thStyle: { width: "20%" }, },
            { key: "last_changed", sortable: true, thStyle: { width: "25%" }, },
            { key: "description", sortable: false, thStyle: { width: "50%" }, },
            { key: "completed", sortable: false, thStyle: { width: "10%" }, },
            { key: "delete", thStyle: { width: "10%" } }
        ],

        // global settings for completed tasks
        per_page_completed: 16,
        current_page_completed: 1,
        fields_completed: [
            { key: "task_name", sortable: true, thStyle: { width: "30%" }, },
            { key: "last_changed", label: "Completed on", sortable: true, thStyle: { width: "50%" }, },
            { key: "delete", thStyle: { width: "20%" } }
        ]
    },
    mounted: function () {
        this.checkLogin(); // function call on page load
    },
    computed: {
        rowsRunning() {
            return this.running_tasks.length; // function for pagination computation
        },
        rowsCompleted() {
            return this.completed_tasks.length; // function for pagination computation
        }
    },
    methods: {
        // function that checks if user is present in session on the server
        checkLogin: function () {
            console.log("Checking session...");
            // get request without parameters
            axios({
                method: 'get',
                url: 'API/login.php',
                config: {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            }).then((response) => {
                // if no error
                if (!response.data.error) {
                    // save data from session to application
                    this.acronym = response.data.acronym;
                    this.user_id = response.data.id;
                    this.loadUserTasks(); // call task loader for set user
                }
            }).catch((response) => {
                // catch and log error
                console.log(response);
            });
        },
        // function that logs in the user
        loginUser: function () {
            console.log("Logging in... ");

            // parameter validation
            if (!this.login_username || !this.login_password) {
                this.errors.push("Invalid login parameters.");
                return false;
            } else if (this.login_username.length > 60 || this.login_password.length < 8) {
                this.errors.push("Username or password of incorrect length.");
                return false;
            }

            // creating post method data
            let formData = new FormData();
            formData.append('username', this.login_username)
            formData.append('password', this.login_password)

            // post request
            axios({
                method: 'post',
                url: 'API/login.php',
                data: formData,
                config: {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            }).then((response) => {
                // if no error present
                if (!response.data.error) {
                    // save user data from server after login to application
                    this.acronym = response.data.acronym;
                    this.user_id = response.data.id;
                    this.loadUserTasks(); // load user tasks
                    document.getElementById('login_modal').style.display = 'none' // hide login modal
                    // reset login form 
                    this.login_username = '';
                    this.login_password = '';
                } else {
                    this.login_status = response.data.error; // inform user of error while logging in
                }
            }).catch((response) => {
                // catch and log error
                console.log(response);
            });
        },
        // function that registers the user
        registerUser: function () {
            console.log("Registering user...");

            // parameter validation
            if (!this.register_usernames || !this.register_password || !this.repeat_password) {
                this.errors.push("Invalid register parameters.");
                return false;
            } else if (this.register_usernames.length > 60 || this.register_password.length < 8 || this.repeat_password.length < 8) {
                this.errors.push("Username or password of incorrect length.");
                return false;
            }

            // creating data for post request
            let formData = new FormData();
            formData.append('username', this.register_usernames)
            formData.append('password', this.register_password)
            formData.append('repeat_password', this.repeat_password)

            // post request
            axios({
                method: 'post',
                url: 'API/register.php',
                data: formData,
                config: {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            }).then((response) => {
                // if no error
                if (!response.data.error) {
                    this.login_username = response.data.username; // set username for faster login
                    document.getElementById('register_modal').style.display = 'none' // hide register modal
                    // reset register form
                    this.register_usernames = '';
                    this.register_password = '';
                    this.repeat_password = '';
                } else {
                    this.register_status = response.data.error; // inform user of error while registering
                }
            }).catch((response) => {
                // catch and log error
                console.log(response);
            });;
        },
        // function for logging out user
        logoutUser: function () {
            console.log("Logging out...");

            // post request
            axios({
                method: 'post',
                url: 'API/logout.php',
                config: {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            }).then((response) => {
                // if no error
                if (!response.data.error) {
                    // reset user data
                    this.login_username = '';
                    this.login_password = '';
                    this.acronym = '';
                    this.login_status = '';
                    this.register_usernames = '';
                    this.register_password = '';
                    this.repeat_password = '';
                    this.register_status = '';

                    // reset user task data
                    this.task_name = '';
                    this.task_description = '';
                    this.completed_tasks = [];
                    this.running_tasks = [];

                    // reset errors
                    this.errors = [];
                }
            }).catch((response) => {
                // catch and log error
                console.log(response);
            });
        },
        // function that ads a task for a user
        addTask: function () {
            console.log("Adding task...");

            // parameter validation
            if (!this.user_id || !this.task_name) {
                this.errors.push("Invalid task data.");
                return false;
            }

            // creating data for post request
            let formData = new FormData();
            formData.append('user_id', this.user_id)
            formData.append('task_name', this.task_name)
            formData.append('task_description', this.task_description)

            // post request
            axios({
                method: 'post',
                url: 'API/tasks.php',
                data: formData,
                config: {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            }).then((response) => {
                // if no error
                if (!response.data.error) {
                    // reset task creation form
                    this.task_name = '';
                    this.task_description = '';
                    this.loadUserTasks(); // reload user tasks
                }
            }).catch((response) => {
                // catch and log error
                console.log(response);
            });
        },
        // function for loading user tasks
        loadUserTasks: function () {
            console.log("Loading tasks...");

            // get request with parameters
            axios.get(
                `API/tasks.php`, {
                params: {
                    user_id: this.user_id
                },
                config: {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            }).then((response) => {
                // if no error
                if (!response.data.error) {
                    // set tasks according to completion status with filtering
                    this.running_tasks = response.data.tasks.filter(t => !Number(t.completed));
                    this.completed_tasks = response.data.tasks.filter(t => Number(t.completed));
                }
            }).catch((response) => {
                // catch and log error
                console.log(response);
            });
        },
        // function that updates a specific task
        updateTask: function (value, type, id) {
            console.log("Updating task...");

            // parameter validation
            if (!type || !['task_name', 'description', 'completed'].includes(type)) {
                this.errors.push("Invalid data type.");
                return false;
            } else if (type != "description" && !value) {
                this.errors.push("Invalid update data.");
                return false;
            } else if (!id || isNaN(id)) {
                this.errors.push("Invalid task id.");
                return false
            }

            if (type == "completed") {
                value = true;
            }

            // creating data for post request
            let formData = new FormData();
            formData.append('id', id); // get id from client
            formData.append("type", type);
            formData.append("value", value);

            // post request
            axios({
                method: 'post',
                url: 'API/updater.php',
                data: formData,
                config: {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            }).then((response) => {
                // if no error
                if (!response.data.error) {
                    this.loadUserTasks(); // reload user tasks
                }
            }).catch((response) => {
                // catch and log error
                console.log(response);
            });
        },
        // function for deleting a specific task
        deleteTask: function (id) {
            console.log("Deleting task...");

            // parameter validation
            if (!id || isNaN(id)) {
                this.errors.push("Invalid task id.");
                return false
            }

            // creating data for post request
            let formData = new FormData();
            formData.append('id', id); // get id from client

            // post request
            axios({
                method: 'post',
                url: 'API/delete_task.php',
                data: formData,
                config: {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            }).then((response) => {
                // if no error
                if (!response.data.error) {
                    this.loadUserTasks(); // reload user tasks
                }
            }).catch((response) => {
                // catch and log error
                console.log(response);
            });
        }
    }
});