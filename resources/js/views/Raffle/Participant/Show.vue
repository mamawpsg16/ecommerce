<template>
    <Modal class="modal-lg" targetModal="participant-details-modal" :modaltitle="participant.name" :backdrop="true" :escKey="false">
        <template #body>
            <form-wizard finishButtonText="Update" ref="formWizard" subtitle="Participant Details" :validateOnBack="true" color="#3176FF">
                <tab-content title="Personal Information" icon="fa-solid fa-user" :beforeChange="validateParticipantDetails">
                    <div class="row justify-content-center mb-3">
                        <div class="col-md-6">
                            <label class="mb-1">Event <span class="text-danger" v-if="edit">*</span></label>
                            <VueMultiselect v-if="edit" :loading="loadingEvents" :disabled="loadingEvents || !edit" :class="{ inputInvalidClass : checkInputValidity('participant','event',['required'])}" v-model="participant.event" track-by="label" label="label" placeholder="Event" :options="events"></VueMultiselect>
                            <Input v-else type="text" :disabled="!edit" :value="participant.event_name"/>
                            <div  v-if="v$.participant.event.$dirty" :class="{ 'text-danger': checkInputValidity('participant','event',['required'])}">
                                <p v-if="v$.participant.event.required.$invalid">
                                    Event is required.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row  mb-2">
                        <div class="col-md-6">
                            <label class="mb-1">Name <span class="text-danger" v-if="edit">*</span></label>
                            <Input type="text" :disabled="!edit" placeholder="First Name" v-model="participant.name"  :class="{ inputInvalidClass : checkInputValidity('participant','name',['required'])}" required   autocomplete="name" />
                            <div  v-if="v$.participant.name.$dirty" :class="{ 'text-danger': checkInputValidity('participant','name',['required'])}">
                                <p v-if="v$.participant.name.required.$invalid">
                                    Name is required.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="mb-1">Email Address <span class="text-danger" v-if="edit">*</span></label>
                            <Input type="email" :disabled="!edit" placeholder="Email Address" v-model="participant.email"  :class="{ inputInvalidClass : checkInputValidity('participant','email',['required','email']) }" required/>
                            <div v-if="v$.participant.email.$dirty" :class="{ 'text-danger': checkInputValidity('participant','email',['required','email']) }">
                                <p v-if="v$.participant.email.required.$invalid">
                                    Email Address is required.
                                </p>
                                <p v-if="v$.participant.email.email.$invalid">
                                    Email Address is invalid.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row  mb-2">
                        <div class="col-md-6">
                            <label class="mb-1">Date of Birth <span class="text-danger" v-if="edit">*</span></label>
                            <VueDatePicker v-if="edit"  :disabled="!edit" :class="{ inputInvalidClass : checkInputValidity('participant','birth_date',['required'])}"  v-model="participant.birth_date" placeholder="Date of Birth" format="MM-dd-yyyy" required></VueDatePicker>
                            <Input v-else type="text" :disabled="!edit" :value="participant.birthday"/>
                            <div  v-if="v$.participant.birth_date.$dirty" :class="{ 'text-danger':  checkInputValidity('participant','birth_date',['required']) }">
                                <p v-if="v$.participant.birth_date.required.$invalid">
                                    Date of Birth is required.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="mb-1">Phone # <span class="text-danger" v-if="edit">*</span></label>
                            <Input type="number" :disabled="!edit"  placeholder="Phone Number"   v-model="participant.personal_phone_number" :class="{ inputInvalidClass : checkInputValidity('participant','personal_phone_number',['required', 'minLength', 'maxLength'])}"  required autocomplete="name" />
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
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <label class="mb-1">Address <span class="text-danger" v-if="edit">*</span></label>
                            <textarea class="form-control" :disabled="!edit" v-model="participant.address" :class="{ inputInvalidClass : checkInputValidity('participant','address',['required'])}" placeholder="Address " rows="2" required></textarea>
                            <div  v-if="v$.participant.address.$dirty" :class="{ 'text-danger':  checkInputValidity('participant','address',['required'])}">
                                <p v-if="v$.participant.address.required.$invalid">
                                    Address is required.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                </tab-content>
                <tab-content title="Company Information" icon="fa-solid fa-building" :beforeChange="validateCompanyDetails">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="mb-1">Industry <span class="text-danger" v-if="edit">*</span></label>
                            <Input type="text" :disabled="!edit" placeholder="Industry" v-model="company_details.industry"  :class="{ inputInvalidClass : checkInputValidity('company_details','industry',['required'])}" required   autocomplete="name" />
                            <div  v-if="v$.company_details.industry.$dirty" :class="{ 'text-danger': checkInputValidity('company_details','industry',['required'])}">
                                <p v-if="v$.company_details.industry.required.$invalid">
                                    Industry is required.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="mb-1">Company <span class="text-danger" v-if="edit">*</span></label>
                            <Input type="text" :disabled="!edit" placeholder="Company" v-model="company_details.company" />
                            <div  v-if="v$.company_details.company.$dirty" :class="{ 'text-danger': checkInputValidity('company_details','company',['required'])}">
                                <p v-if="v$.company_details.company.required.$invalid">
                                    Company is required.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="mb-1">Position <span class="text-danger" v-if="edit">*</span></label>
                            <Input type="text" :disabled="!edit" placeholder="Position" v-model="company_details.position"  :class="{ inputInvalidClass : checkInputValidity('company_details','position',['required'])}" autocomplete="position" required/>
                            <div  v-if="v$.company_details.position.$dirty" :class="{ 'text-danger': checkInputValidity('company_details','position',['required'])}">
                                <p v-if="v$.company_details.position.required.$invalid">
                                    Last Name is required.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="mb-1">Company Phone #</label>
                            <Input type="number" :disabled="!edit"  placeholder="Phone Number"   v-model="company_details.company_phone_number" :class="{ inputInvalidClass : checkInputValidity('company_details','company_phone_number',['minLength', 'maxLength'])}"  required autocomplete="name" />
                            <div  v-if="v$.company_details.company_phone_number.$dirty" :class="{ 'text-danger':  checkInputValidity('company_details','company_phone_number',[ 'minLength', 'maxLength'])}">
                                <p v-if="v$.company_details.company_phone_number.minLength.$invalid">
                                    Phone number must be at least 11 characters.
                                </p>
                                <p v-if="v$.company_details.company_phone_number.maxLength.$invalid">
                                    Phone number must be no more than 13 characters.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label class="mb-1">Company Address <span class="text-danger" v-if="edit">*</span></label>
                            <textarea class="form-control" :disabled="!edit" v-model="company_details.company_address" :class="{ inputInvalidClass : checkInputValidity('company_details','company_address',['required'])}" placeholder="Company Address" rows="2" required></textarea>
                            <div  v-if="v$.company_details.company_address.$dirty" :class="{ 'text-danger':  checkInputValidity('company_details','company_address',['required'])}">
                                <p v-if="v$.company_details.company_address.required.$invalid">
                                    Company Address is required.
                                </p>
                            </div>
                        </div>
                    </div>
                </tab-content>
                <template v-slot:next="props">
                    <button type="button"  v-if="props.activeTabIndex  == 0 && edit" class="btn btn-md btn-primary me-1 px-5">Next</button>
                </template>
                <template v-slot:custom-buttons-right="props">
                    <!-- <button type="button"  v-if="!edit" class="btn btn-md btn-primary me-1 px-3" @click="editDetails"><i class="fa-solid fa-pencil"></i></button> -->
                    <!-- <button type="button"  v-else class="btn btn-md btn-danger me-1 px-3" @click="cancelEdit">Cancel</button> -->
                    <span v-if="props.isLastStep"></span>
                </template>
                <template v-slot:finish="props">
                    <span v-if="props.isLastStep && !edit"></span>
                </template>
            </form-wizard>
        </template>
    </Modal>
</template>

<script>
import Rouelette from './Components/WheelOfFortune.vue';
import Modal from '@/components/Modal/modal.vue';
import Input from '@/components/Form/Input.vue'
import {FormWizard, TabContent} from 'vue3-form-wizard'
import VueDatePicker from '@vuepic/vue-datepicker';
import { useVuelidate } from '@vuelidate/core'
import { required, email, maxLength, minLength } from '@vuelidate/validators';
import { swalConfirmation, swalSuccess, swalError, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
import { checkValidity } from '@/helpers/Vuelidate/InputValidation.js';
import { deepClone } from '@/helpers/PartialHelpers/index.js';
import VueMultiselect from 'vue-multiselect'
    export default {
        name:'Participant Details',
        setup () {
            return { v$: useVuelidate({ $autoDirty: true , participant: {} }) }
        },
        props:['participant_details'],
        emits:['loadUpdatedparticipants'],
        data(){
            return{
                details:[],
                prevDetails:{},
                edit:false,
                isUpdating:false,
                items:[],
                participant_id:null,
                event_id:null,
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
            VueMultiselect,
            Rouelette
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

            editDetails(){
                this.edit = true;
            },

            cancelEdit(){
                this.edit = false;
                this.resetForm();
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
            

            resetForm(){
                this.prevDetails = deepClone(this.participant_details);
                this.participant = this.participant_details;
                this.company_details = this.participant_details.company_details;
                this.$refs.formWizard.reset();
                this.$refs.formWizard.activateAll();
                this.v$.$reset();
            },

            formatData(student){
                const middle_name = student.middle_name ? `${ titleCase(student.middle_name)}.` : '';
                const birth_date = formatDate(undefined, student.date_of_birth, 'date');
                
                const student_details = {
                        ...student,
                        name: `${ titleCase(student.first_name)} ${middle_name} ${ titleCase(student.last_name) }`,
                        health_information:{
                            ...student.health_information,
                            last_health_checkup: formatDate(undefined, student.health_information.last_health_checkup, 'date'),
                        },
                        basic_information:{
                            first_name:  student.first_name,
                            middle_name: student.middle_name,
                            last_name: student.last_name,
                            email: student.email,
                            phone_number_1:student.phone_number_1,
                            phone_number_2:student.phone_number_2,
                            date_of_birth:student.date_of_birth,
                            date_of_birth_name: birth_date,
                            gender_name: student.gender.name,
                            id: student.id,
                            school_year_name: student.enrollments[0].school_year.name,
                            school_year: {
                                label:student.enrollments[0].school_year.name,
                                value:student.enrollments[0].school_year.id
                            },
                            gender: {
                                label:student.gender.name,
                                value:student.gender.id,
                            },
                        }
                }; 

                return student_details;
            },

            async update(){
                if(!await this.v$.$validate()){
                    return;
                }

                this.isUpdating = true;

                const formData = new FormData();

                if(this.file){
                    formData.append('image',this.file);
                }
                const date_of_birth = new Date(this.student.date_of_birth);

                // Ensure the month and day are formatted with leading zeros if needed
                const formattedMonth = String(date_of_birth.getMonth() + 1).padStart(2, '0');
                const formattedDay = String(date_of_birth.getDate()).padStart(2, '0');
                // Format the date as 'YYYY-MM-DD'
                const formattedDate = `${date_of_birth.getFullYear()}-${formattedMonth}-${formattedDay}`;

                const student = {
                    ...this.student,
                    gender_id: this.student.gender.value,
                    school_year_id: this.student.school_year.value,
                    date_of_birth: formattedDate, // Assign the formatted date string
                };

                console.log(student,'student');


                delete student.gender;
                delete student.school_year;

                const guardians = this.guardians.map(guardian =>{
                    const { guardian_type, ...rest } = guardian; // Destructure guardian_type and capture the rest of the properties
                    return {
                        ...rest, // Spread the rest of the properties
                        guardian_type_id: guardian_type.value // Add the new property
                    };
                });
                
                formData.append('student_information',JSON.stringify(student));

                formData.append('guardians', JSON.stringify(guardians));

                formData.append('address_information', JSON.stringify(this.address_information));
                formData.append('health_information',JSON.stringify(this.health_information));
               
                axios.post(`/api/update-student`,formData,{
                    headers:{
                        'Content-Type': 'multipart/form-data',
                        Authorization: this.auth_token,
                    }
                })
                .then((response) => {
                const { student } = response.data;
                
                    this.isUpdating = false;
                  
                    this.edit = false;
                    const formattedData = this.formatData(student);
                    this.prevDetails = deepClone(formattedData);
                    this.$emit('loadUpdatedStudents');
                    SwalDefault.fire({
                        icon: "success",
                        text: "Succesfully registered!",
                        showConfirmButton: false,
                        timer: 1500
                    });
                    this.resetForm();
                    
                  
                })
                .catch((error) => {
                      this.isUpdating = false;
                    console.log(error);
                });
            },


            updateConfirmation(){
                swalConfirmation().then((result) => {
                    if (result.isConfirmed) {
                       this.update()
                    }
                });
            },
        },

        mounted(){
        },

        watch:{
            participant_details(){
                this.resetForm();
                this.prevDetails = deepClone(this.participant_details);
                this.participant = this.participant_details;
                this.company_details = this.participant_details.company_details;
            },

            // isUpdating(newValue, oldValue) {
            //     if (newValue) {
            //         SwalDefault.fire({
            //             title: '<i class="fa fa-cog fa-spin"></i>&nbsp;Updating...',
            //             text: "Saving changes, kindly wait.",
            //             showConfirmButton: false,
            //             allowOutsideClick: false,
            //             allowEscapeKey: false,
            //         });
            //     }
            // }
        }
    }
</script>

<style scoped>
.inputInvalidClass {
  border: 1px solid red; /* Adjust the style as needed */
  border-radius: 5px;
}

</style>