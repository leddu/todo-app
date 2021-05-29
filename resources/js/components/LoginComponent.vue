<template>

    <div v-if="loginForm" class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div  class="card-header"><p class="h4 text-center mb-4">Login</p></div>

                    <div class="card-body">
                        <form @submit.prevent="login">
                            <label for="defaultFormLoginEmailEx" class="grey-text">Your email</label>
                            <input type="email" id="defaultFormLoginEmailEx" v-model="logins.email" class="form-control"/>
                            <br/>
                            <label for="defaultFormLoginPasswordEx" class="grey-text">Your password</label>
                            <input type="password" id="defaultFormLoginPasswordEx" v-model="logins.password" class="form-control"/>
                            <div class="text-center mt-4">
                                <button class="btn btn-submit btn-dark" type="submit">Login</button>
                            </div>
                            <br>
                            <p class="h6 text-center mb-4">Don't have account? Please register.</p>
                        </form>
                        <div class="alert alert-danger" v-if="error">
                            {{errors[0]}}
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div  class="card-header"><p class="h4 text-center mb-4">Register</p></div>

                    <div class="card-body">
                        <div class="alert alert-danger" v-if="regError">
                            <li v-for="item in regErrors">
                                {{ item }}
                            </li>
                        </div>
                        <form @submit.prevent="register">
                            <label class="grey-text">Your email</label>
                            <input type="email" v-model="registers.email" class="form-control"/>
                            <br/>
                            <label class="grey-text">Your password</label>
                            <input type="password"  v-model="registers.password" class="form-control"/>
                            <label class="grey-text">Repeat your password</label>
                            <input type="password" v-model="registers.password_confirmation" class="form-control"/>
                            <div class="text-center mt-4">
                                <button class="btn btn-submit btn-dark" type="submit">Register</button>
                            </div>
                        </form>
                        <br>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                logins: {},
                errors: [],
                error: false,
                loginForm: true,
                id: "",
                registers: {},
                regErrors: [],
                regError: false
            }
        },
        methods: {
            login () {
                this.regError = false;
                axios.post('/login', this.logins)
                    .then( response => {
                        this.id = response.data.id;
                        localStorage.id = this.id;
                        console.log(response.data);
                        this.loginForm = false;
                        window.location.reload();
                    })
                    .catch(error => {
                        this.error = true;
                        this.errors.push(error.response.data.message);
                    });
            },
            register () {
                this.error = false;
                this.regErrors = [];
                this.validate(this.registers.email,this.registers.password,this.registers.password_confirmation);
                if(this.regErrors.length > 0)
                {
                    this.regError = true;
                }
                else
                {
                    axios.post('/register', this.registers)
                        .then( response => {
                            this.regError = false;
                            this.id = response.data.id;
                            console.log(response.data);
                            window.location.reload();
                        })
                        .catch(error => {
                            const containsKey = (obj, key ) => Object.keys(obj).includes(key);
                            this.regErrors = [];
                            if (error.response.status == 422) {
                                //console.log(error.response.data.errors.name.length);
                                if (containsKey(error.response.data.errors, 'email')) {
                                    this.regErrors.push(error.response.data.errors.email[0])
                                }
                                if (containsKey(error.response.data.errors, 'password')) {
                                    this.regErrors.push(error.response.data.errors.password[0])
                                }
                                if (containsKey(error.response.data.errors, 'password_confirmation')) {
                                    this.regErrors.push(error.response.data.errors.password_confirmation[0])
                                }
                            }
                            this.regError = true;
                        });
                }

            },
            validate(email,password,passwordConfirmed) {
                if (!email) {
                    this.regErrors.push("Email required");
                }
                if (!password) {
                    this.regErrors.push("Password required");
                    return;

                }
                else
                {
                    if (password.length < 5) {
                        this.regErrors.push("Password must be at least 3 characters")
                    }
                }
                if (!passwordConfirmed)
                {
                    this.regErrors.push("The password confirmation does not match.");

                }
                else
                {
                    if(password != passwordConfirmed)
                    {
                        this.regErrors.push("The password confirmation does not match.");
                    }
                }
            }
        }
}
</script>

<style scoped>

</style>
