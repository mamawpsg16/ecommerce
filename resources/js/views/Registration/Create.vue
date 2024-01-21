<template>
    <Modal class="modal-xl" targetModal="student-registration-modal" modaltitle="Registration" :backdrop="true" :escKey="false">
        <template #body>
            <form-wizard @on-complete="registerConfirmation" finishButtonText="Register" ref="formWizard" subtitle="Student Registration" :validateOnBack="true" color="#3176FF">
                <tab-content title="Student Information" icon="fa-solid fa-user" :beforeChange="validateStudentDetails">
                    <div class="row mb-3">
                        <div class="d-flex flex-column align-items-center text-end">
                            <img :src="image ?? defaultProfileImage" class="img-fluid mb-4 rounded-circle" style="height: 250px; width: 250px; border: 2px solid #ccc;" alt="Default Profile Image">
                            <div class="d-flex justify-content-center align-items-center">
                                <input class="form-control object-fit-cover " type="file" id="formFile" style="width: 250px;" @change="uploadImage" accept="image/*">
                                <button v-if="image" type="button" class="ms-2 btn btn-sm btn-danger" @click="removeImage"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end mb-3">
                        <div class="col-3">
                            <label>School Year <span class="text-danger">*</span></label>
                            <VueMultiselect :loading="loadingSchoolYears" :disabled="loadingSchoolYears" :class="{ inputInvalidClass : checkInputValidity('student','school_year',['required']) }" v-model="student.school_year" track-by="label" label="label" placeholder="Select S.Y" :allow-empty="false" :options="school_years"></VueMultiselect>
                            <div  v-if="v$.student.school_year.$dirty" :class="{ 'text-danger': checkInputValidity('student','school_year',['required']) }">
                                <p v-if="v$.student.school_year.required.$invalid">
                                    School Year is required.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label>First Name <span class="text-danger">*</span></label>
                            <Input type="text" v-model="student.first_name" :class="{ inputInvalidClass : checkInputValidity('student','first_name',['required']) }" required   autocomplete="name" />
                            <div  v-if="v$.student.first_name.$dirty" :class="{ 'text-danger': checkInputValidity('student','first_name',['required']) }">
                                <p v-if="v$.student.first_name.required.$invalid">
                                    First Name is required.
                                </p>
                            </div>
                        </div>

                        <div class="col-4">
                            <label>Middle Name</label>
                            <Input type="text" v-model="student.middle_name"/>
                        </div>

                        <div class="col-4">
                            <label>Last Name <span class="text-danger">*</span></label>
                            <Input type="text" v-model="student.last_name" :class="{ inputInvalidClass : checkInputValidity('student','last_name',['required']) }" autocomplete="last_name" required/>
                            <div  v-if="v$.student.last_name.$dirty" :class="{ 'text-danger': checkInputValidity('student','last_name',['required']) }">
                                <p v-if="v$.student.last_name.required.$invalid">
                                    Last Name is required.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4">
                            <label>Email Address <span class="text-danger">*</span></label>
                            <Input type="email" v-model="student.email" :class="{ inputInvalidClass : checkInputValidity('student','email',['required','email']) || emailExists }"  @input="debouncedCheckEmailExistence" required/>
                            <div v-if="v$.student.email.$dirty" :class="{ 'text-danger': checkInputValidity('student','email',['required','email']) || emailExists }">
                                <p v-if="v$.student.email.required.$invalid">
                                    Email Address is required.
                                </p>
                                <p v-if="v$.student.email.email.$invalid">
                                    Email Address is invalid.
                                </p>
                                <p v-if="emailExists">
                                   Email Address already exists.
                                </p>
                            </div>
                        </div>

                        <div class="col-4">
                            <label>Phone #1 <span class="text-danger">*</span></label>
                            <Input type="number" step="0.01"  v-model="student.phone_number_1" :class="{ inputInvalidClass : checkInputValidity('student','phone_number_1',['required', 'minLength', 'maxLength']) }"  required autocomplete="name" />
                            <div  v-if="v$.student.phone_number_1.$dirty" :class="{ 'text-danger':  checkInputValidity('student','phone_number_1',['required', 'minLength', 'maxLength']) }">
                                <p v-if="v$.student.phone_number_1.required.$invalid">
                                    Phone number is required.
                                </p>
                                <p v-if="v$.student.phone_number_1.minLength.$invalid">
                                    Phone number must be at least 11 characters.
                                </p>
                                <p v-if="v$.student.phone_number_1.maxLength.$invalid">
                                    Phone number must be no more than 13 characters.
                                </p>
                            </div>
                        </div>

                        <div class="col-4">
                            <label for="student_last_name" >Phone #2</label>
                            <Input type="number" step="0.01" v-model="student.phone_number_2" />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4">
                            <label>Gender <span class="text-danger">*</span></label>
                            <VueMultiselect  :loading="loadingGenders" :disabled="loadingGenders" :class="{ inputInvalidClass : checkInputValidity('student','gender',['required']) }" v-model="student.gender" track-by="label" label="label" placeholder="Select Gender" :allow-empty="false" :options="genders"></VueMultiselect>
                            <div  v-if="v$.student.gender.$dirty" :class="{ 'text-danger': checkInputValidity('student','gender',['required']) }">
                                <p v-if="v$.student.gender.required.$invalid">
                                    Gender is required.
                                </p>
                            </div>
                        </div>
                        <div class="col-4">
                            <label>Date of Birth <span class="text-danger">*</span></label>
                            <VueDatePicker  :class="{ inputInvalidClass : checkInputValidity('student','date_of_birth',['required']) }"  v-model="student.date_of_birth" placeholder="Select Date of Birth" format="MM-dd-yyyy" required></VueDatePicker>
                            <div  v-if="v$.student.date_of_birth.$dirty" :class="{ 'text-danger':  checkInputValidity('student','date_of_birth',['required']) }">
                                <p v-if="v$.student.date_of_birth.required.$invalid">
                                    Date of Birth is required.
                                </p>
                            </div>
                        </div>
                    </div>

                </tab-content>
                <tab-content title="Guardian Information" icon="fa-solid fa-hands-holding-child" :beforeChange="validateGuardianDetails">
                    <div class="row mt-2">
                        <div class="col-12 text-end">
                            <button type="button" @click="addGuardian"  class="btn btn-primary text-end me-2">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div v-for="(guardian, index) in guardians" :key="index" class="row align-items-center">
                        <h2 class="mb-3">{{ guardians[index].guardian_type?.label }}</h2>   
                        <div v-for="(property, key) in guardian" :key="key" class="col-4 mb-3">
                            <label>{{ guardians_label_options[key].label }}  <span class="text-danger" v-if="guardians_label_options[key].required">*</span></label>
                            <Input :type="!key.includes('phone') ? 'text' : 'number'" v-model="guardians[index][key]" v-if="!['guardian_type'].includes(key) && !guardians_label_options[key].required"/>
                            <Input :type="!key.includes('phone') ? 'text' : 'number'" v-model="guardians[index][key]" v-if="!['guardian_type'].includes(key) &&  guardians_label_options[key].required" :class="{ inputInvalidClass : checkLoopInputValidity('guardians', key, guardians_label_options[key].validations, index)}"/>
                            <VueMultiselect v-if="['guardian_type'].includes(key)" v-model="guardians[index].guardian_type" :class="{ inputInvalidClass : checkLoopInputValidity('guardians', key, guardians_label_options[key].validations, index)}"  track-by="label" label="label" :placeholder="`Select ${guardians_label_options[key].label}`" :allow-empty="false" :options="relationships"></VueMultiselect>
                            <template v-if="guardians_label_options[key].required">
                                <div  v-if="v$.guardians[index][key].$dirty" :class="{ 'text-danger':  checkLoopValidationErrors('guardians', key, guardians_label_options[key].validations, index)?.length }">
                                    <span v-for="error in checkLoopValidationErrors('guardians', key, guardians_label_options[key].validations, index)" :key="error">
                                        <span v-if="error == 'required'">
                                            {{ guardians_label_options[key].label }} is required.
                                        </span>
                                        <span v-if="error == 'minLength'">
                                            {{ guardians_label_options[key].label }} must be at least 11 characters.
                                        </span>
                                        <span v-if="error == 'maxLength'">
                                            {{ guardians_label_options[key].label }} must be no more than 13 characters.
                                        </span>
                                        <span v-if="error == 'email'">
                                            {{ guardians_label_options[key].label }} is invalid.
                                        </span>
                                    </span>
                                </div>
                            </template>
                        </div>
                        <div class="col-3">
                          <br>
                          <button @click="deleteGuardian(index)" v-if="index != 0" type="button" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-minus"></i>
                          </button>
                        </div>
                    </div>
                </tab-content>
                <tab-content title="Address Information" icon="fa-solid fa-location-dot" :beforeChange="validateAddressDetails">
                   <div class="row mb-3">
                        <div class="col-12">
                            <label>Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" v-model="address_information.address" :class="{ inputInvalidClass : checkInputValidity('address_information','address',['required']) }" placeholder="Address..." id="floatingTextarea" rows="5" required></textarea>
                            <div  v-if="v$.address_information.address.$dirty" :class="{ 'text-danger':  checkInputValidity('address_information','address',['required'])}">
                                <p v-if="v$.address_information.address.required.$invalid">
                                    Address is required.
                                </p>
                            </div>
                        </div>
                    </div>
                   <div class="row mb-3">
                        <div class="col-4">
                            <label>Landmark <span class="text-danger">*</span></label>
                            <Input type="email" v-model="address_information.landmark" :class="{ inputInvalidClass : checkInputValidity('address_information','landmark',['required']) }" required/>
                            <div  v-if="v$.address_information.landmark.$dirty" :class="{ 'text-danger':  checkInputValidity('address_information','landmark',['required'])}">
                                <p v-if="v$.address_information.landmark.required.$invalid">
                                    Landmark is required.
                                </p>
                            </div>
                        </div>
                        <div class="col-4">
                            <label>Contact Person <span class="text-danger">*</span></label>
                            <Input type="email" v-model="address_information.contact_person" :class="{ inputInvalidClass : checkInputValidity('address_information','contact_person',['required']) }"  required/>
                            <div  v-if="v$.address_information.contact_person.$dirty" :class="{ 'text-danger':  checkInputValidity('address_information','contact_person',['required'])}">
                                <p v-if="v$.address_information.contact_person.required.$invalid">
                                    Contact person is required.
                                </p>
                            </div>
                        </div>
                        <div class="col-4">
                            <label>Phone # <span class="text-danger">*</span></label>
                            <Input type="number" step="0.1" v-model="address_information.phone_number"  :class="{ inputInvalidClass : checkInputValidity('address_information','phone_number',['required','minLength','maxLength']) }" required/>
                            <div  v-if="v$.address_information.phone_number.$dirty" :class="{ 'text-danger':  checkInputValidity('address_information','phone_number',['required','minLength','maxLength'])}">
                                <p v-if="v$.address_information.phone_number.required.$invalid">
                                    Phone number is required.
                                </p>
                                <p v-if="v$.address_information.phone_number.minLength.$invalid">
                                    Phone number must be at least 11 characters.
                                </p>
                                <p v-if="v$.address_information.phone_number.maxLength.$invalid">
                                    Phone number must be no more than 13 characters.
                                </p>
                            </div>
                        </div>
                    </div>
                </tab-content>
                <tab-content title="Health Information" icon="fa-solid fa-notes-medical">
                    <div class="row mb-3">
                        <div class="col-4">
                            <label>Height <code>(cm)</code></label>
                            <Input type="number" step="0.1" v-model="health_information.height"/>
                        </div>
                        <div class="col-4">
                            <label>Weight <code>(cm)</code></label>
                            <Input type="number" step="0.1" v-model="health_information.weight"/>
                        </div>
                        <div class="col-4">
                            <label>Blood Type</label>
                            <Input type="text" v-model="health_information.blood_type" :class="{ inputInvalidClass : checkInputValidity('health_information','blood_type',['maxLength']) }"/>
                            <div  v-if="v$.health_information.blood_type.$dirty" :class="{ 'text-danger':  checkInputValidity('health_information','blood_type',['maxLength'])}">
                                <p v-if="v$.health_information.blood_type.maxLength.$invalid">
                                    Blood Type must be no more than 5 characters.
                                </p>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label>Allergies</label>
                            <Input type="text" v-model="health_information.allergies"/>
                        </div>
                        <div class="col-4">
                            <label>Medications</label>
                            <Input type="text" v-model="health_information.medications"/>
                        </div>
                        <div class="col-4">
                            <label>Emergency Contact Person</label>
                            <Input type="text" v-model="health_information.emergency_contact_person"/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label>Emergency Contact #</label>
                            <Input type="number" v-model="health_information.emergency_contact_number"/>
                        </div>
                        <div class="col-4">
                            <label>Last checkup</label>
                            <Input type="text" v-model="health_information.last_checkup"/>
                        </div>
                    </div>
                </tab-content>
                <template v-slot:next="props">
                    <button type="button"  v-if="props.activeTabIndex  == 0" class="btn btn-md btn-primary me-1 px-5" :disabled="firstStepDisable">Next</button>
                </template>
            </form-wizard>
        </template>
    </Modal>
</template>

<script>
import Modal from '@/components/Modal/modal.vue';
// import FloatingInput from '@/components/Form/FloatingInput.vue'
import Input from '@/components/Form/Input.vue'
import {FormWizard, TabContent} from 'vue3-form-wizard'
import VueDatePicker from '@vuepic/vue-datepicker';
import { useVuelidate } from '@vuelidate/core'
import { required, email, maxLength, minLength } from '@vuelidate/validators';
// import defaultProfile from '@/../../public/storage/default_images/profile.png';
import { swalConfirmation, swalSuccess, swalError, SwalDefault } from '@/helpers/Notification/sweetAlert.js';
import { checkValidity, checkLoopValidity, checkLoopErrors } from '@/helpers/Vuelidate/InputValidation.js';
import VueMultiselect from 'vue-multiselect'
import debounce from 'lodash/debounce'; 
    export default {
        name:'Student Registration',
        setup () {
            return { v$: useVuelidate({ $autoDirty: true ,student: {} }) }
        },
        emits: ['loadUpdatedStudents'],
        data(){
            return{
                defaultProfileImage: null,
                loadingWizard: false,
                image:null,
                file:null,
                student:{
                    first_name:null,
                    middle_name:null,
                    last_name:null,
                    email:null,
                    phone_number_1:null,
                    phone_number_2:null,
                    date_of_birth:null,
                    gender:null,
                    school_year:null,
                },
                isSaving:false,
                genders:[],
                relationships:[],
                school_years:[],
                loadingSchoolYears:false,
                loadingGenders:false,
                loadingRelationships:false,
                guardians:[
                    {
                        first_name:null,
                        middle_name:null,
                        last_name:null,
                        email:null,
                        phone_number_1:null,
                        phone_number_2:null,
                        guardian_type:null,
                    }
                ],
                guardians_label_options:{
                    first_name:{
                        label:'First Name',
                        validations:['required'],
                        required: true,
                    },
                    middle_name:{
                        label:'Middle Name',
                        required: false,
                    },
                    last_name:{
                        label:'Last Name',
                        validations:['required'],
                        required: true,
                    },
                    email:{
                        label:'Email Address',
                        validations:['required','email'],
                        required: true,
                    },
                    phone_number_1:{
                        label:'Phone #1',
                        validations:['required','maxLength','minLength'],
                        required: true,
                    },
                    phone_number_2:{
                        label:'Phone #2',
                        required: false,
                    },
                    guardian_type:{
                        label:'Relationship',
                        validations:['required'],
                        required: true,
                    }
                },
                address_information:{
                    address:null,
                    landmark:null,
                    contact_person:null,
                    phone_number:null,
                },
                health_information:{
                    height:null,
                    weight:null,
                    blood_type:null,
                    allergies:null,
                    medications:null,
                    emergency_contact_person:null,
                    emergency_contact_number:null,
                    last_checkup:null,
                },
                auth_token:`Bearer ${localStorage.getItem('auth-token')}`,
                emailExists:false,
                firstStepDisable:false
                
            }
        },
        validations () {
            return {
                student: {
                    first_name: { required },
                    last_name: { required },
                    email: { required, email },
                    phone_number_1: { required, maxLength: maxLength(13), minLength: minLength(11) },
                    date_of_birth: { required },
                    gender: { required },
                    school_year: { required },
                },
                guardians:this.guardians.map((guardian) => ({
                    first_name: { required },
                    last_name: { required },
                    email: { required, email },
                    phone_number_1: { required, maxLength: maxLength(13), minLength: minLength(11) },
                    guardian_type: { required },
                })),
                address_information: {
                    address: { required },
                    landmark: { required },
                    contact_person: { required },
                    phone_number: { required,  maxLength: maxLength(13), minLength: minLength(11) }
                },
                health_information: {
                    blood_type: { maxLength: maxLength(5)}
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
            await this.getSchoolYears()
            await this.getGenderOptions()
            await this.getGuardianTypes()
        },
        methods:{
            async getGenderOptions(){
                this.loadingGenders = true;
                await axios.get('/api/genderOptions', { 
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                .then((response) => {
                    const { genders } = response.data;
                    console.log(genders, response);
                    this.genders = genders;
                    this.loadingGenders = false;
                    console.log(response,'GENDERS');
                })
                .catch((error) =>{
                    console.log(error,'error');
                });
            },

            async getGuardianTypes(){
                await axios.get('/api/guardianTypeOptions', { 
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                .then((response) => {
                    const { guardianTypes } = response.data;
                    console.log(guardianTypes, response);

                    this.relationships = guardianTypes;
                    console.log(response,'GUARDIANS');
                })
                .catch((error) =>{
                    console.log(error,'ERROR');
                });
            },
            
            async getSchoolYears(){
                this.loadingSchoolYears = true;

                await axios.get('/api/schoolYearOptions', { 
                    headers: {
                        Authorization: this.auth_token
                    }
                })
                .then((response) => {
                    const { schoolYears } = response.data;

                    this.school_years = schoolYears;
                    this.loadingSchoolYears = false;
                })
                .catch((error) =>{
                    console.log(error,'ERROR');
                });
            },

            removeImage(){
                this.file = null;
                this.image = null;
            },

            debouncedCheckEmailExistence: debounce(function() {
                this.firstStepDisable = true; 
                this.emailExists = false;
                if(this.student.email.length > 0){
                    this.checkEmailExistence();
                }
            }, 500),

            // Function to check email existence (replace with your actual implementation)
            async checkEmailExistence() {
                try {
                    const response = await axios.post('/api/student-email-existence', {email: this.student.email},{   headers: {
                        Authorization: this.auth_token
                    }}
                    );

                    const { exists } = response.data;

                    // Update the component state with the result
                    this.emailExists = exists;
                    this.firstStepDisable = false;
                } catch (error) {
                    // Handle errors
                    console.error('Error checking email existence:', error);
                }
            },

            checkInputValidity(parentProperty = null, dataProperty, validations = []) {
               return checkValidity(this.v$, parentProperty, dataProperty, validations);
            },
            
            checkLoopInputValidity(parentProperty = null, dataProperty, validations = [], index = null) {
               return checkLoopValidity(this.v$, parentProperty, dataProperty, validations, index);
            },

            checkLoopValidationErrors(parentProperty = null, dataProperty, validations = [], index = null){
                return checkLoopErrors(this.v$, parentProperty, dataProperty, validations, index);
            },

            validateStudentDetails(){
                // this.v$.student.$touch()
                // const isValid =  this.v$.student.$errors.length || this.emailExists ? false : true ;
                // return isValid;
                return true;
            },

            validateGuardianDetails(){
                // this.v$.guardians.$touch()
                // const isValid =  this.v$.guardians.$errors.length ? false : true ;
                // return isValid;
                return true;
            },

            validateAddressDetails(){
                // this.v$.address_information.$touch()
                // const isValid =  this.v$.address_information.$errors.length ? false : true ;
                // return isValid;
                return true;
            },

            uploadImage(e){
                e.preventDefault()
                const file = e.target.files[0];
                this.file = file;
                if (file) {
                    // Use FileReader to read the file as a data URL
                    const reader = new FileReader();
                    reader.onload = () => {
                        this.image = reader.result; // Set the imageUrl to the data URL
                    };
                    reader.readAsDataURL(file);
                }
            },

            addGuardian() {
                this.guardians.push({
                    first_name:null,
                    middle_name:null,
                    last_name:null,
                    email:null,
                    phone_number_1:null,
                    phone_number_2:null,
                    guardian_type:null,
                });
            },

            deleteGuardian(index) {
                this.guardians.splice(index, 1);
            },

            formReset(){
                this.image = null;
                this.file = null;
                this.student ={
                    first_name:null,
                    middle_name:null,
                    last_name:null,
                    email:null,
                    phone_number_1:null,
                    phone_number_2:null,
                    date_of_birth:null,
                    gender:null,
                    school_year:null,
                };
                this.guardians =[
                    {
                        first_name:null,
                        middle_name:null,
                        last_name:null,
                        email:null,
                        phone_number_1:null,
                        phone_number_2:null,
                        guardian_type:null,
                    }
                ];
                this.address_information = {
                    address:null,
                    landmark:null,
                    contact_person:null,
                    phone_number:null,
                };
                this.health_information = {
                    height:null,
                    weight:null,
                    blood_type:null,
                    allergies:null,
                    medications:null,
                    emergency_contact_person:null,
                    emergency_contact_number:null,
                    last_checkup:null,
                };
                this.emailExists = false;
                this.firstStepDisable = false; 
                this.$refs.formWizard.reset();
                this.v$.$reset();
            },

            async register(){
                if(!await this.v$.$validate()){
                    return;
                }

                this.isSaving = true;

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
               
                axios.post('/api/student',formData,{
                    headers:{
                        Authorization: this.auth_token,
                    }
                })
                .then((response) => {
                    this.isSaving = false;
                    this.formReset();
                    this.$emit('loadUpdatedStudents');
                    SwalDefault.fire({
                        icon: "success",
                        text: "Succesfully registered, You'll be receiving an email for registration approval in the following days.",
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