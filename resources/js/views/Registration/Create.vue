<template>
    <Modal class="modal-lg" targetModal="participant-registration-modal" modaltitle="Event Registration" :backdrop="true" :escKey="false">
        <template #body>
            <form-wizard @on-complete="registerConfirmation" finishButtonText="Register" ref="formWizard" subtitle="Event Registration" :validateOnBack="true" color="#3176FF">
                <tab-content title="Personal Information" icon="fa-solid fa-user" :beforeChange="validateParticipantDetails">
                    <div class="row justify-content-end mb-3">
                        <div class="col-md-6 col-lg-4">
                            <label class="mb-1">Event <span class="text-danger">*</span></label>
                            <VueMultiselect :loading="loadingEvents" :disabled="loadingEvents" :class="{ inputInvalidClass : checkInputValidity('participant','event',['required'])}" v-model="participant.event" track-by="label" label="label" placeholder="Event" :options="events"></VueMultiselect>
                            <div  v-if="v$.participant.event.$dirty" :class="{ 'text-danger': checkInputValidity('participant','event',['required'])}">
                                <p v-if="v$.participant.event.required.$invalid">
                                    Event is required.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <label class="mb-1">Name <span class="text-danger">*</span></label>
                            <Input type="text" placeholder="First Name" v-model="participant.name"  :class="{ inputInvalidClass : checkInputValidity('participant','name',['required'])}" required   autocomplete="name" />
                            <div  v-if="v$.participant.name.$dirty" :class="{ 'text-danger': checkInputValidity('participant','name',['required'])}">
                                <p v-if="v$.participant.name.required.$invalid">
                                    Name is required.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label class="mb-1">Date of Birth <span class="text-danger">*</span></label>
                            <VueDatePicker :class="{ inputInvalidClass : checkInputValidity('participant','birth_date',['required'])}"  v-model="participant.birth_date" placeholder="Date of Birth" format="MM-dd-yyyy" required></VueDatePicker>
                            <div  v-if="v$.participant.birth_date.$dirty" :class="{ 'text-danger':  checkInputValidity('participant','birth_date',['required']) }">
                                <p v-if="v$.participant.birth_date.required.$invalid">
                                    Date of Birth is required.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4  mb-3">
                            <label class="mb-1">Email Address <span class="text-danger">*</span></label>
                            <Input type="email" placeholder="Email Address" v-model="participant.email"  :class="{ inputInvalidClass : checkInputValidity('participant','email',['required','email']) }" required/>
                            <div v-if="v$.participant.email.$dirty" :class="{ 'text-danger': checkInputValidity('participant','email',['required','email']) }">
                                <p v-if="v$.participant.email.required.$invalid">
                                    Email Address is required.
                                </p>
                                <p v-if="v$.participant.email.email.$invalid">
                                    Email Address is invalid.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4  mb-3">
                            <label class="mb-1">Phone # <span class="text-danger">*</span></label>
                            <Input type="number"  placeholder="Phone Number"   v-model="participant.personal_phone_number" :class="{ inputInvalidClass : checkInputValidity('participant','personal_phone_number',['required', 'minLength', 'maxLength'])}"  required autocomplete="name" />
                            <div  v-if="v$.participant.personal_phone_number.$dirty" :class="{ 'text-danger':  checkInputValidity('participant','personal_phone_number',['required', 'minLength', 'maxLength'])}">
                                <p v-if="v$.participant.personal_phone_number.required.$invalid">
                                    Phone number is required.
                                </p>
                                <p v-if="v$.participant.personal_phone_number.minLength.$invalid">
                                    Phone number must be at least 11 characters.
                                </p>
                                <p v-if="v$.participant.personal_phone_number.maxLength.$invalid">
                                    Phone number must be no more than 13 characters.
                                </p>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="mb-1">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" v-model="participant.address" :class="{ inputInvalidClass : checkInputValidity('participant','address',['required'])}" placeholder="Address " rows="2" required></textarea>
                            <div  v-if="v$.participant.address.$dirty" :class="{ 'text-danger':  checkInputValidity('participant','address',['required'])}">
                                <p v-if="v$.participant.address.required.$invalid">
                                    Address is required.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                </tab-content>
                <tab-content title="Company Information" icon="fa-solid fa-building" :beforeChange="validateCompanyDetails">
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <label class="mb-1">Industry <span class="text-danger">*</span></label>
                            <Input type="text" placeholder="Industry" v-model="company_details.industry"  :class="{ inputInvalidClass : checkInputValidity('company_details','industry',['required'])}" required   autocomplete="name" />
                            <div  v-if="v$.company_details.industry.$dirty" :class="{ 'text-danger': checkInputValidity('company_details','industry',['required'])}">
                                <p v-if="v$.company_details.industry.required.$invalid">
                                    Industry is required.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label class="mb-1">Company <span class="text-danger">*</span></label>
                            <Input type="text" placeholder="Company" v-model="company_details.company" />
                            <div  v-if="v$.company_details.company.$dirty" :class="{ 'text-danger': checkInputValidity('company_details','company',['required'])}">
                                <p v-if="v$.company_details.company.required.$invalid">
                                    Company is required.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="mb-1">Position <span class="text-danger">*</span></label>
                            <Input type="text" placeholder="Position" v-model="company_details.position"  :class="{ inputInvalidClass : checkInputValidity('company_details','position',['required'])}" autocomplete="position" required/>
                            <div  v-if="v$.company_details.position.$dirty" :class="{ 'text-danger': checkInputValidity('company_details','position',['required'])}">
                                <p v-if="v$.company_details.position.required.$invalid">
                                    Last Name is required.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="mb-1">Company Phone # <span class="text-danger">*</span></label>
                            <Input type="number"  placeholder="Phone Number"   v-model="company_details.company_phone_number" :class="{ inputInvalidClass : checkInputValidity('company_details','company_phone_number',['minLength', 'maxLength'])}"  required autocomplete="name" />
                            <div  v-if="v$.company_details.company_phone_number.$dirty" :class="{ 'text-danger':  checkInputValidity('company_details','company_phone_number',[ 'minLength', 'maxLength'])}">
                                <p v-if="v$.company_details.company_phone_number.minLength.$invalid">
                                    Phone number must be at least 11 characters.
                                </p>
                                <p v-if="v$.company_details.company_phone_number.maxLength.$invalid">
                                    Phone number must be no more than 13 characters.
                                </p>
                            </div>
                        </div>


                        <div class="col-12">
                            <label class="mb-1">Company Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" v-model="company_details.company_address" :class="{ inputInvalidClass : checkInputValidity('company_details','company_address',['required'])}" placeholder="Company company_address " rows="2" required></textarea>
                            <div  v-if="v$.company_details.company_address.$dirty" :class="{ 'text-danger':  checkInputValidity('company_details','company_address',['required'])}">
                                <p v-if="v$.company_details.company_address.required.$invalid">
                                    Company Address is required.
                                </p>
                            </div>
                        </div>
                    </div>
                </tab-content>
                <!-- <template v-slot:next="props">
                    <button type="button"  v-if="props.activeTabIndex  == 0" class="btn btn-md btn-primary me-1 px-5" :disabled="firstStepDisable">Next</button>
                </template> -->
            </form-wizard>
        </template>
    </Modal>
</template>

<script>
import Modal from '@/components/Modal/modal.vue';
import Input from '@/components/Form/Input.vue'
import {FormWizard, TabContent} from 'vue3-form-wizard'
import VueDatePicker from '@vuepic/vue-datepicker';
import { useVuelidate } from '@vuelidate/core'
import { required, email, maxLength, minLength } from '@vuelidate/validators';
import { swalConfirmation, swalSuccess, swalError, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
import { checkValidity } from '@/helpers/Vuelidate/InputValidation.js';
import VueMultiselect from 'vue-multiselect'
import debounce from 'lodash/debounce'; 
    export default {
        name:'participant Registration',
        setup () {
            return { v$: useVuelidate({ $autoDirty: true , participant: {} }) }
        },
        emits: ['loadUpdatedParticipants'],
        data(){
            return{
                participant:{
                    name:null,
                    address:null,
                    email:null,
                    personal_phone_number:null,
                    birth_date:null,
                    event:null,
                },
                isSaving:false,
                events:[],
                loadingEvents:false,
                company_details:{
                    industry:null,
                    company:null,
                    position:null,
                    company_phone_number:null,
                    company_address:null,
                },
                auth_token:`Bearer ${localStorage.getItem('auth-token')}`,
            }
        },
        validations () {
            return {
                participant: {
                    name: { required },
                    email: { required, email },
                    personal_phone_number: { required, maxLength: maxLength(13), minLength: minLength(11) },
                    birth_date: { required },
                    event: { required },
                    address: { required },
                },
                company_details: {
                    industry: { required },
                    company: { required },
                    position: { required },
                    company_phone_number: {maxLength: maxLength(13), minLength: minLength(11) },
                    company_address: { required },
                },
            }
        },
        components:{
            Modal,
            FormWizard,
            TabContent,
            Input,
            VueDatePicker,
            VueMultiselect
        },
        computed:{
           
        },
        async created(){
            await this.getEvents()
        },
        methods:{
            async getEvents(){
                this.loadingEvents = true;
                await axios.get('/api/raffle/get-events', { 
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                .then((response) => {
                    const { events } = response.data;
                    this.events = events;
                    this.loadingEvents = false;
                })
                .catch((error) =>{
                    console.log(error,'error');
                });
            },

            checkInputValidity(parentProperty = null, dataProperty, validations = []) {
               return checkValidity(this.v$, parentProperty, dataProperty, validations);
            },
            

            validateParticipantDetails(){
                this.v$.participant.$touch()
                const isValid =  this.v$.participant.$errors.length ? false : true ;
                return isValid;
            },

            validateCompanyDetails(){
                this.v$.company_details.$touch()
                const isValid =  this.v$.company_details.$errors.length ? false : true ;
                return isValid;
            },

            formReset(){
                this.participant = {
                    name:null,
                    email:null,
                    personal_phone_number:null,
                    birth_date:null,
                    gender:null,
                    event:null,
                },
             
                this.company_details = {
                    industry:null,
                    company:null,
                    position:null,
                    company_phone_number:null,
                    company_address:null
                };

                this.$refs.formWizard.reset();
                this.v$.$reset();
            },

            async register(){
                if(!await this.v$.$validate()){
                    return;
                }

                this.isSaving = true;

                const birthDate = new Date(this.participant.birth_date);
                const today = new Date();

                // Calculate the difference in years
                const age = today.getFullYear() - birthDate.getFullYear();

                // Check if the birthday has occurred this year
                const hasBirthdayOccurred = (today.getMonth() > birthDate.getMonth()) || 
                                            (today.getMonth() === birthDate.getMonth() && today.getDate() >= birthDate.getDate());

                // If birthday has not occurred yet this year, subtract 1 from age
                const finalAge = hasBirthdayOccurred ? age : age - 1;

                 // Ensure the month and day are formatted with leading zeros if needed
                 const formattedMonth = String(birthDate.getMonth() + 1).padStart(2, '0');
                const formattedDay = String(birthDate.getDate()).padStart(2, '0');
                // Format the date as 'YYYY-MM-DD'
                const formattedBirthDate = `${birthDate.getFullYear()}-${formattedMonth}-${formattedDay}`;


                const data = {
                    ...this.participant,
                    ...this.company_details,
                    age:finalAge,
                    birth_date: formattedBirthDate, // Assign the formatted date string
                };
                
                console.log(data,'data');
                axios.post('/api/raffle/participants',data,{
                    headers:{
                        Authorization: this.auth_token,
                    }
                })
                .then((response) => {
                    const { message } = response.data;
                    this.isSaving = false;
                    this.formReset();

                    this.$emit('loadUpdatedParticipants');

                    SwalDefault.fire({
                        icon: "success",
                        text: message,
                        showConfirmButton: false,
                    });
                })
                .catch((error) => {
                      this.isSaving = false;
                    console.log(error);
                });
            },

            registerConfirmation(){
                swalConfirmation().then((result) => {
                    if (result.isConfirmed) {
                       this.register()
                    }
                });
            },
        },

        mounted(){
            console.log(this.$refs.formWizard,'form wizard');
        },

        watch: {
            isSaving(newValue, oldValue) {
                if (newValue) {
                    SwalDefault.fire({
                        title: '<i class="fa fa-cog fa-spin"></i>&nbsp;Saving...',
                        text: "Saving changes, kindly wait.",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    });
                }
            }
        },
    }
</script>

<style scoped>
.inputInvalidClass {
  border: 1px solid red; /* Adjust the style as needed */
  border-radius: 5px;
}

</style>