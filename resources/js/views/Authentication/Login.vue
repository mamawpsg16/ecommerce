<template>
        <form @submit.prevent="login" class="mt-5">
            <div class="d-flex flex-column align-items-center">
                <div class="col-3">
                    <label for="exampleInputEmail1" class="form-label">Email</label>
                    <input required type="email" v-model="email" @click="clearFormErrors" :class="{ inputInvalidClass : checkInputValidity(undefined,'email',['required','email']) }" class="form-control"  autocomplete="email" >
                    <div  v-if="v$.email.$dirty" :class="{ 'text-danger': checkInputValidity(undefined,'email',['required','email']) }">
                        <p v-if="v$.email.required.$invalid">
                            Email is required
                        </p>
                        <p v-if="v$.email.email.$invalid">
                            Email must be a valid email
                        </p>
                    </div>
                </div>
                <div class="col-3">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input required type="password" v-model="password" @click="clearFormErrors" :class="{ inputInvalidClass : checkInputValidity(undefined,'password',['required', 'minLength']) }" class="form-control"  autocomplete="current-password" >
                    <div  v-if="v$.password.$dirty" :class="{ 'text-danger': checkInputValidity(undefined,'password',['required', 'minLength']) }">
                        <p v-if="v$.password.required.$invalid">
                            Password is required
                        </p>
                        <p v-if="v$.password.minLength.$invalid">
                            Password must have a min length of 8 character
                        </p>
                    </div>
                    <p :class="{ 'text-danger': (this.form_error)}" >{{ form_error }}</p>
                </div>
            </div>
            <div class="d-flex flex-column align-items-center">
                <div class="col-3 text">
                    <!-- <p>Dont have account yet,  <router-link :to="{name:'register'}"> Sign up now!</router-link></p> -->
                    <button type="submit" class="btn btn-primary" :disabled="isLoggingIn">{{ isLoggingIn ? 'Logging In' : 'Login' }}</button>
                </div>
            </div>
        </form>
</template>

<script>

import { swalSuccess, swalError  } from '@/helpers/Notification/sweetAlert.js';
import { useVuelidate } from '@vuelidate/core'
import { required, email, minLength  } from '@vuelidate/validators'
import { checkValidity, checkLoopValidity, checkLoopErrors } from '@/helpers/Vuelidate/InputValidation.js';
import axios from 'axios';
    export default {
        setup () {
            return { v$: useVuelidate({ $autoDirty: true }) }
        },
        name:'Login',
        data(){
            return{
                email: null,
                password: null,
                password_confirmation: null,
                form_error:null,
                isLoggingIn:false
            }
        },
        validations () {
            return {
                email: { required, email }, 
                password: { required, minLength: minLength(8) },
            }
        },
        components: {
        },
        created(){
        },
        computed:{
          
        },
        methods:{
            clearFormErrors(){
                this.form_error = null;
            },
            async login(){
                if(!await this.v$.$validate()){
                    return;
                }
                this.isLoggingIn = true;
                await axios.get("/sanctum/csrf-cookie");
                axios.post('/login',{email:this.email, password: this.password})
                .then((response) => {
                    console.log(response,'RESPONSE');
                    const { status, message, authentication_token } = response.data;
                    if(status == 200){
                        swalSuccess({ 
                            icon: 'success',
                            text: 'Redirecting to dashboard.',
                            title: 'Logged in successfully!',
                            showConfirmButton: false,
                        })

                        localStorage.setItem('auth-token', authentication_token);
                        
                        window.location.href = '/'
                    }else if(status == 404){
                        this.isLoggingIn = false;
                        this.v$.password.$dirty = true;
                        this.form_error = message;
                    }
                })
                .catch(function (error) {
                    console.log(error,'ERRORS');
                    if(error.response?.data?.errors){
                        swalError({
                            icon: 'error',
                            title: error.response?.data.errors,
                            text: "Something went wrong!",
                            showConfirmButton: false,
                        })
                    }
                });
            },

            checkInputValidity(parentProperty = null, dataProperty, validations = []) {
               return checkValidity(this.v$, parentProperty, dataProperty, validations);
            },
        },
    }
</script>

<style scoped>

.inputInvalidClass {
  border: 1px solid red; /* Adjust the style as needed */
  border-radius: 5px;
}
</style>