<template>
    <div v-if="createForm" class="container" id="container" >
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div  class="card-header"><p class="h4 text-center mb-4">My tasks</p></div>
                        <div class="card-body">
                            <form @submit.prevent="logout">
                                <div class="text-right mt-4">
                                    <button class="btn btn-submit btn-dark" type="submit">Logout</button>
                                </div>
                            </form>
                        </div>
                    <br>
                    <br>
                    <div class="row">

                        <!--                          ADD TASK                           -->
                        <div class="col">
                            <div class="card-body">
                                <div  class="card-header"><p class="h4 text-center mb-4">Add task</p></div>
                                <br>
                                <form @submit.prevent="createTask">
                                    <div class="form-group">
                                        <input type="text" v-model="fields.name" class="form-control"placeholder="Name *"/>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" v-model="fields.description" class="form-control"placeholder="Description *"/>
                                    </div>
                                    <label  class="grey-text">Deadline *</label>
                                    <input type="date" v-model="fields.date" class="form-control"/>
                                    <div class="text-center mt-4">
                                        <button class="btn btn-submit btn-dark" type="submit">Add</button>
                                    </div>
                                </form>
                                <br>
                                <!--                         ADD ALERT                          -->

                                <div class="alert alert-danger" v-if="error">
                                    <ul>
                                        <li v-for="item in errors">
                                            {{ item }}
                                        </li>
                                    </ul>
                                </div>
                                <div class="alert alert-success" v-if="created">
                                    <ul>
                                        <li>
                                            {{ createdMessage }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!--                         FILTERS                          -->
                        <div class="col">
                            <div class="card-body">
                                <div  class="card-header"><p class="h4 text-center mb-4">Filters</p></div>
                                <div class="text-center mt-4">
                                    <p>Completed</p>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" @change="filter()" v-model="filters.checkbox" type="checkbox">
                                </div>
                                    <div class="form-group">
                                        <input type="text" @keyup="filter()"  v-model="filters.name" class="form-control"placeholder="Name"/>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" @keyup="filter()" v-model="filters.description" class="form-control"placeholder="Description"/>
                                    </div>
                                    <div class="form-group">
                                        <p>Created at: </p><input type="date" @change="filter()"  v-model="filters.createDate" class="form-control"/>
                                    </div>
                                    <div class="form-group">
                                        <p>Deadline: </p><input type="date" @change="filter()" v-model="filters.finishDate" class="form-control"/>
                                    </div>

                                    <div class="text-center mt-4">
                                        <button class="btn btn-submit btn-dark" v-on:click="filter()">Filter</button>
                                        <button class="btn btn-submit btn-dark" v-on:click="resetFilters()">Reset</button>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <!--                         TASKS LIST                          -->
                    <div  class="card-body" v-if="this.tasks.length > 0">
                        <div  class="card-header"><p class="h4 text-center mb-4">Tasks list</p></div>
                        <br>
                        <table class="table table-hover table-bordered" >
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Created at</th>
                                <th>Deadline</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>

                            <tbody>
                            <tr v-for="item in tasks" :class="{ 'green' : item.completed_at != null}">
                                <td>{{item.name}}</td>
                                <td>{{item.description}}</td>
                                <td>{{ moment(item.created_date).format('MMM Do YY') }}</td>
                                <td>{{ moment(item.complete_at).format('MMM Do YY') }}</td>
                                <td>
                                    <form @submit.prevent="editTask(item.id)">
                                        <button class="btn btn-info btn-xs">Edit</button>
                                    </form>
                                </td>
                                <td>
                                    <form @submit.prevent="deleteTask(item.id)">
                                            <button class="btn btn-danger btn-xs">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!--                         DELETE ALERT                          -->

                        <div class="alert alert-danger" v-if="deleted">
                            <ul>
                                <li>
                                    {{ deletedMessage }}
                                </li>
                            </ul>
                        </div>
                        <!--                         PAGINATION                          -->

                        <ul class="breadcrumb">
                            <li style="margin-right: 10px">Page</li>
                            <li>{{currentPage}}</li>
                            <li>/</li>
                            <li>{{totalPage}}</li>
                        </ul>
                        <nav aria-label="Page navigation example">
                            <ul class="pagination">
                                <li v-for="x in totalPage"   class="page-item"><button style="margin-right: 10px;"  class="btn btn-dark"  v-on:click="paginate({x})">{{x}}</button></li>
                            </ul>
                        </nav>
                        <!--                         EDIT ALERT                          -->

                        <div class="alert alert-info" v-if="updated">
                            <ul>
                                <li>
                                    {{ updatedMessage }}
                                </li>
                            </ul>
                        </div>
                        <div class="alert alert-danger" v-if="errorE">
                            <ul>
                                <li v-for="item in errors">
                                    {{ item }}
                                </li>
                            </ul>
                        </div>
                        <!--                         EDIT TASK                          -->

                        <div v-if="editForm" >
                            <div  class="card-header"><p class="h4 text-center mb-4">Edit task</p></div>
                            <br>
                            <form @submit.prevent="updateTask(edits.id)">
                                <div class="form-group">
                                    <input type="text" v-model="edits.name" class="form-control"placeholder="Name"/>
                                </div>
                                <div class="form-group">
                                    <input type="text" v-model="edits.description" class="form-control"placeholder="Description"/>
                                </div>
                                <label  class="grey-text">Deadline</label>
                                <input type="date" v-model="edits.complete_at" class="form-control"/>
                                <div class="text-center mt-4">
                                    <p>Completed</p>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" v-model="edits.checkbox" type="checkbox">
                                </div>
                                <div class="text-center mt-4">
                                    <button class="btn btn-submit btn-info" type="submit">Edit task</button>
                                </div>
                            </form>
                        </div>
                        <br>
                    </div>
                    <!--                         TASKS LIST EMPTY                       -->
                    <div v-else>
                        <div  class="card-header"><p class="h4 text-center mb-4">No tasks</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
export default {
    data () {
        return {
            fields: {},
            edits: {},
            updates: {},
            tasks: {},
            errors: [],
            error: false,
            errorE:false,
            created: false,
            editForm: false,
            updated: false,
            deleted:false,
            createdMessage: "Task added",
            updatedMessage: "Task edited",
            deletedMessage: "Task deleted",
            filters: {},
            createForm: true,
            totalPage: null,
            currentPage: null,
            id: "",
        }
    },
    mounted() {
        axios.get('/task').then(response => {
            this.currentPage = response.data.current_page;
            this.tasks = response.data.data;
            this.totalPage = response.data.last_page;
            console.log(response.data);
            this.filters.checkbox = false;
        })
    },
    methods: {
        logout () {
            axios.get('/logout')
                .then( response => {
                    console.log("Logout");
                    createForm: false;
                    localStorage.id = "";
                    window.location.reload();                })
                .catch(error => {
                    if (error.response.status == 422) {
                        console.log("Error")
                    }
                    console.log('Error');
                });
        },
        createTask () {
            this.errors = [];
            this.error = false;
            this.errorE = false;
            this.createValidator(this.fields.name, this.fields.description, this.fields.date);
            const containsKey = (obj, key ) => Object.keys(obj).includes(key);
            if(this.errors.length > 0){
                this.error = true;
            }
            else
            {
                axios.post('/task' + '?page=' + this.currentPage, this.fields)
                    .then( response => {
                        this.currentPage = response.data.current_page;
                        this.totalPage = response.data.last_page;
                        console.log(response.data.data)
                        this.errors = [];
                        this.error = false;
                        this.tasks = response.data.data;
                        this.created = true;
                        setTimeout(()=> {
                            this.created = false;
                        },2000);
                        //this.totalPage = response.data.last_page;
                        console.log(response.data);
                    })
                    .catch(error => {
                        this.errors = [];
                        this.created = false;
                        if (error.response.status == 422) {
                            //console.log(error.response.data.errors.name.length);
                            if (containsKey(error.response.data.errors, 'name')) {
                                this.errors.push(error.response.data.errors.name[0])
                            }
                            if (containsKey(error.response.data.errors, 'description')) {
                                this.errors.push(error.response.data.errors.description[0])
                            }
                            if (containsKey(error.response.data.errors, 'date')) {
                                this.errors.push(error.response.data.errors.date[0])
                            }
                        }
                        console.log(error.response.data);
                        this.error = true;
                    });
            }

        },
        deleteTask (id) {
            axios.delete('/task/' + id)
                .then(response => {
                    this.currentPage = response.data.current_page;
                    this.tasks = response.data.data;
                    this.deleted = true;
                    setTimeout(()=> {
                        this.deleted = false;
                    },2000);
                    this.totalPage = response.data.last_page;
                    this.created = false;
                    this.editForm = false;
                    console.log(response.data);
                })
        },
        filter () {
            this.error = false;
            this.errorE = false;
            axios.post('/task/filter', this.filters).then(response => {
                this.currentPage = response.data.current_page;
                this.tasks = response.data.data;
                this.totalPage = response.data.last_page;
                console.log(response.data);
            })
        },
        editTask (id) {
            this.error = false;
            this.errorE = false;
            axios.get('/task/' + id)
                .then(response => {
                    this.edits = response.data;
                    console.log(response.data);
                    this.deleted = false;
                    this.created = false;
                    if(response.data.completed_at == null)
                    {
                        this.edits.checkbox = false;
                    }
                    else
                    {
                        this.edits.checkbox = true;
                    }
                    window.scrollTo(0,document.body.scrollHeight);
                    console.log(response.data)
                })
            this.editForm = true;
        },
        updateTask (id) {
            this.error = false;
            this.errors = [];
            this.created = false;
            this.updates.name = this.edits.name;
            this.updates.description = this.edits.description;
            this.updates.date = this.edits.complete_at;
            this.updates.checkbox = this.edits.checkbox;
            const containsKey = (obj, key ) => Object.keys(obj).includes(key);

            this.createValidator(this.updates.name, this.updates.description, this.updates.date);
            if(this.errors.length > 0)
            {
                this.errorE = true;
            }
            else
            {
                axios.put('/task/' + id, this.updates)
                    .then(response => {
                        this.errorE = false;
                        this.currentPage = response.data.current_page;
                        this.tasks = response.data.data;
                        this.updated = true;
                        setTimeout(()=> {
                            this.updated = false;
                        },2000);
                        this.totalPage = response.data.last_page;
                        console.log(response.data);
                        this.editForm = false;
                    })
                    .catch(error => {
                        this.errors = [];
                        this.updated = false;
                        this.error = false;
                        console.log(error.response.status);
                        if (error.response.status == 422) {
                            //console.log(error.response.data.errors.name.length);
                            if (containsKey(error.response.data.errors, 'name')) {
                                this.errors.push(error.response.data.errors.name[0])
                            }
                            if (containsKey(error.response.data.errors, 'description')) {
                                this.errors.push(error.response.data.errors.description[0])
                            }
                            if (containsKey(error.response.data.errors, 'date')) {
                                this.errors.push(error.response.data.errors.date[0])
                            }
                        }
                        console.log(error.response.data);
                        this.errorE = true;
                    });
            }

        },
        resetFilters() {
            this.filters = {};
            this.deleted = false;
            this.errorE = false;
            this.error = false;
            axios.get('/task').then(response => {
                this.currentPage = response.data.current_page;
                this.tasks = response.data.data;
                console.log(response.data);
                this.totalPage = response.data.last_page;
                window.scrollTo(0,document.body.scrollHeight);
            });
        },
        paginate(page){
            console.log(page);
            axios.get('/task?page=' + page.x).then(response => {
                this.tasks = response.data.data;
                this.currentPage = response.data.current_page;
                console.log(response.data);
            });
        },
        createValidator(name, description, date) {
            if(!name) {
                this.errors.push("Name required");
            }
            else
            {
                if(name.length < 3) {
                    this.errors.push("Name must be at least 3 characters");
                }
            }
            if(!description) {
                this.errors.push("Description required");
            }
            else
            {
                if(description.length < 3) {
                    this.errors.push("Description must be at least 3 characters");
                }
            }
            if(!date) {
                this.errors.push("Deadline date required");
            }
            else
            {
                var dateFormat = moment(date, 'YYYY-MM-DD', true);
                if(!dateFormat.isValid()) {
                    this.errors.push("Deadline date format is not good");
                }
                else
                {
                    let check = moment(date);
                    if(!check.isSameOrAfter(moment(),"day")) {
                        this.errors.push("Deadline date must be after or equal to now");
                    }
                }
            }

        },
        moment: function (date) {
            if(date == null)
            {
                return moment();
            }
            return moment(date);
        }
    }
}
</script>

<style scoped>

</style>
